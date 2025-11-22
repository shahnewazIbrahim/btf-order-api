@extends('layouts.app')

@section('title', 'Storefront � BTF Order Management')

@section('content')
    <div x-data="storefront()" class="space-y-10">
        {{-- Topbar inside store (store-level nav) --}}
        <div class="bg-white border border-slate-100 rounded-2xl shadow-sm px-4 py-3 flex flex-wrap items-center gap-3 justify-between">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold">B</div>
                <div>
                    <p class="text-sm font-semibold text-slate-900">BTF Store</p>
                    <p class="text-xs text-slate-500">Shop � Wishlist � Track</p>
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
                    <button @click="toggleWishlistDrawer" class="px-3 py-1.5 rounded-md bg-rose-50 text-rose-700 border border-rose-100 hover:bg-rose-100">
                        Wishlist (<span x-text="wishlist.length"></span>)
                    </button>
                </div>
                <div class="relative">
                    <button @click="toggleCartDrawer" class="px-3 py-1.5 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">
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
                            <button @click="logoutToken" class="px-2 py-1 text-[11px] rounded-md border border-slate-200 text-slate-700 hover:bg-slate-100">Logout</button>
                        </div>
                    </template>
                    <template x-if="!token">
                        <button @click="showAuth = true" class="px-3 py-1.5 rounded-md border border-slate-200 text-slate-700 hover:bg-slate-50">Login</button>
                    </template>
                </div>
            </div>
        </div>

        {{-- Hero Banner --}}
        <section class="relative overflow-hidden rounded-3xl border border-slate-100 bg-gradient-to-r from-indigo-50 via-sky-50 to-white p-8 shadow-sm">
            <div class="grid gap-6 md:grid-cols-2 items-center">
                <div class="space-y-4">
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-500">Live API � Real Orders</p>
                    <h1 class="text-4xl font-semibold text-slate-900 leading-tight">Unlock new arrivals with exclusive deals and fast checkout.</h1>
                    <p class="text-sm text-slate-600 max-w-xl">
                        Browse products, pick variants, add to wishlist, and checkout securely. Every action hits your `/api/v1` endpoints�no mocks.
                    </p>
                    <div class="flex items-center gap-3">
                        <button @click="scrollToCatalog" class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700">Shop now</button>
                        <button @click="filters.search=''; loadProducts()" class="px-4 py-2 rounded-lg bg-white text-indigo-700 border border-indigo-100 text-sm font-semibold hover:bg-indigo-50">View all</button>
                    </div>
                    <div class="flex items-center gap-6 text-xs text-slate-600">
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 bg-emerald-500 rounded-full"></span> In-stock products
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 bg-indigo-500 rounded-full"></span> Variant pricing
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 bg-rose-500 rounded-full"></span> Wishlist support
                        </div>
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
                                <span class="text-[11px] px-2 py-1 rounded-full border bg-emerald-50 text-emerald-700 border-emerald-200">Token set</span>
                            </div>
                            <div class="bg-slate-50 border border-slate-100 rounded-lg p-3 text-sm text-slate-700" x-show="user">
                                <p class="font-semibold" x-text="user?.name"></p>
                                <p class="text-xs text-slate-500" x-text="user?.email"></p>
                                <p class="text-[11px] text-slate-400">Role: <span x-text="user?.role?.name || 'N/A'"></span></p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button @click="loadOrders" class="px-3 py-2 text-xs font-semibold bg-slate-900 text-white rounded-md hover:bg-slate-800">Refresh orders</button>
                                <button @click="logoutToken" class="px-3 py-2 text-xs font-medium bg-slate-100 text-slate-700 rounded-md hover:bg-slate-200">Logout</button>
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
                                <span class="text-[11px] px-2 py-1 rounded-full border bg-slate-100 text-slate-500 border-slate-200">No token</span>
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-medium text-slate-700">Email</label>
                                <input x-model="auth.email" type="email" class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500" placeholder="customer@example.com">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-medium text-slate-700">Password</label>
                                <input x-model="auth.password" type="password" class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500" placeholder="password">
                            </div>
                            <div class="flex items-center gap-2">
                                <button @click="login" class="px-3 py-2 text-sm font-semibold bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Login</button>
                                <button @click="register" class="px-3 py-2 text-sm font-semibold bg-slate-900 text-white rounded-md hover:bg-slate-800">Quick Register</button>
                            </div>
                            <p class="text-[11px] text-slate-500">Seed users: customer@example.com / vendor@example.com / admin@example.com (password: password).</p>
                        </div>
                    </template>
                </div>
            </div>
        </section>
        {{-- Category chips --}}
        <section class="flex flex-wrap gap-2">
            <template x-for="cat in categories" :key="cat">
                <button @click="toggleCategory(cat)"
                        class="px-3 py-1.5 rounded-full border text-xs font-semibold"
                        :class="activeCategories.includes(cat) ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'">
                    <span x-text="cat"></span>
                </button>
            </template>
            <button @click="clearCategories" class="px-3 py-1.5 rounded-full border text-xs text-slate-600 border-slate-200 hover:bg-slate-50">Clear filters</button>
        </section>

        {{-- Catalog + panels --}}
        <section id="catalog" class="grid gap-6 lg:grid-cols-4">
            <div class="lg:col-span-3 bg-white border border-slate-100 rounded-2xl shadow-sm p-5 space-y-4">
                <div class="flex flex-wrap items-center gap-3 justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-500">Catalog</p>
                        <p class="text-sm text-slate-700">Pick products & variants</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <input x-model="filters.search" @keyup.enter="loadProducts" placeholder="Search name or description" class="rounded-md border border-slate-200 px-3 py-2 text-sm focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                        <select x-model="filters.sort" @change="loadProducts" class="rounded-md border border-slate-200 px-3 py-2 text-sm focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="new">Newest</option>
                            <option value="price_asc">Price: Low to High</option>
                            <option value="price_desc">Price: High to Low</option>
                        </select>
                        <button @click="loadProducts" class="px-3 py-2 text-sm bg-slate-900 text-white rounded-md hover:bg-slate-800">Refresh</button>
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-4">
                    <template x-for="product in filteredProducts()" :key="product.id">
                        <div class="border border-slate-100 rounded-xl shadow-sm overflow-hidden flex flex-col hover:-translate-y-0.5 hover:shadow transition">
                            <div class="relative aspect-video bg-slate-100">
                                <img :src="product.image_url || 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=600&q=60'"
                                     class="w-full h-full object-cover" alt="">
                                <button @click.stop="toggleWishlist(product)" class="absolute top-2 right-2 h-8 w-8 rounded-full bg-white shadow flex items-center justify-center text-rose-500 hover:bg-rose-50">
                                    <template x-if="isWishlisted(product.id)">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 fill-rose-500" viewBox="0 0 24 24"><path d="M12 21s-6-4.35-9-8.7C-1 5.65 4.5-.35 9 3.2 13.5-.35 19 5.65 21 12.3 18 16.65 12 21 12 21z"/></svg>
                                    </template>
                                    <template x-if="!isWishlisted(product.id)">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 21s-6-4.35-9-8.7C-1 5.65 4.5-.35 9 3.2 13.5-.35 19 5.65 21 12.3 18 16.65 12 21 12 21z"/></svg>
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
                                          :class="product.is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-500 border-slate-200'">
                                        <span x-text="product.is_active ? 'Active' : 'Inactive'"></span>
                                    </span>
                                </div>
                                <p class="text-sm text-slate-600 line-clamp-2" x-text="product.description || 'No description'"></p>
                                <p class="text-lg font-semibold text-slate-900">? <span x-text="product.base_price"></span></p>
                                <div class="space-y-1">
                                    <label class="text-xs font-semibold text-slate-700">Variant</label>
                                    <select class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
                                            @change="selectVariant(product.id, $event.target.value)">
                                        <option value="">None</option>
                                        <template x-for="variant in variants[product.id] || []" :key="variant.id">
                                            <option :value="variant.id" x-text="variantLabel(variant)"></option>
                                        </template>
                                    </select>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input type="number" min="1" value="1"
                                           @input="updateQuantity(product.id, $event.target.value)"
                                           class="w-20 rounded-md border border-slate-200 px-2 py-1 text-sm focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                    <button @click="addToCart(product)" class="flex-1 px-3 py-2 text-sm bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Add to cart</button>
                                    <button @click="openQuickView(product)" class="px-3 py-2 text-xs rounded-md border border-slate-200 text-slate-700 hover:bg-slate-50">Details</button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                <p x-show="!filteredProducts().length" class="text-xs text-slate-500">No products found.</p>
            </div>

            {{-- Side panels: Cart & Tracking combined --}}
            <div class="space-y-4">
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
                                    <span>Qty: <input type="number" min="1" class="w-16 border border-slate-200 rounded-md px-2 py-1 text-sm"
                                                      :value="item.qty"
                                                      @input="changeCartQty(idx, $event.target.value)"></span>
                                    <span>Price: <span x-text="item.price"></span></span>
                                </div>
                            </div>
                        </template>
                        <p x-show="!cart.length" class="text-xs text-slate-500">Cart is empty.</p>
                    </div>
                    <div class="border-t border-slate-100 pt-3 flex items-center justify-between">
                        <div class="space-y-1 text-xs text-slate-500">
                            <p>Payment</p>
                            <select x-model="payment.method" class="rounded-md border border-slate-200 px-3 py-2 text-sm focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="cod">Cash on delivery</option>
                                <option value="card">Card (mock)</option>
                            </select>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-slate-900">Total</p>
                            <p class="text-lg font-bold text-slate-900">? <span x-text="cartTotal().toFixed(2)"></span></p>
                        </div>
                    </div>
                    <button @click="checkout" class="w-full px-4 py-2 text-sm font-semibold bg-emerald-600 text-white rounded-md hover:bg-emerald-700">Place Order</button>
                    <p class="text-[11px] text-slate-500">Posts to /api/v1/orders with your cart.</p>
                </div>

                <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-5 space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-slate-500">Tracking</p>
                            <p class="text-sm text-slate-700">Your recent orders</p>
                        </div>
                        <button @click="loadOrders" class="text-xs px-3 py-1 rounded-md bg-slate-900 text-white hover:bg-slate-800">Refresh</button>
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
                                            <span class="text-slate-400">�</span>
                                            <span x-text="item.quantity"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                        <p x-show="!orders.length" class="text-xs text-slate-500">No orders yet.</p>
                    </div>
                </div>
            </div>
        </section>
        {{-- Wishlist drawer modal --}}
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
                                <img :src="item.image_url || 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&w=200&q=60'" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-slate-900" x-text="item.name"></p>
                                <p class="text-xs text-slate-500" x-text="item.slug"></p>
                            </div>
                            <div class="flex flex-col gap-1 text-xs">
                                <button @click="addToCart(item)" class="px-3 py-1 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">Add to cart</button>
                                <button @click="removeFromWishlist(item.id)" class="text-rose-600 hover:underline">Remove</button>
                            </div>
                        </div>
                    </template>
                    <p x-show="!wishlist.length" class="text-xs text-slate-500">Wishlist is empty.</p>
                </div>
            </div>
        </div>

        {{-- Cart drawer modal --}}
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
                                <span>Qty: <input type="number" min="1" class="w-16 border border-slate-200 rounded-md px-2 py-1 text-sm"
                                                  :value="item.qty"
                                                  @input="changeCartQty(idx, $event.target.value)"></span>
                                <span>Price: <span x-text="item.price"></span></span>
                            </div>
                        </div>
                    </template>
                    <p x-show="!cart.length" class="text-xs text-slate-500">Cart is empty.</p>
                </div>
                <div class="mt-4 border-t border-slate-100 pt-3 flex items-center justify-between">
                    <p class="text-sm font-semibold text-slate-900">Total</p>
                    <p class="text-lg font-bold text-slate-900" x-text="cartTotal().toFixed(2)"></p>
                </div>
                <button @click="checkout" class="w-full mt-3 px-4 py-2 text-sm font-semibold bg-emerald-600 text-white rounded-md hover:bg-emerald-700">Checkout</button>
            </div>
        </div>

        {{-- Quick view modal --}}
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
                        <p class="text-2xl font-bold text-slate-900">? <span x-text="quickView?.base_price"></span></p>
                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-slate-700">Variant</label>
                            <select class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
                                    @change="selectVariant(quickView.id, $event.target.value)">
                                <option value="">None</option>
                                <template x-for="variant in variants[quickView.id] || []" :key="variant.id">
                                    <option :value="variant.id" x-text="variantLabel(variant)"></option>
                                </template>
                            </select>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="number" min="1" value="1"
                                   @input="updateQuantity(quickView.id, $event.target.value)"
                                   class="w-24 rounded-md border border-slate-200 px-2 py-1 text-sm focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                            <button @click="addToCart(quickView)" class="flex-1 px-4 py-2 text-sm bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Add to cart</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('storefront', () => ({
                apiBase: '/api/v1',
                token: localStorage.getItem('api_token') || '',
                user: null,
                showAuth: true,
                filters: { search: '', sort: 'new' },
                categories: ['Fashion', 'Electronics', 'Home', 'Beauty', 'Sports'],
                activeCategories: [],
                products: [],
                variants: {},
                qtyByProduct: {},
                cart: [],
                orders: [],
                wishlist: JSON.parse(localStorage.getItem('wishlist') || '[]'),
                wishlistOpen: false,
                cartOpen: false,
                payment: { method: 'cod' },
                quickView: null,

                auth: { email: 'customer@example.com', password: 'password' },

                init() {
                    this.loadProducts();
                    this.loadPersistedCart();
                    if (this.token) {
                        this.fetchMe();
                        this.loadOrders();
                    }
                },

                scrollToCatalog() {
                    document.getElementById('catalog')?.scrollIntoView({ behavior: 'smooth' });
                },

                persistCart() {
                    localStorage.setItem('cart', JSON.stringify(this.cart));
                },

                loadPersistedCart() {
                    const saved = localStorage.getItem('cart');
                    if (saved) this.cart = JSON.parse(saved);
                },

                async api(url, options = {}) {
                    const headers = options.headers || {};
                    if (!(options.body instanceof FormData)) {
                        headers['Content-Type'] = 'application/json';
                    }
                    if (this.token) headers['Authorization'] = `Bearer ${this.token}`;
                    const res = await fetch(`${this.apiBase}${url}`, { ...options, headers });
                    const text = await res.text();
                    let json;
                    try { json = text ? JSON.parse(text) : null; } catch (e) { json = text; }
                    return { ok: res.ok, status: res.status, json };
                },

                async login() {
                    const res = await this.api('/auth/login', {
                        method: 'POST',
                        body: JSON.stringify(this.auth),
                    });
                    if (res.ok && res.json?.access_token) {
                        this.token = res.json.access_token;
                        localStorage.setItem('api_token', this.token);
                        this.fetchMe();
                        this.loadOrders();
                    } else {
                        alert('Login failed');
                    }
                },

                async register() {
                    const res = await this.api('/auth/register', {
                        method: 'POST',
                        body: JSON.stringify({
                            name: this.auth.email.split('@')[0] || 'Customer',
                            email: this.auth.email,
                            password: this.auth.password,
                            password_confirmation: this.auth.password,
                            role: 'Customer',
                        }),
                    });
                    if (res.ok && res.json?.access_token) {
                        this.token = res.json.access_token;
                        localStorage.setItem('api_token', this.token);
                        this.fetchMe();
                        this.loadOrders();
                    } else {
                        alert('Register failed');
                    }
                },

                logoutToken() {
                    this.token = '';
                    localStorage.removeItem('api_token');
                    this.orders = [];
                    this.user = null;
                },

                async fetchMe() {
                    const res = await this.api('/auth/me', { method: 'GET' });
                    if (res.ok) this.user = res.json;
                },

                async loadProducts() {
                    const params = new URLSearchParams({ per_page: 60 });
                    if (this.filters.search) params.append('search', this.filters.search);
                    const res = await this.api(`/products?${params.toString()}`, { method: 'GET' });
                    if (res.ok) {
                        let items = res.json?.data || res.json || [];
                        if (this.filters.sort === 'price_asc') items = items.sort((a, b) => (a.base_price ?? 0) - (b.base_price ?? 0));
                        if (this.filters.sort === 'price_desc') items = items.sort((a, b) => (b.base_price ?? 0) - (a.base_price ?? 0));
                        this.products = items;
                        this.fetchVariantsForAll();
                    }
                },

                async fetchVariantsForAll() {
                    for (const p of this.products) {
                        const res = await this.api(`/products/${p.id}/variants`, { method: 'GET' });
                        if (res.ok) {
                            this.variants = { ...this.variants, [p.id]: res.json };
                        }
                    }
                },

                variantLabel(variant) {
                    const price = variant.price ?? 'N/A';
                    const attr = variant.name || variant.sku;
                    return `${attr} � ${price}`;
                },

                selectVariant(productId, variantId) {
                    this.qtyByProduct[productId] = this.qtyByProduct[productId] || { qty: 1, variant_id: null };
                    this.qtyByProduct[productId].variant_id = variantId || null;
                },

                updateQuantity(productId, qty) {
                    this.qtyByProduct[productId] = this.qtyByProduct[productId] || { qty: 1, variant_id: null };
                    this.qtyByProduct[productId].qty = Number(qty || 1);
                },

                addToCart(product) {
                    const meta = this.qtyByProduct[product.id] || { qty: 1, variant_id: null };
                    const variant = (this.variants[product.id] || []).find(v => v.id == meta.variant_id);
                    const price = variant?.price ?? product.base_price;
                    this.cart.push({
                        product_id: product.id,
                        variant_id: variant?.id || null,
                        name: product.name,
                        slug: product.slug,
                        image_url: product.image_url,
                        variant_label: variant ? this.variantLabel(variant) : null,
                        qty: meta.qty || 1,
                        price: Number(price),
                    });
                    this.persistCart();
                },

                removeFromCart(idx) {
                    this.cart.splice(idx, 1);
                    this.persistCart();
                },

                clearCart() {
                    this.cart = [];
                    this.persistCart();
                },

                changeCartQty(idx, qty) {
                    this.cart[idx].qty = Number(qty || 1);
                    this.persistCart();
                },

                cartCount() {
                    return this.cart.reduce((sum, item) => sum + Number(item.qty || 0), 0);
                },

                cartTotal() {
                    return this.cart.reduce((sum, item) => sum + item.qty * item.price, 0);
                },

                async checkout() {
                    if (!this.token) {
                        alert('Login first');
                        return;
                    }
                    if (!this.cart.length) {
                        alert('Cart is empty');
                        return;
                    }
                    const items = this.cart.map(i => ({
                        product_id: i.product_id,
                        variant_id: i.variant_id,
                        qty: i.qty,
                        price: i.price,
                    }));
                    const res = await this.api('/orders', {
                        method: 'POST',
                        body: JSON.stringify({ items, discount: 0 }),
                    });
                    if (res.ok) {
                        alert('Order placed!');
                        this.clearCart();
                        this.loadOrders();
                    } else {
                        alert('Order failed (check token/stock)');
                    }
                },

                statusBadge(status) {
                    return {
                        'bg-yellow-50 text-yellow-700 border border-yellow-200': status === 'pending',
                        'bg-blue-50 text-blue-700 border border-blue-200': status === 'processing',
                        'bg-indigo-50 text-indigo-700 border border-indigo-200': status === 'shipped',
                        'bg-emerald-50 text-emerald-700 border border-emerald-200': status === 'delivered',
                        'bg-rose-50 text-rose-700 border border-rose-200': status === 'cancelled',
                    };
                },

                async loadOrders() {
                    if (!this.token) return;
                    const res = await this.api('/orders?per_page=30', { method: 'GET' });
                    if (res.ok) {
                        this.orders = res.json?.data || res.json || [];
                    }
                },

                // Wishlist
                toggleWishlist(product) {
                    if (this.isWishlisted(product.id)) {
                        this.removeFromWishlist(product.id);
                    } else {
                        this.wishlist.push(product);
                        this.persistWishlist();
                    }
                },

                isWishlisted(id) {
                    return this.wishlist.some(p => p.id === id);
                },

                removeFromWishlist(id) {
                    this.wishlist = this.wishlist.filter(p => p.id !== id);
                    this.persistWishlist();
                },

                persistWishlist() {
                    localStorage.setItem('wishlist', JSON.stringify(this.wishlist));
                },

                toggleWishlistDrawer() {
                    this.wishlistOpen = true;
                },

                toggleCartDrawer() {
                    this.cartOpen = true;
                },

                // Category filter (client-side heuristic)
                productCategory(product) {
                    const name = product.name?.toLowerCase() || '';
                    if (name.includes('shoe') || name.includes('sneaker')) return 'Fashion';
                    if (name.includes('phone') || name.includes('laptop') || name.includes('headphone')) return 'Electronics';
                    if (name.includes('sofa') || name.includes('table') || name.includes('chair')) return 'Home';
                    if (name.includes('cream') || name.includes('skin')) return 'Beauty';
                    if (name.includes('ball') || name.includes('fitness') || name.includes('run')) return 'Sports';
                    return 'Fashion';
                },

                filteredProducts() {
                    if (!this.activeCategories.length) return this.products;
                    return this.products.filter(p => this.activeCategories.includes(this.productCategory(p)));
                },

                toggleCategory(cat) {
                    if (this.activeCategories.includes(cat)) {
                        this.activeCategories = this.activeCategories.filter(c => c !== cat);
                    } else {
                        this.activeCategories.push(cat);
                    }
                },

                clearCategories() {
                    this.activeCategories = [];
                },

                openQuickView(product) {
                    this.quickView = product;
                },
            }));
        });
    </script>
@endsection
 