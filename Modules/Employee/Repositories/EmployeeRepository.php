<?php
namespace Modules\Employee\Repositories;

use App\Models\User;
use App\Traits\ApiCrudTrait;
use Modules\People\Entities\Employee;
use Modules\Employee\Repositories\Interfaces\EmployeeRepositoryInterface;

class EmployeeRepository implements EmployeeRepositoryInterface{
    use ApiCrudTrait;

    protected $model;

    public function __construct(User $employee)
    {
        $this->model = $employee;
    }

    public function allData()
    {
        return $this->model->query();
    }
    
}
?>