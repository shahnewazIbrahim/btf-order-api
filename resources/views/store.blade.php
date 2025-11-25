@extends('layouts.app')

@section('title', 'Storefront - BTF Order Management')

@section('content')
    <div x-data="storefront()" class="space-y-10">

        @include('store.partials.topbar')
        @include('store.partials.hero')
        @include('store.partials.category_chips')

        <section id="catalog" class="grid gap-6 lg:grid-cols-4">
            <div class="lg:col-span-3 bg-white border border-slate-100 rounded-2xl shadow-sm p-5 space-y-4">
                @include('store.partials.catalog_header')

                <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-4">
                    <template x-for="product in filteredProducts()" :key="product.id">
                        @include('store.partials.product_card')
                    </template>
                </div>

                <p x-show="!filteredProducts().length" class="text-xs text-slate-500">No products found.</p>
            </div>

            <div class="space-y-4">
                @include('store.partials.cart_panel')
                @include('store.partials.tracking_panel')
            </div>
        </section>

        @include('store.partials.wishlist_drawer')
        @include('store.partials.cart_drawer')
        @include('store.partials.quick_view_modal')

    </div>


    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('storefront', () => ({
                apiBase: '/api/v1',

                token: localStorage.getItem('api_token') || '',
                user: null,
                showAuth: true,

                filters: {
                    search: '',
                    sort: 'new'
                },
                categories: ['Fashion', 'Electronics', 'Home', 'Beauty', 'Sports'],
                activeCategories: [],

                products: [],
                productsLoading: false,

                variants: {}, // { [productId]: [] }
                variantsLoadedIds: new Set(), // cache tracker
                variantsLoadingById: {}, // { [productId]: true/false }

                qtyByProduct: {},
                cart: [],
                orders: [],

                wishlist: JSON.parse(localStorage.getItem('wishlist') || '[]'),
                wishlistOpen: false,
                cartOpen: false,

                payment: {
                    method: 'cod'
                },
                quickView: null,

                auth: {
                    email: 'customer@example.com',
                    password: 'password'
                },

                init() {
                    this.token = localStorage.getItem('api_token') || '';
                    this.loadPersistedCart();
                    this.loadProducts();


                    if (this.token) {
                        this.fetchMe();
                        this.loadOrders();
                    }
                },

                scrollToCatalog() {
                    document.getElementById('catalog')?.scrollIntoView({
                        behavior: 'smooth'
                    });
                },

                // ---------------- API helper ----------------
                async api(url, options = {}) {
                    const headers = options.headers || {};
                    if (!(options.body instanceof FormData)) {
                        headers['Content-Type'] = 'application/json';
                    }
                    if (this.token) headers['Authorization'] = `Bearer ${this.token}`;

                    const res = await fetch(`${this.apiBase}${url}`, {
                        ...options,
                        headers
                    });
                    const text = await res.text();

                    let json;
                    try {
                        json = text ? JSON.parse(text) : null;
                    } catch (e) {
                        json = text;
                    }

                    return {
                        ok: res.ok,
                        status: res.status,
                        json
                    };
                },

                // ---------------- Auth ----------------
                async login() {
                    try {
                        const res = await this.api('/auth/login', {
                            method: 'POST',
                            body: JSON.stringify(this.auth),
                        });

                        if (!res.ok) {
                            console.log("login failed raw:", res.json);
                            alert(res.json?.message || 'Login failed');
                            return;
                        }

                        // ✅ support multiple token shapes
                        const token =
                            res.json?.access_token ||
                            res.json?.token ||
                            res.json?.data?.access_token ||
                            res.json?.data?.token ||
                            '';

                        if (!token) {
                            console.log("token not found in response:", res.json);
                            alert("Token not found from API response");
                            return;
                        }

                        this.token = token;
                        localStorage.setItem('api_token', token);

                        await this.fetchMe();
                        await this.loadOrders();

                        alert("Logged in!");
                    } catch (e) {
                        console.error(e);
                        alert("Login error");
                    }
                },

                async register() {
                    try {
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

                        if (!res.ok) {
                            console.log("register failed raw:", res.json);
                            alert(res.json?.message || 'Register failed');
                            return;
                        }

                        const token =
                            res.json?.access_token ||
                            res.json?.token ||
                            res.json?.data?.access_token ||
                            res.json?.data?.token ||
                            '';

                        if (!token) {
                            console.log("token not found in response:", res.json);
                            alert("Token not found from API response");
                            return;
                        }

                        this.token = token;
                        localStorage.setItem('api_token', token);

                        await this.fetchMe();
                        await this.loadOrders();

                        alert("Registered & logged in!");
                    } catch (e) {
                        console.error(e);
                        alert("Register error");
                    }
                },


                logoutToken() {
                    this.token = '';
                    localStorage.removeItem('api_token');
                    this.orders = [];
                    this.user = null;
                },

                async fetchMe() {
                    const res = await this.api('/auth/me', {
                        method: 'GET'
                    });
                    if (res.ok) this.user = res.json;
                },

                // ---------------- Products ----------------
                async loadProducts() {
                    if (this.productsLoading) return;
                    this.productsLoading = true;

                    try {
                        const params = new URLSearchParams({
                            per_page: 60
                        });
                        if (this.filters.search) params.append('search', this.filters.search);

                        const res = await this.api(`/products?${params.toString()}`, {
                            method: 'GET'
                        });

                        if (res.ok) {
                            let items = res.json?.data || res.json || [];

                            if (this.filters.sort === 'price_asc')
                                items = items.sort((a, b) => (a.base_price ?? 0) - (b.base_price ??
                                    0));
                            if (this.filters.sort === 'price_desc')
                                items = items.sort((a, b) => (b.base_price ?? 0) - (a.base_price ??
                                    0));

                            this.products = items;
                            // ❌ no prefetch loop here
                            // variants will be lazy loaded
                        }
                    } finally {
                        this.productsLoading = false;
                    }
                },

                // ✅ lazy load variants per product + cache + de-dup
                async ensureVariants(productId) {
                    if (!productId) return;
                    if (this.variantsLoadedIds.has(productId)) return;
                    if (this.variantsLoadingById[productId]) return;

                    this.variantsLoadingById[productId] = true;

                    try {
                        const res = await this.api(`/products/${productId}/variants`, {
                            method: 'GET'
                        });
                        if (res.ok) {
                            this.variants[productId] = res.json || [];
                            this.variantsLoadedIds.add(productId);
                        } else {
                            this.variants[productId] = [];
                        }
                    } finally {
                        this.variantsLoadingById[productId] = false;
                    }
                },

                variantLabel(variant) {
                    const price = variant.price ?? 'N/A';
                    const attr = variant.name || variant.sku;
                    return `${attr} - ${price}`;
                },

                selectVariant(productId, variantId) {
                    this.qtyByProduct[productId] = this.qtyByProduct[productId] || {
                        qty: 1,
                        variant_id: null
                    };
                    this.qtyByProduct[productId].variant_id = variantId || null;
                },

                updateQuantity(productId, qty) {
                    this.qtyByProduct[productId] = this.qtyByProduct[productId] || {
                        qty: 1,
                        variant_id: null
                    };
                    this.qtyByProduct[productId].qty = Number(qty || 1);
                },

                // ---------------- Cart ----------------
                persistCart() {
                    localStorage.setItem('cart', JSON.stringify(this.cart));
                },

                loadPersistedCart() {
                    const saved = localStorage.getItem('cart');
                    if (saved) this.cart = JSON.parse(saved);
                },

                addToCart(product) {
                    const meta = this.qtyByProduct[product.id] || {
                        qty: 1,
                        variant_id: null
                    };
                    const variant = (this.variants[product.id] || []).find(v => v.id == meta
                        .variant_id);

                    const price = Number(variant?.price ?? product.base_price ?? 0);

                    this.cart.push({
                        product_id: product.id,
                        variant_id: variant?.id || null,
                        name: product.name,
                        slug: product.slug,
                        image_url: product.image_url,
                        variant_label: variant ? this.variantLabel(variant) : null,
                        qty: Number(meta.qty || 1),
                        price,
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
                    return this.cart.reduce((sum, item) => sum + (Number(item.qty) * Number(item
                        .price)), 0);
                },

                async checkout() {
                    if (!this.token) return alert('Login first');
                    if (!this.cart.length) return alert('Cart is empty');

                    const items = this.cart.map(i => ({
                        product_id: i.product_id,
                        variant_id: i.variant_id,
                        qty: i.qty,
                        price: i.price,
                    }));

                    const res = await this.api('/orders', {
                        method: 'POST',
                        body: JSON.stringify({
                            items,
                            discount: 0
                        }),
                    });

                    if (res.ok) {
                        alert('Order placed!');
                        this.clearCart();
                        this.loadOrders();
                    } else {
                        alert('Order failed (check token/stock)');
                    }
                },

                // ---------------- Orders ----------------
                statusBadge(status) {
                    switch (status) {
                        case 'pending':
                            return 'bg-yellow-50 text-yellow-700 border border-yellow-200';
                        case 'processing':
                            return 'bg-blue-50 text-blue-700 border border-blue-200';
                        case 'shipped':
                            return 'bg-indigo-50 text-indigo-700 border border-indigo-200';
                        case 'delivered':
                            return 'bg-emerald-50 text-emerald-700 border border-emerald-200';
                        case 'cancelled':
                            return 'bg-rose-50 text-rose-700 border border-rose-200';
                        default:
                            return 'bg-slate-100 text-slate-600 border border-slate-200';
                    }
                },

                async loadOrders() {
                    try {
                        if (!this.token) return;

                        const res = await this.api('/orders?per_page=30', {
                            method: 'GET'
                        });

                        if (res.ok) {
                            const list = res.json?.data || res.json || [];

                            // ✅ dedupe by id (fallback order_number)
                            const map = new Map();

                            if (list?.length) {
                                list.forEach(o => {
                                    const key = o.id ?? o.order_number;
                                    if (!map.has(key)) map.set(key, o);
                                });

                                return this.orders = Array.from(map.values());
                            }
                            return this.orders = [];
                        }
                    } catch (e) {
                        console.error(e);
                    }
                },


                // ---------------- Wishlist ----------------
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

                // ---------------- Category filter ----------------
                productCategory(product) {
                    const name = product.name?.toLowerCase() || '';
                    if (name.includes('shoe') || name.includes('sneaker')) return 'Fashion';
                    if (name.includes('phone') || name.includes('laptop') || name.includes('headphone'))
                        return 'Electronics';
                    if (name.includes('sofa') || name.includes('table') || name.includes('chair'))
                        return 'Home';
                    if (name.includes('cream') || name.includes('skin')) return 'Beauty';
                    if (name.includes('ball') || name.includes('fitness') || name.includes('run'))
                        return 'Sports';
                    return 'Fashion';
                },

                filteredProducts() {
                    if (!this.activeCategories.length) return this.products;
                    return this.products.filter(p => this.activeCategories.includes(this
                        .productCategory(p)));
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
