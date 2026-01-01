<?php

namespace App\Services\ContentManagement;

use App\Models\FlashCard;
use Illuminate\Database\Eloquent\Builder;

class FlashCardService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

       public function getFlashCardsByContent(?int $contentId = null, string $orderBy = 'created_at', string $order = 'desc'): Builder
    {
        return FlashCard::with('content')->where('content_id', $contentId)->orderBy($orderBy, $order);
    }

   
}
