{{-- store/partials/tracking_panel.blade.php --}}
<div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-5 space-y-3">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-xs uppercase tracking-wide text-slate-500">Tracking</p>
            <p class="text-sm text-slate-700">Your recent orders</p>
        </div>
        <button @click="loadOrders"
                class="text-xs px-3 py-1 rounded-md bg-slate-900 text-white hover:bg-slate-800">
            Refresh
        </button>
    </div>

    <div class="space-y-3 max-h-72 overflow-auto pr-1">
        <template x-for="order in orders" :key="order.id">
            <div class="border border-slate-100 rounded-lg p-3 shadow-sm space-y-1">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-semibold text-slate-900" x-text="order.order_number"></p>
                    <span class="text-[11px] px-2 py-1 rounded-full border" :class="statusBadge(order.status)">
                        <span x-text="order.status"></span>
                    </span>
                </div>

                <p class="text-xs text-slate-500" x-text="order.created_at"></p>
                <p class="text-sm text-slate-800">Total: <span x-text="order.total"></span></p>

                <div class="text-[11px] text-slate-600">
                    <template x-for="item in order.items" :key="item.id">
                        <div>
                            <span x-text="item.product?.name || 'Product'"></span>
                            <span class="text-slate-400">x</span>
                            <span x-text="item.quantity"></span>
                        </div>
                    </template>
                </div>
            </div>
        </template>

        <p x-show="!orders.length" class="text-xs text-slate-500">No orders yet.</p>
    </div>
</div>
