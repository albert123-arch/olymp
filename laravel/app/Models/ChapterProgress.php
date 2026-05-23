<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChapterProgress extends Model
{
    protected $table = 'chapter_progress';

    public const CREATED_AT = null;

    protected $fillable = [
        'user_id',
        'chapter_id',
        'status',
        'last_opened_at',
        'completed_at',
    ];

    protected $casts = [
        'last_opened_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }
}
