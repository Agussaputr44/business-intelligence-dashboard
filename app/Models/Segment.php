<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Segment extends Model
{
    protected $fillable = ['segment'];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
