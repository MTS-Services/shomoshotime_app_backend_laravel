<?php

namespace App\Http\Controllers\API\V1\UserPanel;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\V1\MockTestAttemptResource;
use App\Http\Resources\API\V1\QuestionResource;
use App\Http\Resources\API\V1\QuestionSetAnalyticResource;
use App\Http\Resources\API\V1\QuestionSetResource;
use App\Services\QuestionManagement\QuestionSetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class QuestionController extends Controller
{
    protected QuestionSetService $questionSetService;

    public function __construct(QuestionSetService $questionSetService)
    {
        $this->questionSetService = $questionSetService;
    }

    /**
     * Get all question sets with pagination
     */
    public function getQuestionSets(Request $request)
    {
        try {
            $user = request()->user();
            if (! $user) {
                return sendResponse(false, 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
            }

            $questions = $this->questionSetService->getQuestionSets();
            $contents = $questions->paginate($request->input('per_page', 10));

            return sendResponse(
                true,
                'Questions Set data fetched successfully.',
                QuestionSetResource::collection($contents),
                Response::HTTP_OK
            );
        } catch (Throwable $e) {
            Log::error('Get Question Sets Error: '.$e->getMessage());

            return sendResponse(false, 'Something went wrong: '.$e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get questions for a specific question set
     */
    public function getQuestions(Request $request)
    {
        try {
            $user = $request->user();
            if (! $user) {
                return sendResponse(false, 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
            }

            $validator = Validator::make($request->all(), [
                'question_set_id' => ['required', 'integer', 'exists:question_sets,id'],
                'per_page' => ['nullable', 'integer'],
            ]);
            $questionSetId = $request->input('question_set_id');
            if ($questionSetId == null) {
                return sendResponse(false, 'Validation Error', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            $question = $this->questionSetService->getQuestions($questionSetId);
            $questions = $question->paginate($request->input('per_page', 10));

            return sendResponse( true,  'Questions fetched successfully.',  QuestionResource::collection($questions), Response::HTTP_OK
            );

        } catch (Throwable $e) {
            Log::error('Get Questions Error', ['message' => $e->getMessage(),'trace' => $e->getTraceAsString(),]);

            return sendResponse(false,  'Something went wrong', null,Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Submit an answer for a question
     */
    public function submitAnswer(Request $request)
    {
        try {
            $user = request()->user();
            if (! $user) {
                return sendResponse(false, 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
            }

            // Validation
            $validator = Validator::make($request->all(), [
                'question_set_id' => 'required|integer|exists:question_sets,id',
                'question_id' => 'required|integer|exists:questions,id',
                'answer' => 'required|string|in:option_a,option_b,option_c,option_d',
            ]);

            if ($validator->fails()) {
                return sendResponse(false, 'Validation Error', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $questionSetId = $request->input('question_set_id');
            $questionId = $request->input('question_id');
            $answer = strtolower(trim($request->input('answer')));

            $result = $this->questionSetService->submitAnswer($questionSetId, $questionId, $answer);

            return sendResponse(true, 'Answer submitted successfully.', $result, Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('Submit Answer Error: '.$e->getMessage());

            return sendResponse(false, $e->getMessage(), null, Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Start a mock test
     */
    public function startMockTest(Request $request)
    {
        try {
            $user = request()->user();
            if (! $user) {
                return sendResponse(false, 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
            }

            // Validation
            $validator = Validator::make($request->all(), [
                'question_set_id' => 'required|integer|exists:question_sets,id',
            ]);

            if ($validator->fails()) {
                return sendResponse(false, 'Validation Error', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $questionSetId = $request->input('question_set_id');
            $result = $this->questionSetService->startMockTest($questionSetId);

            // Wrap mock_attempt with resource if available
            if (isset($result['mock_attempt']) && class_exists('App\Http\Resources\API\V1\MockTestAttemptResource')) {
                $mockAttempt = \App\Models\MockTestAttempt::find($result['mock_attempt']['id']);
                if ($mockAttempt) {
                    $result['mock_attempt'] = new MockTestAttemptResource($mockAttempt);
                }
            }

            return sendResponse(true, $result['message'], $result, Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('Start Mock Test Error: '.$e->getMessage());

            return sendResponse(false, $e->getMessage(), null, Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Get question set progress
     */
    public function getProgress(Request $request)
    {
        try {
            $user = request()->user();
            if (! $user) {
                return sendResponse(false, 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
            }

            $validator = Validator::make($request->all(), [
                'question_set_id' => 'required|integer|exists:question_sets,id',
            ]);

            if ($validator->fails()) {
                return sendResponse(false, 'Validation Error', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $result = $this->questionSetService->getQuestionSetProgress($request->input('question_set_id'));

            // Wrap attempts with resource if available
            if (isset($result['mock_tests']['attempts']) && class_exists('App\Http\Resources\API\V1\MockTestAttemptResource')) {
                $result['mock_tests']['attempts'] = MockTestAttemptResource::collection(
                    collect($result['mock_tests']['attempts'])
                );
            }

            return sendResponse(true, 'Progress fetched successfully.', $result, Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('Get Progress Error: '.$e->getMessage());

            return sendResponse(false, 'Something went wrong: '.$e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get question statistics
     */
    public function getQuestionStatistics(Request $request)
    {
        try {
            $user = request()->user();
            if (! $user) {
                return sendResponse(false, 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
            }

            $validator = Validator::make($request->all(), [
                'question_set_id' => 'required|integer|exists:question_sets,id',
                'question_id' => 'required|integer|exists:questions,id',
            ]);

            if ($validator->fails()) {
                return sendResponse(false, 'Validation Error', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $result = $this->questionSetService->getQuestionStatistics(
                $request->input('question_set_id'),
                $request->input('question_id')
            );

            return sendResponse(true, 'Question statistics fetched successfully.', $result, Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('Get Question Statistics Error: '.$e->getMessage());

            return sendResponse(false, 'Something went wrong: '.$e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get analytics for question set
     */
    public function getAnalytics(Request $request)
    {
        try {
            $user = request()->user();
            if (! $user) {
                return sendResponse(false, 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
            }

            $validator = Validator::make($request->all(), [
                'question_set_id' => 'required|integer|exists:question_sets,id',
            ]);

            if ($validator->fails()) {
                return sendResponse(false, 'Validation Error', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $result = $this->questionSetService->getQuestionSetProgress($request->input('question_set_id'));

            // Wrap with QuestionSetAnalyticResource if available
            if (class_exists('App\Http\Resources\API\V1\QuestionSetAnalyticResource')) {
                $analytic = \App\Models\QuestionSetAnalytic::where('user_id', $user->id)
                    ->where('question_set_id', $request->input('question_set_id'))
                    ->first();

                if ($analytic) {
                    $result['analytics'] = new QuestionSetAnalyticResource($analytic);
                }
            }

            return sendResponse(true, 'Analytics fetched successfully.', $result, Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('Get Analytics Error: '.$e->getMessage());

            return sendResponse(false, 'Something went wrong: '.$e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get mock test result
     */
    public function getMockTestResult(Request $request)
    {
        try {
            $user = request()->user();
            if (! $user) {
                return sendResponse(false, 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
            }

            $validator = Validator::make($request->all(), [
                'question_set_id' => 'required|integer|exists:question_sets,id',
                'attempt_number' => 'required|integer|min:1|max:3',
            ]);

            if ($validator->fails()) {
                return sendResponse(false, 'Validation Error', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $result = $this->questionSetService->getMockTestResult(
                $request->input('question_set_id'),
                $request->input('attempt_number')
            );

            // Wrap with MockTestAttemptResource if we can get the model
            if (class_exists('App\Http\Resources\API\V1\MockTestAttemptResource')) {
                $mockAttempt = \App\Models\MockTestAttempt::where('user_id', $user->id)
                    ->where('question_set_id', $request->input('question_set_id'))
                    ->where('attempt_number', $request->input('attempt_number'))
                    ->first();

                if ($mockAttempt) {
                    $result = new MockTestAttemptResource($mockAttempt);
                }
            }

            return sendResponse(true, 'Mock test result fetched successfully.', $result, Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('Get Mock Test Result Error: '.$e->getMessage());

            return sendResponse(false, $e->getMessage(), null, Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Get all mock test results for a question set
     */
    public function getAllMockTestResults(Request $request)
    {
        try {
            $user = request()->user();
            if (! $user) {
                return sendResponse(false, 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
            }

            $validator = Validator::make($request->all(), [
                'question_set_id' => 'required|integer|exists:question_sets,id',
            ]);

            if ($validator->fails()) {
                return sendResponse(false, 'Validation Error', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $progress = $this->questionSetService->getQuestionSetProgress($request->input('question_set_id'));
            $mockAttempts = $progress['mock_tests']['attempts'] ?? [];

            // Wrap with MockTestAttemptResource
            if (class_exists('App\Http\Resources\API\V1\MockTestAttemptResource')) {
                $mockAttempts = MockTestAttemptResource::collection(collect($mockAttempts));
            }

            return sendResponse(true, 'All mock test results fetched successfully.', [
                'question_set_id' => $request->input('question_set_id'),
                'total_attempts' => is_countable($mockAttempts) ? count($mockAttempts) : 0,
                'attempts' => $mockAttempts,
            ], Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('Get All Mock Test Results Error: '.$e->getMessage());

            return sendResponse(false, 'Something went wrong: '.$e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get user dashboard with all question sets progress
     */
    public function getDashboard(Request $request)
    {
        try {
            $user = request()->user();
            if (! $user) {
                return sendResponse(false, 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
            }

            $questions = $this->questionSetService->getQuestionSets();
            $allQuestionSets = $questions->get();

            return sendResponse(
                true,
                'Dashboard data fetched successfully.',
                QuestionSetResource::collection($allQuestionSets),
                Response::HTTP_OK
            );
        } catch (Throwable $e) {
            Log::error('Get Dashboard Error: '.$e->getMessage());

            return sendResponse(false, 'Something went wrong: '.$e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
