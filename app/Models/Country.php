<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = ['country'];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
