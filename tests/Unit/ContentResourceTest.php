<?php

use App\Http\Resources\API\V1\ContentResource;
use Tests\TestCase;

uses(TestCase::class);

use App\Models\Content;
use Illuminate\Http\Request;

function contentResourceArray(array $attributes = []): array
{
    $counts = [
        'study_guide_activities_count' => $attributes['study_guide_activities_count'] ?? null,
        'flash_card_activities_count' => $attributes['flash_card_activities_count'] ?? null,
        'flash_cards_count' => $attributes['flash_cards_count'] ?? null,
    ];

    unset(
        $attributes['study_guide_activities_count'],
        $attributes['flash_card_activities_count'],
        $attributes['flash_cards_count'],
    );

    $content = new Content(array_merge([
        'id' => 1,
        'sort_order' => 0,
        'title' => 'Test guide',
        'subtitle' => null,
        'category' => 'Test',
        'file' => 'contents/test.pdf',
        'file_type' => 'pdf',
        'type' => Content::TYPE_STUDY_GUIDE,
        'is_publish' => Content::IS_PUBLISH,
        'total_pages' => 10,
    ], $attributes));

    foreach ($counts as $key => $value) {
        if ($value !== null) {
            $content->setAttribute($key, $value);
        }
    }

    return (new ContentResource($content))->toArray(Request::create('/'));
}

it('caps study guide percent completed at 100', function () {
    $data = contentResourceArray([
        'study_guide_activities_count' => 26,
        'total_pages' => 10,
    ]);

    expect($data['study_guide_percent_completed'])->toBe(100.0)
        ->and($data['study_guide_activities_count'])->toBe(26);
});

it('calculates study guide percent completed within total pages', function () {
    $data = contentResourceArray([
        'study_guide_activities_count' => 5,
        'total_pages' => 10,
    ]);

    expect($data['study_guide_percent_completed'])->toBe(50.0);
});

it('caps flash card percent completed at 100', function () {
    $data = contentResourceArray([
        'type' => Content::TYPE_FLASHCARD,
        'flash_card_activities_count' => 15,
        'flash_cards_count' => 10,
    ]);

    expect($data['flash_card_percent_completed'])->toBe(100.0);
});
