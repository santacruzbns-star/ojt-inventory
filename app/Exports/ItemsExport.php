<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ItemsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithEvents, WithCustomStartCell, WithDrawings
{
    protected $items;

    public function __construct($items)
    {
        $this->items = $items;
    }

    public function collection()
    {
        return $this->items;
    }

    public function headings(): array
    {
        return [
            'PRODUCT',
            'CATEGORY',
            'SERIAL NO.',
            'UOM',
            'TOTAL QTY',
            'REMAINING QTY',
            'REMARK / STATUS',
        ];
    }

    public function map($item): array
    {
        return [
            strtoupper($item->item_name),
            strtoupper($item->category->item_category_name ?? 'UNCATEGORIZED'),
            strtoupper($item->item_serialno ?? '-'),
            strtoupper($item->uom->item_uom_name ?? '-'),
            $item->item_quantity ?? '0',
            $item->item_quantity_remaining ?? '0',
            strtoupper($item->item_remark ?? '-'),
        ];
    }

    public function startCell(): string
    {
        return 'A6';
    }

    public function drawings()
    {
        $drawings = [];
        $logoPath = public_path('storage/img/login-logo.png');

        if (file_exists($logoPath)) {
            $drawing = new Drawing();
            $drawing->setName('Goldtown Logo');
            $drawing->setPath($logoPath);
            $drawing->setHeight(75);
            $drawing->setCoordinates('A1');
            $drawing->setOffsetX(10);
            $drawing->setOffsetY(10);
            $drawings[] = $drawing;
        }

        return $drawings;
    }

    public function styles(Worksheet $sheet)
    {
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // 1. Page Setup
                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageMargins()->setTop(0.75)->setRight(0.5)->setLeft(0.5)->setBottom(0.75);

                // 2. Header Text (Right Aligned)
                $sheet->mergeCells('D1:G1');
                $sheet->setCellValue('D1', 'GOLDTOWN');
                $sheet->getStyle('D1')->getFont()->setBold(true)->setSize(24);
                $sheet->getStyle('D1')->getAlignment()->setHorizontal('right');

                $sheet->mergeCells('D2:G2');
                $sheet->setCellValue('D2', 'ITEM INVENTORY REPORT');
                $sheet->getStyle('D2')->getFont()->setBold(true);
                $sheet->getStyle('D2')->getAlignment()->setHorizontal('right');

                $sheet->mergeCells('D3:G3');
                $sheet->setCellValue('D3', 'NATIONAL HIGHWAY, LAPASAN, CAGAYAN DE ORO CITY');
                $sheet->getStyle('D3')->getAlignment()->setHorizontal('right');

                $sheet->mergeCells('D4:G4');
                $sheet->setCellValue('D4', '9000 MISAMIS ORIENTAL | (088) 856 7111');
                $sheet->getStyle('D4')->getAlignment()->setHorizontal('right');

                // 3. Generated Date
                $sheet->mergeCells('A5:C5');
                $sheet->setCellValue('A5', 'GENERATED DATE: ' . strtoupper(now()->format('F d, Y')));
                $sheet->getStyle('A5')->getFont()->setItalic(true);

                // 4. Table Header Styling (Navy Blue Background)
                $sheet->getStyle('A6:G6')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1a252f'],
                    ],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);

                $highestRow = $sheet->getHighestRow();

                // 5. Data Row Styling (Clean - No Background Color)
                for ($row = 7; $row <= $highestRow; $row++) {
                    
                    // Apply Borders only
                    $sheet->getStyle("A$row:G$row")->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['rgb' => '94a3b8'],
                            ],
                        ],
                    ]);

                    // Bold Quantities
                    $sheet->getStyle("E$row:F$row")->getFont()->setBold(true);

                   
                }
            },
        ];
    }
}