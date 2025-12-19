<?php

namespace App\Exports;

use Modules\MyVisiting\Entities\MyVisiting;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;

class CustomerWisedVisitsExport implements
    FromCollection,
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
        $query = MyVisiting::with(['dealer', 'user']);

        if ($this->request->selected_dealer !== 'all') {
            $query->where('dealer_id', $this->request->selected_dealer);
        }

        if ($this->request->selected_employee !== 'all') {
            $query->where('ref_id', $this->request->selected_employee);
        }

        if ($this->request->selected_status === 'visited') {
            $query->whereNotNull('checkout_time');
        }

        if ($this->request->selected_status === 'none-visited') {
            $query->whereNull('checkout_time');
        }

        if ($this->request->start_date && $this->request->end_date) {
            $query->whereBetween('date', [$this->request->start_date, $this->request->end_date]);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Dealer Name',
            'Dealer Address',
            'Contact Number',
            'Ref Reg No',
            'Ref Name',
            'Visit Date',
            'Check In Time',
            'Check Out Time',
            'Status',
        ];
    }

    public function map($row): array
    {
        return [
            $row->dealer->business_name ?? '-',
            $row->dealer->business_address ?? '-',
            $row->dealer->business_tel ?? '-',
            $row->user->reg_number ?? '-',
            $row->user->first_name ?? '-',
            $row->date,
            $row->time,
            $row->checkout_time ?? 'Not checked out yet',
            $row->checkout_time ? 'Visited' : 'Not Visited',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                // ✅ Title row (Row 1)
                $event->sheet->mergeCells('A1:I1');
                $event->sheet->setCellValue('A1', 'Customer Wised Visits Report');

                $event->sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);

                // ✅ Headings row (Row 2)
                $event->sheet->getStyle('A2:I2')->applyFromArray([
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
