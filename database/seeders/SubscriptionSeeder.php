<?php

namespace Database\Seeders;

use App\Models\Subscription;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subscriptions = [
            [
                'sort_order' => 1,
                'duration' => 'Weekly',
                'price' => 10,
                'features' => json_encode([
                    'Access to all study guides',
                    '500+ practice questions',
                    'Basic flashcard sets',
                    'Limited mock exams',
                    'Email support',
                ]),
                'tag' => null,
                'status' => Subscription::STATUS_ACTIVE,
            ],
            [
                'sort_order' => 2,
                'duration' => 'Monthly',
                'price' => 30,
                'features' => json_encode([
                    'Everything in Weekly',
                    'Unlimited practice questions',
                    'All flashcard sets',
                    'Performance analytics',
                    'Priority support',
                ]),
                'tag' => 'Most Popular',
                'status' => Subscription::STATUS_ACTIVE,
            ],
            [
                'sort_order' => 3,
                'duration' => 'Annually',
                'price' => 249,
                'features' => json_encode([
                    'Everything in Monthly',
                    'Save 44% vs monthly',
                    'Downloadable materials',
                    'Advanced analytics',
                    '1-on-1 coaching',
                    'Lifetime updates',
                    '24/7 support',
                ]),
                'tag' => null,
                'status' => Subscription::STATUS_ACTIVE,
            ],
        ];

        // Insert all subscriptions at once
        Subscription::insert($subscriptions);
    }
}
