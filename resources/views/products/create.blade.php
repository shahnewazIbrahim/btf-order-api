@extends('layouts.app')

@section('title', 'Create Product – BTF Order Management')

@section('content')
    <div class="max-w-xl">
        <div class="mb-4 flex items-center justify-between gap-3">
            <div>
                <h1 class="text-lg font-semibold text-slate-800">Create Product</h1>
                <p class="text-xs text-slate-500">
                    This form uses the same ProductService used by the API.
                </p>
            </div>
            <a href="{{ route('admin.products.index') }}" class="text-xs text-indigo-600 hover:underline">
                Back to list
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
            <form action="{{ route('admin.products.store') }}" method="POST" class="space-y-4" enctype="multipart/form-data">
                @csrf

                <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-700" for="name">Name</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-700" for="description">Description</label>
                    <textarea
                        id="description"
                        name="description"
                        rows="3"
                        class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Optional short description...">{{ old('description') }}</textarea>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-700" for="image">Image</label>
                    <input
                        type="file"
                        id="image"
                        name="image"
                        accept="image/*"
                        class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-700" for="base_price">Base Price</label>
                    <input
                        type="number"
                        step="0.01"
                        id="base_price"
                        name="base_price"
                        value="{{ old('base_price') }}"
                        required
                        class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="flex items-center gap-2">
                    <input
                        type="checkbox"
                        id="is_active"
                        name="is_active"
                        value="1"
                        class="rounded border-slate-300"
                        {{ old('is_active', true) ? 'checked' : '' }}>
                    <label for="is_active" class="text-xs text-slate-700">Active</label>
                </div>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                    Save Product
                </button>
            </form>
        </div>
    </div>
@endsection
