<?php

namespace Database\Seeders;

use App\Models\FlashCard;
use Illuminate\Database\Seeder;

class FlashCardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // content_id = 1
        FlashCard::create([
            'content_id' => 1,
            'sort_order' => 1,
            'question' => 'What is Laravel?',
            'answer' => 'Laravel is a PHP web application framework.',
        ]);

        FlashCard::create([
            'content_id' => 1,
            'sort_order' => 2,
            'question' => 'What is a migration?',
            'answer' => 'A migration is a way to version control database changes.',
        ]);

        FlashCard::create([
            'content_id' => 1,
            'sort_order' => 3,
            'question' => 'What is Eloquent?',
            'answer' => 'Eloquent is Laravel’s ORM.',
        ]);

        FlashCard::create([
            'content_id' => 1,
            'sort_order' => 4,
            'question' => 'What is a Seeder?',
            'answer' => 'A seeder populates the database with data.',
        ]);

        // content_id = 2
        FlashCard::create([
            'content_id' => 2,
            'sort_order' => 1,
            'question' => 'What is MVC?',
            'answer' => 'MVC stands for Model View Controller.',
        ]);

        FlashCard::create([
            'content_id' => 2,
            'sort_order' => 2,
            'question' => 'What is a Controller?',
            'answer' => 'A controller handles request logic.',
        ]);

        FlashCard::create([
            'content_id' => 2,
            'sort_order' => 3,
            'question' => 'What is a Model?',
            'answer' => 'A model represents database data.',
        ]);

        FlashCard::create([
            'content_id' => 2,
            'sort_order' => 4,
            'question' => 'What is a View?',
            'answer' => 'A view handles UI rendering.',
        ]);

        // content_id = 3
        FlashCard::create([
            'content_id' => 3,
            'sort_order' => 1,
            'question' => 'What is an API?',
            'answer' => 'API allows systems to communicate.',
        ]);

        FlashCard::create([
            'content_id' => 3,
            'sort_order' => 2,
            'question' => 'What is REST?',
            'answer' => 'REST is an architectural style.',
        ]);

        FlashCard::create([
            'content_id' => 3,
            'sort_order' => 3,
            'question' => 'What is JSON?',
            'answer' => 'JSON is a data interchange format.',
        ]);

        FlashCard::create([
            'content_id' => 3,
            'sort_order' => 4,
            'question' => 'What is HTTP?',
            'answer' => 'HTTP is a web communication protocol.',
        ]);
    }
}
