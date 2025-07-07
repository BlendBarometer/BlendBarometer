<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module_level_answer extends Model
{
    protected $table = 'module_level_answer';

    public $timestamps = false;

    protected $fillable = [
        'answer',
        'description',
    ];
}
