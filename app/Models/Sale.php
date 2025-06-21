<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $table = 'fact_sales';
    protected $fillable = [
        'segment_id', 'country_id', 'product_id', 'discount_band_id', 'time_id',
        'units_sold', 'gross_sales', 'discounts', 'sales', 'cogs', 'profit'
    ];

    public function segment()
    {
        return $this->belongsTo(Segment::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function discountBand()
    {
        return $this->belongsTo(DiscountBand::class, 'discount_band_id');
    }

    public function time()
    {
        return $this->belongsTo(Time::class);
    }
}
