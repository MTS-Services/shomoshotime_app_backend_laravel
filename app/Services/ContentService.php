<?php

namespace App\Services;

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

    //     public function getContents(string $orderBy = 'created_at', string $order = 'desc'): Builder
    // {
    //     return Content::orderBy($orderBy, $order)->latest();
    // }
    public function getContents(?int $type = null, string $orderBy = 'created_at', string $order = 'desc'): Builder
    {
        $query = Content::orderBy($orderBy, $order)->isPublish()->latest();

        if (! is_null($type)) {
            $query->where('type', $type);
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

    public function createContent(array $data, $file = null): Content
    {
        return DB::transaction(function () use ($data, $file) {

            if ($file) {
                $data['file'] = $this->handleFileUpload(
                    $file,
                    'contents',
                    $data['title'] ?? 'content'
                );
            }
            $data['is_publish'] = $data['is_publish'] ?? Content::NOT_PUBLISH;
            $data['created_by'] = Auth::id();

            return Content::create($data);
        });
    }

    public function updateContent(Content $content, array $data, $file = null): Content
    {
        return DB::transaction(function () use ($content, $data, $file) {

            if ($file) {
                if ($content->file) {
                    $this->fileDelete($content->file);
                }

                // Upload new file
                $data['file'] = $this->handleFileUpload(
                    $file,
                    'contents',
                    $data['title'] ?? 'content'
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
            if ($content->file) {
                $this->fileDelete($content->file);
            }
            $content->delete();
        });
    }
}
