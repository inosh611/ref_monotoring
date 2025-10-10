<?php
namespace Modules\MyCollections\Repositories;


use App\Traits\ApiCrudTrait;
use Modules\MyCollections\Entities\Cash;
use Modules\MyCollections\Repositories\Interfaces\CashRepositoryInterface;


class CashRepository implements CashRepositoryInterface{
    use ApiCrudTrait;

    protected $model;

    public function __construct(Cash $cash)
    {
        $this->model = $cash;
    }

    public function allData()
    {
        return $this->model->query();
    }
    
}
?>