<?php

namespace App\Actions\Orders;

use App\Models\Order;
use App\Services\OrderService;

class CreateOrder
{
    public function __construct(protected OrderService $orders)
    {
    }

    public function __invoke(array $data, $customer): Order
    {
        return $this->orders->create($data, $customer);
    }
}
