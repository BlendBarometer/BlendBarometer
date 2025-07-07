<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    public $timestamps = false;

    protected $table = "question";

    protected $fillable = [
        'id',
        'question_category_id',
        'sub_category_id',
        'text',
        'label',
    ];

    public function questionCategory()
    {
        return $this->belongsTo(Question_category::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(Sub_category::class);
    }
}