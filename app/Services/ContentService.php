<?php

namespace App\Services;

use App\Models\Content;
use Illuminate\Database\Eloquent\Builder;

class ContentService
{
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
}
