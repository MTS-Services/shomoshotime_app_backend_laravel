<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;

class Property extends BaseModel
{
    protected $fillable = [
        'sort_order',
        'user_id',
        'category_id',
        'property_type_id',
        'area_id',
        'title',
        'description',
        'price',
        'status',
        'is_featured',
        'expires_at',
        'renew_at',
        'renew_count',
        'slug',

        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'status' => 'integer',
        'is_featured' => 'boolean',
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
    public const STATUS_PENDING = 0;
    public const STATUS_OPEN = 1;
    public const STATUS_ARCHIVE = 2;
    public const STATUS_SOLD = 3;
    public const STATUS_DELETED = 4;
    public const STATUS_EXPIRED = 5;

    public static function statusList(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_OPEN => 'Open',
            self::STATUS_ARCHIVE => 'Archived',
            self::STATUS_SOLD => 'Sold',
            self::STATUS_DELETED => 'Deleted',
            self::STATUS_EXPIRED => 'Expired',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusList()[$this->status] ?? 'Unknown';
    }

    public function getStatusBtnLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_OPEN => 'Pending',
            self::STATUS_PENDING => 'Archived',
            self::STATUS_ARCHIVE => 'Sold',
            self::STATUS_SOLD => 'Open',
            self::STATUS_DELETED => 'Restore',
            self::STATUS_EXPIRED => 'Renew',
            default => 'Change Status',
        };
    }


    public function getStatusBtnColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_OPEN => 'btn-success',
            self::STATUS_SOLD => 'btn-danger',
            self::STATUS_PENDING => 'btn-warning',
            self::STATUS_ARCHIVE => 'btn-secondary',
            self::STATUS_DELETED => 'btn-dark',
            self::STATUS_EXPIRED => 'btn-info',
            default => 'btn-secondary',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_OPEN => 'badge-success',
            self::STATUS_SOLD => 'badge-danger',
            self::STATUS_PENDING => 'badge-warning',
            self::STATUS_ARCHIVE => 'badge-secondary',
            self::STATUS_DELETED => 'badge-dark',
            self::STATUS_EXPIRED => 'badge-info',
            default => 'badge-default',
        };
    }

    //    status  Scope

    /**
     * Scope for Pending properties
     */

    public function scopeSelf(Builder $query): Builder
    {
        return $query->where('user_id', Auth::id());
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for Open properties
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    /**
     * Scope for Archived properties
     */
    public function scopeArchived(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ARCHIVE);
    }

    /**
     * Scope for Sold properties
     */
    public function scopeSold(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_SOLD);
    }

    /**
     * Scope for Deleted properties
     */
    public function scopeDeleted(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_DELETED);
    }

    /**
     * Scope for Expired properties
     */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_EXPIRED);
    }




    // Featured Property
    public const FEATURED = 1;
    public const NOT_FEATURED = 0;

    // Static method to get featured list
    public static function featuredList(): array
    {
        return [
            self::FEATURED => 'Featured',
            self::NOT_FEATURED => 'Not Featured',
        ];
    }

    // Getter for featured label
    public function getFeaturedLabelAttribute()
    {
        return $this->is_featured ? self::featuredList()[$this->is_featured] : 'Unknown';
    }

    // Scope to filter featured properties
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', self::FEATURED);
    }

    // Scope to filter non-featured properties
    public function scopeNotFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', self::NOT_FEATURED);
    }

    /* ===================== ===================== ===================== =====================
                                    Start of Relation's
    ===================== ===================== ===================== ===================== */

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class, 'property_type_id', 'id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id', 'id');
    }


    public function primaryImage()
    {
        return $this->hasOne(PropertyImage::class, 'property_id', 'id')->where('is_primary', 1);
    }

    public function nonPrimaryImages()
    {
        return $this->hasMany(PropertyImage::class, 'property_id', 'id')->where('is_primary', 0);
    }

    public function images()
    {
        return $this->hasMany(PropertyImage::class, 'property_id', 'id');
    }

    public function views()
    {
        return $this->hasMany(PropertyView::class, 'property_id', 'id');
    }

    /* ===================== ===================== ===================== =====================
                                    End of Relation's
    ===================== ===================== ===================== ===================== */
}
