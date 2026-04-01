<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModuleInformationField extends Model
{
    protected $table = 'module_information_field';

    protected $fillable = [
        'key',
        'title',
        'placeholder',
        'maxlength',
        'sort_order',
        'is_active',
    ];

    public function answers()
    {
        return $this->hasMany(ModuleInformationAnswer::class, 'module_information_field_id');
    }
}
