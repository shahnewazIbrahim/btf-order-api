<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users'    => User::count(),
            'products' => Product::count(),
            'orders'   => Order::count(),
        ];

        $recentProducts = Product::latest()->take(5)->get();
        $recentOrders   = Order::with('customer')->latest()->take(5)->get();

        return view('dashboard', compact('stats', 'recentProducts', 'recentOrders'));
    }
}
