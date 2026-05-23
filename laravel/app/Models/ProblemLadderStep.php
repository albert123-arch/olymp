<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProblemLadderStep extends Model
{
    protected $table = 'problem_ladder_steps';

    protected $fillable = [
        'ladder_id',
        'problem_id',
        'step_label',
        'step_title',
        'step_type',
        'difficulty_level',
        'sort_order',
        'hint_html',
        'teacher_note_html',
    ];

    protected $casts = [
        'difficulty_level' => 'integer',
        'sort_order' => 'integer',
    ];

    public function ladder(): BelongsTo
    {
        return $this->belongsTo(ProblemLadder::class, 'ladder_id');
    }

    public function problem(): BelongsTo
    {
        return $this->belongsTo(Problem::class);
    }
}
