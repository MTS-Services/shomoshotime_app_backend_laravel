<?php

namespace App\Http\Resources\API\V1\ChatManagement;

use App\Http\Resources\API\V1\UserManagement\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id ?? 'N/A',
            'name' => $this->name ?? 'N/A',
            'type' => $this->type ?? 'N/A',
            'last_message_at' => $this->last_message_at ?? 'N/A',

            'created_at' => $this->created_at_formatted,
            'updated_at' => $this->updated_at_formatted,

            'participants' => $this->whenLoaded('participants') ? ParticipantResource::collection($this->whenLoaded('participants')) : 'N/A',
            'messages' => $this->whenLoaded('messages') ? MessageResource::collection($this->whenLoaded('messages')) : 'N/A'
        ];
    }
}
