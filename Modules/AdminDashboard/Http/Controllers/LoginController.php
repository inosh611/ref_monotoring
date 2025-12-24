<?php

namespace Modules\AdminDashboard\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Dealers\Entities\Shop;
use Modules\Orders\Entities\Order;
use Modules\MyCollections\Entities\Payment;
use Modules\MyVisiting\Entities\MyVisiting;
use Illuminate\Contracts\Support\Renderable;

class LoginController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function login()
    {
        $today = Carbon::today();

        // =========================
        // KPI: Total Dealers
        // =========================
        $totalDealers = Shop::query()->count();

        // =========================
        // KPI: Today's Visits (ALL employees)
        // =========================
        $todayVisits = MyVisiting::query()
            ->whereDate('date', $today->toDateString())
            ->count();

        // =========================
        // KPI: Today's Collection (ALL employees)
        // =========================
        $todayCollection = (float) Payment::query()
            ->whereDate('created_at', $today->toDateString())
            ->sum('paid_amount');

        // =========================
        // KPI: Pending Collections (Due today + overdue)
        // Definition: expected_collection_date <= today AND paid_amount < total_price
        // =========================
        $pendingCollections = Order::query()
            ->whereNotNull('expected_collection_date')
            ->whereDate('expected_collection_date', '<=', $today->toDateString())
            ->where(function ($q) {
                $q->whereNull('paid_amount')
                  ->orWhereColumn('paid_amount', '<', 'total_price');
            })
            ->count();

        // =========================
        // Table: Today Route Visits (ALL employees)
        // We calculate: for each visit, what amount this employee collected today from that shop
        // status:
        //   - if collected > 0 and has cheque payments => "Cheque Received"
        //   - if collected > 0 => "Completed"
        //   - else => "Pending Collection"
        // =========================
        $todayRouteVisits = $this->getTodayRouteVisits($today);

        // =========================
        // Pie Chart: Payment Split Today
        // =========================
        $paymentSplit = $this->getTodayPaymentSplit($today);

        // =========================
        // Bar Chart: Monthly Collection (last 6 months)
        // =========================
        $monthlyCollection = $this->getMonthlyCollectionLast6Months();

        return Inertia::render('Modules/AdminDashBoard/Dashboard', [
            'kpis' => [
                'totalDealers' => $totalDealers,
                'todayVisits' => $todayVisits,
                'todayCollection' => $todayCollection,
                'pendingCollections' => $pendingCollections,
            ],
            'todayRouteVisits' => $todayRouteVisits,
            'paymentSplit' => $paymentSplit,
            'monthlyCollection' => $monthlyCollection,
        ]);
    }

    private function getTodayRouteVisits(Carbon $today): array
    {
        $visits = MyVisiting::query()
            ->with(['dealer', 'user']) // dealer=Shop, user=Employee (ref_id)
            ->whereDate('date', $today->toDateString())
            ->orderBy('time')
            ->get();

        if ($visits->isEmpty()) return [];

        // Preload today payments grouped by (user_id + shop_id)
        // We map visit.dealer_id (Shop id) to orders.shop_id
        $paymentsToday = Payment::query()
            ->with(['order'])
            ->whereDate('created_at', $today->toDateString())
            ->get();

        // Build an index: [user_id][shop_id] => sums + hasCheque
        $index = [];
        foreach ($paymentsToday as $p) {
            $uid = (int)($p->user_id ?? 0);
            $shopId = (int)($p->order?->shop_id ?? 0);
            if (!$uid || !$shopId) continue;

            if (!isset($index[$uid])) $index[$uid] = [];
            if (!isset($index[$uid][$shopId])) {
                $index[$uid][$shopId] = [
                    'sum' => 0,
                    'hasCheque' => false,
                ];
            }

            $index[$uid][$shopId]['sum'] += (float)($p->paid_amount ?? 0);
            if (strtolower((string)$p->collection_type) === 'cheque') {
                $index[$uid][$shopId]['hasCheque'] = true;
            }
        }

        $rows = [];
        foreach ($visits as $v) {
            $employee = $v->user;
            $dealer = $v->dealer;

            $employeeName = trim(($employee?->first_name ?? '') . ' ' . ($employee?->last_name ?? ''));
            $dealerName = ($dealer?->business_name ?? 'N/A') . ' - ' . ($dealer?->business_address ?? '');

            $uid = (int)($v->ref_id ?? 0);
            $shopId = (int)($v->dealer_id ?? 0);

            $collected = (float)($index[$uid][$shopId]['sum'] ?? 0);
            $hasCheque = (bool)($index[$uid][$shopId]['hasCheque'] ?? false);

            $status = 'Pending Collection';
            if ($collected > 0 && $hasCheque) $status = 'Cheque Received';
            elseif ($collected > 0) $status = 'Completed';

            $rows[] = [
                'id' => $v->id,
                'employee_name' => $employeeName ?: '-',
                'dealer_name' => $dealerName,
                'in_time' => $v->time ?? '-',
                'out_time' => $v->checkout_time ?? null,
                'collection_amount' => $collected,
                'status' => $status,
            ];
        }

        return $rows;
    }

    private function getTodayPaymentSplit(Carbon $today): array
    {
        // If your system has NO "credit" payment type, you can keep credit=0.
        $cash = (float) Payment::query()
            ->whereDate('created_at', $today->toDateString())
            ->whereRaw('LOWER(collection_type) = ?', ['cash'])
            ->sum('paid_amount');

        $cheque = (float) Payment::query()
            ->whereDate('created_at', $today->toDateString())
            ->whereRaw('LOWER(collection_type) = ?', ['cheque'])
            ->sum('paid_amount');

        // Optional (only if you use a third type in payment table)
        $credit = (float) Payment::query()
            ->whereDate('created_at', $today->toDateString())
            ->whereRaw('LOWER(collection_type) = ?', ['credit'])
            ->sum('paid_amount');

        return [
            'cash' => $cash,
            'cheque' => $cheque,
            'credit' => $credit,
        ];
    }

    private function getMonthlyCollectionLast6Months(): array
    {
        $labels = [];
        $data = [];

        $start = Carbon::now()->startOfMonth()->subMonths(5);

        for ($i = 0; $i < 6; $i++) {
            $month = $start->copy()->addMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();

            $sum = (float) Payment::query()
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('paid_amount');

            $labels[] = $month->format('M');
            $data[] = $sum;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }
    

    public function index() {}

    public function create() {}

    public function store(Request $request)
    {
        //
    }


    public function show($id)
    {
        return view('admindashboard::show');
    }


    public function edit($id)
    {
        return view('admindashboard::edit');
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
