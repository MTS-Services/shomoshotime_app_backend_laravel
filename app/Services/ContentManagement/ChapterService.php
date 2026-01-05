<?php

namespace App\Services\ChapterManagement;

use App\Http\Traits\FileManagementTrait;
use App\Models\Chapter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChapterService
{
    use FileManagementTrait;
   public function getChapters(?int $type = null, string $orderBy = 'created_at', string $order = 'desc'): Builder
    {
        $query = Chapter::orderBy($orderBy, $order)->isPublish()->latest();
        if (! is_null($type)) {
            $query->where('type', $type);
        }

        return $query;
    }

    public function findChapter($id): ?Chapter
    {
        $chapter = Chapter::findOrFail($id);
        if (! $chapter) {
            throw new \Exception('Chapter not found');
        }

        return $chapter;
    }

    public function createChapter(array $data, $file = null): Chapter
    {
        return DB::transaction(function () use ($data, $file) {

            if ($file) {
                $data['file'] = $this->handleFileUpload(
                    $file,
                    'Chapters',
                    $data['title'] ?? 'Chapter'
                );
            }
            $data['created_by'] = Auth::id();

            return Chapter::create($data);
        });
    }

    public function updateChapter(Chapter $chapter, array $data, $file = null): Chapter
    {
        return DB::transaction(function () use ($chapter, $data, $file) {

            if ($file) {
                if ($chapter->file) {
                    $this->fileDelete($chapter->file);
                }

                // Upload new file
                $data['file'] = $this->handleFileUpload(
                    $file,
                    'Chapters',
                    $data['title'] ?? 'Chapter'
                );
            }

            $data['updated_by'] = Auth::id();
            $chapter->update($data);

            return $chapter;
        });
    }

    public function deleteChapter(Chapter $chapter): void
    {
        DB::transaction(function () use ($chapter) {
            if ($chapter->file) {
                $this->fileDelete($chapter->file);
            }
            $chapter->forceDelete();
        });
    }
}
