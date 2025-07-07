<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GraphDescription extends Model
{
    protected $table = 'graph_description';
    public $timestamps = false;

    protected $fillable = [
        'graph_type',
        'sub_category_id',
        'description',
    ];
}
