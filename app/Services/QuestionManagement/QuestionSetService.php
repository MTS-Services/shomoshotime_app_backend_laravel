<?php

namespace App\Services\QuestionManagement;

use App\Models\MockTestAttempt;
use App\Models\Question;
use App\Models\QuestionAnswer;
use App\Models\QuestionSet;
use App\Models\QuestionSetAnalytic;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuestionSetService
{
    /**
     * Submit an answer for a question
     */
    public function submitAnswer(int $questionSetId, int $questionId, string $answer): array
    {
        return DB::transaction(function () use ($questionSetId, $questionId, $answer) {
            $userId = Auth::id();

            // 1. Get question and validate
            $question = Question::where('question_set_id', $questionSetId)
                ->where('id', $questionId)
                ->firstOrFail();

            // 2. Get or create analytics record
            $analytics = QuestionSetAnalytic::firstOrCreate(
                [
                    'user_id' => $userId,
                    'question_set_id' => $questionSetId,
                ],
                [
                    'current_mode' => 'practice',
                    'created_by' => $userId,
                ]
            );

            // 3. Validate user can answer (check mode restrictions)
            $this->validateAnswerSubmission($analytics, $questionSetId);

            // 4. Check if answer is correct
            $isCorrect = $question->isCorrectAnswer($answer);

            // 5. Get or create question answer record
            $questionAnswer = QuestionAnswer::firstOrNew([
                'user_id' => $userId,
                'question_set_id' => $questionSetId,
                'question_id' => $questionId,
            ]);

            // 6. Determine if this is first time answering this question in current mode
            $isFirstTimeAnsweringInPractice = ! $questionAnswer->exists || $questionAnswer->getTotalPracticeAttempts() === 0;

            // 7. Update question answer based on current mode
            if ($analytics->isPracticeMode()) {
                $this->updatePracticeAnswer($questionAnswer, $answer, $isCorrect, $isFirstTimeAnsweringInPractice);
            } else {
                $this->updateMockTestAnswer($questionAnswer, $answer, $isCorrect, $analytics->current_mock_attempt_number);
            }

            $questionAnswer->last_mode = $analytics->current_mode;
            $questionAnswer->last_answer = $answer;
            $questionAnswer->updated_by = $userId;
            $questionAnswer->save();

            // 8. Update analytics and check completion
            if ($analytics->isPracticeMode()) {
                $this->updatePracticeAnalytics($analytics, $isCorrect, $isFirstTimeAnsweringInPractice, $questionSetId);
            } else {
                $this->updateMockTestAnalytics($analytics, $isCorrect, $questionSetId);
            }

            // 9. Return response with current state
            return [
                'success' => true,
                'is_correct' => $isCorrect,
                'correct_answer' => $question->answer,
                'current_mode' => $analytics->current_mode,
                'practice_completed' => $analytics->practice_completed,
                'mock_test_attempts' => $analytics->mock_test_attempts,
                'can_start_mock_test' => $analytics->canStartMockTest(),
                'remaining_mock_attempts' => $analytics->getRemainingMockAttempts(),
                'question_stats' => [
                    'practice_attempts' => $questionAnswer->getTotalPracticeAttempts(),
                    'practice_correct' => $questionAnswer->practice_correct_attempts,
                    'practice_accuracy' => round($questionAnswer->getPracticeAccuracy(), 2),
                ],
            ];
        });
    }

    /**
     * Start a new mock test
     */
    public function startMockTest(int $questionSetId): array
    {
        return DB::transaction(function () use ($questionSetId) {
            $userId = Auth::id();

            $analytics = QuestionSetAnalytic::where('user_id', $userId)
                ->where('question_set_id', $questionSetId)
                ->first();

            // Check if analytics exists
            if (! $analytics) {
                throw new Exception('You must complete practice mode before starting a mock test.');
            }

            // Validate can start mock test
            if (! $analytics->canStartMockTest()) {
                if (! $analytics->practice_completed) {
                    throw new Exception('You must complete practice mode before starting a mock test.');
                }
                if ($analytics->hasCompletedAllMockTests()) {
                    throw new Exception('You have already completed all 3 mock test attempts.');
                }
                throw new Exception('Cannot start mock test at this time.');
            }

            // Check if there's already an in-progress mock test
            $existingAttempt = MockTestAttempt::where('user_id', $userId)
                ->where('question_set_id', $questionSetId)
                ->where('status', MockTestAttempt::STATUS_IN_PROGRESS)
                ->first();

            if ($existingAttempt) {
                throw new Exception('You already have a mock test in progress. Please complete it first.');
            }

            // Increment mock test attempts
            $analytics->mock_test_attempts++;
            $analytics->current_mode = 'mock_test';
            $analytics->current_mock_attempt_number = $analytics->mock_test_attempts;
            $analytics->current_mock_questions_answered = 0;
            $analytics->updated_by = $userId;
            $analytics->save();

            // Create new mock test attempt record
            $totalQuestions = Question::where('question_set_id', $questionSetId)->count();

            if ($totalQuestions === 0) {
                throw new Exception('No questions available in this question set.');
            }

            $mockAttempt = MockTestAttempt::create([
                'user_id' => $userId,
                'question_set_id' => $questionSetId,
                'attempt_number' => $analytics->mock_test_attempts,
                'total_questions' => $totalQuestions,
                'status' => MockTestAttempt::STATUS_IN_PROGRESS,
                'created_by' => $userId,
            ]);

            return [
                'success' => true,
                'message' => 'Mock test started successfully',
                'mock_attempt' => [
                    'id' => $mockAttempt->id,
                    'attempt_number' => $mockAttempt->attempt_number,
                    'total_questions' => $mockAttempt->total_questions,
                    'status' => $mockAttempt->status,
                ],
                'analytics' => [
                    'current_mode' => $analytics->current_mode,
                    'remaining_attempts' => $analytics->getRemainingMockAttempts(),
                ],
            ];
        });
    }

    /**
     * Validate if user can submit answer in current state
     */
    private function validateAnswerSubmission(QuestionSetAnalytic $analytics, int $questionSetId): void
    {
        // If in practice mode, always allow
        if ($analytics->isPracticeMode()) {
            return;
        }

        // If in mock test mode, check if attempt exists and is not completed
        $currentAttempt = MockTestAttempt::where('user_id', Auth::id())
            ->where('question_set_id', $questionSetId)
            ->where('attempt_number', $analytics->current_mock_attempt_number)
            ->first();

        if (! $currentAttempt) {
            throw new Exception('No active mock test found. Please start a mock test first.');
        }

        if ($currentAttempt->isCompleted()) {
            throw new Exception('This mock test has already been completed.');
        }
    }

    /**
     * Update practice mode answer statistics
     */
    private function updatePracticeAnswer(QuestionAnswer $questionAnswer, string $answer, bool $isCorrect, bool $isFirstTime): void
    {
        if ($isFirstTime) {
            $questionAnswer->practice_first_answered_at = now();
        }

        $questionAnswer->practice_correct_attempts += $isCorrect ? 1 : 0;
        $questionAnswer->practice_failed_attempts += $isCorrect ? 0 : 1;
        $questionAnswer->practice_last_answer = $answer;
    }

    /**
     * Update mock test mode answer statistics
     */
    private function updateMockTestAnswer(QuestionAnswer $questionAnswer, string $answer, bool $isCorrect, int $attemptNumber): void
    {
        $questionAnswer->mock_correct_attempts += $isCorrect ? 1 : 0;
        $questionAnswer->mock_failed_attempts += $isCorrect ? 0 : 1;
        $questionAnswer->last_mock_attempt_number = $attemptNumber;
    }

    /**
     * Update practice mode analytics
     */
    private function updatePracticeAnalytics(QuestionSetAnalytic $analytics, bool $isCorrect, bool $isFirstTime, int $questionSetId): void
    {
        // Only count as new question if it's first time answering
        if ($isFirstTime) {
            $analytics->practice_questions_answered++;
        }

        // Always count correct answers
        if ($isCorrect) {
            $analytics->practice_correct_answers++;
        }

        // Check if practice is completed (all questions answered at least once)
        if (! $analytics->practice_completed) {
            $totalQuestions = Question::where('question_set_id', $questionSetId)->count();

            if ($analytics->practice_questions_answered >= $totalQuestions) {
                $analytics->practice_completed = true;
                $analytics->practice_completed_at = now();
            }
        }

        $analytics->updated_by = Auth::id();
        $analytics->save();
    }

    /**
     * Update mock test mode analytics
     */
    private function updateMockTestAnalytics(QuestionSetAnalytic $analytics, bool $isCorrect, int $questionSetId): void
    {
        $analytics->current_mock_questions_answered++;
        $analytics->updated_by = Auth::id();
        $analytics->save();

        // Update current mock test attempt
        $mockAttempt = MockTestAttempt::where('user_id', Auth::id())
            ->where('question_set_id', $questionSetId)
            ->where('attempt_number', $analytics->current_mock_attempt_number)
            ->first();

        if (! $mockAttempt) {
            throw new Exception('Mock test attempt not found.');
        }

        $mockAttempt->questions_answered++;
        $mockAttempt->correct_answers += $isCorrect ? 1 : 0;
        $mockAttempt->wrong_answers += $isCorrect ? 0 : 1;
        $mockAttempt->updated_by = Auth::id();

        // Check if mock test is completed
        if ($mockAttempt->questions_answered >= $mockAttempt->total_questions) {
            $mockAttempt->score_percentage = $mockAttempt->calculateScorePercentage();
            $mockAttempt->complete();

            // Update best score if this is better
            if ($mockAttempt->score_percentage > $analytics->best_mock_percentage) {
                $analytics->best_mock_score = $mockAttempt->correct_answers;
                $analytics->best_mock_percentage = $mockAttempt->score_percentage;
            }

            // Reset to practice mode
            $analytics->current_mode = 'practice';
            $analytics->current_mock_attempt_number = 0;
            $analytics->current_mock_questions_answered = 0;
            $analytics->save();
        }

        $mockAttempt->save();
    }

    /**
     * Get question set progress for a user
     */
    public function getQuestionSetProgress(int $questionSetId): array
    {
        $userId = Auth::id();

        $questionSet = QuestionSet::with(['questions'])->findOrFail($questionSetId);
        $totalQuestions = $questionSet->getTotalQuestions();

        $analytics = QuestionSetAnalytic::where('user_id', $userId)
            ->where('question_set_id', $questionSetId)
            ->first();

        if (! $analytics) {
            return [
                'question_set' => [
                    'id' => $questionSet->id,
                    'title' => $questionSet->title,
                    'subtitle' => $questionSet->subtitle,
                    'category' => $questionSet->category,
                    'difficulty' => $questionSet->getDifficultyLabel(),
                    'total_questions' => $totalQuestions,
                ],
                'mode' => 'practice',
                'practice' => [
                    'completed' => false,
                    'questions_answered' => 0,
                    'correct_answers' => 0,
                    'progress_percentage' => 0,
                ],
                'mock_tests' => [
                    'can_start' => false,
                    'attempts_used' => 0,
                    'attempts_remaining' => 3,
                    'best_score' => 0,
                    'best_percentage' => 0,
                    'attempts' => [],
                ],
            ];
        }

        $mockAttempts = MockTestAttempt::where('user_id', $userId)
            ->where('question_set_id', $questionSetId)
            ->orderBy('attempt_number', 'asc')
            ->get();

        return [
            'question_set' => [
                'id' => $questionSet->id,
                'title' => $questionSet->title,
                'subtitle' => $questionSet->subtitle,
                'category' => $questionSet->category,
                'difficulty' => $questionSet->getDifficultyLabel(),
                'total_questions' => $totalQuestions,
            ],
            'mode' => $analytics->current_mode,
            'practice' => [
                'completed' => $analytics->practice_completed,
                'questions_answered' => $analytics->practice_questions_answered,
                'correct_answers' => $analytics->practice_correct_answers,
                'progress_percentage' => round($analytics->getPracticeProgress(), 2),
                'completed_at' => $analytics->practice_completed_at?->format('Y-m-d H:i:s'),
            ],
            'mock_tests' => [
                'can_start' => $analytics->canStartMockTest(),
                'attempts_used' => $analytics->mock_test_attempts,
                'attempts_remaining' => $analytics->getRemainingMockAttempts(),
                'best_score' => $analytics->best_mock_score,
                'best_percentage' => (float) $analytics->best_mock_percentage,
                'current_attempt' => $analytics->isMockTestMode() ? [
                    'attempt_number' => $analytics->current_mock_attempt_number,
                    'questions_answered' => $analytics->current_mock_questions_answered,
                    'progress_percentage' => round(($analytics->current_mock_questions_answered / $totalQuestions) * 100, 2),
                ] : null,
                'attempts' => $mockAttempts->map(function ($attempt) {
                    return [
                        'attempt_number' => $attempt->attempt_number,
                        'status' => $attempt->status,
                        'total_questions' => $attempt->total_questions,
                        'questions_answered' => $attempt->questions_answered,
                        'correct_answers' => $attempt->correct_answers,
                        'wrong_answers' => $attempt->wrong_answers,
                        'score_percentage' => (float) $attempt->score_percentage,
                        'started_at' => $attempt->started_at->format('Y-m-d H:i:s'),
                        'completed_at' => $attempt->completed_at?->format('Y-m-d H:i:s'),
                    ];
                }),
            ],
        ];
    }

    /**
     * Get detailed question statistics for a user
     */
    public function getQuestionStatistics(int $questionSetId, int $questionId): array
    {
        $userId = Auth::id();

        $question = Question::where('question_set_id', $questionSetId)
            ->where('id', $questionId)
            ->firstOrFail();

        $answer = QuestionAnswer::where('user_id', $userId)
            ->where('question_set_id', $questionSetId)
            ->where('question_id', $questionId)
            ->first();

        if (! $answer) {
            return [
                'question_id' => $questionId,
                'never_answered' => true,
                'practice' => [
                    'attempts' => 0,
                    'correct' => 0,
                    'failed' => 0,
                    'accuracy' => 0,
                ],
                'mock_test' => [
                    'attempts' => 0,
                    'correct' => 0,
                    'failed' => 0,
                    'accuracy' => 0,
                ],
            ];
        }

        return [
            'question_id' => $questionId,
            'never_answered' => false,
            'practice' => [
                'attempts' => $answer->getTotalPracticeAttempts(),
                'correct' => $answer->practice_correct_attempts,
                'failed' => $answer->practice_failed_attempts,
                'accuracy' => round($answer->getPracticeAccuracy(), 2),
                'last_answer' => $answer->practice_last_answer,
                'first_answered_at' => $answer->practice_first_answered_at?->format('Y-m-d H:i:s'),
            ],
            'mock_test' => [
                'attempts' => $answer->getTotalMockAttempts(),
                'correct' => $answer->mock_correct_attempts,
                'failed' => $answer->mock_failed_attempts,
                'accuracy' => round($answer->getMockAccuracy(), 2),
                'last_attempt_number' => $answer->last_mock_attempt_number,
            ],
            'last_mode' => $answer->last_mode,
            'last_answer' => $answer->last_answer,
        ];
    }

    /**
     * Get all question sets with user progress
     */
    public function getQuestionSets(
        string $current_mode = 'practice',
        string $orderBy = 'created_at',
        string $order = 'desc'
    ): Builder {
        $query = QuestionSet::withCount('questions');

        // Always eager-load analytics for the current user & mode

        if ($current_mode === 'practice') {
            $query->with(['questionAnswers', 'analytics' => function ($q) use ($current_mode) {
                $q->where('user_id', Auth::id())
                    ->where('current_mode', $current_mode)->where('practice_completed', false);
            }])
                ->where(function ($q) use ($current_mode) {
                    $q->whereHas('analytics', function ($q2) use ($current_mode) {
                        $q2->where('user_id', Auth::id())
                            ->where('current_mode', $current_mode)->where('practice_completed', false);
                    })
                        ->orWhereDoesntHave('analytics', function ($q2) {
                            $q2->where('user_id', Auth::id());
                        });
                });
        }

        if ($current_mode === 'mock_test') {
            $query->with(['questionAnswers', 'analytics' => function ($q) use ($current_mode) {
                $q->where('user_id', Auth::id())
                    ->where('current_mode', $current_mode)->orWhere(function ($q2) {
                        $q2->where('current_mode', 'practice')
                            ->where('practice_completed', true);
                    });
            }])
                ->whereHas('analytics', function ($q) use ($current_mode) {
                    $q->where('user_id', Auth::id())
                        ->where('current_mode', $current_mode)->orWhere(function ($q2) {
                            $q2->where('current_mode', 'practice')
                                ->where('practice_completed', true);
                        });
                });
        }

        return $query->orderBy($orderBy, $order);
    }

    /**
     * Get questions for a specific question set
     */
    public function getQuestions(?int $questionSetId = null, string $orderBy = 'created_at', string $order = 'desc'): Builder
    {
        return Question::with('questionSet')->where('question_set_id', $questionSetId)->orderBy($orderBy, $order);

    }

    /**
     * Find a question set by ID
     */
    public function findData($id): ?QuestionSet
    {
        $model = QuestionSet::with(['questions', 'analytics' => function ($query) {
            $query->where('user_id', Auth::id());
        }])->findOrFail($id);

        if (! $model) {
            throw new Exception('Question set not found');
        }

        return $model;
    }

    /**
     * Create a new question set
     */
    public function createQuestion(array $data): QuestionSet
    {
        return DB::transaction(function () use ($data) {
            $data['status'] = $data['status'] ?? QuestionSet::STATUS_EASY;
            $data['created_by'] = Auth::id();

            return QuestionSet::create($data);
        });
    }

    /**
     * Update an existing question set
     */
    public function updateQuestion($findData, array $data): QuestionSet
    {
        return DB::transaction(function () use ($findData, $data) {
            $data['updated_by'] = Auth::id();
            $findData->update($data);

            return $findData;
        });
    }

    /**
     * Delete a question set
     */
    public function deleteQuestion($findData): void
    {
        DB::transaction(function () use ($findData) {
            $findData->forceDelete();
        });
    }

    /**
     * Get mock test result
     */
    public function getMockTestResult(int $questionSetId, int $attemptNumber): array
    {
        $userId = Auth::id();

        $mockAttempt = MockTestAttempt::where('user_id', $userId)
            ->where('question_set_id', $questionSetId)
            ->where('attempt_number', $attemptNumber)
            ->firstOrFail();

        if (! $mockAttempt->isCompleted()) {
            throw new Exception('Mock test is not yet completed.');
        }

        $questionSet = QuestionSet::findOrFail($questionSetId);

        return [
            'question_set' => [
                'id' => $questionSet->id,
                'title' => $questionSet->title,
                'subtitle' => $questionSet->subtitle,
            ],
            'attempt_number' => $mockAttempt->attempt_number,
            'total_questions' => $mockAttempt->total_questions,
            'correct_answers' => $mockAttempt->correct_answers,
            'wrong_answers' => $mockAttempt->wrong_answers,
            'score_percentage' => (float) $mockAttempt->score_percentage,
            'status' => $mockAttempt->status,
            'started_at' => $mockAttempt->started_at->format('Y-m-d H:i:s'),
            'completed_at' => $mockAttempt->completed_at?->format('Y-m-d H:i:s'),
        ];
    }
}
