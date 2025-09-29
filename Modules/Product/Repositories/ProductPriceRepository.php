<?php

namespace Modules\Product\Repositories;

use App\Traits\ApiCrudTrait;
use Illuminate\Http\Request;

use Modules\Product\Entities\ProductPrice;
use Modules\Product\Repositories\Interfaces\ProductPriceRepositoryInterface;


class ProductPriceRepository implements ProductPriceRepositoryInterface
{
    use ApiCrudTrait;

    protected $model;

    public function __construct(ProductPrice $productPrice)
    {
        $this->model = $productPrice;
    }
    
    public function dataTable(Request $request)
    {
        $query = $this->model->newQuery()->with('product');

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
