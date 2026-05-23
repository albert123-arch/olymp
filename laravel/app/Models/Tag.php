<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tag extends Model
{
    protected $table = 'tags';

    public const UPDATED_AT = null;

    protected $fillable = [
        'slug',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function texts(): HasMany
    {
        return $this->hasMany(TagText::class);
    }

    public function problems(): BelongsToMany
    {
        return $this->belongsToMany(Problem::class, 'problem_tags');
    }
}
