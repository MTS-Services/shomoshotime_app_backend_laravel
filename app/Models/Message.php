<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Message extends BaseModel
{
     protected $fillable =[
        'sort_order',
        'conversation_id',
        'sender_id',
        'message_content',
        'send_at',
        'status',

        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'send_at' => 'datetime',
    ];

    public const STATUS_SENT = 1;
    public const STATUS_DELIVERED = 2;
    public const STATUS_READ = 3;
    public const STATUS_FAILED = 4;

    

    /* ===================== ===================== ===================== =====================
                                    Start of Relation's
    ===================== ===================== ===================== ===================== */

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class, 'conversation_id','id');
    }
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id','id');
    }

    public function scopeSelf(Builder $query): Builder
    {
        return $query->where('sender_id', Auth::id());
    }
    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class, 'last_read_message_id','id');
    }

    public function messageReadAt(): HasMany
    {
        return $this->hasMany(MessageRead::class, 'message_id', 'id');
    }
    public function files(): HasMany
    {
        return $this->hasMany(MessageFile::class, 'message_id', 'id');
    }

    /* ===================== ===================== ===================== =====================
                                    End of Relation's
    ===================== ===================== ===================== ===================== */

}
