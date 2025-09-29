<?php

namespace Modules\Product\Repositories;

use App\Traits\ApiCrudTrait;
use Modules\Product\Entities\Product;
use Illuminate\Http\Request;

use Modules\Product\Repositories\Interfaces\ProductRepositoryInterface;


class ProductRepository implements ProductRepositoryInterface
{
    use ApiCrudTrait;

    protected $model;

    public function __construct(Product $product)
    {
        $this->model = $product;
    }
    
    public function dataTable(Request $request)
    {
        $query = $this->model->newQuery()->with('unit', 'latestPrice');

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
                $row->row_num = ($start ?? 0) + $i; // 1,2,3... per page
                $row->latest_price = optional($row->latestPrice)->price;
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
