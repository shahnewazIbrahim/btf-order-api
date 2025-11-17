<?php

namespace App\Listeners;

use App\Events\InventoryLowStock;
use App\Mail\LowStockMail;
use Illuminate\Support\Facades\Mail;

class SendLowStockNotification
{
    public function handle(InventoryLowStock $event): void
    {
        $product = $event->inventory->variant->product;
        $vendor  = $product->vendor;

        if (!$vendor || !$vendor->email) {
            return;
        }

        Mail::to($vendor->email)->queue(
            new LowStockMail($event->inventory)
        );
    }
}
