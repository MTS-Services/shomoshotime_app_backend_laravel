<?php

namespace Database\Seeders;

use App\Models\CompanyInformation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanyInformationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CompanyInformation::create([
            'sort_order' => 1,
            'user_id' => 3, // Make sure user with ID 1 exists
            'company_name' => 'حلول التقنية العالمية',
            'company_description' => 'مزود رائد لخدمات التكنولوجيا وتطوير البرمجيات.',
            'address' => '١٢٣ شارع سيليكون، سان فرانسيسكو، كاليفورنيا',
            'social_links' => json_encode([
                'facebook' => 'https://facebook.com/globaltech',
                'twitter' => 'https://twitter.com/globaltech',
                'linkedin' => 'https://linkedin.com/company/globaltech'
            ]),
            'website' => 'https://globaltech.com',
        ]);
    }
}
