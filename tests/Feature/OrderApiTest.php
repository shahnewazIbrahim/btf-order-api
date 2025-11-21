<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer;
    protected User $vendor;
    protected User $admin;

    protected Product $product;
    protected ProductVariant $variant;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::create(['name' => 'Admin']);
        $vendorRole = Role::create(['name' => 'Vendor']);
        $customerRole = Role::create(['name' => 'Customer']);

        $this->admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role_id' => $adminRole->id,
        ]);

        $this->vendor = User::create([
            'name' => 'Vendor',
            'email' => 'vendor@example.com',
            'password' => bcrypt('password'),
            'role_id' => $vendorRole->id,
        ]);

        $this->customer = User::create([
            'name' => 'Customer',
            'email' => 'customer@example.com',
            'password' => bcrypt('password'),
            'role_id' => $customerRole->id,
        ]);

        $this->product = Product::create([
            'user_id' => $this->vendor->id,
            'name' => 'Test Product',
            'slug' => 'test-product',
            'description' => 'Demo',
            'base_price' => 100,
            'is_active' => true,
        ]);

        $this->variant = ProductVariant::create([
            'product_id' => $this->product->id,
            'sku' => 'TP-001',
            'name' => 'Default',
            'attributes' => null,
            'price' => 100,
        ]);

        Inventory::create([
            'product_variant_id' => $this->variant->id,
            'stock' => 5,
            'low_stock_threshold' => 2,
        ]);
    }

    public function test_customer_can_place_order_and_deduct_inventory()
    {
        $payload = [
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'variant_id' => $this->variant->id,
                    'qty' => 2,
                    'price' => 100,
                ],
            ],
        ];

        $response = $this->actingAs($this->customer, 'api')
            ->postJson('/api/v1/orders', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment(['status' => 'pending']);

        $this->assertDatabaseHas('inventories', [
            'product_variant_id' => $this->variant->id,
            'stock' => 3,
        ]);
    }

    public function test_admin_can_cancel_and_restore_stock()
    {
        /** @var Order $order */
        $order = Order::create([
            'order_number' => 'ORD-123',
            'customer_id' => $this->customer->id,
            'subtotal' => 100,
            'discount' => 0,
            'total' => 100,
            'status' => 'processing',
        ]);

        $order->items()->create([
            'product_id' => $this->product->id,
            'product_variant_id' => $this->variant->id,
            'quantity' => 1,
            'unit_price' => 100,
            'total_price' => 100,
        ]);

        Inventory::where('product_variant_id', $this->variant->id)->update(['stock' => 4]);

        $response = $this->actingAs($this->admin, 'api')
            ->postJson("/api/v1/orders/{$order->id}/status", ['status' => 'cancelled']);

        $response->assertStatus(200)
            ->assertJsonFragment(['status' => 'cancelled']);

        $this->assertDatabaseHas('inventories', [
            'product_variant_id' => $this->variant->id,
            'stock' => 5, // restored
        ]);
    }
}
