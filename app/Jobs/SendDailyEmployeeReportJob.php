<?php

namespace App\Jobs;

use App\Mail\DailyEmployeeReportMail;
use App\Models\User;
use App\Services\DashboardReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SendDailyEmployeeReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $userId) {}

    public function handle(DashboardReportService $service): void
    {
        $user = User::select('id', 'first_name', 'last_name', 'reg_number')->findOrFail($this->userId);
        $employeeName = trim($user->first_name . ' ' . $user->last_name);
        $date = Carbon::today()->format('Y-m-d');
        $companyName = config('app.name', 'Your Company');
        $companyLogoPath = public_path('images/Admin-panel/panel-logo.png');

        // ✅ Get dashboard arrays from DB (not from Vue props)
        $todayVisiting = $service->todayVisiting($user->id);
        $confirmedOrders = $service->confirmedOrders($user->id);
        $todayExpectedCollections = $service->todayExpectedCollections($user->id);
        $todayOrders = $service->todayOrders($user->id);
        $todayCollections = $service->todayCollections($user->id);

        $pendingTotal = collect($todayExpectedCollections)->sum(fn($x) => (float)($x['balance'] ?? 0));


        $pdf = Pdf::loadView('pdf.employee_dashboard_report', [
            'companyName' => $companyName,
            'companyLogoPath' => $companyLogoPath,
            'todayOrders' => $todayOrders,                 
            'todayCollections' => $todayCollections,       
            'employeeName' => "{$employeeName} ({$user->reg_number})",
            'date' => $date,
            'todayVisiting' => $todayVisiting,
            'confirmedOrders' => $confirmedOrders,
            'todayExpectedCollections' => $todayExpectedCollections,
            'pendingTotal' => $pendingTotal,
        ])->setPaper('a4', 'portrait');
       

        $fileName = "Daily_Report_{$user->id}_{$date}.pdf";
        Storage::disk('local')->put("reports/{$fileName}", $pdf->output());

        $pdfPath = storage_path("app/reports/{$fileName}");

        $adminEmail = config('reports.admin_email');

        Mail::to($adminEmail)->send(new DailyEmployeeReportMail(
            "{$employeeName} ({$user->reg_number})",
            $date,
            $pdfPath
        ));
    }
}
