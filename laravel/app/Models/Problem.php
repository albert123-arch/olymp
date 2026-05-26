<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Problem extends Model
{
    protected $table = 'problems';

    protected $fillable = [
        'chapter_id',
        'problem_code',
        'book_number',
        'difficulty',
        'problem_type',
        'sort_order',
        'is_published',
        'source_name',
        'source_year',
        'source_round',
        'source_grade',
        'source_problem_number',
        'source_url',
        'source_note',
    ];

    protected $casts = [
        'book_number' => 'integer',
        'difficulty' => 'integer',
        'sort_order' => 'integer',
        'is_published' => 'boolean',
        'source_year' => 'integer',
    ];

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function texts(): HasMany
    {
        return $this->hasMany(ProblemText::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(ProblemMedia::class)->orderBy('sort_order')->orderBy('id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'problem_tags');
    }

    public function gradeLevels(): BelongsToMany
    {
        return $this->belongsToMany(GradeLevel::class, 'problem_grade_levels')
            ->withPivot('is_primary')
            ->withTimestamps()
            ->orderBy('grade_number');
    }

    public function ladderSteps(): HasMany
    {
        return $this->hasMany(ProblemLadderStep::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(UserProblemProgress::class);
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    public function getSourceCompactAttribute(): ?string
    {
        $separator = ' '."\u{00B7}".' ';
        $parts = array_filter([
            $this->source_name,
            $this->source_year,
            $this->source_grade ? 'Grade '.$this->source_grade : null,
            $this->source_problem_number ? 'Problem '.$this->source_problem_number : null,
        ]);

        return $parts === [] ? null : implode($separator, $parts);
    }
}
