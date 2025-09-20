<?php

namespace Modules\Dealers\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Owner extends Model
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
        'owner_position',
        'nic_copy'
    ];

    public function shops()
    {
        return $this->hasMany(Shop::class);
    }

    protected static function newFactory()
    {
        return \Modules\Dealers\Database\factories\OwnerFactory::new();
    }
}
