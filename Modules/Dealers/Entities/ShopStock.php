<?php

namespace Modules\Dealers\Entities;

use App\Models\User;
use Modules\Orders\Entities\Item;
use Modules\Orders\Entities\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShopStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'shop_id',
        'item_id',
        'user_id',
        'order_id',
        'quantity',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
    
    protected static function newFactory()
    {
        return \Modules\Dealers\Database\factories\ShopStockFactory::new();
    }
}
