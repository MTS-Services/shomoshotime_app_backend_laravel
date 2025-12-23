<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class PropertyImage extends BaseModel
{
    protected $fillable = [
        'sort_order',
        'property_id',
        'type',
        'is_primary',
        'file',

        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->appends = array_merge(parent::getAppends(), [
            'modified_file',

        ]);
    }
    //
    public const TYPE_IMAGE = 1;
    public const TYPE_VIDEO = 2;
    public const TYPE_UNKNOWN = 0;

    public static function typeList(): array
    {
        return [
            self::TYPE_UNKNOWN => 'Unknown',
            self::TYPE_IMAGE => 'Image',
            self::TYPE_VIDEO => 'Video',
        ];
    }

    public const PRIMARY = 1;
    public const NOT_PRIMARY = 0;

    public static function primaryList(): array
    {
        return [
            self::PRIMARY => 'Yes',
            self::NOT_PRIMARY => 'No',
        ];
    }

    public function getPrimaryLabelAttribute(): string
    {
        return self::primaryList()[$this->is_primary] ?? 'Unknown';
    }

    public function scopePrimary(Builder $query): Builder
    {
        return $query->where('is_primary', self::PRIMARY);
    }

    public function scopeNotPrimary(Builder $query): Builder
    {
        return $query->where('is_primary', self::NOT_PRIMARY);
    }


    /* ===================== ===================== ===================== =====================
                                    Start of Relation's
    ===================== ===================== ===================== ===================== */

    public function property()
    {
        return $this->belongsTo(Property::class);
    }


    /* ===================== ===================== ===================== =====================
                                    End of Relation's
    ===================== ===================== ===================== ===================== */

    public function getModifiedFileAttribute(): string
    {
        return $this->file ?
            (Str::startsWith($this->file, 'https://') ? $this->file : asset('storage/' . $this->file))
            : asset('default_img/no_img.jpg');
    }
}
