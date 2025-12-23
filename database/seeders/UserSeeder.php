<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Test User',
            'email_verified_at' => now(),
            'status' => User::STATUS_ACTIVE,
            'email' => 'user@dev.com',
            'password' => 'user@dev.com',
            'is_admin' => User::NOT_ADMIN,
        ]);
        User::create([
            'name' => 'Test Admin',
            'email_verified_at' => now(),
            'status' => User::STATUS_ACTIVE,
            'email' => 'admin@dev.com',
            'password' => 'admin@dev.com',
            'is_admin' => User::ADMIN,
        ]);

    }
}
