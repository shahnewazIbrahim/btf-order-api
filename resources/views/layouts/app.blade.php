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
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-600 text-white font-bold">
                    B
                </span>
                <div>
                    <h1 class="text-sm font-semibold">BTF Order Management</h1>
                    <p class="text-xs text-slate-500">Laravel E-Commerce API – Demo UI</p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <nav class="hidden md:flex items-center gap-3 text-sm">
                    <a href="{{ route('store') }}"
                       class="px-3 py-1 rounded-md {{ request()->routeIs('store') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }}">
                        Store
                    </a>
                    <a href="{{ route('admin.dashboard') }}"
                       class="px-3 py-1 rounded-md {{ request()->routeIs('admin.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }}">
                        Admin
                    </a>
                </nav>

                {{-- Auth info --}}
                @php($webUser = auth('web')->user())
                @if($webUser)
                    <div class="flex items-center gap-2">
                        <div class="text-right">
                            <p class="text-xs font-medium text-slate-800">
                                {{ $webUser->name }}
                            </p>
                            <p class="text-[11px] text-slate-400">
                                Role: {{ $webUser->role->name ?? 'N/A' }}
                            </p>
                        </div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button
                                type="submit"
                                class="text-xs px-3 py-1 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700">
                                Logout
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}"
                       class="text-xs px-3 py-1 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">
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
                <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs px-3 py-2">
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
    <footer class="border-t bg-white">
        <div class="max-w-7xl mx-auto px-4 py-3 text-xs text-slate-500 flex justify-between">
            <span>Bangladesh Thalassemia Foundation – Assignment</span>
            <span>Built with Laravel + Tailwind</span>
        </div>
    </footer>
</div>
</body>
</html>
