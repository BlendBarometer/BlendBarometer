<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sub_category extends Model
{
    protected $table = "sub_category";

    public $timestamps = false;

    protected $fillable = [
        'id',
        'question_category_id',
        'name',
    ];

    public function questionCategory()
    {
        return $this->belongsTo(Question_category::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
