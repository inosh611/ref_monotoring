<?php

namespace Modules\Orders\Repositories;

use App\Traits\ApiCrudTrait;
use Modules\Orders\Entities\Order;
use Modules\Orders\Repositories\Interfaces\OrderRepositoryInterface;

class OrderRepository implements OrderRepositoryInterface
{
    use ApiCrudTrait;

    protected $model;

    public function __construct(Order $dealer)
    {
        $this->model = $dealer;
    }
    public function orderCount(){
        $count = $this->model->count();
        return $count;
    }
}
