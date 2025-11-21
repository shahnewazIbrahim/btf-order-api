<?php

namespace App\Services;

use App\Events\InventoryLowStock;
use App\Events\OrderStatusUpdated;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderService
{
    protected array $allowedTransitions = [
        'pending'    => ['processing', 'cancelled'],
        'processing' => ['shipped', 'cancelled'],
        'shipped'    => ['delivered', 'cancelled'],
        'delivered'  => [],         // আর কিছুতে যাবে না
        'cancelled'  => [],         // আর কিছুতে যাবে না
    ];

    public function listForUser($user)
    {
        $query = Order::with('items.product', 'items.variant')
            ->where('customer_id', $user->id)
            ->orderByDesc('id');

        // Admin/Vendor চাইলে এখানে role অনুযায়ী adjust করতে পারো

        return $query->paginate(15);
    }

    /**
     * Create order + deduct inventory
     * $data = ['items' => [ ['product_id'=>, 'variant_id'=>?, 'qty'=>, 'price'=>?], ... ]]
     */
    public function create(array $data, $customer): Order
    {
        return DB::transaction(function () use ($data, $customer) {
            $itemsData = $data['items'] ?? [];

            if (empty($itemsData)) {
                throw new \InvalidArgumentException('Order items required');
            }

            $subtotal = 0;

            foreach ($itemsData as $item) {
                $subtotal += $item['price'] * $item['qty'];
            }

            $discount = $data['discount'] ?? 0;
            $total = $subtotal - $discount;

            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'customer_id'  => $customer->id,
                'subtotal'     => $subtotal,
                'discount'     => $discount,
                'total'        => $total,
                'status'       => 'pending',
            ]);

            foreach ($itemsData as $item) {
                /** @var Product $product */
                $product = Product::findOrFail($item['product_id']);

                $variant = null;
                if (!empty($item['variant_id'])) {
                    $variant = ProductVariant::findOrFail($item['variant_id']);

                    if ($variant->product_id !== $product->id) {
                        throw ValidationException::withMessages([
                            'variant_id' => "Variant {$variant->id} does not belong to product {$product->id}",
                        ]);
                    }
                }

                OrderItem::create([
                    'order_id'           => $order->id,
                    'product_id'         => $product->id,
                    'product_variant_id' => $variant?->id,
                    'quantity'           => $item['qty'],
                    'unit_price'         => $item['price'],
                    'total_price'        => $item['qty'] * $item['price'],
                ]);

                // inventory deduct (if variant)
                if ($variant) {
                    /** @var Inventory $inventory */
                    $inventory = $variant->inventory()->lockForUpdate()->first();

                    if (! $inventory || $inventory->stock < $item['qty']) {
                        throw new \RuntimeException('Insufficient stock for variant ' . $variant->id);
                    }

                    $inventory->stock -= $item['qty'];
                    $inventory->save();

                    // Fire low-stock event after deduction
                    if ($inventory->stock <= $inventory->low_stock_threshold) {
                        event(new InventoryLowStock($inventory));
                    }
                }
            }

            // এখানে চাইলে OrderCreated event fire করে email/pdf job dispatch করতে পারো

            return $order->load('items.product', 'items.variant');
        });
    }

    /**
     * Update order status + rollback inventory on cancel
     */
    public function updateStatus(Order $order, string $newStatus): Order
    {
        $oldStatus = $order->status;

        if (! isset($this->allowedTransitions[$oldStatus])) {
            throw ValidationException::withMessages([
                'status' => "Invalid current status: {$oldStatus}",
            ]);
        }

        if (! in_array($newStatus, $this->allowedTransitions[$oldStatus], true)) {
            throw ValidationException::withMessages([
                'status' => "Status cannot change from {$oldStatus} to {$newStatus}",
            ]);
        }

        return DB::transaction(function () use ($order, $oldStatus, $newStatus) {
            // cancel হলে inventory rollback (আগের মতো)
            if ($newStatus === 'cancelled') {
                $this->rollbackInventory(
                    $order->items()->with('variant.inventory')->lockForUpdate()->get()
                );
            }

            $order->status = $newStatus;
            $order->save();

            event(new OrderStatusUpdated($order->fresh('items.product', 'customer'), $oldStatus, $newStatus));

            return $order;
        });
    }

    protected function rollbackInventory(Collection $items): void
    {
        foreach ($items as $item) {
            if (! $item->product_variant_id) {
                continue;
            }

            $inventory = Inventory::where('product_variant_id', $item->product_variant_id)
                ->lockForUpdate()
                ->first();

            if ($inventory) {
                $inventory->stock += $item->quantity;
                $inventory->save();
            }
        }
    }
}
