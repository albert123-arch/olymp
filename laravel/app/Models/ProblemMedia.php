<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProblemMedia extends Model
{
    protected $table = 'problem_media';

    public const UPDATED_AT = null;

    protected $fillable = [
        'problem_id',
        'role',
        'lang',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'sort_order',
        'is_published',
        'created_at',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'sort_order' => 'integer',
        'is_published' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function problem(): BelongsTo
    {
        return $this->belongsTo(Problem::class);
    }

    public function texts(): HasMany
    {
        return $this->hasMany(ProblemMediaText::class, 'media_id');
    }
}
