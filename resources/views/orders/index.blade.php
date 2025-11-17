@extends('layouts.app')

@section('title', 'Orders – BTF Order Management')

@section('content')
    <div class="mb-4 flex items-center justify-between gap-3">
        <h1 class="text-lg font-semibold text-slate-800">Orders</h1>
        <p class="text-xs text-slate-500">
            Read-only summary of orders and statuses.
        </p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="min-w-full divide-y divide-slate-100 text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">Order #</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">Customer</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">Items</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">Total</th>
                    <th class="px-4 py-2 text-center text-xs font-medium text-slate-500 uppercase">Status</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">Created</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($orders as $order)
                    <tr>
                        <td class="px-4 py-2 text-xs text-slate-700 font-mono">
                            {{ $order->order_number }}
                        </td>
                        <td class="px-4 py-2 text-sm text-slate-700">
                            {{ $order->customer->name ?? 'N/A' }}
                            <div class="text-xs text-slate-400">
                                {{ $order->customer->email ?? '' }}
                            </div>
                        </td>
                        <td class="px-4 py-2 text-xs text-slate-600">
                            @foreach($order->items as $item)
                                <div>
                                    {{ $item->product->name ?? 'N/A' }}
                                    × {{ $item->quantity }}
                                </div>
                            @endforeach
                        </td>
                        <td class="px-4 py-2 text-right text-sm text-slate-800">
                            {{ number_format($order->total, 2) }}
                        </td>
                        <td class="px-4 py-2 text-center">
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
                        </td>
                        <td class="px-4 py-2 text-right text-xs text-slate-500">
                            {{ $order->created_at?->format('Y-m-d H:i') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-xs text-slate-400">
                            No orders found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($orders instanceof \Illuminate\Pagination\AbstractPaginator)
            <div class="px-4 py-3 border-t border-slate-100 bg-slate-50">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
@endsection
