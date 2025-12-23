<?php

namespace App\Services\ChatManagement;

use App\Http\Traits\FileManagementTrait;
use App\Models\Conversation;
use App\Models\Participant;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ConversationService
{
    use FileManagementTrait;

    public function getConversations($conversationBy = 'sort_order', $conversation = 'asc')
    {
        return Conversation::orderBy($conversationBy, $conversation)->latest();
    }

    public function getConversation(string $encryptedId): Conversation|Collection
    {
        return Conversation::findOrFail(decrypt($encryptedId));
    }

    public function getDeletedOrder(string $encryptedId): Conversation|Collection
    {
        return Conversation::onlyTrashed()->findOrFail(decrypt($encryptedId));
    }

    public function createConversation(array $data, $participantId): Conversation
    {
      
        return DB::transaction(function () use ($data, $participantId) {
            
            $data['user_id']    = Auth::id();
            $data['created_by'] = Auth::id();

            // Create conversation
            $conversation = Conversation::create($data);

            // Add current user as participant
            Participant::create([
                'user_id'        => Auth::id(),
                'conversation_id' => $conversation->id,
                'joined_at'      => now(),
            ]);

            // Add other participant
            Participant::create([
                'user_id'        => $participantId,
                'conversation_id' => $conversation->id,
                'joined_at'      => now(),
            ]);

            return $conversation;
        });
    }

    public function delete(Conversation $conversation): void
    {
        $conversation->update(['deleted_by' => user()->id]);
        $conversation->delete();
    }

    public function restore(string $encryptedId): void
    {
        $conversation = $this->getDeletedOrder($encryptedId);
        $conversation->update(['updated_by' => user()->id]);
        $conversation->restore();
    }

    public function permanentDelete(string $encryptedId): void
    {
        $conversation = $this->getDeletedOrder($encryptedId);
        $conversation->forceDelete();
    }

    public function toggleStatus(Conversation $conversation): void
    {
        $conversation->update([
            'status' => !$conversation->status,
            'updated_by' => user()->id,
        ]);
    }
}
