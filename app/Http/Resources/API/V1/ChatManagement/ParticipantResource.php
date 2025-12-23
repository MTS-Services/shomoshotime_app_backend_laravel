<?php

namespace App\Http\Resources\API\V1\ChatManagement;

use App\Http\Resources\API\V1\UserManagement\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParticipantResource extends JsonResource
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
            'conversation_id' => $this->conversation_id ?? 'N/A',
            'user_id' => $this->user_id ?? 'N/A',
            'last_read_message_id' => $this->last_read_message_id ?? 'N/A',
            'joined_at' => $this->joined_at ?? 'N/A',
            'is_muted' => $this->is_muted ?? 'N/A',

            'created_at' => $this->created_at_formatted,
            'updated_at' => $this->updated_at_formatted,

            'user' => $this->whenLoaded('user') ? new UserResource($this->whenLoaded('user')) : 'N/A',
            'conversation' => $this->whenLoaded('conversation') ? new ConversationResource($this->whenLoaded('conversation')) : 'N/A',
            'message' => $this->whenLoaded('message') ? new MessageResource($this->whenLoaded('message')) : 'N/A',

        ];
    }
}
