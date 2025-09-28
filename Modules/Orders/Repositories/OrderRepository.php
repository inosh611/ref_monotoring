<?php

namespace Modules\Orders\Repositories;

use App\Traits\ApiCrudTrait;
use Illuminate\Support\Carbon;
use Modules\MyVisiting\Entities\MyVisiting;
use Modules\Orders\Repositories\Interfaces\OrderRepositoryInterface;

class OrderRepository implements OrderRepositoryInterface
{
    use ApiCrudTrait;

    protected $model;

    public function __construct(MyVisiting $dealer)
    {
        $this->model = $dealer;
    }
    
}
