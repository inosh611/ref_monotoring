<?php
namespace Modules\Chat\Repositories;

use App\Models\User;
use App\Traits\ApiCrudTrait;
use Modules\Customers\Entities\Customer;

use Modules\Customers\Repositories\Interfaces\CustomerRepositoryInterface;

class ChatRepository implements CustomerRepositoryInterface{
    use ApiCrudTrait;

    protected $model;

    public function __construct(Customer $customer)
    {
        $this->model = $customer;
    }

    public function allData()
    {
        return $this->model->query();
    }
    
}
?>