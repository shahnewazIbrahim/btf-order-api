<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\ProductVariantController;
use App\Http\Controllers\Api\V1\InventoryController;

Route::prefix('v1')
    ->name('api.')              // 🔥 সব নামের আগে api. prefix
    ->group(function () {

        // Simple health check: GET /api/v1/health
        Route::get('/health', fn() => response()->json(['status' => 'ok']))
            ->name('health');   // name: api.health

        // ---------- Auth ----------
        Route::post('auth/register', [AuthController::class, 'register'])
            ->name('auth.register'); // api.auth.register
        Route::post('auth/login', [AuthController::class, 'login'])
            ->name('auth.login');    // api.auth.login

        Route::middleware('auth:api')->group(function () {
            Route::get('auth/me', [AuthController::class, 'me'])
                ->name('auth.me');         // api.auth.me
            Route::post('auth/refresh', [AuthController::class, 'refresh'])
                ->name('auth.refresh');    // api.auth.refresh
            Route::post('auth/logout', [AuthController::class, 'logout'])
                ->name('auth.logout');     // api.auth.logout

            // ---------- Products ----------
            Route::apiResource('products', ProductController::class)
                ->names('products'); // api.products.index, api.products.store, ...
            Route::post('products/import', [ProductController::class, 'import'])
                ->name('products.import');

            // 🔹 Product variants (nested)
            Route::get('products/{product}/variants', [ProductVariantController::class, 'index'])
                ->name('api.products.variants.index');
            Route::post('products/{product}/variants', [ProductVariantController::class, 'store'])
                ->name('api.products.variants.store');
            Route::get('products/{product}/variants/{variant}', [ProductVariantController::class, 'show'])
                ->name('api.products.variants.show');
            Route::put('products/{product}/variants/{variant}', [ProductVariantController::class, 'update'])
                ->name('api.products.variants.update');
            Route::delete('products/{product}/variants/{variant}', [ProductVariantController::class, 'destroy'])
                ->name('api.products.variants.destroy');

            // 🔹 Inventory per variant
            Route::get('variants/{variant}/inventory', [InventoryController::class, 'show'])
                ->name('api.variants.inventory.show');
            Route::put('variants/{variant}/inventory', [InventoryController::class, 'update'])
                ->name('api.variants.inventory.update');

            // ---------- Orders ----------
            Route::get('orders', [OrderController::class, 'index'])
                ->name('orders.index');    // api.orders.index
            Route::post('orders', [OrderController::class, 'store'])
                ->name('orders.store');    // api.orders.store
            Route::get('orders/{order}', [OrderController::class, 'show'])
                ->name('orders.show');     // api.orders.show
            Route::post('orders/{order}/status', [OrderController::class, 'updateStatus'])
                ->name('orders.updateStatus'); // api.orders.updateStatus
            Route::get('orders/{order}/invoice', [OrderController::class, 'invoice'])
                ->name('orders.invoice');
        });
    });
