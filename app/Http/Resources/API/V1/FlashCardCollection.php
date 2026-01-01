<?php

namespace App\Http\Resources\API\V1;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FlashCardCollection extends JsonResource
{
     protected $collects = FlashCardResource::class;
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
