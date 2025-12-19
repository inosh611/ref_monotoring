<?php

namespace App\Exports;

use Modules\Orders\Entities\Order;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Modules\MyVisiting\Entities\MyVisiting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class OrderExport implements FromCollection,
    WithHeadings,
    WithMapping,
    WithEvents,
    ShouldAutoSize,
    WithCustomStartCell
{
   protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    // ✅ Headings will be written on row 2
    public function startCell(): string
    {
        return 'A2';
    }

    public function collection()
    {
        $query = Order::with(['shop', 'user']); 

    if ($this->request->selected_shop && $this->request->selected_shop !== 'all') {
        $query->where('shop_id', $this->request->selected_shop);
    }

    if ($this->request->selected_employee && $this->request->selected_employee !== 'all') {
        $query->where('user_id', $this->request->selected_employee);
    }

    if ($this->request->selected_order_status && $this->request->selected_order_status !== 'all') {
        $query->where('order_status', $this->request->selected_order_status);
    }

    if ($this->request->selected_payment_status && $this->request->selected_payment_status !== 'all') {
        $query->where('payment_status', $this->request->selected_payment_status);
    }

    if ($this->request->start_date && $this->request->end_date) {
        $query->whereBetween('expected_order_date', [$this->request->start_date, $this->request->end_date]);
    } elseif ($this->request->start_date) {
        $query->whereDate('expected_order_date', '>=', $this->request->start_date);
    } elseif ($this->request->end_date) {
        $query->whereDate('expected_order_date', '<=', $this->request->end_date);
    }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Order Number',
            'Shop Name',
            'Shop Address',
            'Shop Contact',
            'Ref Reg',
            'Ref Name',
            'Total Price',
            'Paid Amount',
            'Payment Status',
            'Order Status',
            'Expected Order Date',
        ];
    }

    public function map($row): array
    {
        return [
            $row->order_number ?? '-',
            $row->shop->business_name ?? '-',
            $row->shop->business_address ?? '-',
            $row->shop->business_tel ?? '-',
            $row->user->reg_number ?? '-',
            $row->user->first_name ?? '-',
            $row->total_price,
            $row->paid_amount,
            $row->payment_status,
            $row->order_status,
            $row->expected_order_date,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                // ✅ Title row (Row 1)
                $event->sheet->mergeCells('A1:K1');
                $event->sheet->setCellValue('A1', 'ORDER REPORT');

                $event->sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);

                // ✅ Headings row (Row 2)
                $event->sheet->getStyle('A2:K2')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => 'center'],
                ]);

                // Row heights
                $event->sheet->getRowDimension(1)->setRowHeight(35);
                $event->sheet->getRowDimension(2)->setRowHeight(25);
            }
        ];
    }
}
