<?php

namespace Modules\Orders\Repositories;

use App\Traits\ApiCrudTrait;
use Modules\Orders\Entities\Item;
use Modules\Orders\Repositories\Interfaces\ItemRepositoryInterface;


class ItemRepository implements ItemRepositoryInterface
{
    use ApiCrudTrait;

    protected $model;

    public function __construct(Item $Item)
    {
        $this->model = $Item;
    }
    
}
