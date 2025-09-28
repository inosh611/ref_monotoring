<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait ApiCrudTrait
{
    public function dataTable(Request $request)
    {
        $query = $this->model->newQuery();

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

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $model = $this->model->findOrFail($id);
        $model->update($data);
        return $model;
    }

    public function delete($id)
    {
        return $this->model->findOrFail($id)->delete();
    }

    public function allData()
    {
        return $this->model->query()->get();
    }
}
