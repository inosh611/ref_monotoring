<?php

namespace Modules\Dealers\Repositories;


use App\Traits\ApiCrudTrait;
use Modules\Dealers\Entities\Shop;
use Modules\Dealers\Repositories\Interfaces\DealerRepositoryInterface;
use Illuminate\Http\Request;

class DealerRepository implements DealerRepositoryInterface
{
    use ApiCrudTrait;

    protected $model;

    public function __construct(Shop $dealer)
    {
        $this->model = $dealer;
    }

    public function dataTable(Request $request)
    {
        $query = $this->model->newQuery()->with('owner');

        if ($request) {
            $pageSize = $request->input('page.pagesize', 10);
            $search = $request->input('page.search', '');
            $sortColumn = $request->input('page.sort_column', 'id');
            $sortDirection = $request->input('page.sort_direction', 'asc');

            if (!empty($search)) {
                $shopColumns = $this->model->getFillable();

                $query->where(function ($q) use ($shopColumns, $search) {
                    // Search in shop table columns
                    foreach ($shopColumns as $column) {
                        $q->orWhere($column, 'like', "%{$search}%");
                    }

                    // Search in owner relationship (first_name, last_name, nic, etc.)
                    $q->orWhereHas('owner', function ($ownerQuery) use ($search) {
                        $ownerQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('nic', 'like', "%{$search}%")
                            ->orWhere('contact_number', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
                });
            }


            $query->orderBy($sortColumn, $sortDirection);

            $results = $query->paginate($pageSize, ['*'], 'page', $request->input('page.current_page', 1));
            
            return [
                'data' => $results->items(),
                'total' => $results->total(),
                'current_page' => $results->currentPage(),
                'last_page' => $results->lastPage(),
            ];
        }

        return $query->get();
    }
    public function find($id)
    {
        return $this->model->with('owner')->findOrFail($id);
    }
}
