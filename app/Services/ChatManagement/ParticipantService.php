<?php

namespace App\Services\ChatManagement;

use App\Http\Traits\FileManagementTrait;
use App\Models\Conversation;
use App\Models\Participant;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class ParticipantService
{
    use FileManagementTrait;

    public function getParticipants($participantBy = 'sort_order', $participant = 'asc')
    {
        return Participant::orderBy($participantBy, $participant)->latest();
    }

    public function getParticipant(string $encryptedId): Participant|Collection
    {
        return Participant::findOrFail(decrypt($encryptedId));
    }

    public function getDeletedOrder(string $encryptedId): Participant|Collection
    {
        return Participant::onlyTrashed()->findOrFail(decrypt($encryptedId));
    }

    public function createParticipant(array $data): Participant
    {
    //    dd($data);
        $data['user_id'] = Auth::id();
        $data['created_by'] = Auth::id();
        $participant = Participant::create($data);
        return $participant;
    }

    public function delete(Participant $participant): void
    {
        $participant->update(['deleted_by' => user()->id]);
        $participant->delete();
    }

    public function restore(string $encryptedId): void
    {
        $participant = $this->getDeletedOrder($encryptedId);
        $participant->update(['updated_by' => user()->id]);
        $participant->restore();
    }

    public function permanentDelete(string $encryptedId): void
    {
        $participant = $this->getDeletedOrder($encryptedId);
        $participant->forceDelete();
    }

    public function toggleStatus(Participant $participant): void
    {
        $participant->update([
            'status' => !$participant->status,
            'updated_by' => user()->id,
        ]);
    }
}