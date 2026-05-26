<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProblemLadderGradeLevel extends Model
{
    protected $table = 'problem_ladder_grade_levels';

    protected $fillable = [
        'problem_ladder_id',
        'grade_level_id',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function problemLadder(): BelongsTo
    {
        return $this->belongsTo(ProblemLadder::class);
    }

    public function gradeLevel(): BelongsTo
    {
        return $this->belongsTo(GradeLevel::class);
    }
}
