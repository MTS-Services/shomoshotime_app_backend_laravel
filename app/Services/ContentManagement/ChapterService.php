<?php

namespace App\Services\ContentManagement;

use App\Http\Traits\FileManagementTrait;
use App\Models\Chapter;
use App\Models\Content;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class ChapterService
{
    use FileManagementTrait;

    public function getChaptersByContent(?int $contentId = null, string $orderBy = 'created_at', string $order = 'desc'): Builder
    {

        return Chapter::with('content')->where('content_id', $contentId)->orderBy($orderBy, $order);
    }

    public function createChapter(array $data, $file = null): array
    {
        try {
            $content = Content::findOrFail($data['content_id']);

            if ($content->type === Content::TYPE_FLASHCARD) {
                return [
                    'data' => null,
                    'message' => 'Cannot create Study Guide: Content is a flash card.',
                ];
            }

            return DB::transaction(function () use ($data, $file) {

                if ($file) {
                    $mimeType = $file->getMimeType();
                    $data['file_type'] = $this->detectFileType($mimeType);
                    if ($data['file_type'] === 'invalid') {
                        return [
                            'data' => null,
                            'message' => 'Only audio and PDF files are allowed.',
                        ];
                    }

                    $data['file'] = $this->handleFileUpload(
                        $file,
                        'chapters',
                        $data['file_type']
                    );
                }

                $data['created_by'] = Auth::id();

                return [
                    'data' => Chapter::create($data),
                    'message' => 'Chapter created successfully',
                ];
            });

        } catch (Throwable $e) {
            return [
                'data' => null,
                'message' => 'Something went wrong: '.$e->getMessage(),
            ];
        }
    }

    private function detectFileType(string $mimeType): string
    {
        return match (true) {
            str_starts_with($mimeType, 'audio/') => 'audio',
            $mimeType === 'application/pdf' => 'pdf',
            default => 'invalid',
        };
    }

    public function updateChapter(int $id, array $data, $file = null): array
    {
        try {
            $chapter = Chapter::findOrFail($id);

            return DB::transaction(function () use ($chapter, $data, $file) {

                if ($file) {
                    $mimeType = $file->getMimeType();
                    $data['file_type'] = $this->detectFileType($mimeType);

                    if ($data['file_type'] === 'invalid') {
                        return [
                            'data' => null,
                            'message' => 'Only audio and PDF files are allowed.',
                        ];
                    }
                    if (! empty($chapter->file)) {
                        $this->fileDelete($chapter->file);
                    }

                    $data['file'] = $this->handleFileUpload(
                        $file,
                        'chapters',
                        $data['file_type']
                    );
                }

                $data['updated_by'] = Auth::id();

                $chapter->update($data);

                return [
                    'data' => $chapter->fresh(),
                    'message' => 'Chapter updated successfully',
                ];
            });

        } catch (Throwable $e) {
            return [
                'data' => null,
                'message' => 'Something went wrong: '.$e->getMessage(),
            ];
        }
    }

    public function deleteChapter(int $id): array
    {
        try {
            $chapter = Chapter::findOrFail($id);

            return DB::transaction(function () use ($chapter) {

                if (! empty($chapter->file)) {
                    $this->fileDelete($chapter->file);
                }

                $chapter->forceDelete();

                return [
                    'data' => null,
                    'message' => 'Chapter deleted successfully',
                ];
            });
        
        } catch (Throwable $e) {
            return [
                'data' => null,
                'message' => 'Content not found: '.$e->getMessage(),
            ];
        }
    }
}
