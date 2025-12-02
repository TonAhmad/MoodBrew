<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * UserSeeder - Seed default users untuk testing
 * 
 * Creates:
 * - 1 Admin account
 * - 2 Cashier accounts
 * - 2 Sample customer accounts
 */
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin Account
        User::create([
            'name' => 'Admin MoodBrew',
            'email' => 'admin@moodbrew.id',
            'password' => Hash::make('admin123'),
            'role' => User::ROLE_ADMIN,
            'email_verified_at' => now(),
        ]);

        // Cashier Accounts
        User::create([
            'name' => 'Kasir Satu',
            'email' => 'kasir1@moodbrew.id',
            'password' => Hash::make('kasir123'),
            'role' => User::ROLE_CASHIER,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Kasir Dua',
            'email' => 'kasir2@moodbrew.id',
            'password' => Hash::make('kasir123'),
            'role' => User::ROLE_CASHIER,
            'email_verified_at' => now(),
        ]);

        // Sample Customer Accounts (untuk testing)
        User::create([
            'name' => 'Budi Customer',
            'email' => 'budi@email.com',
            'password' => Hash::make('customer123'),
            'role' => User::ROLE_CUSTOMER,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Sari Customer',
            'email' => 'sari@email.com',
            'password' => Hash::make('customer123'),
            'role' => User::ROLE_CUSTOMER,
            'email_verified_at' => now(),
        ]);
    }
}
