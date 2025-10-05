<?php

namespace Modules\MyCollections\Entities;

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
        'collection_type',
        'paid_amount',
        'cheque_id',
        'cash_id',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function cheque()
    {
        return $this->belongsTo(Cheque::class);
    }

    public function cash()
    {
        return $this->belongsTo(Cash::class);
    }
    
    protected static function newFactory()
    {
        return \Modules\MyCollections\Database\factories\PaymentFactory::new();
    }
}
