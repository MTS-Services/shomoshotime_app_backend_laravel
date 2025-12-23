<?php

namespace App\Http\Resources\API\V1\ChatManagement;

use App\Http\Resources\API\V1\UserManagement\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageReadResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'message_id' => $this->message_id,
            'user_id' => $this->user_id,
            'read_at' => $this->read_at,
            
            'created_at' => $this->created_at_formatted ?? 'N/A',
            'updated_at' => $this->updated_at_formatted ?? 'N/A',

            'user' => $this->whenLoaded('user') ? new UserResource($this->user) : null,
            'message' => $this->whenLoaded('message') ? new MessageResource($this->message) : null

        ];
    }
}
