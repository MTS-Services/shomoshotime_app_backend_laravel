<?php

namespace Database\Seeders;

use App\Models\UserProfile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userIds = DB::table('users')->pluck('id')->take(4); // Make sure you have users in the database

        foreach ($userIds as $index => $userId) {
            UserProfile::create([
                'sort_order'   => $index + 1,
                'user_id'      => $userId,
                'dob'          => now()->subYears(rand(20, 40))->format('Y-m-d'),
                'gender'       => $index % 3, // 0, 1, 2
                'city'      => 'مدينة الكويت',
                'country'   => 'الكويت',
                'postal_code'  => '13001',
                'bio'       => 'مرحبًا! أنا ملف تعريف مستخدم تجريبي.',
                'website'      => 'https://example.com/user' . $userId,
                'social_links' => json_encode([
                    'facebook' => 'https://facebook.com/user' . $userId,
                    'twitter' => 'https://twitter.com/user' . $userId,
                ]),
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }
    }
}
