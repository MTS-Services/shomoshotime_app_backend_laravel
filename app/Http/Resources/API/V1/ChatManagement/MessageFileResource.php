<?php

namespace App\Http\Resources\API\V1\ChatManagement;

use App\Models\MessageFile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageFileResource extends JsonResource
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
            'message_id' => $this->message_id ?? 'N/A',
            'file' => $this->modified_file ?? 'N/A',
            'type' => $this->type ?? 'N/A',
            'types' => [
                MessageFile::TYPE_IMAGE . ': ' . 'Image',
                MessageFile::TYPE_VIDEO . ': ' . 'Video',
                MessageFile::TYPE_AUDIO . ': ' . 'Audio',
                MessageFile::TYPE_FILE . ': ' . 'File',
                MessageFile::TYPE_TEXT . ': ' . 'Text',
            ],

            'created_at' => $this->created_at_formatted ?? 'N/A',
            'updated_at' => $this->updated_at_formatted ?? 'N/A',
        ];
    }
}
