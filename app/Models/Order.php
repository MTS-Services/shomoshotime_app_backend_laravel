<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends BaseModel
{
    protected $fillable = [

        'sort_order',
        'user_id',
        'package_id',
        'amount',
        'total_ad',
        'order_id',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];


    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->appends = array_merge(parent::getAppends(), [
            'status_list',
            'status_label',
            'status_color',

        ]);
    }


    public const STATUS_PENDING = 0;
    public const STATUS_SUCCESS = 1;
    public const STATUS_FAILED = 2;
    public const STATUS_CANCELLED = 3;

    public static function statusList()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_SUCCESS => 'Success',
            self::STATUS_FAILED => 'Failed',
            self::STATUS_CANCELLED => 'Cancelled',
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
            self::STATUS_PENDING => 'info',
            self::STATUS_SUCCESS => 'success',
            self::STATUS_FAILED => 'error',
            self::STATUS_CANCELLED => 'secondary',
            default => 'neutral',
        };
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeSuccess(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_SUCCESS);
    }

    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class, 'package_id', 'id');
    }

    public function userAd(): HasOne
    {
        return $this->hasOne(UserAd::class, 'order_id', 'id');
    }

    protected static function booted()
    {
        // static::updating(function ($model) {
        //     $model->userAd->update([
        //         'status' => $model->status
        //     ]);
        // });
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
