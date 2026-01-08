<?php

namespace App\Services\ContentManagement;

use App\Http\Traits\FileManagementTrait;
use App\Models\Content;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContentService
{
    use FileManagementTrait;

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function getContents($type = 0,?string $file_type = null, ?string $category = null, string $orderBy = 'created_at', string $order = 'desc'): Builder
    {
        $query = Content::orderBy($orderBy, $order)->latest();
        if (! is_null($type)) {
            $query->where('type', $type);
        }
        
        if (! is_null($category)) {
            $query->where('category', $category);
        }
        if (! is_null($file_type)) {
            $query->where('file_type', $file_type);
        }
        return $query;
    }

    public function findContent($id): ?Content
    {
        $content = Content::findOrFail($id);
        if (! $content) {
            throw new \Exception('Content not found');
        }

        return $content;
    }

    public function createContent(array $data, $file): Content
    {
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
                    'contents',
                    $data['file_type']
                );
            }
            $data['created_by'] = Auth::id();

            return Content::create($data);
        });
    }

    private function detectFileType(string $mimeType): string
    {
        return match (true) {
            str_starts_with($mimeType, 'audio/') => 'audio',
            $mimeType === 'application/pdf' => 'pdf',
            default => 'invalid',
        };
    }

    public function updateContent(Content $content, array $data, $file): Content
    {
        return DB::transaction(function () use ($content, $data, $file) {

             if ($file) {
                    $mimeType = $file->getMimeType();
                    $data['file_type'] = $this->detectFileType($mimeType);

                    if ($data['file_type'] === 'invalid') {
                        return [
                            'data' => null,
                            'message' => 'Only audio and PDF files are allowed.',
                        ];
                    }
                    if (! empty($content->file)) {
                        $this->fileDelete($content->file);
                    }

                    $data['file'] = $this->handleFileUpload(
                        $file,
                        'contents',
                        $data['file_type']
                    );
                }

            $data['updated_by'] = Auth::id();
            $content->update($data);

            return $content;
        });
    }

    public function deleteContent(Content $content): void
    {
        DB::transaction(function () use ($content) {
            $content->forceDelete();
        });
    }
}
