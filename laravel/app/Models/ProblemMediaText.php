<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProblemMediaText extends Model
{
    protected $table = 'problem_media_texts';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'media_id',
        'lang',
        'caption_html',
        'alt_text',
    ];

    public function media(): BelongsTo
    {
        return $this->belongsTo(ProblemMedia::class, 'media_id');
    }
}
