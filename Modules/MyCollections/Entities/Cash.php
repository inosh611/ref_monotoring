<?php

namespace Modules\MyCollections\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cash extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'payment_id',
        'cash_amount',
        'cash_amount_text',
        'cash_receipt_number'
    ];
    
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

     /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \Modules\MyCollections\Database\factories\CashFactory::new();
    }
}
