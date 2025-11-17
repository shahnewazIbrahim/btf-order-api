@extends('layouts.app')

@section('title', 'Product Details – BTF Order Management')

@section('content')
    <div class="max-w-3xl">
        <div class="mb-4 flex items-center justify-between gap-3">
            <div>
                <h1 class="text-lg font-semibold text-slate-800">Product Details</h1>
                <p class="text-xs text-slate-500">
                    Basic product information from the database.
                </p>
            </div>
            <a href="{{ route('products.index') }}" class="text-xs text-indigo-600 hover:underline">
                Back to list
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 space-y-3">
            <div>
                <p class="text-xs text-slate-500 uppercase">Name</p>
                <p class="text-sm font-semibold text-slate-800">{{ $product->name }}</p>
            </div>

            <div>
                <p class="text-xs text-slate-500 uppercase">Slug</p>
                <p class="text-sm text-slate-700">{{ $product->slug }}</p>
            </div>

            <div>
                <p class="text-xs text-slate-500 uppercase">Vendor</p>
                <p class="text-sm text-slate-700">{{ $product->vendor->name ?? 'N/A' }}</p>
            </div>

            <div>
                <p class="text-xs text-slate-500 uppercase">Base Price</p>
                <p class="text-sm font-semibold text-slate-800">
                    {{ number_format($product->base_price, 2) }}
                </p>
            </div>

            <div>
                <p class="text-xs text-slate-500 uppercase">Status</p>
                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px]
                    {{ $product->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                    {{ $product->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>

            @if($product->description)
                <div>
                    <p class="text-xs text-slate-500 uppercase">Description</p>
                    <p class="text-sm text-slate-700">{{ $product->description }}</p>
                </div>
            @endif
        </div>
    </div>
@endsection
