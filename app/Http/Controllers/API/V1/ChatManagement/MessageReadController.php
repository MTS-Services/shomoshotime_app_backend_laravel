<?php

namespace App\Http\Controllers\API\V1\ChatManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\ChatManagement\MessageReadStoreRequest;
use App\Http\Resources\API\V1\Chatmanagement\MessageReadResource;
use App\Models\Message;
use App\Services\ChatManagement\MessageReadService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class MessageReadController extends Controller
{
    public MessageReadService $messageReadService;

    public function __construct(MessageReadService $messageReadService)
    {
        $this->messageReadService = $messageReadService;
    }


    public function index(Request $request)
    {
        try {
            $messages = $this->messageReadService->getMessageReads()->where('user_id', Auth::id())->get();
            if ($messages->isEmpty()) {
                return sendResponse(false, 'Messages Read not found.', null, Response::HTTP_NOT_FOUND);
            }
            return sendResponse(true, 'Messages Read retrieved successfully.', MessageReadResource::collection($messages), Response::HTTP_OK);
        } catch (Throwable $error) {
            return sendResponse(false, $error->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function storeMessageRead(MessageReadStoreRequest $request, $id)
    {
        
        try {
            $messageRead= Message::find($id);
            if (!$messageRead) {
                return sendResponse(false, 'Message not found.', null, Response::HTTP_NOT_FOUND);
            }
            $validated = $request->validated();
            $this->messageReadService->createMessageRead($validated,$messageRead);
            
            return sendResponse(true, 'Message read created successfully.', null, Response::HTTP_CREATED);
        } catch (Throwable $error) {
            Log::error('Failed to create message read: ', ['exception' => $error]);
            return sendResponse(false, 'Failed to create message read.' . $error->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
