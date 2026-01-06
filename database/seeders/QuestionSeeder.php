<?php

namespace Database\Seeders;

use App\Models\Question;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        Question::insert([
            [
                'question_set_id' => 1,
                'file' => null,
                'question' => 'What is the typical frequency range for diagnostic ultrasound?',
                'option_a' => '1–15 MHz',
                'option_b' => '20–40 MHz',
                'option_c' => '50–100 MHz',
                'option_d' => 'Above 100 MHz',
                'answer' => 'option_a',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question_set_id' => 1,
                'file' => null,
                'question' => 'Which artifact is caused by the presence of a strong reflector?',
                'option_a' => 'Reverberation',
                'option_b' => 'Shadowing',
                'option_c' => 'Enhancement',
                'option_d' => 'Refraction',
                'answer' => 'option_a',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question_set_id' => 2,
                'file' => null,
                'question' => 'What does the Doppler shift depend on?',
                'option_a' => 'Beam width',
                'option_b' => 'Pulse repetition frequency',
                'option_c' => 'Angle between beam and flow',
                'option_d' => 'Transducer size',
                'answer' => 'option_c',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
