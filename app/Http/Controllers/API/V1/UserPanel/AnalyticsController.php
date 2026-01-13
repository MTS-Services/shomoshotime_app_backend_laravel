<?php

namespace App\Http\Controllers\API\V1\UserPanel;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AnalyticsController extends Controller
{
    protected AnalyticsService $service;

    public function __construct(AnalyticsService $service)
    {
        $this->service = $service;
    }

    public function analytics(Request $request)
    {
        try {
            $user = request()->user();
            if (! $user) {
                return sendResponse(false, 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
            }
            $data = [];
            $study = $request->input('studyAnalytics', null);
            $flashcard = $request->input('flashcardAnalytics', null);
            $practiceAccuracy = $request->input('practiceAccuracy', null);
            $mocktestAccuracy = $request->input('mockTestAccuracy', null);
            $practiceProgress = $request->input('practiceProgress', null);
            $mocktestProgress = $request->input('mockTestProgress', null);

            if($study){
                $data['studyAnalytics'] = $this->service->overallUserStudyGuideProgress($user->id);
            } 
            if($flashcard){
                $data['flashcardAnalytics'] = $this->service->overallUserFlashcardProgress($user->id);
            } 

            if($practiceAccuracy){
                $data['practiceAccuracy'] = $this->service->getUserOverallPracticeAccuracy($user->id);
            }

            if($mocktestAccuracy){
                $data['mocktestAccuracy'] = $this->service->getUserOverallMockAccuracy($user->id);
            } 
            if($practiceProgress){
                $data['practiceProgress'] = $this->service->getUserOverallPracticeProgress($user->id);
            }
            if($mocktestProgress){
                $data['mocktestProgress'] = $this->service->getUserOverallMockProgress($user->id);
            }

           if(empty($data)){
                return sendResponse(false, 'No analytics type selected', null, Response::HTTP_BAD_REQUEST);
           }

            return sendResponse(true, 'User analytics fetched successfully', $data, Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('Get Todos Error: '.$e->getMessage());

            return sendResponse(false, 'Something went wrong.'.$e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
