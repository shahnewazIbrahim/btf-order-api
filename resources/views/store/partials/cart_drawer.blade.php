{{-- store/partials/cart_drawer.blade.php --}}
<div x-show="cartOpen" x-cloak class="fixed inset-0 bg-slate-900/40 flex items-center justify-end z-40">
    <div class="w-full max-w-md bg-white h-full shadow-2xl p-5 overflow-auto">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold text-slate-900">Cart</h3>
            <button @click="cartOpen=false" class="text-xs text-slate-500 hover:text-slate-700">Close</button>
        </div>

        <div class="space-y-3">
            <template x-for="(item, idx) in cart" :key="idx">
                <div class="border border-slate-100 rounded-lg p-3 space-y-1">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-slate-900" x-text="item.name"></p>
                        <button @click="removeFromCart(idx)" class="text-[11px] text-rose-600 hover:underline">Remove</button>
                    </div>

                    <p class="text-[11px] text-slate-500" x-text="item.variant_label || 'No variant'"></p>

                    <div class="flex items-center gap-3 text-sm text-slate-700">
                        <span>
                            Qty:
                            <input type="number" min="1"
                                   class="w-16 border border-slate-200 rounded-md px-2 py-1 text-sm"
                                   :value="item.qty"
                                   @input="changeCartQty(idx, $event.target.value)">
                        </span>
                        <span>Price: <span x-text="item.price"></span></span>
                    </div>
                </div>
            </template>

            <p x-show="!cart.length" class="text-xs text-slate-500">Cart is empty.</p>
        </div>

        <div class="mt-4 border-t border-slate-100 pt-3 flex items-center justify-between">
            <p class="text-sm font-semibold text-slate-900">Total</p>
            <p class="text-lg font-bold text-slate-900">৳ <span x-text="cartTotal().toFixed(2)"></span></p>
        </div>

        <button @click="checkout"
                class="w-full mt-3 px-4 py-2 text-sm font-semibold bg-emerald-600 text-white rounded-md hover:bg-emerald-700">
            Checkout
        </button>
    </div>
</div>

