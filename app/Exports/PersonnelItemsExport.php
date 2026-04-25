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

class PersonnelItemsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithEvents, WithCustomStartCell, WithDrawings
{
    protected $outbounds;

    public function __construct($outbounds)
    {
        $this->outbounds = $outbounds;
    }

    public function collection()
    {
        return $this->outbounds;
    }

    public function headings(): array
    {
        return [
            'ASSET NAME',
            'PRODUCT NAME',
            'SERIAL NO.',
            'ISSUED DATE',
            'RECEIVED DATE',
            'CUSTODIAN',
            'BRANCH',
            'DEPARTMENT',
            'STATUS',
        ];
    }

    public function map($row): array
    {
        $branchName = strtoupper($row->personnel->branch->branch_name ?? 'UNASSIGNED');
        $deptName = strtoupper($row->personnel->branch->branch_department ?? 'UNASSIGNED');
        $catName = strtoupper($row->item->category->item_category_name ?? '-');

        return [
            $catName,
            strtoupper(($row->item->item_name ?? '-') . ' (X' . $row->personnel_item_quantity . ')'),
            strtoupper($row->item->item_serialno ?? '-'),
            $row->personnel_date_issued ? strtoupper(\Carbon\Carbon::parse($row->personnel_date_issued)->format('M d, Y')) : '-',
            $row->personnel_date_receive ? strtoupper(\Carbon\Carbon::parse($row->personnel_date_receive)->format('M d, Y')) : '-',
            strtoupper($row->personnel->personnel_name ?? '-'),
            $branchName,
            $deptName,
            strtoupper($row->personnel_item_remarks ?? '-'),
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
        foreach (range('A', 'I') as $col) {
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
                $sheet->mergeCells('F1:I1');
                $sheet->setCellValue('F1', 'GOLDTOWN');
                $sheet->getStyle('F1')->getFont()->setBold(true)->setSize(24);
                $sheet->getStyle('F1')->getAlignment()->setHorizontal('right');

                $sheet->mergeCells('F2:I2');
                $sheet->setCellValue('F2', 'INVENTORY REPORT');
                $sheet->getStyle('F2')->getFont()->setBold(true);
                $sheet->getStyle('F2')->getAlignment()->setHorizontal('right');

                $sheet->mergeCells('F3:I3');
                $sheet->setCellValue('F3', 'NATIONAL HIGHWAY, LAPASAN, CAGAYAN DE ORO CITY');
                $sheet->getStyle('F3')->getAlignment()->setHorizontal('right');

                $sheet->mergeCells('F4:I4');
                $sheet->setCellValue('F4', '9000 MISAMIS ORIENTAL | (088) 856 7111');
                $sheet->getStyle('F4')->getAlignment()->setHorizontal('right');

                // 3. Generated Date
                $sheet->mergeCells('A5:D5');
                $sheet->setCellValue('A5', 'GENERATED DATE: ' . strtoupper(now()->format('F d, Y')));
                $sheet->getStyle('A5')->getFont()->setItalic(true);

                // 4. Table Header Styling (Navy Blue Background)
                $sheet->getStyle('A6:I6')->applyFromArray([
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
                    $sheet->getStyle("A$row:I$row")->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['rgb' => '94a3b8'],
                            ],
                        ],
                    ]);

                    
                    
                }
            },
        ];
    }
}