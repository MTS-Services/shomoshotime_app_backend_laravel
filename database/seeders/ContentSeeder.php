<?php

namespace Database\Seeders;

use App\Models\Content;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'spi' => 'SPI',
            'vascular' => 'Vascular',
            'obgyn' => 'OB/GYN',
            'abdomen' => 'Abdomen',
        ];

        $data = [];
        $sortOrder = 1;

        foreach ($categories as $key => $label) {

            // 3 Study Guides
            for ($i = 1; $i <= 3; $i++) {
                $data[] = [
                    'sort_order' => $sortOrder++,
                    'title' => "{$label} Study Guide {$i}",
                    'subtitle' => "{$label} Core Concepts Part {$i}",
                    'category' => $key,
                    'type' => Content::TYPE_STUDY_GUIDE,
                    'is_publish' => Content::IS_PUBLISH,
                ];
            }

            // 3 Flashcards
            for ($i = 1; $i <= 3; $i++) {
                $data[] = [
                    'sort_order' => $sortOrder++,
                    'title' => "{$label} Flashcards {$i}",
                    'subtitle' => "{$label} Quick Revision {$i}",
                    'category' => $key,
                    'type' => Content::TYPE_FLASHCARD,
                    'is_publish' => Content::IS_PUBLISH,
                ];
            }
        }

        Content::insert($data);
    }
}
