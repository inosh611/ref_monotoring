<?php

namespace Modules\Report\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Exports\CustomerWisedVisitsExport;
use App\Exports\OrderExport;
use Maatwebsite\Excel\Facades\Excel;

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
}
