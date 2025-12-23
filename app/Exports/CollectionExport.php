<?php

namespace App\Exports;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Modules\Orders\Entities\Order;
use Modules\MyCollections\Entities\Payment;

class CollectionExport implements FromCollection, WithEvents, ShouldAutoSize
{
    protected array $filters;
    protected string $titleText;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;

        $from = $filters['start_date'] ?? '';
        $to   = $filters['end_date'] ?? '';

        $employeeName = $filters['employee_name'] ?? 'All Employees';
        $employeeReg  = $filters['employee_reg'] ?? '';

        $empText = $employeeReg ? "{$employeeName} ({$employeeReg})" : $employeeName;

        $this->titleText = "Collection Report - {$empText}"
            . (($from || $to) ? " ({$from} - {$to})" : "");
    }

    public function collection()
    {
        /**
         * We need invoice-level rows, so we build a query grouped by order_id
         * - received_amount = SUM(payments.paid_amount) per order
         * - collector_id = payments.user_id (filtered)
         */
        $paymentAgg = Payment::query()
            ->selectRaw('order_id, user_id as collector_id, COALESCE(SUM(paid_amount),0) as received_amount')
            ->groupBy('order_id', 'user_id');

        // If employee selected => filter paymentAgg by collector (THIS IS THE MAIN FIX)
        if (!empty($this->filters['selected_employee']) && $this->filters['selected_employee'] !== 'all') {
            $paymentAgg->where('user_id', $this->filters['selected_employee']);
        }

        // Date range should apply on payments if you want collections by payment date
        // (If you prefer order date, remove this block)
        if (!empty($this->filters['start_date']) && !empty($this->filters['end_date'])) {
            $paymentAgg->whereBetween('created_at', [
                $this->filters['start_date'] . ' 00:00:00',
                $this->filters['end_date'] . ' 23:59:59',
            ]);
        } elseif (!empty($this->filters['start_date'])) {
            $paymentAgg->where('created_at', '>=', $this->filters['start_date'] . ' 00:00:00');
        } elseif (!empty($this->filters['end_date'])) {
            $paymentAgg->where('created_at', '<=', $this->filters['end_date'] . ' 23:59:59');
        }

        // Collection type filter applies to payments
        if (!empty($this->filters['selected_collection_type']) && $this->filters['selected_collection_type'] !== 'all') {
            $paymentAgg->where('collection_type', $this->filters['selected_collection_type']);
        }

        // Orders query joined with payment aggregation
        $q = Order::query()
            ->with(['shop'])
            ->joinSub($paymentAgg, 'pay', function ($join) {
                $join->on('orders.id', '=', 'pay.order_id');
            })
            ->select([
                'orders.*',
                'pay.received_amount',
                'pay.collector_id',
            ]);

        // Shop filter
        if (!empty($this->filters['selected_shop']) && $this->filters['selected_shop'] !== 'all') {
            $q->where('orders.shop_id', $this->filters['selected_shop']);
        }

        // Search
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $q->where(function ($w) use ($search) {
                $w->where('orders.order_number', 'like', "%{$search}%")
                  ->orWhere('orders.total_price', 'like', "%{$search}%");
            });
        }

        $orders = $q->orderBy('orders.shop_id')
            ->orderBy('orders.created_at')
            ->get();

        // Load collectors (employees) for showing name + reg no
        $collectorIds = $orders->pluck('collector_id')->unique()->filter()->values()->all();
        $collectors = User::whereIn('id', $collectorIds)
            ->get(['id','first_name','last_name','reg_number'])
            ->keyBy('id');

        // Build rows
        $rows = [];
        $rows[] = [$this->titleText];

        // Header row (10 columns now)
        $rows[] = [
            'Shop Name',
            'Collector Reg No',
            'Collector Name',
            'Invoice ID',
            'Created At',
            'Invoice Age (Days)',
            'Status',
            'Status Update At',
            'Total Amount',
            'Received Amount',
            'Credit Amount',
        ];

        $currentShop = null;
        $shopTotal = 0;
        $shopReceived = 0;
        $shopCredit = 0;

        foreach ($orders as $o) {
            $shopName = $o->shop?->business_name ?? 'N/A';

            // shop change -> total row + blank row
            if ($currentShop !== null && $currentShop !== $shopName) {
                $rows[] = ['', '', '', '', '', '', 'Total', '', $shopTotal, $shopReceived, $shopCredit];
                $rows[] = array_fill(0, 11, ''); // blank row

                $shopTotal = 0;
                $shopReceived = 0;
                $shopCredit = 0;
            }

            $currentShop = $shopName;

            $created = $o->created_at ? Carbon::parse($o->created_at) : null;
            $ageDays = $created ? $created->diffInDays(now()) : '';

            $totalAmount = (float) ($o->total_price ?? 0);
            $received = (float) ($o->received_amount ?? 0);
            $credit = $totalAmount - $received;

            $shopTotal += $totalAmount;
            $shopReceived += $received;
            $shopCredit += $credit;

            $collector = $collectors->get($o->collector_id);
            $collectorReg = $collector?->reg_number ?? 'N/A';
            $collectorName = trim(($collector?->first_name ?? '') . ' ' . ($collector?->last_name ?? '')) ?: 'N/A';

            $rows[] = [
                $shopName,
                $collectorReg,
                $collectorName,
                $o->order_number ?? '',
                $created ? $created->format('Y-m-d') : '',
                $ageDays,
                $this->mapStatus($o->order_status),
                $o->updated_at ? Carbon::parse($o->updated_at)->format('Y-m-d H:i') : '',
                $totalAmount,
                $received,
                $credit,
            ];
        }

        // last shop total
        if ($currentShop !== null) {
            $rows[] = ['', '', '', '', '', '', 'Total', '', $shopTotal, $shopReceived, $shopCredit];
        }

        return new Collection($rows);
    }

    private function mapStatus($status): string
    {
        if ($status == 3 || $status === 'delivered') return 'Delivered';
        if ($status == 2 || $status === 'pending') return 'Pending';
        return (string) ($status ?? '');
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                // Now 11 columns: A-K
                $event->sheet->mergeCells('A1:K1');
                $event->sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $event->sheet->getStyle('A1')->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Header row styling
                $event->sheet->getStyle('A2:K2')->getFont()->setBold(true);
                $event->sheet->getStyle('A2:K2')->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Vertical align
                $event->sheet->getStyle('A:K')->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER);

                // Numeric columns: Total/Received/Credit => I-K
                $event->sheet->getStyle('I:K')->getNumberFormat()->setFormatCode('#,##0.00');

                // Color Total rows green where column G == "Total"
                $highestRow = $event->sheet->getHighestRow();
                for ($r = 3; $r <= $highestRow; $r++) {
                    $val = (string) $event->sheet->getCell("G{$r}")->getValue();
                    if (trim($val) === 'Total') {
                        $event->sheet->getStyle("G{$r}:K{$r}")
                            ->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setARGB('C6EFCE');

                        $event->sheet->getStyle("G{$r}:K{$r}")->getFont()->setBold(true);
                    }
                }
            }
        ];
    }
}
