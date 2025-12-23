<?php

namespace App\Models;


use App\Models\BaseModel;

class UserProfile extends BaseModel
{

    protected $fillable = [
        'user_id',
        'sort_order',
        'dob',
        'gender',
        'city',
        'country',
        'postal_code',
        'bio',
        'website',
        'social_links',

        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'social_links' => 'array',
        'dob' => 'date',
        'gender' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->appends = array_merge(parent::getAppends(), [
            'modified_image',
        ]);
    }

    public const GENDER_MALE   = 0;
    public const GENDER_FEMALE = 1;
    public const GENDER_OTHER  = 2;

    /**
     * Get list of genders (English).
     */
    public static function genderList(): array
    {
        return [
            self::GENDER_MALE   => 'Male',
            self::GENDER_FEMALE => 'Female',
            self::GENDER_OTHER  => 'Other',
        ];
    }
    /**
     * Accessor for gender label (English).
     */
    public function getGenderLabelAttribute(): string
    {
        return self::genderList()[$this->gender] ?? 'Unknown';
    }

    /* ===================== ===================== ===================== =====================
                                    Start of Relation's
    ===================== ===================== ===================== ===================== */

    // Relations

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /* ===================== ===================== ===================== =====================
                                    End of Relation's
    ===================== ===================== ===================== ===================== */
    public function getModifiedImageAttribute()
    {
        return auth_storage_url($this->image);
    }
}
