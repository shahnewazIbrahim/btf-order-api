{{-- store/partials/hero.blade.php --}}
<section class="relative overflow-hidden rounded-3xl border border-slate-100 bg-gradient-to-r from-indigo-50 via-sky-50 to-white p-8 shadow-sm">
    <div class="grid gap-6 md:grid-cols-2 items-center">
        <div class="space-y-4">
            <p class="text-xs uppercase tracking-[0.3em] text-slate-500">Live API - Real Orders</p>
            <h1 class="text-4xl font-semibold text-slate-900 leading-tight">
                Unlock new arrivals with exclusive deals and fast checkout.
            </h1>
            <p class="text-sm text-slate-600 max-w-xl">
                Browse products, pick variants, add to wishlist, and checkout securely. Every action hits your /api/v1 endpoints.
            </p>

            <div class="flex items-center gap-3">
                <button @click="scrollToCatalog"
                        class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700">
                    Shop now
                </button>
                <button @click="filters.search=''; loadProducts()"
                        class="px-4 py-2 rounded-lg bg-white text-indigo-700 border border-indigo-100 text-sm font-semibold hover:bg-indigo-50">
                    View all
                </button>
            </div>

            <div class="flex items-center gap-6 text-xs text-slate-600">
                <div class="flex items-center gap-2"><span class="h-2 w-2 bg-emerald-500 rounded-full"></span> In-stock products</div>
                <div class="flex items-center gap-2"><span class="h-2 w-2 bg-indigo-500 rounded-full"></span> Variant pricing</div>
                <div class="flex items-center gap-2"><span class="h-2 w-2 bg-rose-500 rounded-full"></span> Wishlist support</div>
            </div>
        </div>

        <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-5 space-y-3">
            <template x-if="token">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">Account</p>
                            <p class="text-sm text-slate-800">You're logged in</p>
                        </div>
                        <span class="text-[11px] px-2 py-1 rounded-full border bg-emerald-50 text-emerald-700 border-emerald-200">
                            Token set
                        </span>
                    </div>

                    <div class="bg-slate-50 border border-slate-100 rounded-lg p-3 text-sm text-slate-700" x-show="user">
                        <p class="font-semibold" x-text="user?.name"></p>
                        <p class="text-xs text-slate-500" x-text="user?.email"></p>
                        <p class="text-[11px] text-slate-400">
                            Role: <span x-text="user?.role?.name || 'N/A'"></span>
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <button @click="loadOrders"
                                class="px-3 py-2 text-xs font-semibold bg-slate-900 text-white rounded-md hover:bg-slate-800">
                            Refresh orders
                        </button>
                        <button @click="logoutToken"
                                class="px-3 py-2 text-xs font-medium bg-slate-100 text-slate-700 rounded-md hover:bg-slate-200">
                            Logout
                        </button>
                    </div>
                </div>
            </template>

            <template x-if="!token">
                <div class="space-y-3" x-show="showAuth">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">Account</p>
                            <p class="text-sm text-slate-800">Login to continue</p>
                        </div>
                        <span class="text-[11px] px-2 py-1 rounded-full border bg-slate-100 text-slate-500 border-slate-200">
                            No token
                        </span>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-medium text-slate-700">Email</label>
                        <input x-model="auth.email" type="email"
                               class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="customer@example.com">
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-medium text-slate-700">Password</label>
                        <input x-model="auth.password" type="password"
                               class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="password">
                    </div>

                    <div class="flex items-center gap-2">
                        <button @click="login"
                                class="px-3 py-2 text-sm font-semibold bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Login
                        </button>
                        <button @click="register"
                                class="px-3 py-2 text-sm font-semibold bg-slate-900 text-white rounded-md hover:bg-slate-800">
                            Quick Register
                        </button>
                    </div>

                    <p class="text-[11px] text-slate-500">
                        Seed users: customer@example.com / vendor@example.com / admin@example.com (password: password).
                    </p>
                </div>
            </template>
        </div>
    </div>
</section>
