<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModuleInformationAnswer extends Model
{
    protected $table = 'module_information_answer';

    protected $fillable = [
        'user_id',
        'module_information_field_id',
        'answer',
    ];

    public function field()
    {
        return $this->belongsTo(ModuleInformationField::class, 'module_information_field_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
