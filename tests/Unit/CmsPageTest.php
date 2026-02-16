<?php

use App\Models\CmsPage;

it('provides the allowed cms page types', function () {
    expect(CmsPage::allowedTypes())
        ->toBeArray()
        ->toMatchArray([
            CmsPage::TYPE_PRIVACY_POLICY,
            CmsPage::TYPE_TERMS_CONDITION,
            CmsPage::TYPE_ABOUT_US,
        ]);
});

it('normalizes valid types and ignores invalid ones', function (?string $input, ?string $expected) {
    expect(CmsPage::normalizeType($input))->toBe($expected);
})->with([
    'null input' => [null, null],
    'already normalized' => [CmsPage::TYPE_PRIVACY_POLICY, CmsPage::TYPE_PRIVACY_POLICY],
    'sluggable string' => ['Privacy Policy', CmsPage::TYPE_PRIVACY_POLICY],
    'hyphen string' => ['terms-condition', CmsPage::TYPE_TERMS_CONDITION],
    'invalid type' => ['custom', 'custom'],
]);
