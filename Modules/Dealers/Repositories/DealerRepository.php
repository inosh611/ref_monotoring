<?php
namespace Modules\Dealers\Repositories;


use App\Traits\ApiCrudTrait;
use Modules\Dealers\Entities\Dealer;

use Modules\Dealers\Repositories\Interfaces\DealerRepositoryInterface;

class DealerRepository implements DealerRepositoryInterface{
    use ApiCrudTrait;

    protected $model;

    public function __construct(Dealer $dealer)
    {
        $this->model = $dealer;
    }

    public function allData()
    {
        return $this->model->query();
    }
    
}
?>