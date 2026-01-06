<?php

namespace Database\Seeders;

use App\Models\QuestionSet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuestionSetSeeder extends Seeder
{
    public function run(): void
    {
        QuestionSet::insert([
            [
                'category'   => 'SPI',
                'title'      => 'John',
                'subtitle'   => 'What is the typical frequency range for diagnostic ultrasound?',
                'status'     => QuestionSet::STATUS_EASY,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category'   => 'SPI',
                'title'      => 'Question 2',
                'subtitle'   => 'Which artifact is caused by the presence of a strong reflector?',
                'status'     => QuestionSet::STATUS_MEDIUM,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category'   => 'Vascular',
                'title'      => 'Question 3',
                'subtitle'   => 'What does the Doppler shift depend on?',
                'status'     => QuestionSet::STATUS_HARD,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}