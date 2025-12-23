<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\PropertyImage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PropertyImageSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $randomColors = [
            'FF0000',
            'FFA500',
            'FFFF00',
            'FFFF00',
            '008000',
            '0000FF',
            '800080',
            'FFC0CB',
            'A52A2A',
            '808080',
            '000000',
            'FFFFFF',
            'F0F8FF',
            'FAEBD7',
            '00FFFF',
            '7FFFD4',
            'F0FFFF',
            'F5F5DC',
            'FFE4C4',
            'FFEBCD',
            'DEB887',
            '5F9EA0',
            'D2691E',
            'FF7F50',
            '6495ED',
            'DC143C',
            '00FFFF',
            '008B8B',
            'B8860B',
            '006400',
            'BDB76B',
            '8B008B',
            '556B2F',
            'FF8C00',
            '8B0000',
            'E9967A',
            '00BFFF',
        ];


        $propertyIds = Property::pluck('id')->toArray();

        foreach ($propertyIds as $propertyId) {
            // Create a single primary image for each property
            PropertyImage::create([
                'property_id' => $propertyId,
                'type' => PropertyImage::TYPE_IMAGE,
                'file' => 'https://placehold.co/600x400/' . $randomColors[array_rand($randomColors)] . '/' . $randomColors[array_rand($randomColors)] . '?text=' . 'PROPERTY+' . $propertyId,
                'is_primary' => PropertyImage::PRIMARY,
            ]);


            // Create 2-3 additional non-primary images for the same property
            for ($j = 0; $j < rand(2, 3); $j++) {
                PropertyImage::create([
                    'property_id' => $propertyId,
                    'type' => PropertyImage::TYPE_IMAGE,
                    'file' => 'https://placehold.co/600x400/f2f2f2/' . $randomColors[array_rand($randomColors)] . '?text=' . 'PROPERTY+' . $propertyId,
                    'is_primary' => PropertyImage::NOT_PRIMARY,
                ]);
            }
        }
    }
}
