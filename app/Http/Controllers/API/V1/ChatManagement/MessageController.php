<?php

namespace App\Http\Controllers\API\V1\ChatManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\ChatManagement\MessageCreateRequest;
use App\Http\Resources\API\V1\ChatManagement\MessageResource;
use App\Models\Message;
use App\Services\ChatManagement\MessageService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class MessageController extends Controller
{
    public MessageService $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    public function index(Request $request)
    {
        try {
            $messages = $this->messageService->getMessages()->self()->with('files')->get();
            if ($messages->isEmpty()) {
                return sendResponse(false, 'Messages not found.', null, Response::HTTP_NOT_FOUND);
            }
            return sendResponse(true, 'Messages retrieved successfully.', MessageResource::collection($messages), Response::HTTP_OK);
        } catch (Throwable $error) {
            return sendResponse(false, $error->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function createMessage(MessageCreateRequest $request)
    {

        // dd($request->validated());
        try {
            $validated = $request->validated();
            DB::transaction(function () use ($validated, $request) {
                $message = $this->messageService->createMessage($validated);
                if (!empty($validated['files'])) {
                    $files = $request->file('files');
                    foreach ($files as $file) {
                        if ($file instanceof UploadedFile) {
                            $this->messageService->syncMessageFiles($message, $file);
                        }
                    }
                }
            });

            return sendResponse(true, 'Message created successfully.', null, Response::HTTP_CREATED);
        } catch (Throwable $error) {
            // Log the detailed error for debugging.
            Log::error('Failed to create message: ', ['exception' => $error]);

            // Return a generic error response to the user.
            return sendResponse(false, 'Failed to create message due to a server error.', null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
