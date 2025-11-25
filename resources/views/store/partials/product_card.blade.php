{{-- store/partials/product_card.blade.php --}}
<div class="border border-slate-100 rounded-xl shadow-sm overflow-hidden flex flex-col hover:-translate-y-0.5 hover:shadow transition">
    <div class="relative aspect-video bg-slate-100">
        <img :src="product.image_url || 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=600&q=60'"
             class="w-full h-full object-cover" alt="">

        <button @click.stop="toggleWishlist(product)"
                class="absolute top-2 right-2 h-8 w-8 rounded-full bg-white shadow flex items-center justify-center text-rose-500 hover:bg-rose-50">
            <template x-if="isWishlisted(product.id)">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 fill-rose-500" viewBox="0 0 24 24">
                    <path d="M12 21s-6-4.35-9-8.7C-1 5.65 4.5-.35 9 3.2 13.5-.35 19 5.65 21 12.3 18 16.65 12 21 12 21z"/>
                </svg>
            </template>
            <template x-if="!isWishlisted(product.id)">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M12 21s-6-4.35-9-8.7C-1 5.65 4.5-.35 9 3.2 13.5-.35 19 5.65 21 12.3 18 16.65 12 21 12 21z"/>
                </svg>
            </template>
        </button>
    </div>

    <div class="p-4 flex flex-col gap-2 flex-1">
        <div class="flex items-start justify-between gap-2">
            <div>
                <p class="font-semibold text-slate-900" x-text="product.name"></p>
                <p class="text-xs text-slate-500" x-text="product.slug"></p>
            </div>
            <span class="text-[11px] px-2 py-1 rounded-full border"
                  :class="product.is_active
                        ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                        : 'bg-slate-100 text-slate-500 border-slate-200'">
                <span x-text="product.is_active ? 'Active' : 'Inactive'"></span>
            </span>
        </div>

        <p class="text-sm text-slate-600 line-clamp-2" x-text="product.description || 'No description'"></p>
        <p class="text-lg font-semibold text-slate-900">৳ <span x-text="product.base_price"></span></p>

        <div class="space-y-1">
            <label class="text-xs font-semibold text-slate-700">Variant</label>
            <select class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
                    @focus="ensureVariants(product.id)"
                    @change="selectVariant(product.id, $event.target.value)">
                <option value="">None</option>
                <template x-for="variant in variants[product.id] || []" :key="variant.id">
                    <option :value="variant.id" x-text="variantLabel(variant)"></option>
                </template>
                <option x-show="variantsLoadingById[product.id]" disabled>Loading variants...</option>
            </select>
        </div>

        <div class="flex items-center gap-2">
            <input type="number" min="1" value="1"
                   @input="updateQuantity(product.id, $event.target.value)"
                   class="w-20 rounded-md border border-slate-200 px-2 py-1 text-sm focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">

            <button @click="addToCart(product)"
                    class="flex-1 px-3 py-2 text-sm bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                Add to cart
            </button>

            <button @click="openQuickView(product)"
                    class="px-3 py-2 text-xs rounded-md border border-slate-200 text-slate-700 hover:bg-slate-50">
                Details
            </button>
        </div>
    </div>
</div>
