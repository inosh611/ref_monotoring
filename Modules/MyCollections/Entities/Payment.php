<?php

namespace Modules\MyCollections\Entities;

use App\Models\User;
use Modules\Orders\Entities\Order;
use Illuminate\Database\Eloquent\Model;
use Modules\MyCollections\Entities\Cash;
use Modules\MyCollections\Entities\Cheque;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'order_id',
        'user_id',
        'collection_type',
        'paid_amount',
        'paid_amount_text',
        'comment',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function cash()
    {
        return $this->hasOne(Cash::class);
    }

    public function cheque()
    {
        return $this->hasOne(Cheque::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    protected static function newFactory()
    {
        return \Modules\MyCollections\Database\factories\PaymentFactory::new();
    }
}
