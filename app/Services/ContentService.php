<?php

namespace App\Services;

use App\Http\Traits\FileManagementTrait;
use App\Models\Content;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
        $query = Content::orderBy($orderBy, $order);

        if (!is_null($type)) {
            $query->where('type', $type);
        }

        return $query;
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
            $data['created_by'] = Auth::id();

            return Content::create($data);
        });
    }


}
