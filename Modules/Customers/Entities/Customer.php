<?php

namespace Modules\Customers\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'first_name',
        'last_name',
        'nic',
        'contact_number',
        'address',
        'email',
        'registration_doc',
        'sign_application',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Customers\Database\factories\CustomerFactory::new();
    }
}
