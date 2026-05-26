<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProblemLadder extends Model
{
    protected $table = 'problem_ladders';

    protected $fillable = [
        'course_id',
        'chapter_id',
        'title',
        'slug',
        'description',
        'main_method',
        'difficulty_level',
        'sort_order',
        'is_published',
    ];

    protected $casts = [
        'difficulty_level' => 'integer',
        'sort_order' => 'integer',
        'is_published' => 'boolean',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(ProblemLadderStep::class, 'ladder_id')->orderBy('sort_order')->orderBy('id');
    }

    public function texts(): HasMany
    {
        return $this->hasMany(ProblemLadderText::class, 'problem_ladder_id');
    }

    public function gradeLevels(): BelongsToMany
    {
        return $this->belongsToMany(GradeLevel::class, 'problem_ladder_grade_levels')
            ->withPivot('is_primary')
            ->withTimestamps()
            ->orderBy('grade_number');
    }
}
