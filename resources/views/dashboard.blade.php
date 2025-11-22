@extends('layouts.app')

@section('title', 'Dashboard – BTF Order Management')

@section('content')
    <div class="grid gap-6 md:grid-cols-3 mb-6">
        {{-- Stat cards --}}
        <div class="bg-white rounded-xl shadow-sm p-4 border border-slate-100">
            <p class="text-xs uppercase tracking-wide text-slate-500 mb-1">Users</p>
            <p class="text-2xl font-semibold text-slate-800">{{ $stats['users'] }}</p>
            <p class="text-xs text-slate-400 mt-1">Total registered users</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-4 border border-slate-100">
            <p class="text-xs uppercase tracking-wide text-slate-500 mb-1">Products</p>
            <p class="text-2xl font-semibold text-slate-800">{{ $stats['products'] }}</p>
            <p class="text-xs text-slate-400 mt-1">Available products</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-4 border border-slate-100">
            <p class="text-xs uppercase tracking-wide text-slate-500 mb-1">Orders</p>
            <p class="text-2xl font-semibold text-slate-800">{{ $stats['orders'] }}</p>
            <p class="text-xs text-slate-400 mt-1">Total orders placed</p>
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        {{-- Recent products --}}
        <div class="bg-white rounded-xl shadow-sm p-4 border border-slate-100">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-slate-800">Recent Products</h2>
                <a href="{{ route('admin.products.index') }}" class="text-xs text-indigo-600 hover:underline">
                    View all
                </a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($recentProducts as $product)
                    <div class="py-2 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-800">
                                {{ $product->name }}
                            </p>
                            <p class="text-xs text-slate-500">
                                Base price: {{ number_format($product->base_price, 2) }}
                            </p>
                        </div>
                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] text-slate-600">
                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                @empty
                    <p class="text-xs text-slate-400">No products yet.</p>
                @endforelse
            </div>
        </div>

        {{-- Recent orders --}}
        <div class="bg-white rounded-xl shadow-sm p-4 border border-slate-100">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-slate-800">Recent Orders</h2>
                <a href="{{ route('admin.orders.index') }}" class="text-xs text-indigo-600 hover:underline">
                    View all
                </a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($recentOrders as $order)
                    <div class="py-2 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-800">
                                {{ $order->order_number }}
                            </p>
                            <p class="text-xs text-slate-500">
                                Customer: {{ $order->customer->name ?? 'N/A' }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-slate-800">
                                {{ number_format($order->total, 2) }}
                            </p>
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px]
                                @class([
                                    'bg-yellow-100 text-yellow-700' => $order->status === 'pending',
                                    'bg-blue-100 text-blue-700' => $order->status === 'processing',
                                    'bg-indigo-100 text-indigo-700' => $order->status === 'shipped',
                                    'bg-emerald-100 text-emerald-700' => $order->status === 'delivered',
                                    'bg-rose-100 text-rose-700' => $order->status === 'cancelled',
                                ])
                            ">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400">No orders yet.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
