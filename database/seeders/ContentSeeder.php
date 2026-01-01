<?php

namespace Database\Seeders;

use App\Models\Content;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Content::insert([
            [
                'sort_order' => 1,
                'title' => 'Biology Study Guide',
                'subtitle' => 'Chapter 1 to 5',
                'category' => 'science',
                'type' => Content::TYPE_STUDY_GUIDE,
                'file' => 'contents/biology-guide.pdf',
                'is_publish' => Content::IS_PUBLISH
            ],
            [
                'sort_order' => 2,
                'title' => 'Math Flashcards',
                'subtitle' => 'Algebra & Geometry',
                'category' => 'mathematics',
                'type' => Content::TYPE_FLASHCARD,
                'file' => 'contents/math-flashcards.mp3',
                'is_publish' => Content::IS_PUBLISH
            ],
            [
                'sort_order' => 3,
                'title' => 'History Study Guide',
                'subtitle' => 'World War II Overview',
                'category' => 'history',
                'type' => Content::TYPE_STUDY_GUIDE,
                'file' => 'contents/history-guide.pdf',
                'is_publish' => Content::NOT_PUBLISH
            ],
            [
                'sort_order' => 4,
                'title' => 'Chemistry Flashcards',
                'subtitle' => 'Periodic Table & Reactions',
                'category' => 'chemistry',
                'type' => Content::TYPE_FLASHCARD,
                'file' => 'contents/chemistry-flashcards.mp3',
                'is_publish' => Content::NOT_PUBLISH
            ],
            [
                'sort_order' => 5,
                'title' => 'English Study Guide',
                'subtitle' => 'Grammar and Composition',
                'category' => 'language',
                'type' => Content::TYPE_STUDY_GUIDE,
                'file' => 'contents/english-guide.pdf',
                'is_publish' => Content::IS_PUBLISH
            ],
            [
                'sort_order' => 6,
                'title' => 'Geography Flashcards',
                'subtitle' => 'Countries and Capitals',
                'category' => 'geography',
                'type' => Content::TYPE_FLASHCARD,
                'file' => 'contents/geography-flashcards.mp3',
                'is_publish' => Content::IS_PUBLISH
            ],
        ]);
    }
}
