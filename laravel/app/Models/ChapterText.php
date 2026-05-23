<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChapterText extends Model
{
    protected $table = 'chapter_texts';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'chapter_id',
        'lang',
        'title',
        'description_html',
        'theory_html',
        'examples_html',
        'worksheet_html',
        'teacher_notes_html',
    ];

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }
}
