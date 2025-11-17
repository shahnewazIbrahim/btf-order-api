<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleAndUserSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        $adminRole   = Role::firstOrCreate(['name' => 'Admin']);
        $vendorRole  = Role::firstOrCreate(['name' => 'Vendor']);
        $customerRole = Role::firstOrCreate(['name' => 'Customer']);

        // Admin user
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('password'),
                'role_id'  => $adminRole->id,
            ]
        );

        // Vendor user
        User::firstOrCreate(
            ['email' => 'vendor@example.com'],
            [
                'name'     => 'Demo Vendor',
                'password' => Hash::make('password'),
                'role_id'  => $vendorRole->id,
            ]
        );

        // Customer user
        User::firstOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name'     => 'Demo Customer',
                'password' => Hash::make('password'),
                'role_id'  => $customerRole->id,
            ]
        );
    }
}

