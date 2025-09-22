<?php

namespace Modules\MyVisiting\Repositories;

use App\Traits\ApiCrudTrait;
use Illuminate\Support\Carbon;
use Modules\MyVisiting\Entities\MyVisiting;
use Modules\MyVisiting\Repositories\Interfaces\MyVisitingRepositoryInterface;

class MyVisitingRepository implements MyVisitingRepositoryInterface
{
    use ApiCrudTrait;

    protected $model;

    public function __construct(MyVisiting $dealer)
    {
        $this->model = $dealer;
    }
    public function updateCheckOut($user_id, $dealer_id, array $data)
    {
        $today = Carbon::today()->toDateString();

        $model = $this->model
            ->where('ref_id', $user_id)
            ->where('dealer_id', $dealer_id)
            ->whereDate('date', $today)
            ->firstOrFail();

       $checkOut =  $model->update($data);
       return $checkOut;
    }
}
