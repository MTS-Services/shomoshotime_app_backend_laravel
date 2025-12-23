<?php

namespace App\Http\Controllers\API\V1\ChatManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\ChatManagement\ConversationCreateRequest;
use App\Http\Resources\API\V1\ChatManagement\ConversationResource;
use App\Models\Conversation;
use App\Models\User;
use App\Services\ChatManagement\ConversationService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class ConversationController extends Controller
{
    public ConversationService $conversationService;

    public function __construct(ConversationService $conversationService)
    {
        $this->conversationService = $conversationService;
    }


    public function index(Request $request)
    {
        try {
            $conversations = $this->conversationService->getConversations()->whereHas('participants', function ($query) {
                $query->where('user_id', Auth::id());
            })->with(['participants.user', 'messages' => function ($q) {
                $q->with('files')->latest()->limit(1);
            }])->get();
            if ($conversations->isEmpty()) {
                return sendResponse(false, 'لم يتم العثور على الطلبات.', null, Response::HTTP_NOT_FOUND);
            }
            return sendResponse(true, 'تم استرجاع قائمة الطلبات بنجاح.', ConversationResource::collection($conversations), Response::HTTP_OK);
        } catch (Throwable $error) {
            return sendResponse(false, $error->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function createConversation(ConversationCreateRequest $request, $participantId)
    {
        try {
            $participant = User::where([
                ['id', $participantId],
                ['is_admin', User::NOT_ADMIN]
            ])->first();

            if (!$participant) {
                return sendResponse(false, 'Participant not found.', null, Response::HTTP_NOT_FOUND);
            }

            $participant->load('participants.conversation.participants');

            $conversationExists = $participant->participants->filter(function ($participantRelation) {
                return $participantRelation->conversation->participants->contains('user_id', Auth::id());
            })->isNotEmpty();

            if ($conversationExists) {
                return sendResponse(false, 'A conversation with this participant already exists.', null, Response::HTTP_CONFLICT);
            }

            $validated = $request->validated();
            $this->conversationService->createConversation($validated, $participantId, $participant);

            return sendResponse(true, 'Conversation created successfully.', null, Response::HTTP_CREATED);
        } catch (Throwable $error) {
            Log::error('Failed to create conversation: ', ['exception' => $error]);
            return sendResponse(false, 'Failed to create conversation.', $error->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
