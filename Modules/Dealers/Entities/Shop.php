<?php

namespace Modules\Dealers\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'business_address',
        'business_tel',
        'registration_doc',
        'sign_application',
        'photo_of_the_shop',
        'owner_id',
        'business_name'
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    protected static function newFactory()
    {
        return \Modules\Dealers\Database\factories\ShopFactory::new();
    }
}
