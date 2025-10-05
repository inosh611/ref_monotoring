<?php
namespace Modules\MyCollections\Repositories;
use App\Traits\ApiCrudTrait;
use Modules\MyCollections\Entities\Payment;
use Modules\MyCollections\Repositories\Interfaces\PaymentRepositoryInterface;

class PaymentRepository implements PaymentRepositoryInterface{
    use ApiCrudTrait;

    protected $model;

    public function __construct(Payment $payment)
    {
        $this->model = $payment;
    }

    public function allData()
    {
        return $this->model->query();
    }
    
}
?>