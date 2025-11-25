{{-- store/partials/cart_panel.blade.php --}}
<div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-5 space-y-3">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-xs uppercase tracking-wide text-slate-500">Cart</p>
            <p class="text-sm text-slate-700">Items ready to order</p>
        </div>
        <button @click="clearCart" class="text-xs text-rose-600 hover:underline">Clear</button>
    </div>

    <div class="space-y-3 max-h-72 overflow-auto pr-1">
        <template x-for="(item, idx) in cart" :key="idx">
            <div class="border border-slate-100 rounded-lg p-3 shadow-sm space-y-1">
                <div class="flex items-center justify-between gap-2">
                    <div>
                        <p class="text-sm font-semibold text-slate-900" x-text="item.name"></p>
                        <p class="text-[11px] text-slate-500" x-text="item.variant_label || 'No variant'"></p>
                    </div>
                    <button @click="removeFromCart(idx)" class="text-[11px] text-rose-600 hover:underline">Remove</button>
                </div>

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

    <div class="border-t border-slate-100 pt-3 flex items-center justify-between">
        <div class="space-y-1 text-xs text-slate-500">
            <p>Payment</p>
            <select x-model="payment.method"
                    class="rounded-md border border-slate-200 px-3 py-2 text-sm focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="cod">Cash on delivery</option>
                <option value="card">Card (mock)</option>
            </select>
        </div>

        <div class="text-right">
            <p class="text-sm font-semibold text-slate-900">Total</p>
            <p class="text-lg font-bold text-slate-900">৳ <span x-text="cartTotal().toFixed(2)"></span></p>
        </div>
    </div>

    <button @click="checkout"
            class="w-full px-4 py-2 text-sm font-semibold bg-emerald-600 text-white rounded-md hover:bg-emerald-700">
        Place Order
    </button>
    <p class="text-[11px] text-slate-500">Posts to /api/v1/orders with your cart.</p>
</div>
