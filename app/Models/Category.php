<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;

class Category extends BaseModel
{
    protected $fillable = [
        'sort_order',
        'name',
        'slug',
        'description',
        'is_active',

        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public const ACTIVE = 1;
    public const INACTIVE = 0;

    public static function adminList(): array
    {
        return [
            self::ACTIVE => 'Yes',
            self::INACTIVE => 'No',
        ];
    }

    public function getAdminLabelAttribute()
    {
        return $this->is_active ? self::adminList()[$this->is_active] : 'Unknown';
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', self::ACTIVE);
    }

    public function scopeInActive(Builder $query): Builder
    {
        return $query->where('is_active', self::INACTIVE);
    }

    /* ===================== ===================== ===================== =====================
                                    Start of Relation's
    ===================== ===================== ===================== ===================== */

    public function properties()
    {
        return $this->hasMany(Property::class);
    }


    /* ===================== ===================== ===================== =====================
                                    End of Relation's
    ===================== ===================== ===================== ===================== */
}
