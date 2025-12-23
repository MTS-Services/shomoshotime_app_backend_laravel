<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends BaseModel
{
    protected $fillable = [
        'sort_order',
        'type',
        'name',
        'last_message_at',

        'created_by',
        'updated_by',
        'deleted_by',
    ];

    //

    public const TYPE_PRIVATE = 0;
    public const TYPE_GROUP   = 1;

    // Labels for each type
    public const TYPE_LABELS = [
        self::TYPE_PRIVATE => 'Private',
        self::TYPE_GROUP   => 'Group',
    ];

    // Colors for each type (can be CSS classes, hex codes, etc.)
    public const TYPE_COLORS = [
        self::TYPE_PRIVATE => 'info',   // example: blue badge
        self::TYPE_GROUP   => 'success',  // example: green badge
    ];

    // Accessor for label
    public function getTypeLabelAttribute(): string
    {
        return self::TYPE_LABELS[$this->type] ?? 'N/A';
    }

    // Accessor for color
    public function getTypeColorAttribute(): string
    {
        return self::TYPE_COLORS[$this->type] ?? 'secondary';
    }

    public function scopePrivate($query): Builder
    {
        return $query->where('type', self::TYPE_PRIVATE);
    }

    public function scopeGroup($query): Builder
    {
        return $query->where('type', self::TYPE_GROUP);
    }

    /* ===================== ===================== ===================== =====================
                                    Start of Relation's
    ===================== ===================== ===================== ===================== */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'conversation_id', 'id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class, 'conversation_id', 'id');
    }

    /* ===================== ===================== ===================== =====================
                                    End of Relation's
    ===================== ===================== ===================== ===================== */
}
