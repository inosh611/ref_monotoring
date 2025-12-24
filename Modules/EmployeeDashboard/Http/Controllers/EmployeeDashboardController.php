<?php

namespace Modules\EmployeeDashboard\Http\Controllers;

use DB;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Orders\Entities\Order;
use Modules\MyCollections\Entities\Payment;
use Illuminate\Contracts\Support\Renderable;
use Modules\Orders\Repositories\Interfaces\OrderRepositoryInterface;
use Modules\Target\Repositories\Interfaces\TargetRepositoryInterface;
use Modules\MyCollections\Repositories\Interfaces\PaymentRepositoryInterface;
use Modules\MyVisiting\Repositories\Interfaces\MyVisitingRepositoryInterface;

class EmployeeDashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    protected $myVisitingRepository;
    protected $targetRepository;
    protected $paymentRepository;
    protected $orderRepository;

    public function __construct(MyVisitingRepositoryInterface $my_visiting_repository, TargetRepositoryInterface $target_repository, PaymentRepositoryInterface $payment_repository, OrderRepositoryInterface $order_repository)
    {
        $this->myVisitingRepository = $my_visiting_repository;
        $this->targetRepository = $target_repository;
        $this->paymentRepository = $payment_repository;
        $this->orderRepository = $order_repository;
    }   

    public function index()
    {
        $startOfWeek = now()->startOfWeek(); // Monday
        $endOfWeek = now()->endOfWeek();     // Sunday
        $visitArray = [];
        $collectionArray = [];

        $weekDays = collect([
            'Mon',
            'Tue',
            'Wed',
            'Thu',
            'Fri',
            'Sat',
            'Sun'
        ]);

        $weeklyVisits = DB::table('my_visitings')
            ->selectRaw('date as date, COUNT(*) as visit_count')
            ->where('ref_id', auth()->id())
            ->whereBetween('date', [$startOfWeek, $endOfWeek])
            ->groupBy('date')
            ->pluck('visit_count', 'date');

        $weeklyCollections = \DB::table('payments')
            ->selectRaw('DATE(created_at) as date, SUM(paid_amount) as total')
            ->where('user_id', auth()->id())
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->groupBy('date')
            ->pluck('total', 'date');


        foreach (range(0, 6) as $i) {
            $date = $startOfWeek->copy()->addDays($i)->toDateString();

            $visitArray[] = $weeklyVisits[$date] ?? 0;
            $collectionArray[] = $weeklyCollections[$date] ?? 0;
        }


        $todayVisiting = $this->myVisitingRepository->getTodayVisiting(auth()->user()->id);
        $employeeTarget = $this->targetRepository->getEmployeeCurrentMonthTarget();
        $todayCollections = $this->paymentRepository->todayTotalCollections();
        $confirmedOrders = $this->orderRepository->todayExpectedOrders();
        $todayExpectedCollections = $this->todayExpectedCollections();

        return Inertia::render("Modules/AdminDashBoard/EmployeeDashboard", [
            'todayVisiting' => $todayVisiting,
            'employeeTarget' => $employeeTarget,
            'todayCollections' => $todayCollections,
            'weeklyVisits'       => $visitArray,
            'weeklyCollections'  => $collectionArray,
            'confirmedOrders' => $confirmedOrders,
            'todayExpectedCollections' => $todayExpectedCollections,
        ]);
    }

   private function todayExpectedCollections(){
         $userId = auth()->id();
    $today = Carbon::today()->toDateString();

    // ✅ Today Expected Collections for this employee
     $todayExpectedCollections = Order::with('shop')
        ->where('user_id', $userId)
        ->whereDate('expected_collection_date', '<=', $today)
        // ✅ Not completed collections (paid < total)
        ->where(function ($q) {
            $q->whereNull('paid_amount')
              ->orWhereColumn('paid_amount', '<', 'total_price');
        })
        ->orderBy('expected_collection_date')
        ->get()
        ->map(function ($o) use ($today) {
            $total = (float) ($o->total_price ?? 0);
            $paid  = (float) ($o->paid_amount ?? 0);
            $balance = max($total - $paid, 0);

            return [
                'id' => $o->id,
                'order_number' => $o->order_number,
                'dealer_name' => ($o->shop?->business_name ?? 'N/A') . ' - ' . ($o->shop?->business_address ?? ''),
                'telephone' => $o->shop?->business_tel ?? '-',
                'expected_date' => $o->expected_collection_date,
                'total_price' => $total,
                'paid_amount' => $paid,
                'balance' => $balance,
                'status' => 'Pending', // because paid < total
            ];
        });
        return $todayExpectedCollections;
   }
}
