@extends('layouts.app')

@section('title', 'Login – BTF Order Management')

@section('content')
    <div class="max-w-md mx-auto mt-8">
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
            <h1 class="text-lg font-semibold text-slate-800 mb-1">Sign in</h1>
            <p class="text-xs text-slate-500 mb-4">
                Use any seeded user, e.g. <code>admin@example.com</code> / <code>password</code>.
            </p>

            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf

                <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-700" for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="admin@example.com">
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-medium text-slate-700" for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="••••••••">
                </div>

                <div class="flex items-center justify-between">
                    <label class="inline-flex items-center gap-2 text-xs text-slate-600">
                        <input type="checkbox" name="remember" class="rounded border-slate-300">
                        Remember me
                    </label>
                </div>

                <button
                    type="submit"
                    class="w-full inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                    Sign in
                </button>
            </form>
        </div>
    </div>
@endsection
