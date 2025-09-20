<?php
namespace Modules\Dealers\Repositories\Interfaces;
use Illuminate\Http\Request;

interface DealerRepositoryInterface{

    public function dataTable(Request $request);
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function allData();
}
?>