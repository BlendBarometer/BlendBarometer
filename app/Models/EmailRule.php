<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class EmailRule extends Model
{
    protected $fillable = ['academy_name', 'email'];
    protected $casts    = ['academy_name' => 'string', 'email' => 'string'];

    public function academy()
    {
        return $this->belongsTo(Academy::class, 'academy_name', 'name');
    }
}
