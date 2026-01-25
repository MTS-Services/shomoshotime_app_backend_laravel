<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\Contracts\OAuthenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable implements OAuthenticatable, MustVerifyEmail
{

    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'sort_order',
        'name',
        'email',
        'password',
        'status',
        'is_admin',
        'image',
        'email_verified_at',
        'last_login_at',
        'is_premium',
        'otp',
        'otp_sent_at',
        'otp_expires_at',
        'fcm_token',
        'google_id',

        'created_by',
        'updated_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'otp',
        'otp_sent_at',
        'otp_expires_at',
        'is_admin',
        'is_premium',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'password' => 'hashed',
        'email_verified_at' => 'datetime',
        'otp_sent_at' => 'datetime',
        'otp_expires_at' => 'datetime',
        'last_login_at' => 'datetime',
        'status' => 'integer',
        'is_admin' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'deleted_by' => 'integer',
    ];

    protected $appends = [
        'modified_image',

        'verify_label',
        'verify_color',

        'status_label',
        'status_color',

        'created_at_human',
        'updated_at_human',
        'last_login_at_human',

        'created_at_formatted',
        'updated_at_formatted',
        'last_login_at_formatted',
    ];

    /* ===================== ===================== ===================== =====================
                                    Start of Relation's
    ===================== ===================== ===================== ===================== */
    public function creater()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
    public function userDevices(): HasMany
    {
        return $this->hasMany(UserDevice::class, 'user_id', 'id');
    }
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
    public function notifications(): HasMany
    {
        return $this->hasMany(PusherNotification::class, 'user_id', 'id');
    }
    /* ===================== ===================== ===================== =====================
                                    End of Relation's
    ===================== ===================== ===================== ===================== */

    public const ADMIN = 1;
    public const NOT_ADMIN = 0;

    public static function getAdminList(): array
    {
        return [
            self::ADMIN => 'Yes',
            self::NOT_ADMIN => 'No',
        ];
    }

    public function getAdminLabelAttribute()
    {
        return $this->is_admin ? self::getAdminList()[$this->is_admin] : 'Unknown';
    }

    public function scopeAdmin(Builder $query): Builder
    {
        return $query->where('is_admin', self::ADMIN);
    }

    public function scopeNotAdmin(Builder $query): Builder
    {
        return $query->where('is_admin', self::NOT_ADMIN);
    }

    public const STATUS_PENDING = 0;
    public const STATUS_ACTIVE = 1;
    public const STATUS_INACTIVE = 2;
    public const STATUS_SUSPENDED = 3;

    public static function getStatusList(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_SUSPENDED => 'Suspended',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::getStatusList()[$this->status] ?? 'Unknown';
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_ACTIVE => 'badge-success',
            self::STATUS_INACTIVE => 'badge-error',
            self::STATUS_PENDING => 'badge-warning',
            self::STATUS_SUSPENDED => 'badge-neutral',
            default => 'badge-default',
        };
    }

    public function getVerifyLabelAttribute()
    {
        return $this->email_verified_at ? 'Verified' : 'Unverified';
    }

    public function getVerifyColorAttribute()
    {
        return $this->email_verified_at ? 'badge-success' : 'badge-error';
    }

    // Verified scope
    public function scopeEmailVerified(Builder $query): Builder
    {
        return $query->whereNotNull('email_verified_at');
    }
    public function scopeEmailUnverified(Builder $query): Builder
    {
        return $query->whereNull('email_verified_at');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_INACTIVE);
    }
    public function scopeSuspended(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_SUSPENDED);
    }

    // Accessor for created time
    public function getCreatedAtFormattedAttribute()
    {
        return timeFormat($this->created_at);
    }

    // Accessor for updated time
    public function getUpdatedAtFormattedAttribute()
    {
        return $this->created_at != $this->updated_at ? timeFormat($this->updated_at) : 'N/A';
    }
    public function getLastLoginAtFormattedAttribute()
    {
        return $this->last_login_at ? timeFormat($this->last_login_at) : 'N/A';
    }
    // Accessor for created time human readable
    public function getCreatedAtHumanAttribute()
    {
        return timeFormatHuman($this->created_at);
    }

    // Accessor for updated time human readable
    public function getUpdatedAtHumanAttribute()
    {
        return $this->created_at != $this->updated_at ? timeFormatHuman($this->updated_at) : 'N/A';
    }


    // Accessor for last login time human readable
    public function getLastLoginAtHumanAttribute()
    {
        return $this->last_login_at ? timeFormatHuman($this->last_login_at) : 'N/A';
    }

    // Accessor for modified image
    public function getModifiedImageAttribute()
    {
        return auth_storage_url($this->image);
    }

    public function isAdmin(): bool
    {
        return $this->is_admin == self::ADMIN;
    }
}
