<?php

namespace Modules\Product\Entities;

use Modules\Product\Entities\Unit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'unit_id',
        'product_name',
        'price'
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    public function productPrice()
    {
        return $this->hasMany(ProductPrice::class, 'product_id', 'id');
    }
     public function latestPrice()
    {
        return $this->hasOne(ProductPrice::class)->latestOfMany('created_at');

    }
    
    protected static function newFactory()
    {
        return \Modules\Product\Database\factories\ProductFactory::new();
    }
}
