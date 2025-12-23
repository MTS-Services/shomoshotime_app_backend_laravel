<?php

namespace App\Http\Controllers\API\V1\ChatManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\ChatManagement\ParticipentStoreRequest;
use App\Http\Resources\API\V1\Chatmanagement\ParticipantResource;
use App\Models\Participant;
use App\Services\ChatManagement\ParticipantService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class ParticipantController extends Controller
{
    public ParticipantService $participantService;

    public function __construct(ParticipantService $participantService)
    {
        $this->participantService = $participantService;
    }

    public function index()
    {
         try {
            $participants = $this->participantService->getParticipants()->with(['conversation','user','message'])->where('user_id', Auth::id())->get();
            if ($participants->isEmpty()) {
                return sendResponse(false, 'لم يتم العثور على الطلبات.', null, Response::HTTP_NOT_FOUND);
            }
            return sendResponse(true, 'تم استرجاع قائمة الطلبات بنجاح.', ParticipantResource::collection($participants), Response::HTTP_OK);
        } catch (Throwable $error) {
            return sendResponse(false, $error->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function storeParticipant(ParticipentStoreRequest $request)
    {
        // dd($request->validated());
        try {
            $validated = $request->validated();
            $par = $this->participantService->createParticipant($validated);
            dd($par);
            return sendResponse(true, 'Participant created successfully.', null, Response::HTTP_CREATED);
        } catch (Throwable $error) {
            Log::error('Failed to create participant: ', ['exception' => $error]);
            return sendResponse(false, 'Failed to create participant.' . $error->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
