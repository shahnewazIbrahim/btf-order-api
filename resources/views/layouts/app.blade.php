<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'BTF Order Management')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- Tailwind CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-100 text-slate-900 min-h-screen">
    <div class="min-h-screen flex flex-col">
        {{-- Top bar --}}
        {{-- Top bar --}}
        <header id="siteHeader"
            class="sticky top-0 z-50 bg-white/90 backdrop-blur border-b border-slate-100 shadow-sm transition-all duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                {{-- EXPANDED HEADER (top of page) --}}
                <div id="headerExpanded" class="py-3 flex flex-col gap-3">

                    {{-- Row 1: Brand + Auth --}}
                    <div class="flex items-center justify-between gap-3">
                        {{-- Brand --}}
                        <div class="flex items-center gap-3">
                            <div class="relative">
                                <span
                                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl
                                   bg-gradient-to-br from-indigo-600 to-sky-500 text-white font-extrabold shadow">
                                    B
                                </span>
                                <span
                                    class="absolute -bottom-1 -right-1 h-4 w-4 rounded-full bg-emerald-500 ring-2 ring-white"></span>
                            </div>

                            <div class="leading-tight">
                                <h1 class="text-base font-semibold text-slate-900 tracking-tight">
                                    BTF Order Management
                                </h1>
                                <p class="text-xs text-slate-500">
                                    Laravel E-Commerce API • Demo UI
                                </p>
                            </div>

                            <span
                                class="hidden sm:inline-flex text-[10px] px-2 py-0.5 rounded-md
                                 bg-indigo-50 text-indigo-700 border border-indigo-100 font-semibold">
                                v1 Live
                            </span>
                        </div>

                        {{-- Auth info --}}
                        @php($webUser = auth('web')->user())
                        @if ($webUser)
                            <div
                                class="flex items-center gap-2 bg-white border border-slate-200 rounded-xl px-2 py-1.5 shadow-sm">
                                <div
                                    class="h-8 w-8 rounded-lg bg-slate-100 grid place-items-center text-slate-700 font-bold text-xs">
                                    {{ strtoupper(substr($webUser->name, 0, 1)) }}
                                </div>

                                <div class="text-right leading-tight">
                                    <p class="text-xs font-semibold text-slate-800">{{ $webUser->name }}</p>
                                    <p class="text-[10px] text-slate-400">{{ $webUser->role->name ?? 'N/A' }}</p>
                                </div>

                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="text-[11px] px-2.5 py-1 rounded-md bg-slate-900 text-white hover:bg-slate-800 transition">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        @else
                            <a href="{{ route('login') }}"
                                class="text-xs px-4 py-2 rounded-lg bg-gradient-to-r from-indigo-600 to-sky-500
                              text-white font-semibold shadow hover:opacity-95 transition">
                                Login
                            </a>
                        @endif
                    </div>

                    {{-- Row 2: Menu centered under brand --}}
                    <nav class="hidden md:flex w-full items-center justify-center gap-2 text-sm">
                        <a href="{{ route('store') }}"
                            class="px-4 py-2 rounded-lg border transition
                   {{ request()->routeIs('store')
                       ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm'
                       : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 hover:text-slate-900' }}">
                            Store
                        </a>

                        <a href="{{ route('admin.dashboard') }}"
                            class="px-4 py-2 rounded-lg border transition
                   {{ request()->routeIs('admin.*')
                       ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm'
                       : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 hover:text-slate-900' }}">
                            Admin
                        </a>
                    </nav>

                    {{-- Mobile menu (expanded) --}}
                    <div class="md:hidden flex items-center gap-1 justify-center">
                        <a href="{{ route('store') }}"
                            class="px-3 py-2 rounded-md text-xs border
                   {{ request()->routeIs('store') ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-slate-600 border-slate-200' }}">
                            Store
                        </a>
                        <a href="{{ route('admin.dashboard') }}"
                            class="px-3 py-2 rounded-md text-xs border
                   {{ request()->routeIs('admin.*') ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-slate-600 border-slate-200' }}">
                            Admin
                        </a>
                    </div>
                </div>

                {{-- COMPACT HEADER (on scroll) --}}
                <div id="headerCompact"
                    class="hidden py-2 items-center justify-between gap-3 relative">

                {{-- Left: Logo --}}
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <span
                            class="inline-flex h-9 w-9 items-center justify-center rounded-xl
                                    bg-gradient-to-br from-indigo-600 to-sky-500 text-white font-extrabold shadow">
                            B
                        </span>
                        <span
                            class="absolute -bottom-1 -right-1 h-3.5 w-3.5 rounded-full bg-emerald-500 ring-2 ring-white"></span>
                    </div>
                    <p class="hidden sm:block text-sm font-semibold text-slate-900">
                        BTF Order Management
                    </p>
                </div>

                {{-- Center: Menu exact middle --}}
                <nav class="absolute left-1/2 -translate-x-1/2 hidden md:flex items-center gap-2 text-sm">
                    <a href="{{ route('store') }}"
                        class="px-4 py-2 rounded-lg border transition
                        {{ request()->routeIs('store')
                            ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm'
                            : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 hover:text-slate-900' }}">
                        Store
                    </a>

                    <a href="{{ route('admin.dashboard') }}"
                        class="px-4 py-2 rounded-lg border transition
                        {{ request()->routeIs('admin.*')
                            ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm'
                            : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 hover:text-slate-900' }}">
                        Admin
                    </a>
                </nav>

                {{-- Right: Auth (compact) --}}
                <div class="flex justify-end">
                    @php($webUser = auth('web')->user())
                    @if ($webUser)
                        <div
                            class="flex items-center gap-2 bg-white border border-slate-200 rounded-xl px-2 py-1.5 shadow-sm">
                            <div
                                class="h-8 w-8 rounded-lg bg-slate-100 grid place-items-center text-slate-700 font-bold text-xs">
                                {{ strtoupper(substr($webUser->name, 0, 1)) }}
                            </div>

                            <div class="text-right leading-tight hidden sm:block">
                                <p class="text-xs font-semibold text-slate-800">{{ $webUser->name }}</p>
                                <p class="text-[10px] text-slate-400">{{ $webUser->role->name ?? 'N/A' }}</p>
                            </div>

                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="text-[11px] px-2.5 py-1 rounded-md bg-slate-900 text-white hover:bg-slate-800 transition">
                                    Logout
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}"
                            class="text-xs px-4 py-2 rounded-lg bg-gradient-to-r from-indigo-600 to-sky-500
                                    text-white font-semibold shadow hover:opacity-95 transition">
                            Login
                        </a>
                    @endif
                </div>
                </div>


                {{-- Compact mobile (scroll) --}}
                <div class="md:hidden hidden py-2 items-center justify-between gap-2" id="headerCompactMobile">
                    <div class="flex items-center gap-2">
                        <span
                            class="inline-flex h-9 w-9 items-center justify-center rounded-xl
                             bg-gradient-to-br from-indigo-600 to-sky-500 text-white font-extrabold shadow">
                            B
                        </span>
                        <div class="flex items-center gap-1">
                            <a href="{{ route('store') }}"
                                class="px-3 py-2 rounded-md text-xs border
                       {{ request()->routeIs('store') ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-slate-600 border-slate-200' }}">
                                Store
                            </a>
                            <a href="{{ route('admin.dashboard') }}"
                                class="px-3 py-2 rounded-md text-xs border
                       {{ request()->routeIs('admin.*') ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-slate-600 border-slate-200' }}">
                                Admin
                            </a>
                        </div>
                    </div>

                    {{-- Auth small --}}
                    @php($webUser = auth('web')->user())
                    @if ($webUser)
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="text-[11px] px-2.5 py-1 rounded-md bg-slate-900 text-white hover:bg-slate-800 transition">
                                Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}"
                            class="text-[11px] px-3 py-2 rounded-md bg-indigo-600 text-white">
                            Login
                        </a>
                    @endif
                </div>

            </div>
        </header>


        {{-- Flash messages --}}
        <main class="flex-1">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                @if (session('success'))
                    <div
                        class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs px-3 py-2">
                        {{ session('success') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="mb-4 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-xs px-3 py-2">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>

        {{-- Footer --}}
        <footer class="mt-10 border-t bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 grid gap-6 md:grid-cols-3">
                {{-- Left --}}
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <span
                            class="h-9 w-9 rounded-xl bg-gradient-to-br from-indigo-600 to-sky-500 text-white grid place-items-center font-extrabold shadow">
                            B
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-slate-900">BTF Order Management</p>
                            <p class="text-xs text-slate-500">Bangladesh Thalassemia Foundation</p>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Demo storefront + admin panel powered by Laravel REST API.
                        Built for assignment & internal learning.
                    </p>
                </div>

                {{-- Middle --}}
                <div class="space-y-2">
                    <p class="text-xs font-semibold text-slate-900 uppercase tracking-wider">Quick Links</p>
                    <div class="flex flex-col gap-1 text-xs">
                        <a href="{{ route('store') }}"
                            class="text-slate-600 hover:text-indigo-600 transition">Storefront</a>
                        <a href="{{ route('admin.dashboard') }}"
                            class="text-slate-600 hover:text-indigo-600 transition">Admin Dashboard</a>
                        <a href="#" class="text-slate-600 hover:text-indigo-600 transition">Support</a>
                        <a href="#" class="text-slate-600 hover:text-indigo-600 transition">Privacy Policy</a>
                    </div>
                </div>

                {{-- Right --}}
                <div class="space-y-2 md:text-right">
                    <p class="text-xs font-semibold text-slate-900 uppercase tracking-wider">Tech Stack</p>
                    <div class="flex md:justify-end flex-wrap gap-2 text-[11px]">
                        <span
                            class="px-2 py-1 rounded-full bg-slate-100 text-slate-700 border border-slate-200">Laravel</span>
                        <span
                            class="px-2 py-1 rounded-full bg-slate-100 text-slate-700 border border-slate-200">Tailwind
                            CSS</span>
                        <span
                            class="px-2 py-1 rounded-full bg-slate-100 text-slate-700 border border-slate-200">Alpine.js</span>
                        <span class="px-2 py-1 rounded-full bg-slate-100 text-slate-700 border border-slate-200">REST
                            API</span>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-100">
                <div
                    class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-slate-500">
                    <span>© {{ date('Y') }} Bangladesh Thalassemia Foundation. All rights reserved.</span>
                    <span class="flex items-center gap-1">
                        Built with ❤️ using Laravel + Tailwind
                    </span>
                </div>
            </div>
        </footer>

    </div>
</body>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const expanded = document.getElementById('headerExpanded');
    const compact = document.getElementById('headerCompact');
    const compactMobile = document.getElementById('headerCompactMobile');

    const isDesktop = () => window.matchMedia('(min-width: 768px)').matches;

    let compactOn = false;
    const SHOW_AT = 120; // scroll এ 120px পার হলে compact show
    const HIDE_AT = 60;  // 60px এর নিচে আসলে expanded ফিরবে

    const applyState = () => {
        // Expanded
        expanded.classList.toggle('hidden', compactOn);

        // Desktop compact
        if (compactOn && isDesktop()) {
            compact.classList.remove('hidden');
            compact.classList.add('flex');
        } else {
            compact.classList.add('hidden');
            compact.classList.remove('flex');
        }

        // Mobile compact
        if (compactOn && !isDesktop()) {
            compactMobile.classList.remove('hidden');
            compactMobile.classList.add('flex');
        } else {
            compactMobile.classList.add('hidden');
            compactMobile.classList.remove('flex');
        }
    };

    const updateCompactState = () => {
        const y = window.scrollY;

        if (!compactOn && y > SHOW_AT) compactOn = true;
        else if (compactOn && y < HIDE_AT) compactOn = false;

        applyState();
    };

    // Throttle scroll with requestAnimationFrame
    let ticking = false;
    const onScroll = () => {
        if (!ticking) {
            window.requestAnimationFrame(() => {
                updateCompactState();
                ticking = false;
            });
            ticking = true;
        }
    };

    updateCompactState();
    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', updateCompactState);
});
</script>




</html>
