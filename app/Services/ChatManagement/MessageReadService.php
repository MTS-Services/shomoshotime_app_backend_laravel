<?php

namespace App\Services\ChatManagement;

use App\Models\Message;
use App\Models\MessageRead;
use Illuminate\Support\Facades\Auth;

class MessageReadService
{
    public function getMessageReads($orderBy = 'sort_order', $order = 'asc')
    {
        return MessageRead::orderBy($orderBy, $order)->latest();
    }

    public function getMessageRead(string $encryptedId): MessageRead
    {
        return MessageRead::findOrFail(decrypt($encryptedId));
    }

    public function getDeletedMessageRead(string $encryptedId): MessageRead
    {
        return MessageRead::onlyTrashed()->findOrFail(decrypt($encryptedId));
    }

    public function createMessageRead(array $data, Message $messageRead): MessageRead
    {
    
        $data['user_id'] = Auth::id();
        $data['message_id'] = $messageRead->id;
        $data['read_at'] = now();
        $data['created_by'] = Auth::id();
        return MessageRead::create($data);
    }

    public function delete(MessageRead $messageRead): void
    {
        $messageRead->update(['deleted_by' => Auth::id()]);
        $messageRead->delete();
    }

    public function restore(string $encryptedId): void
    {
        $messageRead = $this->getDeletedMessageRead($encryptedId);
        $messageRead->update(['updated_by' => Auth::id()]);
        $messageRead->restore();
    }

    public function permanentDelete(string $encryptedId): void
    {
        $messageRead = $this->getDeletedMessageRead($encryptedId);
        $messageRead->forceDelete();
    }

    public function toggleStatus(MessageRead $messageRead): void
    {
        $messageRead->update([
            'status' => !$messageRead->status,
            'updated_by' => Auth::id(),
        ]);
    }
}
