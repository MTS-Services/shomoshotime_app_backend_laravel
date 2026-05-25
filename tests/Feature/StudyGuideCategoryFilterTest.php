<?php

use App\Models\Content;
use App\Models\User;
use App\Services\ContentManagement\ContentService;

function createStudyGuideContent(array $overrides = []): Content
{
    $user = User::factory()->create();

    return Content::query()->create(array_merge([
        'sort_order' => 0,
        'title' => 'Study guide',
        'subtitle' => 'Study guide',
        'total_pages' => 10,
        'category' => 'Abdomen',
        'file' => 'contents/sample.pdf',
        'file_type' => 'pdf',
        'type' => Content::TYPE_STUDY_GUIDE,
        'is_publish' => Content::IS_PUBLISH,
        'created_by' => $user->id,
        'updated_by' => $user->id,
    ], $overrides));
}

it('filters study guides by category and file type', function () {
    createStudyGuideContent(['title' => 'The Liver', 'category' => 'Abdomen']);
    createStudyGuideContent(['title' => 'Carotid Artery', 'category' => 'Vascular']);

    $results = app(ContentService::class)
        ->getContents(Content::TYPE_STUDY_GUIDE, 'pdf', 'Vascular')
        ->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->category)->toBe('Vascular')
        ->and($results->first()->file_type)->toBe('pdf');
});

it('does not apply category filter when category is omitted', function () {
    createStudyGuideContent(['category' => 'Abdomen']);
    createStudyGuideContent(['category' => 'Vascular']);

    $categories = app(ContentService::class)
        ->getContents(Content::TYPE_STUDY_GUIDE, 'pdf', null)
        ->pluck('category')
        ->sort()
        ->values()
        ->all();

    expect($categories)->toBe(['Abdomen', 'Vascular']);
});
