<?php

namespace App\Http\Resources\API\V1\ChatManagement;

use App\Http\Resources\API\V1\UserManagement\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
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
            'sender_id' => $this->sender_id ?? 'N/A',
            'message_content' => $this->message_content ?? 'N/A',
            'send_at' => $this->send_at ?? 'N/A',
            'status' => $this->status ?? 'N/A',
            
            'files' => MessageFileResource::collection($this->whenLoaded('files')),

            'conversation' => $this->whenLoaded('conversation') ? new ConversationResource($this->whenLoaded('conversation')) : 'N/A',
            'user' => $this->whenLoaded('user') ? new UserResource($this->user) : 'N/A',
            'created_at' => $this->created_at_formatted ?? 'N/A',
            'updated_at' => $this->updated_at_formatted ?? 'N/A',
        ];
    }
}
