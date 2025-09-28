<?php

namespace Modules\Product\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'unit_name',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Product\Database\factories\UnitFactory::new();
    }
}
