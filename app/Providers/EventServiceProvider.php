<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        \App\Events\InventoryLowStock::class => [
            \App\Listeners\SendLowStockNotification::class,
        ],

        \App\Events\OrderStatusUpdated::class => [
            \App\Listeners\SendOrderStatusEmail::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}
