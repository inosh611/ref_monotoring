<?php

namespace Modules\Report\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Jobs\SendDailyEmployeeReportJob;
use Illuminate\Contracts\Support\Renderable;

class ReportSendController extends Controller
{
    public function sendDaily(Request $request)
    {
        $userId = auth()->id();

        // ✅ dispatch queued job
        SendDailyEmployeeReportJob::dispatch($userId);

        return response()->json([
            'success' => true,
            'message' => 'Report queued successfully'
        ]);

        return response()->json(['message' => 'Daily report email sent successfully.']);
    }
}
