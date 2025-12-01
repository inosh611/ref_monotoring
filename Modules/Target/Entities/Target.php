<?php

namespace Modules\Target\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Target extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'employee_reg_no',
        'user_id',
        'month',
        'year',
        'target_value',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    protected static function newFactory()
    {
        return \Modules\Target\Database\factories\TargetFactory::new();
    }
}
