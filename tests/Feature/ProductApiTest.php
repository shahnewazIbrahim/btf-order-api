<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $vendor;
    protected Role $vendorRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->vendorRole = Role::create(['name' => 'Vendor']);
        $this->vendor = User::create([
            'name' => 'Vendor User',
            'email' => 'vendor@example.com',
            'password' => bcrypt('password'),
            'role_id' => $this->vendorRole->id,
        ]);
    }

    public function test_vendor_can_create_product()
    {
        $payload = [
            'name' => 'Test Product',
            'description' => 'Desc',
            'base_price' => 10.5,
            'is_active' => true,
        ];

        $response = $this->actingAs($this->vendor, 'api')
            ->postJson('/api/v1/products', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'Test Product']);

        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'user_id' => $this->vendor->id,
        ]);
    }

    public function test_vendor_sees_only_own_products()
    {
        // another vendor
        $otherVendor = User::create([
            'name' => 'Other Vendor',
            'email' => 'other@example.com',
            'password' => bcrypt('password'),
            'role_id' => $this->vendorRole->id,
        ]);

        Product::create(['name' => 'Mine', 'slug' => 'mine', 'user_id' => $this->vendor->id, 'base_price' => 10, 'is_active' => true]);
        Product::create(['name' => 'Not Mine', 'slug' => 'not-mine', 'user_id' => $otherVendor->id, 'base_price' => 20, 'is_active' => true]);

        $response = $this->actingAs($this->vendor, 'api')
            ->getJson('/api/v1/products');

        $response->assertStatus(200);
        $this->assertStringContainsString('Mine', $response->getContent());
        $this->assertStringNotContainsString('Not Mine', $response->getContent());
    }
}
