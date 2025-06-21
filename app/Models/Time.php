<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Time extends Model
{
    protected $table = 'dim_time';
    protected $fillable = ['date', 'month_number', 'month_name', 'year'];

    public function sales()
    {
        return $this->hasMany(Sale::class, 'time_id');
    }
}
