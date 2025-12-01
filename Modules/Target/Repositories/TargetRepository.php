<?php

namespace Modules\Target\Repositories;

use App\Traits\ApiCrudTrait;


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
}
