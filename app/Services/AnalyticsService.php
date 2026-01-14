<?php

namespace App\Services;

use App\Models\Content;
use App\Models\FlashCard;
use App\Models\FlashCardActivity;
use App\Models\MockTestAttempt;
use App\Models\Question;
use App\Models\QuestionAnswer;
use App\Models\QuestionSet;
use App\Models\StudyGuideActivity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function overallUserStudyGuideProgress(int $userId): array
    {
        // Total pages from all published study guides
        $totalPages = Content::studyGuide()
            ->isPublish()
            ->sum('total_pages');

        // Total unique pages attempted by user
        $attemptedPages = StudyGuideActivity::where('user_id', $userId)
            ->whereHas('content', function ($q) {
                $q->where('type', Content::TYPE_STUDY_GUIDE)
                    ->where('is_publish', Content::IS_PUBLISH);
            })
            ->distinct(['content_id', 'page_number'])
            ->count();

        $progress = $totalPages > 0
            ? round(($attemptedPages / $totalPages) * 100, 2)
            : 0;

        return [
            'attempted_pages' => $attemptedPages,
            'total_pages' => $totalPages,
            'progress_percent' => $progress,
        ];
    }

    public function overallUserFlashCardProgress(int $userId): array
    {
        // Total flashcards from all published flashcard contents
        $totalCards = FlashCard::whereHas('content', function ($q) {
            $q->where('type', Content::TYPE_FLASHCARD)
                ->where('is_publish', Content::IS_PUBLISH);
        })->count();

        // Total unique cards attempted by user
        $attemptedCards = FlashCardActivity::where('user_id', $userId)
            ->whereHas('content', function ($q) {
                $q->where('type', Content::TYPE_FLASHCARD)
                    ->where('is_publish', Content::IS_PUBLISH);
            })
            ->distinct('card_id')
            ->count('card_id');

        $progress = $totalCards > 0
            ? round(($attemptedCards / $totalCards) * 100, 2)
            : 0;

        return [
            'attempted_cards' => $attemptedCards,
            'total_cards' => $totalCards,
            'progress_percent' => $progress,
        ];
    }

    public function getUserOverallPracticeAccuracy($userId): float
    {
        $answers = QuestionAnswer::where('user_id', $userId)
            ->where(function ($q) {
                $q->where('practice_correct_attempts', '>', 0)
                    ->orWhere('practice_failed_attempts', '>', 0);
            })
            ->get();

        $correct = $answers->sum('practice_correct_attempts');
        $failed = $answers->sum('practice_failed_attempts');

        $totalAttempts = $correct + $failed;

        if ($totalAttempts === 0) {
            return 0;
        }

        return round(($correct / $totalAttempts) * 100, 2);
    }

    public function getUserOverallPracticeProgress($userId): float
    {
        $totalQuestions = Question::count();

        if ($totalQuestions === 0) {
            return 0;
        }

        $answeredQuestions = QuestionAnswer::where('user_id', $userId)
            ->where(function ($q) {
                $q->where('practice_correct_attempts', '>', 0)
                    ->orWhere('practice_failed_attempts', '>', 0);
            })
            ->distinct('question_id')
            ->count('question_id');

        return round(($answeredQuestions / $totalQuestions) * 100, 2);
    }

    public function getUserOverallMockAccuracy($userId): float
    {
        $attempts = MockTestAttempt::where('user_id', $userId)
            ->where('status', MockTestAttempt::STATUS_COMPLETED)
            ->get();

        if ($attempts->isEmpty()) {
            return 0;
        }

        $totalCorrect = $attempts->sum('correct_answers');
        $totalQuestions = $attempts->sum('total_questions');

        if ($totalQuestions === 0) {
            return 0;
        }

        return round(($totalCorrect / $totalQuestions) * 100, 2);
    }

    public function getUserOverallMockProgress($userId): float
    {
        $totalQuestionSets = QuestionSet::count();

        if ($totalQuestionSets === 0) {
            return 0;
        }

        $maxAttempts = $totalQuestionSets * 3;

        $usedAttempts = MockTestAttempt::where('user_id', $userId)
            ->count();

        return round(($usedAttempts / $maxAttempts) * 100, 2);
    }

    // Admin Home page Total Users analytics methods
    public function totalUsersCount(): int
    {
        return User::count();
    }

    // active users count
    public function activeUsersCount(): int
    {
        return User::active()->count();
    }

    // today's users count
    public function todayNewUsersCount(): int
    {
        return User::whereDate('created_at', Carbon::today())->count();
    }

    // Admin Home Page Total Contents analytics methods
    public function totalContentsCount(): int
    {
        return Content::count();
    }

    // Admin Home page AverageExam analytics methods
    public function getAverageExamScore(): float
    {
        $average = MockTestAttempt::query()
            ->where('status', MockTestAttempt::STATUS_COMPLETED)
            ->avg('score_percentage');

        return round($average ?? 0, 2);
    }

    // Admin Home page UserGrowth analytics methods
    public function getUserGrowthChartData(): array
    {
        $users = User::select(
            DB::raw('COUNT(*) as total'),
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month")
        )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return [
            'labels' => $users->pluck('month')->map(
                fn ($m) => Carbon::createFromFormat('Y-m', $m)->format('M Y')
            ),
            'data' => $users->pluck('total'),
        ];
    }

    // Admin Home page SubscriptionDistribution analytics methods
    public function getSubscriptionDistributionChartData(): array
    {
        $subscriptions = DB::table('user_subscriptions')
            ->join('subscriptions', 'subscriptions.id', '=', 'user_subscriptions.subscription_id')
            ->where('user_subscriptions.is_active', true)
            ->select(
                'subscriptions.duration',
                DB::raw('COUNT(user_subscriptions.id) as total')
            )
            ->groupBy('subscriptions.duration')
            ->orderBy('subscriptions.duration')
            ->get();

        return [
            'labels' => $subscriptions->pluck('duration'),
            'data' => $subscriptions->pluck('total'),
        ];
    }

    // Admin Analytics page KPI Metrics analytics methods
    public function getKpiMetrics(): array
    {
        $avgScore = MockTestAttempt::where('status', 'completed')
            ->avg('score_percentage');

        $totalAttempts = MockTestAttempt::count();

        $passedAttempts = MockTestAttempt::where('status', 'completed')
            ->where('score_percentage', '>=', 60)
            ->count();

        $passRate = $totalAttempts > 0
            ? ($passedAttempts / $totalAttempts) * 100
            : 0;

        return [
            'avg_quiz_score' => round($avgScore ?? 0, 1),
            'completion_rate' => 78.5, // optional: derive from analytics table
            'total_attempts' => number_format($totalAttempts),
            'pass_rate' => round($passRate, 1),
        ];
    }

    public function getMostMissedQuestions(int $limit = 5): array
    {
        $questions = QuestionAnswer::select(
            'question_id',
            DB::raw('SUM(practice_failed_attempts + mock_failed_attempts) AS missed')
        )
            ->groupBy('question_id')
            ->orderByDesc('missed')
            ->limit($limit)
            ->with('question:id,question')
            ->get();

        return $questions->map(fn ($item) => [
            'question' => $item->question->question ?? 'Unknown Question',
            'missed' => (int) $item->missed,
        ])->toArray();
    }

    public function getContentEngagementChart(?int $userId = null): array
    {
        // If $userId is provided, use user-specific progress, else use total/completed counts
        $studyGuidePercent = $userId
            ? $this->overallUserStudyGuideProgress($userId)['progress_percent']
            : 0;

        $flashCardPercent = $userId
            ? $this->overallUserFlashCardProgress($userId)['progress_percent']
            : 0;

        $practicePercent = $userId
            ? $this->getUserOverallPracticeProgress($userId)
            : 0;

        $mockPercent = $userId
            ? $this->getUserOverallMockProgress($userId)
            : 0;

        return [
            'labels' => [
                'Study Guides',
                'Mock Exams',
                'Flash Cards',
                'Practice Questions',
            ],
            'data' => [
                $studyGuidePercent,
                $mockPercent,
                $flashCardPercent,
                $practicePercent,
            ],
        ];
    }
}
