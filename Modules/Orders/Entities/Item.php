<?php

namespace Modules\Orders\Entities;

use Modules\Product\Entities\Product;
use Illuminate\Database\Eloquent\Model;
use Modules\Product\Entities\ProductPrice;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'price_id',
        'product_id',
        'quantity',
        'price',
        'sub_total',
    ];
    
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productPrice()
    {
        return $this->belongsTo(ProductPrice::class, 'price_id', 'id');
    }
    protected static function newFactory()
    {
        return \Modules\Orders\Database\factories\ItemFactory::new();
    }
}
