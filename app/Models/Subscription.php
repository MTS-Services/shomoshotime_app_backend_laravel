<?php

namespace App\Models;

class Subscription extends BaseModel
{
    protected $fillable = [
        'sort_order',
        'duration',
        'price',
        'features',
        'tag',
        'status',

        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'features' => 'array',
        'price' => 'integer',
        'status' => 'integer',
        'tag' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Status Constants
    |--------------------------------------------------------------------------
    */
    public const STATUS_INACTIVE = 0;
    public const STATUS_ACTIVE = 1;

    /*
    |--------------------------------------------------------------------------
    | Status List
    |--------------------------------------------------------------------------
    */
    public static function getStatusList(): array
    {
        return [
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_ACTIVE => 'Active',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */
    public function getStatusLabelAttribute(): string
    {
        return self::getStatusList()[$this->status] ?? 'Unknown';
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_ACTIVE => 'badge-success',
            self::STATUS_INACTIVE => 'badge-error',
            default => 'badge-default',
        };
    }


    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
}
