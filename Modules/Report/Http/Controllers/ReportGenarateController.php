<?php

namespace Modules\Report\Http\Controllers;

use App\Models\User;
use App\Exports\OrderExport;
use App\Exports\StockExport;
use Illuminate\Http\Request;
use App\Exports\CollectionExport;
use Illuminate\Routing\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CustomerWisedVisitsExport;
use Illuminate\Contracts\Support\Renderable;

class ReportGenarateController extends Controller
{
    public function exportCustomerWisedVisits(Request $request)
    {
        $fileName = 'Customer_Wised_Visits_Report_' . now()->format('Y_m_d_His') . '.xlsx';

        return Excel::download(
            new CustomerWisedVisitsExport($request),
            $fileName
        );
    }

    public function exportOrder(Request $request)
    {
        $fileName = 'Order_Report_' . now()->format('Y_m_d_His') . '.xlsx';

        return Excel::download(
            new OrderExport($request),
            $fileName
        );
    }

    public function exportStock(Request $request)
    {
        $fileName = 'Stock_Report_' . now()->format('Y_m_d_His') . '.xlsx';

        return Excel::download(
            new StockExport($request),
            $fileName
        );
    }
    public function exportCollectionReport(Request $request)
{
    $filters = $request->all();

    // Add employee info to title (optional but requested)
    if ($request->selected_employee && $request->selected_employee !== 'all') {
        $user = User::select('first_name','last_name','reg_number')->find($request->selected_employee);

        if ($user) {
            $filters['employee_name'] =
                trim($user->first_name . ' ' . $user->last_name);

            $filters['employee_reg'] = $user->reg_number;
        }
    } else {
        $filters['employee_name'] = 'All Employees';
        $filters['employee_reg'] = '';
    }

    return Excel::download(new CollectionExport($filters), 'Collection_Report.xlsx');
}
}
