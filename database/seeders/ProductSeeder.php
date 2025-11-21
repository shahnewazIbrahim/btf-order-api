<?php

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $vendor = User::whereHas('role', fn($q) => $q->where('name', 'Vendor'))->first();

        if (! $vendor) {
            return;
        }

        $products = [
            [
                'name'        => 'Classic T-Shirt',
                'description' => 'Soft cotton crew neck tee',
                'base_price'  => 25.00,
                'variants'    => [
                    ['sku' => 'TS-RED-M', 'name' => 'Red / M', 'price' => 27.00, 'stock' => 30],
                    ['sku' => 'TS-BLK-L', 'name' => 'Black / L', 'price' => 28.00, 'stock' => 15],
                ],
            ],
            [
                'name'        => 'Running Shoes',
                'description' => 'Lightweight shoes for daily runs',
                'base_price'  => 80.00,
                'variants'    => [
                    ['sku' => 'RS-9', 'name' => 'Size 9', 'price' => 85.00, 'stock' => 20],
                    ['sku' => 'RS-10', 'name' => 'Size 10', 'price' => 85.00, 'stock' => 12],
                ],
            ],
            [
                'name'        => 'Wireless Headphones',
                'description' => 'Noise cancelling over-ear headphones',
                'base_price'  => 150.00,
                'variants'    => [
                    ['sku' => 'WH-STANDARD', 'name' => 'Standard', 'price' => 150.00, 'stock' => 10, 'low_stock_threshold' => 3],
                ],
            ],
        ];

        foreach ($products as $data) {
            $product = Product::updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                [
                    'user_id'     => $vendor->id,
                    'name'        => $data['name'],
                    'description' => $data['description'],
                    'base_price'  => $data['base_price'],
                    'is_active'   => true,
                ]
            );

            foreach ($data['variants'] as $variantData) {
                $variant = ProductVariant::updateOrCreate(
                    ['sku' => $variantData['sku']],
                    [
                        'product_id' => $product->id,
                        'name'       => $variantData['name'] ?? null,
                        'attributes' => $variantData['attributes'] ?? null,
                        'price'      => $variantData['price'] ?? $product->base_price,
                    ]
                );

                Inventory::updateOrCreate(
                    ['product_variant_id' => $variant->id],
                    [
                        'stock'               => $variantData['stock'] ?? 0,
                        'low_stock_threshold' => $variantData['low_stock_threshold'] ?? 5,
                    ]
                );
            }
        }
    }
}
