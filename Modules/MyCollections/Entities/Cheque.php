<?php

namespace Modules\MyCollections\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cheque extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'payment_id',
        'cheque_number',
        'bank',
        'branch',
        'cheque_date',
        'cheque_amount',
        'cheque_amount_text',
        'cheque_type',
        'receipt_number'
    ];
    
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
    protected static function newFactory()
    {
        return \Modules\MyCollections\Database\factories\ChequeFactory::new();
    }
}
