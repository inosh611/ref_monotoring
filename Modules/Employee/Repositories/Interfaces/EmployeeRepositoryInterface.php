<?php
namespace Modules\Employee\Repositories\Interfaces;
use Illuminate\Http\Request;

interface EmployeeRepositoryInterface{

    public function dataTable(Request $request);
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function allData();
}
?>