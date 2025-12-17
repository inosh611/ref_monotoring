<?php

namespace Modules\Report\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\MyVisiting\Entities\MyVisiting;
use Illuminate\Contracts\Support\Renderable;

class ReportController extends Controller
{
    public function index()
    {
        return Inertia::render('Modules/Report/RepotIndex');
    }

    public function customerWisedVisitsReport(Request $request)
    {
        $query = MyVisiting::with(['dealer', 'user']);

        if ($request->selected_dealer && $request->selected_dealer !== 'all') {
            $query->where('dealer_id', $request->selected_dealer);
        }

        if ($request->selected_employee && $request->selected_employee !== 'all') {
            $query->where('user_id', $request->selected_employee);
        }

        if ($request->selected_status === 'visited') {
            $query->whereNotNull('checkout_time');
        }

        if ($request->selected_status === 'none-visited') {
            $query->whereNull('checkout_time');
        }

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('date', [
                $request->start_date,
                $request->end_date
            ]);
        } elseif ($request->start_date) {
            $query->whereDate('date', '>=', $request->start_date);
        } elseif ($request->end_date) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        if ($request->search) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('location', 'like', "%{$search}%")
                    ->orWhere('ref_id', 'like', "%{$search}%")
                    ->orWhereHas('dealer', function ($dq) use ($search) {
                        $dq->where('business_name', 'like', "%{$search}%")
                            ->orWhere('business_address', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('contact_number', 'like', "%{$search}%");
                    });
            });
        }

        $allowedSortColumns = ['id', 'date', 'time', 'checkout_time'];

        $sortColumn = in_array($request->sort_column, $allowedSortColumns)
            ? $request->sort_column
            : 'id';

        $sortDirection = $request->sort_direction === 'desc' ? 'desc' : 'asc';

        $query->orderBy($sortColumn, $sortDirection);

        $perPage = $request->per_page ?? 10;

        $results = $query->paginate($perPage);

        return response()->json([
            'data' => $results->items(),
            'total' => $results->total(),
        ]);
    }
}
