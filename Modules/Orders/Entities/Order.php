<?php

namespace Modules\Orders\Entities;

use Modules\Dealers\Entities\Shop;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'shop_id',
        'user_id',
        'order_status',
        'payment_status',
        'total_price'
    ];
    
        public function items()
        {
            return $this->hasMany(Item::class);
        }
        public function shop()
        {
            return $this->belongsTo(Shop::class);
        }
        public function owner()
        {
            return $this->hasOneThrough(
                'Modules\Dealers\Entities\Owner',
                'Modules\Dealers\Entities\Shop',
                'id', // Foreign key on shops table...
                'id', // Foreign key on owners table...
                'shop_id', // Local key on orders table...
                'owner_id' // Local key on shops table...
            );
        }
    protected static function newFactory()
    {
        return \Modules\Orders\Database\factories\OrderFactory::new();
    }
}
