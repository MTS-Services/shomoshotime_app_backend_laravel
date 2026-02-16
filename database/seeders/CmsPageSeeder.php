<?php

namespace Database\Seeders;

use App\Models\CmsPage;
use App\Models\User;
use Illuminate\Database\Seeder;

class CmsPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('is_admin', true)->first() ?? User::factory()->create(['is_admin' => true]);

        foreach (CmsPage::allowedTypes() as $index => $type) {
            $payload = CmsPage::factory()
                ->state([
                    'type' => $type,
                    'sort_order' => $index,
                    'content' => "Sample content for {$type} page.",
                    'created_by' => $admin->id,
                    'updated_by' => $admin->id,
                ])
                ->make()
                ->toArray();

            CmsPage::updateOrCreate(['type' => $type], $payload);
        }
    }
}
