<?php

namespace Modules\Orders\Repositories;

use App\Traits\ApiCrudTrait;
use Illuminate\Http\Request;

use Modules\Orders\Entities\Order;
use Modules\Orders\Repositories\Interfaces\OrderRepositoryInterface;

class OrderRepository implements OrderRepositoryInterface
{
    use ApiCrudTrait;

    protected $model;

    public function __construct(Order $dealer)
    {
        $this->model = $dealer;
    }
    public function orderCount(){
        $count = $this->model->count();
        return $count;
    }
    public function dataTable(Request $request)
    {
        $query = $this->model->newQuery()->with('shop', 'user', 'items');

        if ($request) {
            $pageSize = $request->input('page.pagesize', 10);
            $search = $request->input('page.search', '');
            $sortColumn = $request->input('page.sort_column', 'id');
            $sortDirection = $request->input('page.sort_direction', 'asc');

            if (!empty($search)) {
                $query->where('name', 'like', "%{$search}%");
            }

            $query->orderBy($sortColumn, $sortDirection);

            $results = $query->paginate($pageSize, ['*'], 'page', $request->input('page.current_page', 1));

            $start  = $results->firstItem(); // 1-based; null if empty
            $rows = collect($results->items())->values()->map(function ($row, $i) use ($start) {
                $row->row_num = ($start ?? 0) + $i; // 1,2,3... per page'
                if ($row->created_at) {
                $row->created_at_formatted = \Carbon\Carbon::parse($row->created_at)->format('Y-F-d');
                $row->business_name = $row->shop ? $row->shop->business_name : null;
    }
                return $row;
            });
            return [
                'data' => $rows,
                'total' => $results->total(),
                'current_page' => $results->currentPage(),
                'last_page' => $results->lastPage(),
            ];
        }

        return $query->get();
    }
}
