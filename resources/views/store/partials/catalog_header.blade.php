{{-- store/partials/catalog_header.blade.php --}}
<div class="flex flex-wrap items-center gap-3 justify-between">
    <div>
        <p class="text-xs uppercase tracking-wide text-slate-500">Catalog</p>
        <p class="text-sm text-slate-700">Pick products & variants</p>
    </div>

    <div class="flex items-center gap-2">
        <input x-model="filters.search"
               @input.debounce.500ms="loadProducts()"
               placeholder="Search name or description"
               class="rounded-md border border-slate-200 px-3 py-2 text-sm focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">

        <select x-model="filters.sort"
                @change="loadProducts()"
                class="rounded-md border border-slate-200 px-3 py-2 text-sm focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
            <option value="new">Newest</option>
            <option value="price_asc">Price: Low to High</option>
            <option value="price_desc">Price: High to Low</option>
        </select>

        <button @click="loadProducts()"
                class="px-3 py-2 text-sm bg-slate-900 text-white rounded-md hover:bg-slate-800">
            Refresh
        </button>
    </div>
</div>
