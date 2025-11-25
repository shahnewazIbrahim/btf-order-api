{{-- store/partials/wishlist_drawer.blade.php --}}
<div x-show="wishlistOpen" x-cloak class="fixed inset-0 bg-slate-900/40 flex items-center justify-end z-40">
    <div class="w-full max-w-md bg-white h-full shadow-2xl p-5 overflow-auto">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold text-slate-900">Wishlist</h3>
            <button @click="wishlistOpen=false" class="text-xs text-slate-500 hover:text-slate-700">Close</button>
        </div>

        <div class="space-y-3">
            <template x-for="(item, idx) in wishlist" :key="item.id">
                <div class="border border-slate-100 rounded-lg p-3 flex items-center gap-3">
                    <div class="h-16 w-16 bg-slate-100 rounded-lg overflow-hidden">
                        <img :src="item.image_url || 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=200&q=60'"
                             class="w-full h-full object-cover">
                    </div>

                    <div class="flex-1">
                        <p class="text-sm font-semibold text-slate-900" x-text="item.name"></p>
                        <p class="text-xs text-slate-500" x-text="item.slug"></p>
                    </div>

                    <div class="flex flex-col gap-1 text-xs">
                        <button @click="addToCart(item)"
                                class="px-3 py-1 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">
                            Add to cart
                        </button>
                        <button @click="removeFromWishlist(item.id)" class="text-rose-600 hover:underline">Remove</button>
                    </div>
                </div>
            </template>

            <p x-show="!wishlist.length" class="text-xs text-slate-500">Wishlist is empty.</p>
        </div>
    </div>
</div>

