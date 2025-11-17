<?php

namespace App\Services;

use App\Events\InventoryLowStock;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Str;

class VariantService
{
    public function listForProduct(Product $product)
    {
        return $product->variants()
            ->with('inventory')
            ->orderBy('id', 'asc')
            ->get();
    }

    public function create(Product $product, array $data): ProductVariant
    {
        // sku না দিলে auto জেনারেট
        if (empty($data['sku'])) {
            $data['sku'] = strtoupper(Str::slug($product->name)) . '-' . Str::upper(Str::random(6));
        }

        $variant = $product->variants()->create([
            'sku'        => $data['sku'],
            'name'       => $data['name'] ?? null,
            'attributes' => $data['attributes'] ?? null,
            'price'      => $data['price'] ?? null,
        ]);

        // প্রাথমিক inventory রেকর্ড
        Inventory::create([
            'product_variant_id'   => $variant->id,
            'stock'                => $data['stock'] ?? 0,
            'low_stock_threshold'  => $data['low_stock_threshold'] ?? 5,
        ]);

        return $variant->load('inventory');
    }

    public function update(ProductVariant $variant, array $data): ProductVariant
    {
        $variant->update([
            'name'       => $data['name'] ?? $variant->name,
            'sku'        => $data['sku'] ?? $variant->sku,
            'attributes' => $data['attributes'] ?? $variant->attributes,
            'price'      => array_key_exists('price', $data) ? $data['price'] : $variant->price,
        ]);


        if (! empty($data['stock']) || isset($data['low_stock_threshold'])) {
            $inventory = $variant->inventory()->firstOrCreate([
                'product_variant_id' => $variant->id,
            ]);

            if (isset($data['stock'])) {
                $inventory->stock = $data['stock'];
            }
            if (isset($data['low_stock_threshold'])) {
                $inventory->low_stock_threshold = $data['low_stock_threshold'];
            }
            $inventory->save();
        }

        return $variant->load('inventory');
    }

    public function delete(ProductVariant $variant): void
    {
        // inventory cascadeOnDelete আছে, তাই শুধু variant delete করলেই হবে
        $variant->delete();
    }
}
