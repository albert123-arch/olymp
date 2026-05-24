<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProblemLadderText extends Model
{
    protected $table = 'problem_ladder_texts';

    protected $fillable = [
        'problem_ladder_id',
        'language_id',
        'title',
        'description',
        'main_method',
    ];

    public function ladder(): BelongsTo
    {
        return $this->belongsTo(ProblemLadder::class, 'problem_ladder_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'language_id');
    }
}

