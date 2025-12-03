<?php

namespace Modules\Target\Repositories;

use App\Traits\ApiCrudTrait;
use Illuminate\Support\Facades\Auth;
use Modules\Target\Entities\Target;
use Modules\Target\Repositories\Interfaces\TargetRepositoryInterface;

class TargetRepository implements TargetRepositoryInterface
{
    use ApiCrudTrait;

    protected $model;

    public function __construct(Target $target)
    {
        $this->model = $target;
    }
    public function getEmployeeCurrentMonthTarget()
    {
        $currentMonth = date('m');
        $currentYear = date('Y');
        
        return $this->model->where('user_id', Auth::user()->id)
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->select('target_value', 'achieved_value')
            ->first();
    }
}
