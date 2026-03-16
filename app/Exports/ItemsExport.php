<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ItemsExport implements FromCollection, WithHeadings, WithStyles, WithEvents
{
    protected $items;

    public function __construct($items)
    {
        $this->items = $items;
    }

    public function collection()
    {
        return $this->items->map(function ($item) {
            return [
                'Product Name' => $item->item_name,
                'Category'     => $item->category->item_category_name ?? '',
                'Brand'        => $item->brand->item_brand_name ?? '',
                'Serial No'    => $item->item_serialno,
                'UOM'          => $item->uom->item_uom_name ?? '',
                'Quantity'     => $item->item_quantity ?? 0,
                'Status'       => $item->item_remark ?? '',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Product Name',
            'Category',
            'Brand',
            'Serial Number',
            'Unit of Measure',
            'Quantity',
            'Status'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Auto size columns
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                // Insert title row
                $sheet->insertNewRowBefore(1, 2);
                $sheet->mergeCells('A1:G1');
                $sheet->setCellValue('A1', 'Inventory Report');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

                // Date row
                $sheet->mergeCells('A2:G2');
                $sheet->setCellValue('A2', 'Date: ' . now()->format('F j, Y'));
                $sheet->getStyle('A2')->getFont()->setItalic(true);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

                // Header styling (row 3 after inserting 2 rows)
                $sheet->getStyle('A3:G3')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '6c757d'], // gray
                    ],
                    'alignment' => ['horizontal' => 'center'],
                ]);

                // Conditional Status colors (column G)
                $highestRow = $sheet->getHighestRow();
                for ($row = 4; $row <= $highestRow; $row++) {
                    $status = $sheet->getCell("G$row")->getValue();

                    switch ($status) {
                        case 'Good':
                            $sheet->getStyle("G$row")->applyFromArray([
                                'fill' => [
                                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                    'startColor' => ['rgb' => '28a745'], // green
                                ],
                                'font' => ['color' => ['rgb' => 'FFFFFF']],
                            ]);
                            break;
                        case 'Damaged':
                            $sheet->getStyle("G$row")->applyFromArray([
                                'fill' => [
                                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                    'startColor' => ['rgb' => 'dc3545'], // red
                                ],
                                'font' => ['color' => ['rgb' => 'FFFFFF']],
                            ]);
                            break;
                        case 'Missing':
                            $sheet->getStyle("G$row")->applyFromArray([
                                'fill' => [
                                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                    'startColor' => ['rgb' => 'ffc107'], // yellow
                                ],
                                'font' => ['color' => ['rgb' => '000000']],
                            ]);
                            break;
                    }
                }
            },
        ];
    }
}