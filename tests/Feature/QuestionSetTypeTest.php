<?php

use App\Models\Question;
use App\Models\QuestionSet;
use App\Models\QuestionSetAnalytic;
use App\Models\User;
use App\Services\QuestionManagement\QuestionSetService;
use Illuminate\Support\Facades\Auth;

it('never allows mock test on practice question sets', function () {
    $user = User::factory()->create();
    Auth::login($user);

    $set = QuestionSet::query()->create([
        'sort_order' => 0,
        'category' => 'Test',
        'type' => QuestionSet::TYPE_PRACTICE,
        'title' => 'Practice only',
        'subtitle' => null,
        'status' => QuestionSet::STATUS_EASY,
        'created_by' => $user->id,
        'updated_by' => $user->id,
    ]);

    $analytic = QuestionSetAnalytic::query()->create([
        'user_id' => $user->id,
        'question_set_id' => $set->id,
        'practice_questions_answered' => 0,
        'practice_correct_answers' => 0,
        'practice_completed' => true,
        'mock_test_attempts' => 0,
        'current_mock_attempt_number' => 0,
        'current_mock_questions_answered' => 0,
        'created_by' => $user->id,
    ]);

    $analytic->setRelation('questionSet', $set);
    expect($analytic->canStartMockTest())->toBeFalse();

    $service = app(QuestionSetService::class);
    expect(fn() => $service->startMockTest($set->id))
        ->toThrow(\Exception::class, 'Mock tests are only available for question sets of type mock test.');
});

it('allows starting mock test on mock question sets without practice completion', function () {
    $user = User::factory()->create();
    Auth::login($user);

    $set = QuestionSet::query()->create([
        'sort_order' => 0,
        'category' => 'Test',
        'type' => QuestionSet::TYPE_MOCK_TEST,
        'title' => 'Mock only',
        'subtitle' => null,
        'status' => QuestionSet::STATUS_EASY,
        'created_by' => $user->id,
        'updated_by' => $user->id,
    ]);

    $analytic = new QuestionSetAnalytic([
        'user_id' => $user->id,
        'question_set_id' => $set->id,
        'practice_completed' => false,
        'mock_test_attempts' => 0,
    ]);
    $analytic->setRelation('questionSet', $set);
    expect($analytic->canStartMockTest())->toBeTrue();
});

it('starts mock test for mock question set without existing analytics', function () {
    $user = User::factory()->create();
    Auth::login($user);

    $set = QuestionSet::query()->create([
        'sort_order' => 0,
        'category' => 'Test',
        'type' => QuestionSet::TYPE_MOCK_TEST,
        'title' => 'Mock only',
        'subtitle' => null,
        'status' => QuestionSet::STATUS_EASY,
        'created_by' => $user->id,
        'updated_by' => $user->id,
    ]);

    Question::query()->create([
        'sort_order' => 0,
        'question_set_id' => $set->id,
        'question' => 'Sample?',
        'option_a' => 'A',
        'option_b' => 'B',
        'option_c' => 'C',
        'option_d' => 'D',
        'answer' => 'option_a',
        'created_by' => $user->id,
        'updated_by' => $user->id,
    ]);

    $service = app(QuestionSetService::class);
    $result = $service->startMockTest($set->id);

    expect($result['success'])->toBeTrue()
        ->and(
            QuestionSetAnalytic::query()
                ->where('user_id', $user->id)
                ->where('question_set_id', $set->id)
                ->exists()
        )->toBeTrue();
});

it('includes completed practice question sets in the practice list', function () {
    $user = User::factory()->create();
    Auth::login($user);

    $completedSet = QuestionSet::query()->create([
        'sort_order' => 0,
        'category' => 'Test',
        'type' => QuestionSet::TYPE_PRACTICE,
        'title' => 'Completed practice',
        'subtitle' => null,
        'status' => QuestionSet::STATUS_EASY,
        'created_by' => $user->id,
        'updated_by' => $user->id,
    ]);

    QuestionSetAnalytic::query()->create([
        'user_id' => $user->id,
        'question_set_id' => $completedSet->id,
        'practice_questions_answered' => 5,
        'practice_correct_answers' => 4,
        'practice_completed' => true,
        'practice_completed_at' => now(),
        'mock_test_attempts' => 0,
        'current_mock_attempt_number' => 0,
        'current_mock_questions_answered' => 0,
        'created_by' => $user->id,
    ]);

    $service = app(QuestionSetService::class);
    $ids = $service->getQuestionSets(QuestionSet::TYPE_PRACTICE)->pluck('id')->all();

    expect($ids)->toContain($completedSet->id);
});

it('returns only mock test type question sets when type is mock test', function () {
    $user = User::factory()->create();
    Auth::login($user);

    $practiceSet = QuestionSet::query()->create([
        'sort_order' => 0,
        'category' => 'Test',
        'type' => QuestionSet::TYPE_PRACTICE,
        'title' => 'Practice',
        'subtitle' => null,
        'status' => QuestionSet::STATUS_EASY,
        'created_by' => $user->id,
        'updated_by' => $user->id,
    ]);

    $mockSet = QuestionSet::query()->create([
        'sort_order' => 0,
        'category' => 'Test',
        'type' => QuestionSet::TYPE_MOCK_TEST,
        'title' => 'Mock',
        'subtitle' => null,
        'status' => QuestionSet::STATUS_EASY,
        'created_by' => $user->id,
        'updated_by' => $user->id,
    ]);

    $service = app(QuestionSetService::class);
    $ids = $service->getQuestionSets(QuestionSet::TYPE_MOCK_TEST)->pluck('id')->all();

    expect($ids)->toContain($mockSet->id);
    expect($ids)->not->toContain($practiceSet->id);
});

it('filters admin-style listing by question set type when applyUserProgressScope is false', function () {
    $user = User::factory()->create();

    $practiceSet = QuestionSet::query()->create([
        'sort_order' => 0,
        'category' => 'Test',
        'type' => QuestionSet::TYPE_PRACTICE,
        'title' => 'Practice',
        'subtitle' => null,
        'status' => QuestionSet::STATUS_EASY,
        'created_by' => $user->id,
        'updated_by' => $user->id,
    ]);

    $mockSet = QuestionSet::query()->create([
        'sort_order' => 0,
        'category' => 'Test',
        'type' => QuestionSet::TYPE_MOCK_TEST,
        'title' => 'Mock',
        'subtitle' => null,
        'status' => QuestionSet::STATUS_EASY,
        'created_by' => $user->id,
        'updated_by' => $user->id,
    ]);

    $service = app(QuestionSetService::class);

    $allIds = $service->getQuestionSets(null, applyUserProgressScope: false)->pluck('id')->all();
    expect($allIds)->toContain($practiceSet->id)->toContain($mockSet->id);

    $practiceOnlyIds = $service->getQuestionSets(QuestionSet::TYPE_PRACTICE, applyUserProgressScope: false)->pluck('id')->all();
    expect($practiceOnlyIds)->toContain($practiceSet->id)->not->toContain($mockSet->id);

    $mockOnlyIds = $service->getQuestionSets(QuestionSet::TYPE_MOCK_TEST, applyUserProgressScope: false)->pluck('id')->all();
    expect($mockOnlyIds)->toContain($mockSet->id)->not->toContain($practiceSet->id);
});

it('builds empty question set list messages by type', function () {
    expect(QuestionSet::emptyListMessageForType(QuestionSet::TYPE_PRACTICE, false))
        ->toBe('No Practice question sets were found.');
    expect(QuestionSet::emptyListMessageForType(QuestionSet::TYPE_MOCK_TEST, false))
        ->toBe('No Mock Test question sets were found.');
    expect(QuestionSet::emptyListMessageForType(QuestionSet::TYPE_MOCK_TEST, true))
        ->toBe('No Mock Test question sets match your search.');
});
