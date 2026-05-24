<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Language extends Model
{
    protected $table = 'languages';

    public $timestamps = false;

    protected $fillable = [
        'code',
        'title',
        'is_default',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function problemLadderTexts(): HasMany
    {
        return $this->hasMany(ProblemLadderText::class, 'language_id');
    }
}
