<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['product', 'manufacturing_price', 'sale_price'];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
