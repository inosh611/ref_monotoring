<?php

namespace Modules\Report\Http\Controllers;

use App\Exports\OrderExport;
use App\Exports\StockExport;
use Illuminate\Http\Request;
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
}
