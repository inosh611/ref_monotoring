<?php

namespace Modules\MyCollections\Repositories;

use Carbon\Carbon;
use App\Traits\ApiCrudTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\MyCollections\Entities\Payment;
use Modules\MyCollections\Repositories\Interfaces\PaymentRepositoryInterface;


class PaymentRepository implements PaymentRepositoryInterface
{
    use ApiCrudTrait;

    protected $model;

    public function __construct(Payment $payment)
    {
        $this->model = $payment;
    }

    public function allData()
    {
        return $this->model->query();
    }

    public function dataTable(Request $request)
    {
        $query = $this->model->newQuery()->with(['user:id,first_name,last_name', 'order:id,order_number'])->orderByDesc('id');;

        if ($request) {
            $pageSize = $request->input('page.pagesize', 10);
            $search = $request->input('search');
            $sortColumn = $request->input('page.sort_column', 'id');
            $sortDirection = $request->input('page.sort_direction', 'asc');

            if (!empty($search)) {
                $orderID = findOrderId($search);
                if ($orderID === null) {
                    $orderID = 0;
                }
                $query->where('order_id', 'like', "%{$orderID}%");
            }

            $query->orderBy($sortColumn, $sortDirection);

            $results = $query->paginate($pageSize, ['*'], 'page', $request->input('page.current_page', 1));

            $start  = $results->firstItem();
            $rows = collect($results->items())->values()->map(function ($row, $i) use ($start) {
                $row->row_num = ($start ?? 0) + $i;

                // display value
                $pretty = Carbon::parse($row->created_at)
                    ->timezone('Asia/Colombo')       // set yours
                    ->format('Y-M-d g.i a');         // 2025-Jan-10 5.25 pm
                $row->date = strtolower($pretty); // -> 2025-jan-10 5.25 pm

                // keep a raw copy if you need correct sorting by date on the frontend
                $row->created_at_raw = Carbon::parse($row->getOriginal('created_at'))->toIso8601String();

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

    public function todayTotalCollections()
    {
        $today = Carbon::now()->startOfDay();
        $totalCollections = $this->model->where('created_at', '>=', $today)->where('user_id', Auth::user()->id)->sum('paid_amount');
        return $totalCollections;
    }
}
