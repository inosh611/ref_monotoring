<?php

namespace Modules\Product\Repositories;

use App\Traits\ApiCrudTrait;
use Modules\Product\Entities\Unit;
use Modules\Product\Repositories\Interfaces\UnitRepositoryInterface;

class UnitRepository implements UnitRepositoryInterface
{
    use ApiCrudTrait;

    protected $model;

    public function __construct(Unit $dealer)
    {
        $this->model = $dealer;
    }
    
}
