<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Content extends BaseModel
{
    /*
    |--------------------------------------------------------------------------
    | Type Constants
    |--------------------------------------------------------------------------
    */
    public const NOT_PUBLISH = 0;
    public const IS_PUBLISH  = 1;
    public const TYPE_STUDY_GUIDE = 0;
    public const TYPE_FLASHCARD  = 1;

    /*
    |--------------------------------------------------------------------------
    | Fillable
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'sort_order',
        'title',
        'subtitle',
        'is_publish',
        'total_pages',
        'category',
        'type',
        'file',
        'file_type',
        'created_by',
        'updated_by',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */
    protected $casts = [
        'type'     => 'integer',
        'is_publish' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Type Helpers
    |--------------------------------------------------------------------------
    */
    public static function getTypeList(): array
    {
        return [
            self::TYPE_STUDY_GUIDE => 'Study Guide',
            self::TYPE_FLASHCARD  => 'Flashcard',
        ];
    }
    public static function getPublishList(): array
    {
        return [
            self::NOT_PUBLISH => 'Not Publish',
            self::IS_PUBLISH  => 'Publish',
        ];
    }

    public function getTypeLabelAttribute(): string
    {
        return self::getTypeList()[$this->type] ?? 'Unknown';
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_STUDY_GUIDE => 'badge-info',
            self::TYPE_FLASHCARD  => 'badge-primary',
            default => 'badge-default',
        };
    }

   
    /* ===================== ===================== ===================== =====================
                                    Start of Relation's
    ===================== ===================== ===================== ===================== */
    public function flashCards(): HasMany
    {
        return $this->hasMany(FlashCard::class, 'content_id', 'id');
    }
   
    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class, 'content_id', 'id');
    }

    public function studyGuideActivities(): HasMany
    {
        return $this->hasMany(StudyGuideActivity::class, 'content_id', 'id');
    }

    public function flashCardActivities(): HasMany
    {
        return $this->hasMany(FlashCardActivity::class, 'content_id', 'id');
    }
   
    /* ===================== ===================== ===================== =====================
                                    End of Relation's
    ===================== ===================== ===================== ===================== */
    public function scopeStudyGuide($query)
    {
        return $query->where('type', self::TYPE_STUDY_GUIDE);
    }

    public function scopeFlashcard($query)
    {
        return $query->where('type', self::TYPE_FLASHCARD);
    }
    public function scopeIsPublish($query)
    {
        return $query->where('is_publish', self::IS_PUBLISH);
    }

    public function scopeNOTPublish($query)
    {
        return $query->where('is_publish', self::NOT_PUBLISH);
    }
}
