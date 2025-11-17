<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\ProductBrowserController;
use App\Http\Controllers\Web\OrderBrowserController;
use App\Http\Controllers\Web\AuthWebController;

// Login routes (session-based web auth)
Route::get('/login', [AuthWebController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthWebController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthWebController::class, 'logout'])->name('logout');

// Web UI routes (already under 'web' middleware via bootstrap/app.php)
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Products
Route::get('/products', [ProductBrowserController::class, 'index'])->name('products.index');
Route::get('/products/create', [ProductBrowserController::class, 'create'])->name('products.create');
Route::post('/products', [ProductBrowserController::class, 'store'])->name('products.store');
Route::get('/products/{product}', [ProductBrowserController::class, 'show'])->name('products.show');
Route::get('/products/{product}/edit', [ProductBrowserController::class, 'edit'])->name('products.edit');
Route::put('/products/{product}', [ProductBrowserController::class, 'update'])->name('products.update');
Route::delete('/products/{product}', [ProductBrowserController::class, 'destroy'])->name('products.destroy');

// Orders
Route::get('/orders', [OrderBrowserController::class, 'index'])->name('orders.index');
Route::get('/orders/create', [OrderBrowserController::class, 'create'])->name('orders.create');
Route::post('/orders', [OrderBrowserController::class, 'store'])->name('orders.store.web');
