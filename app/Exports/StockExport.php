<?php

namespace App\Exports;

use Modules\Dealers\Entities\ShopStock;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class StockExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithEvents,
    ShouldAutoSize,
    WithCustomStartCell
{
    protected $request;
    protected string $dealerName = 'All Dealers';

    public function __construct($request)
    {
        $this->request = $request;
    }

    // Headings will start at Row 3 (Row 1 title, Row 2 dealer info)
    public function startCell(): string
    {
        return 'A3';
    }

    public function collection()
    {
        $query = ShopStock::with([
            'shop',
            'order',
            'item.product.unit',
        ]);

        // Dealer filter (dealer = shop)
        if ($this->request->selected_dealer && $this->request->selected_dealer !== 'all') {
            $query->where('shop_id', $this->request->selected_dealer);

            // Get dealer name for the title (safe)
            $first = (clone $query)->first();
            if ($first && $first->shop && $first->shop->business_name) {
                $this->dealerName = $first->shop->business_name;
            }
        }

        // Order filter (your Vue sends order_id in selected_order_number)
        if ($this->request->selected_order_number && $this->request->selected_order_number !== 'all') {
            $query->where('order_id', $this->request->selected_order_number);
        }

        // Quantity status filter (based on ShopStock.quantity)
        if ($this->request->selected_quantity_status === 'empty') {
            $query->where('quantity', 0);
        } elseif ($this->request->selected_quantity_status === 'none-empty') {
            $query->where('quantity', '>', 0);
        }

        return $query->orderBy('id', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Dealer Name',
            'Dealer Address',
            'Dealer Contact',
            'Order Number',
            'Item Name',
            'Order Quantity',
            'Stock Quantity',
            'Unit',
        ];
    }

    public function map($row): array
    {
        $unit = $row->item->product->unit->unit_name ?? '-';

        return [
            $row->shop->business_name ?? '-',
            $row->shop->business_address ?? '-',
            $row->shop->business_tel ?? '-',
            $row->order->order_number ?? '-',
            $row->item->product->product_name ?? '-',

            // ✅ Order Quantity (from Item table)
            $row->item->quantity ?? 0,

            // ✅ Stock Quantity (from ShopStock table)
            $row->quantity ?? 0,

            $unit,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                // Title row (Row 1)
                $event->sheet->mergeCells('A1:H1');
                $event->sheet->setCellValue('A1', 'STOCK REPORT');

                $event->sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);

                // Dealer row (Row 2)
                $event->sheet->mergeCells('A2:H2');
                $event->sheet->setCellValue('A2', 'Dealer: ' . $this->dealerName);

                $event->sheet->getStyle('A2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);

                // Headings row (Row 3)
                $event->sheet->getStyle('A3:H3')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);

                // Row heights
                $event->sheet->getRowDimension(1)->setRowHeight(35);
                $event->sheet->getRowDimension(2)->setRowHeight(22);
                $event->sheet->getRowDimension(3)->setRowHeight(20);
            }
        ];
    }
}
