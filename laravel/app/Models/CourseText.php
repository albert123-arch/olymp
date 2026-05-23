<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseText extends Model
{
    protected $table = 'course_texts';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'course_id',
        'lang',
        'title',
        'description_html',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
