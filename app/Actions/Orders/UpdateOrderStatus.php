<?php

namespace App\Actions\Orders;

use App\Models\Order;
use App\Services\OrderService;

class UpdateOrderStatus
{
    public function __construct(protected OrderService $orders)
    {
    }

    public function __invoke(Order $order, string $status): Order
    {
        return $this->orders->updateStatus($order, $status);
    }
}
