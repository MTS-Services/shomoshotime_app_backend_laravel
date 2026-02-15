<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CmsPage extends Model
{
    use HasFactory;

    public const TYPE_TERMS_AND_CONDITIONS = 'terms_and_conditions';

    public const TYPE_ABOUT_US = 'about_us';

    public const TYPE_PRIVACY_POLICY = 'privacy_policy';

    protected $fillable = [
        'sort_order',
        'type',
        'content',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public static function getTypeList(): array
    {
        return [
            self::TYPE_TERMS_AND_CONDITIONS => 'Terms & Conditions',
            self::TYPE_ABOUT_US => 'About Us',
            self::TYPE_PRIVACY_POLICY => 'Privacy Policy',
        ];
    }

    public static function normalizeType(string $type): string
    {
        return strtolower(str_replace([' ', '-'], '_', $type));
    }

    public function getTypeLabelAttribute(): string
    {
        return self::getTypeList()[$this->type] ?? ucfirst(str_replace('_', ' ', $this->type));
    }
}
