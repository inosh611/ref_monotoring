<?php
namespace Modules\Dealers\Repositories;

use App\Traits\ApiCrudTrait;
use Modules\Dealers\Entities\ShopStock;
use Modules\Dealers\Repositories\Interfaces\DealerStockRepositoryInterface;


class DealerStockRepository implements DealerStockRepositoryInterface{
    use ApiCrudTrait;

    protected $model;

    public function __construct(ShopStock $dealer)
    {
        $this->model = $dealer;
    }

    public function allData()
    {
        return $this->model->query();
    }
}

?>