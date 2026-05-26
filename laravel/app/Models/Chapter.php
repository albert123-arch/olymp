<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chapter extends Model
{
    protected $table = 'chapters';

    protected $fillable = [
        'course_id',
        'slug',
        'sort_order',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function texts(): HasMany
    {
        return $this->hasMany(ChapterText::class);
    }

    public function problems(): HasMany
    {
        return $this->hasMany(Problem::class)->orderBy('sort_order')->orderBy('id');
    }

    public function ladders(): HasMany
    {
        return $this->hasMany(ProblemLadder::class);
    }

    public function gradeLevels(): BelongsToMany
    {
        return $this->belongsToMany(GradeLevel::class, 'chapter_grade_levels')
            ->withPivot('is_primary')
            ->withTimestamps()
            ->orderBy('grade_number');
    }

    public function progress(): HasMany
    {
        return $this->hasMany(ChapterProgress::class);
    }
}
