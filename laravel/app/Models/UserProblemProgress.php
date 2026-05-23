<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProblemProgress extends Model
{
    protected $table = 'user_problem_progress';

    public const CREATED_AT = null;

    protected $fillable = [
        'user_id',
        'problem_id',
        'status',
        'last_opened_at',
        'solved_at',
    ];

    protected $casts = [
        'last_opened_at' => 'datetime',
        'solved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function problem(): BelongsTo
    {
        return $this->belongsTo(Problem::class);
    }
}
