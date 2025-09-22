<?php
namespace Modules\MyVisiting\Repositories\Interfaces;
use Illuminate\Http\Request;

interface MyVisitingRepositoryInterface{

    public function dataTable(Request $request);
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function allData();
    public function updateCheckOut($user_id, $dealer_id, array $data);
}
?>