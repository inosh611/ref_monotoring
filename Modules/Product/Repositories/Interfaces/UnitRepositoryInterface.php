<?php
namespace Modules\Product\Repositories\Interfaces;
use Illuminate\Http\Request;

interface UnitRepositoryInterface{

    public function dataTable(Request $request);
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function allData();
}
?>