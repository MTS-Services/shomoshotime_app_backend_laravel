<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use InvalidArgumentException;

class CmsPage extends Model
{
    use HasFactory;

    public const TYPE_PRIVACY_POLICY = 'privacy_policy';

    public const TYPE_TERMS_CONDITION = 'terms_condition';

    public const TYPE_ABOUT_US = 'about_us';

    protected $fillable = [
        'sort_order',
        'type',
        'content',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public static function allowedTypes(): array
    {
        return [
            self::TYPE_PRIVACY_POLICY,
            self::TYPE_TERMS_CONDITION,
            self::TYPE_ABOUT_US,
        ];
    }

    public static function labelForType(?string $type): string
    {
        return match ($type) {
            self::TYPE_PRIVACY_POLICY => 'Privacy Policy',
            self::TYPE_TERMS_CONDITION => 'Terms & Conditions',
            self::TYPE_ABOUT_US => 'About Us',
            default => Str::headline((string) $type),
        };
    }

    public static function normalizeType(?string $type): ?string
    {
        if ($type === null) {
            return null;
        }

        $normalized = Str::slug($type, '_');

        return in_array($normalized, self::allowedTypes(), true)
            ? $normalized
            : $type;
    }

    /**
     * Save or update a CMS page based on its type.
     *
     * @return array{0: self, 1: bool} Returns an array with the CmsPage instance and a flag indicating whether it was created.
     */
    public static function saveByType(array $attributes, int $userId): array
    {
        if (! array_key_exists('type', $attributes)) {
            throw new InvalidArgumentException('The type attribute is required.');
        }

        $type = self::normalizeType($attributes['type']);

        if ($type === null) {
            throw new InvalidArgumentException('The type attribute is required.');
        }

        $attributes['type'] = $type;
        $cmsPage = self::where('type', $type)->first();

        if ($cmsPage) {
            $cmsPage->fill(collect($attributes)->except('type')->toArray());
            $cmsPage->updated_by = $userId;
            $cmsPage->save();

            return [$cmsPage->refresh(), false];
        }

        $attributes['created_by'] = $userId;
        $cmsPage = self::create($attributes);

        return [$cmsPage, true];
    }
}
