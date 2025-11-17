<?php

namespace App\Events;

use App\Models\Inventory;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InventoryLowStock
{
    use Dispatchable, SerializesModels;

    public function __construct(public Inventory $inventory)
    {
    }
}
