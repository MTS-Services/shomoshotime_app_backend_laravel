<?php

namespace App\Http\Controllers\Backend\Admin\ChatManagement;

use App\Http\Controllers\Controller;
use App\Http\Traits\AuditRelationTraits;
use App\Services\ChatManagement\ParticipantService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ParticipantController extends Controller
{
    use AuditRelationTraits;

    protected ParticipantService $participantService;

    public function __construct(ParticipantService $participantService)
    {
        $this->participantService = $participantService;
    }

    public static function middleware(): array
    {
        return [
            'auth',
        ];
    }

    protected function redirectIndex(): RedirectResponse
    {
        return redirect()->route('cm.participant.index');
    }

    protected function redirectTrashed(): RedirectResponse
    {
        return redirect()->route('cm.participant.trash');
    }

    /**
     * Display a listing of participants.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = $this->participantService->getParticipants();

            return DataTables::eloquent($query)

                ->editColumn('conversation_id', fn($conversation) => $conversation->conversation->name)
                // ->editColumn('last_read_message_id', fn($conversation) => $conversation->message->id)
                ->editColumn('user_id', fn($conversation) => $conversation->user->name)
                ->editColumn('joined_at', fn($conversation) => timeFormat($conversation->joined_at))
                ->editColumn('created_by', fn($conversation) => $this->creater_name($conversation))
                ->editColumn('created_at', fn($conversation) => $conversation->created_at_formatted)
                ->editColumn('action', fn($conversation) => view('components.admin.action-buttons', [
                    'menuItems' => $this->menuItems($conversation),
                ])->render())
                ->rawColumns(['user_id', 'joined_at', 'action', 'conversation_id'])
                ->make(true);
        }

        return view('backend.admin.chat-management.participant.index');
    }

    protected function menuItems($model): array
    {
        return [
            [
                'routeName' => 'javascript:void(0)',
                'data-id' => encrypt($model->id),
                'className' => 'view',
                'label' => 'Details',
            ],
            [
                'routeName' => 'cm.participant.destroy',
                'params' => [encrypt($model->id)],
                'label' => 'Delete',
                'delete' => true,
            ]
        ];
    }

    /**
     * Show details of a participant.
     */
    public function show(string $id)
    {
        $participant = $this->participantService->getParticipant($id);
        $participant['user_name'] = $participant->user;
        $participant['conversation_name'] = $participant['conversation']->name;
        // $participant['message_name'] = $participant['message']->id;
        $participant['joined_at'] = timeFormat($participant['joined_at']);
        $participant['creater_name'] = $this->creater_name($participant);
        $participant['updater_name'] = $this->updater_name($participant);
        // dd($participant);

        return response()->json($participant);
    }

    /**
     * Delete a participant (soft delete).
     */
    public function destroy(string $id)
    {
        try {
            $participant = $this->participantService->getParticipant($id);
            $this->participantService->delete($participant);

            session()->flash('success', 'Participant deleted successfully!');
        } catch (\Throwable $e) {
            session()->flash('error', 'Participant delete failed!');
            throw $e;
        }

        return $this->redirectIndex();
    }

    /**
     * List trashed participants.
     */
    public function trash(Request $request)
    {
        if ($request->ajax()) {
            $query = $this->participantService->getParticipants()->onlyTrashed();

            return DataTables::eloquent($query)
                ->editColumn('conversation_id', fn($conversation) => $conversation->conversation->name)
                // ->editColumn('last_read_message_id', fn($conversation) => $conversation->message->id)
                ->editColumn('user_id', fn($conversation) => $conversation->user->name)
                ->editColumn('joined_at', fn($conversation) => timeFormat($conversation->joined_at))
                ->editColumn('deleted_by', fn($participant) => $this->deleter_name($participant))
                ->editColumn('deleted_at', fn($participant) => $participant->deleted_at_formatted)
                ->editColumn('action', fn($participant) => view('components.admin.action-buttons', [
                    'menuItems' => $this->trashedMenuItems($participant),
                ])->render())
                ->rawColumns(['deleted_by', 'deleted_at', 'action'])
                ->make(true);
        }

        return view('backend.admin.chat-management.participant.trash');
    }

    /**
     * Action buttons for trashed items.
     */
    protected function trashedMenuItems($model): array
    {
        return [
            [
                'routeName' => 'cm.participant.restore',
                'params' => [encrypt($model->id)],
                'label' => 'Restore',
            ],
            [
                'routeName' => 'cm.participant.permanent-delete',
                'params' => [encrypt($model->id)],
                'label' => 'Permanent Delete',
                'p-delete' => true,
            ]
        ];
    }

    public function restore(string $id)
    {
        try {
            $this->participantService->restore($id);
            session()->flash('success', "Participant restored successfully!");
        } catch (\Throwable $e) {
            session()->flash('error', 'Participant restore failed!');
            throw $e;
        }

        return $this->redirectTrashed();
    }

    public function permanentDelete(string $id)
    {
        try {
            $this->participantService->permanentDelete($id);
            session()->flash('success', "Participant permanently deleted successfully!");
        } catch (\Throwable $e) {
            session()->flash('error', 'Participant permanent delete failed!');
            throw $e;
        }

        return $this->redirectTrashed();
    }
}
