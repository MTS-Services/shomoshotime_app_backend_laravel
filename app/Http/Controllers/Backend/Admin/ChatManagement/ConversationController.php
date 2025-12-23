<?php

namespace App\Http\Controllers\Backend\Admin\ChatManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConversationRequest;
use App\Http\Traits\AuditRelationTraits;
use App\Models\Conversation;
use App\Services\ChatManagement\ConversationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ConversationController extends Controller
{
    use AuditRelationTraits;

    protected ConversationService $conversationService;

    public function __construct(ConversationService $conversationService)
    {
        $this->conversationService = $conversationService;
    }

    public static function middleware(): array
    {
        return [
            'auth',
        ];
    }

    protected function redirectIndex(): RedirectResponse
    {
        return redirect()->route('cm.conversation.index');
    }

    protected function redirectTrashed(): RedirectResponse
    {
        return redirect()->route('cm.conversation.trash');
    }

    /**
     * Display a listing of conversations.
     */
    public function index(Request $request)
{
    if ($request->ajax()) {
        $query = $this->conversationService->getConversations()
            ->private()
            ->with(['participants.user']);

        return DataTables::eloquent($query)
            ->addColumn('conversation_details', function ($conversation) {
                $participants = $conversation->participants->take(2);
                $names = $participants->map(function ($participant) {
                    return $participant->user->name ?? 'N/A';
                });

                // Get the two participant names
                $p1_name = $names->get(0, 'N/A');
                $p2_name = $names->get(1, 'N/A');
                
                // Format the output
                return "Participant 1: {$p1_name}<br>Participant 2: {$p2_name}";
            })
            ->addColumn('type', fn($conversation) => "<span class='badge badge-{$conversation->type_color}'>{$conversation->type_label}</span>")
             ->editColumn('created_by', fn($conversation) => $this->creater_name($conversation))
                ->editColumn('created_at', fn($conversation) => $conversation->created_at_formatted)
            ->editColumn('action', fn($conversation) => view('components.admin.action-buttons', [
                'menuItems' => $this->menuItems($conversation),
            ])->render())
            ->rawColumns(['last_message_at', 'action','created_by', 'type', 'conversation_details'])
            ->make(true);
    }

    return view('backend.admin.chat-management.conversation.index');
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
                'routeName' => 'cm.conversation.destroy',
                'params' => [encrypt($model->id)],
                'label' => 'Delete',
                'delete' => true,
            ]
        ];
    }



    /**
     * Show details of a conversation.
     */
    public function show(string $id)
    {
        $conversation = $this->conversationService->getConversation($id);
        $conversation['creater_name'] = $this->creater_name($conversation);
        $conversation['updater_name'] = $this->updater_name($conversation);

        return response()->json($conversation);
    }

    /**
     * Delete a conversation (soft delete).
     */
    public function destroy(string $id)
    {
        try {
            $conversation = $this->conversationService->getConversation($id);
            $this->conversationService->delete($conversation);

            session()->flash('success', 'Conversation deleted successfully!');
        } catch (\Throwable $e) {
            session()->flash('error', 'Conversation delete failed!');
            throw $e;
        }

        return $this->redirectIndex();
    }

    /**
     * List trashed conversations.
     */
    public function trash(Request $request)
    {
        if ($request->ajax()) {
            $query = $this->conversationService->getConversations()->onlyTrashed();

            return DataTables::eloquent($query)
                ->editColumn('deleted_by', fn($conversation) => $this->deleter_name($conversation))
                ->editColumn('deleted_at', fn($conversation) => $conversation->deleted_at_formatted)
                ->editColumn('action', fn($conversation) => view('components.admin.action-buttons', [
                    'menuItems' => $this->trashedMenuItems($conversation),
                ])->render())
                ->rawColumns(['deleted_by', 'deleted_at', 'action'])
                ->make(true);
        }

        return view('backend.admin.chat-management.conversation.trash');
    }
    /**
     * Action buttons for trashed items
     */
    protected function trashedMenuItems($model): array
    {
        return [
            [
                'routeName' => 'cm.conversation.restore',
                'params' => [encrypt($model->id)],
                'label' => 'Restore',
            ],
            [
                'routeName' => 'cm.conversation.permanent-delete',
                'params' => [encrypt($model->id)],
                'label' => 'Permanent Delete',
                'p-delete' => true,
            ]
        ];
    }

    public function restore(string $id)
    {
        try {
            $this->conversationService->restore($id);
            session()->flash('success', "Conversation restored successfully!");
        } catch (\Throwable $e) {
            session()->flash('error', 'Conversation restore failed!');
            throw $e;
        }

        return $this->redirectTrashed();
    }

    public function permanentDelete(string $id)
    {
        try {
            $this->conversationService->permanentDelete($id);
            session()->flash('success', "Conversation permanently deleted successfully!");
        } catch (\Throwable $e) {
            session()->flash('error', 'Conversation permanent delete failed!');
            throw $e;
        }

        return $this->redirectTrashed();
    }
}
