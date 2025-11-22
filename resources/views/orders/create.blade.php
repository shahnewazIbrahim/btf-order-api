@extends('layouts.app')

@section('title', 'Create Order – BTF Order Management')

@section('content')
    <div class="mb-4 flex items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-semibold text-slate-800">Create Order</h1>
            <p class="text-xs text-slate-500">
                Select quantities for products. Prices are taken from product base price.
            </p>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="text-xs text-indigo-600 hover:underline">
            Back to orders
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
        <form action="{{ route('admin.orders.store.web') }}" method="POST" class="space-y-4">
            @csrf

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-slate-500 uppercase">Product</th>
                        <th class="px-3 py-2 text-right text-xs font-medium text-slate-500 uppercase">Price</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-slate-500 uppercase">Quantity</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    @forelse($products as $product)
                        <tr>
                            <td class="px-3 py-2">
                                <div class="text-sm font-medium text-slate-800">
                                    {{ $product->name }}
                                </div>
                                <div class="text-xs text-slate-400">
                                    {{ $product->slug }}
                                </div>
                            </td>
                            <td class="px-3 py-2 text-right text-sm text-slate-800">
                                {{ number_format($product->base_price, 2) }}
                            </td>
                            <td class="px-3 py-2 text-center">
                                <input
                                    type="number"
                                    name="items[{{ $product->id }}][qty]"
                                    value="{{ old("items.$product->id.qty", 0) }}"
                                    min="0"
                                    class="w-20 rounded-md border border-slate-200 px-2 py-1 text-sm text-center focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-3 py-4 text-center text-xs text-slate-400">
                                No active products available. Please create a product first.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between gap-4 pt-2 border-t border-slate-100 mt-4">
                <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-700" for="discount">Discount</label>
                    <input
                        type="number"
                        step="0.01"
                        id="discount"
                        name="discount"
                        value="{{ old('discount', 0) }}"
                        class="w-32 rounded-md border border-slate-200 px-2 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                    Place Order
                </button>
            </div>
        </form>
    </div>
@endsection
