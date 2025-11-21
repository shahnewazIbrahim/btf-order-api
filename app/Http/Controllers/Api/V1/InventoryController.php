<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\InventoryLowStock;
use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function show(ProductVariant $variant)
    {
        $inventory = $variant->inventory;

        if (! $inventory) {
            return response()->json([
                'product_variant_id' => $variant->id,
                'stock' => 0,
                'low_stock_threshold' => 0,
            ]);
        }

        return response()->json($inventory);
    }

    public function update(Request $request, ProductVariant $variant)
    {
        $this->authorizeVariant($request->user('api'), $variant);

        $data = $request->validate([
            'stock'               => ['required', 'integer', 'min:0'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0'],
        ]);

        $inventory = $variant->inventory()->firstOrCreate([
            'product_variant_id' => $variant->id,
        ]);

        // আগে নতুন মান বসাই
        $inventory->stock = $data['stock'];
        if (isset($data['low_stock_threshold'])) {
            $inventory->low_stock_threshold = $data['low_stock_threshold'];
        }
        $inventory->save();

        // তারপর condition চেক করে event fire করি
        if ($inventory->stock <= $inventory->low_stock_threshold) {
            event(new InventoryLowStock($inventory));
        }

        return response()->json($inventory);
    }

    protected function authorizeVariant(?User $user, ProductVariant $variant): void
    {
        if (! $user || ! $user->role) {
            abort(403);
        }

        $role = $user->role->name;

        if ($role === 'Admin') {
            return;
        }

        if ($role === 'Vendor' && $variant->product->user_id === $user->id) {
            return;
        }

        abort(403);
    }
}
