<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [

            [
                'name'         => 'للايجار',
                'slug'            => 'للايجار',
                'description'  => 'عقارات للإيجار.',
                'is_active'       => Category::ACTIVE, // أو فقط true إذا لم يكن ثابتًا
            ],

            [
                'name'         => 'للبيع',
                'slug'            => 'للبيع',
                'description'  => 'عقارات للبيع.',
                'is_active'       => Category::ACTIVE,
            ],

            [
                'name'         => 'للبدل',
                'slug'            => 'للبدل',
                'description'  => 'عقارات للتبادل.',
                'is_active'       => Category::ACTIVE,
            ],

            // [
            //     'name'         => 'مطلوب',
            //     'slug'            => 'wanted',
            //     'description'  => 'عقارات مطلوبة.',
            //     'is_active'       => Category::ACTIVE,
            // ],

        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
