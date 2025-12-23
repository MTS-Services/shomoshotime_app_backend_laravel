<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Area extends BaseModel
{

    protected $fillable = [
        'sort_order',
        'name',
        'slug',
        'status',

        'created_by',
        'updated_by',
        'deleted_by',
    ];


    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->appends = array_merge(parent::getAppends(), [
            'status_color',
            'status_label',
            'status_btn_label',
            'status_btn_color',
        ]);
    }


    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;


    public static function statusList(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
        ];
    }

    /**
     * Status color badge class
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_ACTIVE => 'badge-success',
            self::STATUS_INACTIVE => 'badge-error',
            default => 'badge-default',
        };
    }

    /**
     * Status toggle button label
     * Example: If current status is Active, label will be 'Inactive'
     */
    public function getStatusBtnLabelAttribute(): string
    {
        return $this->status == self::STATUS_ACTIVE
            ? self::statusList()[self::STATUS_INACTIVE]
            : self::statusList()[self::STATUS_ACTIVE];
    }
    public function getStatusLabelAttribute(): string
    {
        return self::statusList()[$this->status] ?? 'Unknown';
    }


    /**
     * Status toggle button color
     * Example: If current status is Active, button color will be error (red), else success (green)
     */
    public function getStatusBtnColorAttribute(): string
    {
        return $this->status == self::STATUS_ACTIVE ? 'btn-error' : 'btn-success';
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

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeInActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_INACTIVE);
    }
}
