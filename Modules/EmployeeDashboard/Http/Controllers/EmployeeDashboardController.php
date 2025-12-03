<?php

namespace Modules\EmployeeDashboard\Http\Controllers;

use DB;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\MyCollections\Entities\Payment;
use Illuminate\Contracts\Support\Renderable;
use Modules\Target\Repositories\Interfaces\TargetRepositoryInterface;
use Modules\MyCollections\Repositories\Interfaces\PaymentRepositoryInterface;
use Modules\MyVisiting\Repositories\Interfaces\MyVisitingRepositoryInterface;
use Modules\Orders\Repositories\Interfaces\OrderRepositoryInterface;

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

        return Inertia::render("Modules/AdminDashBoard/EmployeeDashboard", [
            'todayVisiting' => $todayVisiting,
            'employeeTarget' => $employeeTarget,
            'todayCollections' => $todayCollections,
            'weeklyVisits'       => $visitArray,
            'weeklyCollections'  => $collectionArray,
            'confirmedOrders' => $confirmedOrders,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('employeedashboard::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('employeedashboard::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('employeedashboard::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
