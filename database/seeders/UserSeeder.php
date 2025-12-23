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
            'name' => 'مسؤل',
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'phone' => '+96550000001',
            'status' => User::STATUS_ACTIVE,
            'user_type' => User::USER_TYPE_ADMIN,
            'language_preference' => User::LANGUAGE_ARABIC,
            'email' => 'admin@dev.com',
            'password' => 'admin@dev.com',
            'is_admin' => User::ADMIN,
        ]);
        User::create([
            'name' => 'مشرف الاختبار',
            'phone' => '+96550000002',
            'status' => User::STATUS_ACTIVE,
            'user_type' => User::USER_TYPE_ADMIN,
            'language_preference' => User::LANGUAGE_ARABIC,
            'email' => 'testadmin@dev.com',
            'password' => 'testadmin@dev.com',
            'is_admin' => User::ADMIN,
        ]);
        User::create([
            'name' => 'مستخدم',
            'phone' => '+96550000003',
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'user_type' => User::USER_TYPE_INDIVIDUAL,
            'language_preference' => User::LANGUAGE_ARABIC,
            'status' => User::STATUS_ACTIVE,
            'email' => 'user@dev.com',
            'password' => 'user@dev.com',
        ]);
        User::create([
            'name' => 'اختبار المستخدم',
            'phone' => '+96550000004',
            'status' => User::STATUS_ACTIVE,
            'user_type' => User::USER_TYPE_INDIVIDUAL,
            'language_preference' => User::LANGUAGE_ARABIC,
            'email' => 'testuser@dev.com',
            'password' => 'testuser@dev.com',
        ]);
    }
}
