<?php
namespace Modules\Orders\Repositories\Interfaces;
use Illuminate\Http\Request;

interface OrderRepositoryInterface{

    public function dataTable(Request $request);
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function allData();
    public function orderCount();
    public function orderFind($orderKey,  $dealerId);
    public function todayExpectedOrders();
}
?>