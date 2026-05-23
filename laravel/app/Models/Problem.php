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
    ];

    protected $casts = [
        'book_number' => 'integer',
        'difficulty' => 'integer',
        'sort_order' => 'integer',
        'is_published' => 'boolean',
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
}
