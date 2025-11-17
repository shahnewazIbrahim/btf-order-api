<?php

namespace App\Listeners;

use App\Events\OrderStatusUpdated;
use App\Mail\OrderStatusUpdatedMail;
use Illuminate\Support\Facades\Mail;

class SendOrderStatusEmail
{
    public function handle(OrderStatusUpdated $event): void
    {
        $customer = $event->order->customer;

        if (! $customer || ! $customer->email) {
            return;
        }

        Mail::to($customer->email)->queue(
            new OrderStatusUpdatedMail($event->order, $event->oldStatus, $event->newStatus)
        );
    }
}
