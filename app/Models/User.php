<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\Contracts\OAuthenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable implements OAuthenticatable, MustVerifyEmail
{

    use HasApiTokens, HasFactory, SoftDeletes, Notifiable;

    protected $fillable = [
        'sort_order',
        'email',
        'password',
        'is_admin',
        'status',
        'phone',
        'name',
        'image',
        'phone_verified_at',
        'email_verified_at',
        'language_preference',
        'user_type',
        'is_banned',
        'last_login_at',
        'otp',
        'otp_sent_at',
        'otp_expires_at',

        'fcm_token',

        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'otp',
        'otp_sent_at',
        'otp_expires_at',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'password' => 'hashed',
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'otp_sent_at' => 'datetime',
        'otp_expires_at' => 'datetime',
        'last_login_at' => 'datetime',
        'user_type' => 'integer',
        'language_preference' => 'integer',
        'status' => 'integer',
        'is_admin' => 'boolean',
        'is_banned' => 'boolean',
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
        'status_btn_label',
        'status_btn_color',

        'created_at_human',
        'updated_at_human',
        'deleted_at_human',
        'last_login_at_human',

        'created_at_formatted',
        'updated_at_formatted',
        'deleted_at_formatted',
        'last_login_at_formatted',
    ];

    /* ===================== ===================== ===================== =====================
                                    Start of Relation's
    ===================== ===================== ===================== ===================== */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id', 'id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class, 'user_id', 'id');
    }

    public function creater()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by', 'id');
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class, 'user_id', 'id');
    }
    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id', 'id');
    }

    public function companyInformation()
    {
        return $this->hasOne(CompanyInformation::class);
    }

    public function properties()
    {
        return $this->hasMany(Property::class);
    }
    /* ===================== ===================== ===================== =====================
                                    End of Relation's
    ===================== ===================== ===================== ===================== */
    public const USER_TYPE_INDIVIDUAL = 0;
    public const USER_TYPE_AGENT = 1;
    public const USER_TYPE_ADMIN = 2;

    public static function userTypeList(): array
    {
        return [
            self::USER_TYPE_INDIVIDUAL => 'Individual',
            self::USER_TYPE_AGENT => 'Agent',
            self::USER_TYPE_ADMIN => 'Admin',
        ];
    }

    public const LANGUAGE_ENGLISH = 0;
    public const LANGUAGE_ARABIC  = 1;

    public static function languageList(): array
    {
        return [
            self::LANGUAGE_ENGLISH => 'en',
            self::LANGUAGE_ARABIC  => 'ar',
        ];
    }

    public function getLanguagePreferenceLabelAttribute(): string
    {
        return self::languageList()[$this->language_preference] ?? 'ar';
    }

    public function getUserTypeLabelAttribute(): string
    {
        return self::userTypeList()[$this->user_type] ?? 'Unknown';
    }

    public const BANNED = 1;
    public const NOT_BANNED = 0;

    public static function bannedList(): array
    {
        return [
            self::BANNED => 'Yes',
            self::NOT_BANNED => 'No',
        ];
    }

    public function getBannedLabelAttribute()
    {
        return $this->is_banned ? self::adminList()[$this->is_banned] : 'Unknown';
    }

    public const ADMIN = 1;
    public const NOT_ADMIN = 0;

    public static function adminList(): array
    {
        return [
            self::ADMIN => 'Yes',
            self::NOT_ADMIN => 'No',
        ];
    }

    public function getAdminLabelAttribute()
    {
        return $this->is_admin ? self::adminList()[$this->is_admin] : 'Unknown';
    }

    public function scopeAdmin(Builder $query): Builder
    {
        return $query->where('is_admin', self::ADMIN);
    }

    public function scopeNotAdmin(Builder $query): Builder
    {
        return $query->where('is_admin', self::NOT_ADMIN);
    }

    public const STATUS_PENDING   = 0;
    public const STATUS_ACTIVE    = 1;
    public const STATUS_INACTIVE  = 2;
    public const STATUS_SUSPENDED = 3;

    public static function statusList(): array
    {
        return [
            self::STATUS_PENDING   => 'Pending',
            self::STATUS_ACTIVE    => 'Active',
            self::STATUS_INACTIVE  => 'Inactive',
            self::STATUS_SUSPENDED => 'Suspended',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusList()[$this->status] ?? 'Unknown';
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

    public function getStatusBtnLabelAttribute(): string
    {
        return $this->status == self::STATUS_ACTIVE ? self::statusList()[self::STATUS_INACTIVE] : self::statusList()[self::STATUS_ACTIVE];
    }

    public function getStatusBtnColorAttribute(): string
    {
        return $this->status == self::STATUS_ACTIVE ? 'btn-error' : 'btn-success';
    }
    // Verify Accessors
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

    public function scopePhoneVerified(Builder $query): Builder
    {
        return $query->whereNotNull('phone_verified_at');
    }
    public function scopePhoneUnverified(Builder $query): Builder
    {
        return $query->whereNull('phone_verified_at');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_INACTIVE);
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

    // Accessor for deleted time
    public function getDeletedAtFormattedAttribute()
    {
        return $this->deleted_at ? timeFormat($this->deleted_at) : 'N/A';
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

    // Accessor for deleted time human readable
    public function getDeletedAtHumanAttribute()
    {
        return $this->deleted_at ? timeFormatHuman($this->deleted_at) : 'N/A';
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
}
