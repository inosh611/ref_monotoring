<?php

namespace Modules\MyCollections\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cash extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'cash_amount',
        'cash_amount_text',
        'cash_receipt_number'
    ];
    
    protected static function newFactory()
    {
        return \Modules\MyCollections\Database\factories\CashFactory::new();
    }
}
