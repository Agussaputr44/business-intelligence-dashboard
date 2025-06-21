<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountBand extends Model
{
    protected $table = 'dim_discount_band';
    protected $fillable = ['discount_band'];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
