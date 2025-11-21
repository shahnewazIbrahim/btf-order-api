<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_invalid_status_transition_throws_validation_exception()
    {
        $order = Order::create([
            'order_number' => 'ORD-999',
            'customer_id' => User::create([
                'name' => 'Customer',
                'email' => 'cust@example.com',
                'password' => bcrypt('password'),
                'role_id' => Role::create(['name' => 'Customer'])->id,
            ])->id,
            'subtotal' => 10,
            'discount' => 0,
            'total' => 10,
            'status' => 'delivered',
        ]);

        $service = $this->app->make(OrderService::class);

        $this->expectException(ValidationException::class);
        $service->updateStatus($order, 'processing');
    }
}
