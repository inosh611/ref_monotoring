<?php

namespace Modules\MyVisiting\Repositories;

use App\Traits\ApiCrudTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Modules\MyVisiting\Entities\MyVisiting;
use Modules\MyVisiting\Repositories\Interfaces\MyVisitingRepositoryInterface;

class MyVisitingRepository implements MyVisitingRepositoryInterface
{
    use ApiCrudTrait;

    protected $model;

    public function __construct(MyVisiting $myVisiting)
    {
        $this->model = $myVisiting;
    }

     public function dataTable(Request $request)
    {
        $query = $this->model->newQuery()->where('ref_id', $user_id ?? auth()->user()->id)->with('dealer');

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
    
    public function getTodayVisiting($user_id)
    {
        $today = Carbon::today()->toDateString();

        $model = $this->model
            ->where('ref_id', $user_id)
            ->whereDate('date', $today)
            ->with('dealer')
            ->get();

        return $model;
    }
    

    public function updateCheckOut($user_id, $dealer_id, array $data)
    {
        $today = Carbon::today()->toDateString();

        $model = $this->model
            ->where('ref_id', $user_id)
            ->where('dealer_id', $dealer_id)
            ->whereDate('date', $today)
            ->firstOrFail();

       $checkOut =  $model->update($data);
       return $checkOut;
    }
}
