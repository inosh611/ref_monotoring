<?php
namespace Modules\Dealers\Repositories;


use App\Traits\ApiCrudTrait;
use Modules\Dealers\Entities\Owner;
use Modules\Dealers\Repositories\Interfaces\OwnerRepositoryInterface;

class OwnerRepository implements OwnerRepositoryInterface{
    use ApiCrudTrait;

    protected $model;

    public function __construct(Owner $dealer)
    {
        $this->model = $dealer;
    }

    public function allData()
    {
        return $this->model->query();
    }
    
}
?>