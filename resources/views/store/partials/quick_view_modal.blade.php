{{-- store/partials/quick_view_modal.blade.php --}}
<div x-show="quickView" x-cloak class="fixed inset-0 bg-slate-900/40 flex items-center justify-center z-50">
    <div class="bg-white w-full max-w-3xl rounded-2xl shadow-2xl overflow-hidden">
        <div class="grid md:grid-cols-2">
            <div class="bg-slate-100 h-full">
                <img :src="quickView?.image_url || 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=800&q=60'"
                     class="w-full h-full object-cover">
            </div>

            <div class="p-5 space-y-3">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xl font-semibold text-slate-900" x-text="quickView?.name"></p>
                        <p class="text-xs text-slate-500" x-text="quickView?.slug"></p>
                    </div>
                    <button @click="quickView=null" class="text-xs text-slate-500 hover:text-slate-700">Close</button>
                </div>

                <p class="text-sm text-slate-600" x-text="quickView?.description || 'No description'"></p>
                <p class="text-2xl font-bold text-slate-900">৳ <span x-text="quickView?.base_price"></span></p>

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-700">Variant</label>
                    <select class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
                            @focus="ensureVariants(quickView.id)"
                            @change="selectVariant(quickView.id, $event.target.value)">
                        <option value="">None</option>
                        <template x-for="variant in variants[quickView.id] || []" :key="variant.id">
                            <option :value="variant.id" x-text="variantLabel(variant)"></option>
                        </template>
                        <option x-show="variantsLoadingById[quickView.id]" disabled>Loading variants...</option>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <input type="number" min="1" value="1"
                           @input="updateQuantity(quickView.id, $event.target.value)"
                           class="w-24 rounded-md border border-slate-200 px-2 py-1 text-sm focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">

                    <button @click="addToCart(quickView)"
                            class="flex-1 px-4 py-2 text-sm bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Add to cart
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

