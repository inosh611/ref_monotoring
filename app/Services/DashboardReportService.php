<?php

namespace App\Services;

use Carbon\Carbon;
use Modules\Orders\Entities\Order;
use Modules\MyCollections\Entities\Payment;
use Modules\MyVisiting\Entities\MyVisiting;

class DashboardReportService
{
    /**
     * Today + Overdue expected collections (Pending only)
     */
    public function todayExpectedCollections(int $userId): array
    {
        $today = Carbon::today()->toDateString();

        return Order::with('shop')
            ->where('user_id', $userId)
            ->whereDate('expected_collection_date', '<=', $today) // today + overdue
            ->where(function ($q) {
                $q->whereNull('paid_amount')
                    ->orWhereColumn('paid_amount', '<', 'total_price');
            })
            ->orderBy('expected_collection_date', 'asc')
            ->get()
            ->map(function ($o) use ($today) {
                $total = (float) ($o->total_price ?? 0);
                $paid  = (float) ($o->paid_amount ?? 0);
                $balance = max($total - $paid, 0);

                $status = ($o->expected_collection_date < $today) ? 'Overdue' : 'Due Today';

                return [
                    'id' => $o->id,
                    'order_number' => $o->order_number,
                    'dealer_name' => ($o->shop?->business_name ?? 'N/A') . ' - ' . ($o->shop?->business_address ?? ''),
                    'telephone' => $o->shop?->business_tel ?? '-',
                    'expected_date' => $o->expected_collection_date,
                    'total_price' => $total,
                    'paid_amount' => $paid,
                    'balance' => $balance,
                    'status' => $status,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * ✅ TODO: Replace this with your real visiting table query
     * Return array to match your PDF template.
     */
    public function todayVisiting(int $userId): array
    {
        $today = Carbon::today()->toDateString();

        return MyVisiting::with(['dealer']) // dealer = Shop
            ->where('ref_id', $userId)      // employee id stored in ref_id
            ->whereDate('date', $today)
            ->orderBy('time', 'asc')
            ->get()
            ->map(function ($v) {

                // photo_of_shop stored like: "shops/abc.jpg" in storage/app/public/...
                $relative = $v->photo_of_shop ? ('storage/' . ltrim($v->photo_of_shop, '/')) : null;

                return [
                    'id' => $v->id,
                    'date' => $v->date,
                    'time' => $v->time,
                    'checkout_date' => $v->checkout_date,
                    'checkout_time' => $v->checkout_time,

                    'dealer' => [
                        'business_name' => $v->dealer?->business_name ?? '-',
                        'business_address' => $v->dealer?->business_address ?? '-',
                        'business_tel' => $v->dealer?->business_tel ?? '-',
                    ],

                    // ✅ For PDF thumbnail (Dompdf needs local file path)
                    'shop_image_file' => $relative ? public_path($relative) : null,

                    // ✅ For showing a clickable text link (optional)
                    'shop_image_url' => $relative ? url($relative) : null,
                ];
            })
            ->values()
            ->all();
    }

        public function todayOrders(int $userId): array
    {
        $today = Carbon::today()->toDateString();

        return Order::with('shop')
            ->where('user_id', $userId)
            ->whereDate('created_at', $today) // ✅ today created orders
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($o) {
                return [
                    'id' => $o->id,
                    'order_number' => $o->order_number,
                    'created_at' => $o->created_at ? Carbon::parse($o->created_at)->format('Y-m-d H:i') : '-',
                    'shop' => [
                        'business_name' => $o->shop?->business_name ?? '-',
                        'business_address' => $o->shop?->business_address ?? '-',
                        'business_tel' => $o->shop?->business_tel ?? '-',
                    ],
                    'total_price' => (float) ($o->total_price ?? 0),
                    'paid_amount' => (float) ($o->paid_amount ?? 0),
                    'order_status' => $o->order_status ?? '-',
                    'payment_status' => $o->payment_status ?? '-',
                ];
            })
            ->values()
            ->all();
    }

    public function todayCollections(int $userId): array
    {
        $today = Carbon::today()->toDateString();

        return Payment::with(['order.shop', 'cash', 'cheque'])
            ->where('user_id', $userId)
            ->whereDate('created_at', $today) // ✅ today collected
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($p) {
                $type = strtolower((string) ($p->collection_type ?? ''));
                $isCash = $type === 'cash';
                $isCheque = $type === 'cheque';

                $chequeNo = $isCheque ? ($p->cheque?->cheque_number ?? '--') : '--';
                $receiptNo = $isCash ? ($p->cash?->cash_receipt_number ?? '--') : ($p->cheque?->receipt_number ?? '--');

                return [
                    'id' => $p->id,
                    'paid_date' => $p->created_at ? Carbon::parse($p->created_at)->format('Y-m-d H:i') : '-',
                    'order_number' => $p->order?->order_number ?? '-',
                    'shop' => [
                        'business_name' => $p->order?->shop?->business_name ?? '-',
                        'business_address' => $p->order?->shop?->business_address ?? '-',
                        'business_tel' => $p->order?->shop?->business_tel ?? '-',
                    ],
                    'payment_type' => $isCash ? 'Cash' : ($isCheque ? 'Cheque' : ($p->collection_type ?? '-')),
                    'cheque_number' => $chequeNo,      // ✅ if cash => "--"
                    'receipt_number' => $receiptNo,    // optional, useful
                    'paid_amount' => (float) ($p->paid_amount ?? 0),
                    'comment' => $p->comment ?? '',
                ];
            })
            ->values()
            ->all();
    }
    public function confirmedOrders(int $userId): array
    {
        // Example placeholder:
        // return Order::with('shop')->where('user_id',$userId)->where('order_status','Confirmed')->whereDate('expected_order_date', today())->get()->toArray();
        return [];
    }
}
