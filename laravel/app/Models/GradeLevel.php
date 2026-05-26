<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class GradeLevel extends Model
{
    protected $table = 'grade_levels';

    protected $fillable = [
        'grade_number',
        'title_ru',
        'title_en',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'grade_number' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function problems(): BelongsToMany
    {
        return $this->belongsToMany(Problem::class, 'problem_grade_levels')
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    public function problemLadders(): BelongsToMany
    {
        return $this->belongsToMany(ProblemLadder::class, 'problem_ladder_grade_levels')
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    public function chapters(): BelongsToMany
    {
        return $this->belongsToMany(Chapter::class, 'chapter_grade_levels')
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    public function label(string $lang = 'en'): string
    {
        return $lang === 'ru' ? $this->title_ru : $this->title_en;
    }
}
