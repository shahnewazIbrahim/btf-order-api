@extends('layouts.app')

@section('title', 'Products – BTF Order Management')

@section('content')
    <div class="mb-4 flex items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-semibold text-slate-800">Products</h1>
            <p class="text-xs text-slate-500">
                Manage products stored in the database.
            </p>
        </div>

        {{-- ✅ Add Product button --}}
        <a href="{{ route('products.create') }}"
           class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-indigo-700">
            + Add Product
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="min-w-full divide-y divide-slate-100 text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">ID</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">Name</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">Vendor</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">Base Price</th>
                    <th class="px-4 py-2 text-center text-xs font-medium text-slate-500 uppercase">Status</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($products as $product)
                    <tr>
                        <td class="px-4 py-2 text-xs text-slate-500">
                            #{{ $product->id }}
                        </td>
                        <td class="px-4 py-2">
                            <div class="font-medium text-slate-800">{{ $product->name }}</div>
                            <div class="text-xs text-slate-400">{{ $product->slug }}</div>
                        </td>
                        <td class="px-4 py-2 text-xs text-slate-600">
                            {{ $product->vendor->name ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-2 text-right text-sm text-slate-800">
                            {{ number_format($product->base_price, 2) }}
                        </td>
                        <td class="px-4 py-2 text-center">
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px]
                                {{ $product->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>

                        <td class="px-4 py-2 text-right text-xs">
                            <div class="inline-flex items-center gap-2">
                                <a href="{{ route('products.edit', $product) }}"
                                   class="px-2 py-1 rounded-md border border-slate-200 text-slate-700 hover:bg-slate-50">
                                    Edit
                                </a>

                                <form action="{{ route('products.destroy', $product) }}" method="POST"
                                      onsubmit="return confirm('Are you sure you want to delete this product?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="px-2 py-1 rounded-md border border-rose-200 text-rose-700 hover:bg-rose-50">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        {{-- ✅ 6 columns --}}
                        <td colspan="6" class="px-4 py-6 text-center text-xs text-slate-400">
                            No products found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($products instanceof \Illuminate\Pagination\AbstractPaginator)
            <div class="px-4 py-3 border-t border-slate-100 bg-slate-50">
                {{ $products->links() }}
            </div>
        @endif
    </div>
@endsection
