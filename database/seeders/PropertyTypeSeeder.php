<?php

namespace Database\Seeders;

use App\Models\PropertyType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PropertyTypeSeeder extends Seeder
{
    public function run()
    {
        $propertyTypes = [
            [
                'name' => 'عقارات',
                'slug' => 'عقارات'
            ],
            [
                'name' => 'شقة',
                'slug' => 'شقة'
            ],
            [
                'name' => 'بيت',
                'slug' => 'بيت'
            ],
            [
                'name' => 'ارض',
                'slug' => 'ارض'
            ],
            [
                'name' => 'عمارة',
                'slug' => 'عمارة'
            ],
            [
                'name' => 'شاليه',
                'slug' => 'شاليه'
            ],
            [
                'name' => 'مزرعة',
                'slug' => 'مزرعة'
            ],
            [
                'name' => 'تجاري',
                'slug' => 'تجاري'
            ],

        ];

        foreach ($propertyTypes as $type) {
            PropertyType::create($type);
            usleep(1000);
        }
    }
}
