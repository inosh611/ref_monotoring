<?php
namespace Modules\MyCollections\Repositories;
use App\Traits\ApiCrudTrait;
use Modules\MyCollections\Entities\Cheque;
use Modules\MyCollections\Repositories\Interfaces\ChequeRepositoryInterface;

class ChequeRepository implements ChequeRepositoryInterface{
    use ApiCrudTrait;

    protected $model;

    public function __construct(Cheque $cheque)
    {
        $this->model = $cheque;
    }

    public function allData()
    {
        return $this->model->query();
    }
    
}
?>