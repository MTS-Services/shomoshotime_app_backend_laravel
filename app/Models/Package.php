<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;

class Package extends BaseModel
{
    protected $fillable = [

        'name',
        'tag',
        'status',
        'tag_color',
        'price',
        'total_ad',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->appends = array_merge(parent::getAppends(), [
            'tag_list',
            'tag_label',
            'status_list',
            'status_label',
            'status_color',

        ]);
    }


    public const TAG_NORMAL = 1;
    public const TAG_SUPER = 2;
    public const TAG_AGENT_SUBSCR = 3;


    public static function tagList(): array
    {
        return [
            self::TAG_NORMAL => 'Normal',
            self::TAG_SUPER => 'Super',
            self::TAG_AGENT_SUBSCR => 'Agent Subscr',
        ];
    }

    public function getTagListAttribute(): array
    {
        return $this->tagList();
    }

    public function getTagLabelAttribute(): string
    {
        return isset($this->tag) ? $this->tagList()[$this->tag] : 'Unknown';
    }


    public const STATUS_ACTIVE = 1;
    public const STATUS_INACTIVE = 0;
    public static function statusList()
    {
        return [
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_ACTIVE => 'Active',
        ];
    }

    public function getStatusListAttribute(): array
    {
        return $this->statusList();
    }

    public function getStatusLabelAttribute(): string
    {
        return isset($this->status) ? $this->statusList()[$this->status] : 'Unknown';
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_ACTIVE => 'success',
            self::STATUS_INACTIVE => 'error',
            default => 'secondary',
        };
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    //



    /* ===================== ===================== ===================== =====================
                                    Start of Relation's
    ===================== ===================== ===================== ===================== */

    // Relations

    /* ===================== ===================== ===================== =====================
                                    End of Relation's
    ===================== ===================== ===================== ===================== */
}
