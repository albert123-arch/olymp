<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProblemText extends Model
{
    protected $table = 'problem_texts';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'problem_id',
        'lang',
        'title',
        'statement_html',
        'hint_html',
        'solution_html',
        'teacher_note_html',
    ];

    public function problem(): BelongsTo
    {
        return $this->belongsTo(Problem::class);
    }
}
