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

    // Match the 9 columns exactly from the PDF
    public function headings(): array
    {
        return [
            'Asset Name',
            'Product Name',
            'Serial No.',
            'Issued Date',
            'Received Date',
            'Custodian',
            'Branch',
            'Department',
            'Status',
        ];
    }

    // Map the data to match the PDF layout
    public function map($row): array
    {
        $branchName = $row->personnel->branch->branch_name ?? 'Unassigned';
        $deptName = $row->personnel->branch->branch_department ?? 'Unassigned';

        return [
            $row->item->category->item_category_name ?? '-',
            ($row->item->item_name ?? '-') . ' (x' . $row->personnel_item_quantity . ')',
            $row->item->item_serialno ?? '-',
            $row->personnel_date_issued ? \Carbon\Carbon::parse($row->personnel_date_issued)->format('M d, Y') : '-',
            $row->personnel_date_receive ? \Carbon\Carbon::parse($row->personnel_date_receive)->format('M d, Y') : '-',
            $row->personnel->personnel_name ?? '-',
            $branchName,
            $deptName,
            $row->personnel_item_remarks ?? '-',
        ];
    }

    // Push the table down to Row 6 to make room for the Header & Logo
    public function startCell(): string
    {
        return 'A6';
    }

    // Add the Logo Icon
    public function drawings()
    {
        $drawings = [];
        $logoPath = public_path('storage/img/login-logo.png');

        // Check if file exists so it doesn't crash if the logo is missing
        if (file_exists($logoPath)) {
            $drawing = new Drawing();
            $drawing->setName('Goldtown Logo');
            $drawing->setDescription('Logo');
            $drawing->setPath($logoPath);
            $drawing->setHeight(75); // Same height as your PDF
            $drawing->setCoordinates('A1');
            $drawing->setOffsetX(10);
            $drawing->setOffsetY(10);
            $drawings[] = $drawing;
        }

        return $drawings;
    }

    public function styles(Worksheet $sheet)
    {
        // Auto size columns A through I
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // 1. Set Page to Landscape & Add Margins
                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageMargins()->setTop(0.75);
                $sheet->getPageMargins()->setRight(0.5);
                $sheet->getPageMargins()->setLeft(0.5);
                $sheet->getPageMargins()->setBottom(0.75);

                // 2. Build the Right-Aligned Header Text
                // (Using F through I to push it to the right side of the sheet)
                
                $sheet->mergeCells('F1:I1');
                $sheet->setCellValue('F1', 'Goldtown');
                $sheet->getStyle('F1')->getFont()->setBold(true)->setSize(24);
                $sheet->getStyle('F1')->getAlignment()->setHorizontal('right');

                $sheet->mergeCells('F2:I2');
                $sheet->setCellValue('F2', 'Inventory Report');
                $sheet->getStyle('F2')->getFont()->setBold(true);
                $sheet->getStyle('F2')->getAlignment()->setHorizontal('right');

                $sheet->mergeCells('F3:I3');
                $sheet->setCellValue('F3', 'National Highway, Lapasan, Cagayan De Oro City');
                $sheet->getStyle('F3')->getAlignment()->setHorizontal('right');

                $sheet->mergeCells('F4:I4');
                $sheet->setCellValue('F4', '9000 Misamis Oriental | (088) 856 7111');
                $sheet->getStyle('F4')->getAlignment()->setHorizontal('right');

                // 3. Insert Generated Date
                $sheet->mergeCells('A5:D5');
                $sheet->setCellValue('A5', 'Generated Date: ' . now()->format('F d, Y'));
                $sheet->getStyle('A5')->getFont()->setItalic(true);

                // 4. Table Header Styling (Row 6)
                $sheet->getStyle('A6:I6')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1a252f'], // Dark navy/gray
                    ],
                    'alignment' => [
                        'horizontal' => 'center',
                        'vertical' => 'center'
                    ],
                ]);

                $highestRow = $sheet->getHighestRow();
                
                // Colors for grouping Branch+Dept combinations
                $rowPalette = [
                    'fef08a', // Light Yellow
                    'dcfce7', // Light Green
                    'e0f2fe', // Light Blue
                    'ffedd5', // Light Orange/Peach
                    'f3e8ff', // Light Purple
                    'ccfbf1', // Light Teal
                ];
                $branchDeptColors = [];
                $colorIndex = 0;

                // 5. Loop through rows to apply grouping colors (Data starts at row 7)
                for ($row = 7; $row <= $highestRow; $row++) {
                    
                    // Column G is Branch, Column H is Department
                    $branch = $sheet->getCell("G$row")->getValue();
                    $dept = $sheet->getCell("H$row")->getValue();
                    $comboKey = $branch . '-' . $dept;

                    if (!isset($branchDeptColors[$comboKey])) {
                        $branchDeptColors[$comboKey] = $rowPalette[$colorIndex % count($rowPalette)];
                        $colorIndex++;
                    }

                    // Apply the background color to the whole row
                    $sheet->getStyle("A$row:I$row")->applyFromArray([
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['rgb' => $branchDeptColors[$comboKey]],
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['rgb' => '94a3b8'],
                            ],
                        ],
                    ]);

                    // 6. Remarks/Status Colors (Column I)
                    $status = $sheet->getCell("I$row")->getValue();
                    $sheet->getStyle("I$row")->getAlignment()->setHorizontal('center');

                    switch ($status) {
                        case 'Good':
                        case 'Received':
                            $sheet->getStyle("I$row")->applyFromArray([
                                'font' => ['color' => ['rgb' => '155724'], 'bold' => true], // Dark green
                            ]);
                            break;
                        case 'Damaged':
                            $sheet->getStyle("I$row")->applyFromArray([
                                'font' => ['color' => ['rgb' => '721c24'], 'bold' => true], // Dark red
                            ]);
                            break;
                        case 'Missing':
                        case 'Not Receive':
                            $sheet->getStyle("I$row")->applyFromArray([
                                'font' => ['color' => ['rgb' => '856404'], 'bold' => true], // Dark yellow/brown
                            ]);
                            break;
                        case 'Returned':
                            $sheet->getStyle("I$row")->applyFromArray([
                                'font' => ['color' => ['rgb' => '004085'], 'bold' => true], // Dark blue
                            ]);
                            break;
                    }
                }
            },
        ];
    }
}