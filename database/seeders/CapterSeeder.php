<?php

namespace Database\Seeders;

use App\Models\Chapter;
use Illuminate\Database\Seeder;

class ChapterSeeder extends Seeder
{
    public function run(): void
    {
        Chapter::insert([
            [
                'content_id' => 1,
                'file'       => 'chapter-1-audio.mp3',
                'file_type'  => 'audio',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
            [
                'content_id' => 1,
                'file'       => 'chapter-1-notes.pdf',
                'file_type'  => 'pdf',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
            [
                'content_id' => 2,
                'file'       => 'chapter-2-audio.mp3',
                'file_type'  => 'audio',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at'=> now(),
                'updated_at'=> now(),
            ],
        ]);
    }
}
