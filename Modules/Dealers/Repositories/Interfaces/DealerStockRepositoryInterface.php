<?php
namespace Modules\Dealers\Repositories\Interfaces;
use Illuminate\Http\Request;

interface DealerStockRepositoryInterface{

    public function dataTable(Request $request);
    public function find($id, $shop_id);
    public function create(array $data);
    public function update(array $data);
    public function delete($id);
    public function allData();
}
?>