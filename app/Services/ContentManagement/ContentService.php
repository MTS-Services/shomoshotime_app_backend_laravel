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

    public function createContent(array $data): Content
    {
        return DB::transaction(function () use ($data) {    
            $data['type'] = $data['type'] ?? Content::TYPE_STUDY_GUIDE;
            $data['is_publish'] = $data['is_publish'] ?? Content::NOT_PUBLISH;
            $data['created_by'] = Auth::id();

            return Content::create($data);
        });
    }

    public function updateContent(Content $content, array $data): Content
    {
        return DB::transaction(function () use ($content, $data) {
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
