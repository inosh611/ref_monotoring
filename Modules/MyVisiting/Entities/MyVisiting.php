<?php

namespace Modules\MyVisiting\Entities;

use App\Models\User;
use Modules\Dealers\Entities\Shop;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MyVisiting extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'dealer_id',
        'ref_id',
        'photo_of_shop',
        'time',
        'date',
        'location',
        'checkout_time',
        'checkout_date',
    ];
    
    public function dealer()
    {
        return $this->belongsTo(Shop::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    protected static function newFactory()
    {
        return \Modules\MyVisiting\Database\factories\MyVisitingFactory::new();
    }
}
