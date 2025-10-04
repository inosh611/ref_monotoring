<?php

namespace Modules\Orders\Entities;

use App\Models\User;
use Illuminate\Support\Str;
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
    
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function owner()
    {
        return $this->hasOneThrough(
            'Modules\Dealers\Entities\Owner',
            'Modules\Dealers\Entities\Shop',
            'id', // Foreign key on shops table
            'id', // Foreign key on owners table
            'shop_id', // Local key on orders table
            'owner_id' // Local key on shops table
        );
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->order_number = self::generateOrderNumber();
        });
    }
    public static function generateOrderNumber()
    {
        // Example: ORD-20251004-0001
        $prefix = 'ORD-' . now()->format('Ymd');
        $lastOrder = self::whereDate('created_at', today())->latest('id')->first();
        
        $nextNumber = $lastOrder
            ? ((int) Str::afterLast($lastOrder->order_number, '-') + 1)
            : 1;

        return $prefix . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
    
    protected static function newFactory()
    {
        return \Modules\Orders\Database\factories\OrderFactory::new();
    }
}
