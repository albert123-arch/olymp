<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProblemTag extends Model
{
    protected $table = 'problem_tags';

    public $incrementing = false;

    public $timestamps = false;

    protected $primaryKey = null;

    protected $fillable = [
        'problem_id',
        'tag_id',
    ];
}
