<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question_category extends Model
{
    protected $table = "question_category";

    public $timestamps = false;

    protected $fillable = [
        'id',
        'form_section_id',
        'name',
        'description',
    ];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function formSection()
    {
        return $this->belongsTo(Form_section::class);
    }
}
