{{-- store/partials/topbar.blade.php --}}
<div class="bg-white border border-slate-100 rounded-2xl shadow-sm px-4 py-3 flex flex-wrap items-center gap-3 justify-between">
    <div class="flex items-center gap-3">
        <div class="h-10 w-10 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold">B</div>
        <div>
            <p class="text-sm font-semibold text-slate-900">BTF Store</p>
            <p class="text-xs text-slate-500">Shop - Wishlist - Track</p>
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-2 text-xs">
        <button class="px-3 py-1.5 rounded-full border border-slate-200 text-slate-700 hover:bg-slate-50">Deals</button>
        <button class="px-3 py-1.5 rounded-full border border-slate-200 text-slate-700 hover:bg-slate-50">New Arrivals</button>
        <button class="px-3 py-1.5 rounded-full border border-slate-200 text-slate-700 hover:bg-slate-50">Best Sellers</button>
        <button class="px-3 py-1.5 rounded-full border border-slate-200 text-slate-700 hover:bg-slate-50">Support</button>
    </div>

    <div class="flex items-center gap-3 text-xs">
        <div class="flex items-center gap-1">
            <select class="rounded-md border border-slate-200 px-2 py-1 text-xs text-slate-700">
                <option>BDT</option>
                <option>USD</option>
            </select>
            <select class="rounded-md border border-slate-200 px-2 py-1 text-xs text-slate-700">
                <option>EN</option>
                <option>BN</option>
            </select>
        </div>

        <div class="relative">
            <button @click="toggleWishlistDrawer"
                    class="px-3 py-1.5 rounded-md bg-rose-50 text-rose-700 border border-rose-100 hover:bg-rose-100">
                Wishlist (<span x-text="wishlist.length"></span>)
            </button>
        </div>

        <div class="relative">
            <button @click="toggleCartDrawer"
                    class="px-3 py-1.5 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">
                Cart (<span x-text="cartCount()"></span>)
            </button>
        </div>

        <div class="flex items-center gap-2">
            <template x-if="token">
                <div class="flex items-center gap-2">
                    <div class="text-right">
                        <p class="text-xs font-semibold text-slate-800" x-text="user?.name || 'Logged in'"></p>
                        <p class="text-[11px] text-slate-500" x-text="user?.role?.name || ''"></p>
                    </div>
                    <button @click="logoutToken"
                            class="px-2 py-1 text-[11px] rounded-md border border-slate-200 text-slate-700 hover:bg-slate-100">
                        Logout
                    </button>
                </div>
            </template>

            <template x-if="!token">
                <button @click="showAuth = true"
                        class="px-3 py-1.5 rounded-md border border-slate-200 text-slate-700 hover:bg-slate-50">
                    Login
                </button>
            </template>
        </div>
    </div>
</div>
