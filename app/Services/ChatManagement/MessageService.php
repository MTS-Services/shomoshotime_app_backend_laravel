<?php

namespace App\Services\ChatManagement;

use App\Http\Traits\FileManagementTrait;
use App\Models\Message;
use App\Models\MessageFile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MessageService
{
    use FileManagementTrait;

    public function getMessages($conversationBy = 'sort_order', $conversation = 'asc')
    {
        return Message::orderBy($conversationBy, $conversation)->latest();
    }

    public function getMessage(string $encryptedId): Message|Collection
    {
        return Message::findOrFail(decrypt($encryptedId));
    }

    public function getDeletedOrder(string $encryptedId): Message|Collection
    {
        return Message::onlyTrashed()->findOrFail(decrypt($encryptedId));
    }

    public function createMessage(array $data): Message
    {
        $data['sender_id'] = Auth::id();
        $data['created_by'] = Auth::id();

        $message = Message::create($data);
        return $message;
    }

    public function delete(Message $message): void
    {
        $message->update(['deleted_by' => user()->id]);
        $message->delete();
    }

    public function restore(string $encryptedId): void
    {
        $message = $this->getDeletedOrder($encryptedId);
        $message->update(['updated_by' => user()->id]);
        $message->restore();
    }

    public function permanentDelete(string $encryptedId): void
    {
        $message = $this->getDeletedOrder($encryptedId);
        $message->forceDelete();
    }

    public function toggleStatus(Message $message): void
    {
        $message->update([
            'status' => !$message->status,
            'updated_by' => user()->id,
        ]);
    }

    public function syncMessageFiles(Message $message, UploadedFile $file): void
    {
      
        if ($file instanceof UploadedFile) {
            MessageFile::create([
                'message_id' => $message->id,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
                'file' => $this->handleFileUpload($file, 'messages'),
                'type' => $this->detectFileType($file)
            ]);
        }
    }


    private function detectFileType(UploadedFile $file): int
    {
    
        $mime = $file->getMimeType();

        if (str_starts_with($mime, 'image/')) {
            return MessageFile::TYPE_IMAGE;
        } elseif (str_starts_with($mime, 'video/')) {
            return MessageFile::TYPE_VIDEO;
        } elseif (str_starts_with($mime, 'audio/')) {
            return MessageFile::TYPE_AUDIO;
        } elseif (str_starts_with($mime, 'application/')) {
            return MessageFile::TYPE_FILE;
        }


        return MessageFile::TYPE_TEXT;
    }
}
