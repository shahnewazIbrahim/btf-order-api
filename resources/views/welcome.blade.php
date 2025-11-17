@extends('layouts.app')

@section('title', 'API Overview')

@section('content')
    <div class="bg-white rounded-lg shadow p-6 space-y-4">
        <h2 class="text-xl font-semibold">API Overview</h2>
        <p class="text-sm text-slate-600">
            This is the backend assignment for Bangladesh Thalassemia Foundation – E-Commerce Order Management System.
        </p>
        <ul class="list-disc list-inside text-sm">
            <li>Versioned API: <code>/api/v1/...</code></li>
            <li>Auth: JWT with refresh tokens</li>
            <li>Roles: Admin, Vendor, Customer</li>
        </ul>
    </div>
@endsection
