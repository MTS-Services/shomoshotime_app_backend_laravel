<?php

namespace Database\Factories;

use App\Models\CmsPage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CmsPage>
 */
class CmsPageFactory extends Factory
{
    protected $model = CmsPage::class;

    public function definition(): array
    {
        $type = fake()->randomElement(CmsPage::allowedTypes());

        return [
            'sort_order' => fake()->numberBetween(0, 100),
            'type' => $type,
            'content' => fake()->paragraphs(3, true),
            'created_by' => User::factory(),
            'updated_by' => null,
        ];
    }
}
