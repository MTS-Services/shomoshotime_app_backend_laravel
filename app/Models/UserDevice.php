<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDevice extends BaseModel
{
    protected $fillable = [
        'device_token',
        'user_agent',
        'ip_address',
        'last_login_at',
        'user_id',

        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'last_login_at' => 'datetime',
    ];

    /* ===================== ===================== ===================== =====================
                                    Start of Relation's
    ===================== ===================== ===================== ===================== */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /* ===================== ===================== ===================== =====================
                                    End of Relation's
    ===================== ===================== ===================== ===================== */
}
