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

    // Match the 7 columns exactly from the Inventory PDF
    public function headings(): array
    {
        return [
            'Product',
            'Category',
            'Serial No.',
            'UOM',
            'Total Qty',
            'Remaining Qty',
            'Remark / Status',
        ];
    }

    // Map the data to match the PDF layout
    public function map($item): array
    {
        return [
            $item->item_name,
            $item->category->item_category_name ?? 'Uncategorized',
            $item->item_serialno ?? '-',
            $item->uom->item_uom_name ?? '-',
            $item->item_quantity ?? '0',
            $item->item_quantity_remaining ?? '0',
            $item->item_remark ?? '-',
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
        // Auto size columns A through G
        foreach (range('A', 'G') as $col) {
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
                // (Using D through G to push it to the right side of the 7-column sheet)
                
                $sheet->mergeCells('D1:G1');
                $sheet->setCellValue('D1', 'Goldtown');
                $sheet->getStyle('D1')->getFont()->setBold(true)->setSize(24);
                $sheet->getStyle('D1')->getAlignment()->setHorizontal('right');

                $sheet->mergeCells('D2:G2');
                $sheet->setCellValue('D2', 'Master Inventory Report');
                $sheet->getStyle('D2')->getFont()->setBold(true);
                $sheet->getStyle('D2')->getAlignment()->setHorizontal('right');

                $sheet->mergeCells('D3:G3');
                $sheet->setCellValue('D3', 'National Highway, Lapasan, Cagayan De Oro City');
                $sheet->getStyle('D3')->getAlignment()->setHorizontal('right');

                $sheet->mergeCells('D4:G4');
                $sheet->setCellValue('D4', '9000 Misamis Oriental | (088) 856 7111');
                $sheet->getStyle('D4')->getAlignment()->setHorizontal('right');

                // 3. Insert Generated Date
                $sheet->mergeCells('A5:C5');
                $sheet->setCellValue('A5', 'Generated Date: ' . now()->format('F d, Y'));
                $sheet->getStyle('A5')->getFont()->setItalic(true);

                // 4. Table Header Styling (Row 6)
                $sheet->getStyle('A6:G6')->applyFromArray([
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
                
                // Colors for grouping Categories
                $rowPalette = [
                    'e0f2fe', // Light Blue
                    'fef08a', // Light Yellow
                    'dcfce7', // Light Green
                    'f3e8ff', // Light Purple
                    'ffedd5', // Light Orange/Peach
                    'ccfbf1', // Light Teal
                    'fce7f3', // Light Pink
                ];
                $categoryColors = [];
                $colorIndex = 0;

                // 5. Loop through rows to apply grouping colors (Data starts at row 7)
                for ($row = 7; $row <= $highestRow; $row++) {
                    
                    // Column B is Category
                    $category = $sheet->getCell("B$row")->getValue();

                    if (!isset($categoryColors[$category])) {
                        $categoryColors[$category] = $rowPalette[$colorIndex % count($rowPalette)];
                        $colorIndex++;
                    }

                    // Apply the background color to the whole row (A to G)
                    $sheet->getStyle("A$row:G$row")->applyFromArray([
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['rgb' => $categoryColors[$category]],
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['rgb' => '94a3b8'],
                            ],
                        ],
                    ]);

                    // Make Total Qty and Remaining Qty Bold (Columns E and F)
                    $sheet->getStyle("E$row:F$row")->getFont()->setBold(true);

                    // 6. Remarks/Status Colors (Column G)
                    $status = $sheet->getCell("G$row")->getValue();
                    
                    switch ($status) {
                        case 'Good':
                            $sheet->getStyle("G$row")->applyFromArray([
                                'font' => ['color' => ['rgb' => '155724'], 'bold' => true], // Dark green
                            ]);
                            break;
                        case 'Damaged':
                            $sheet->getStyle("G$row")->applyFromArray([
                                'font' => ['color' => ['rgb' => '721c24'], 'bold' => true], // Dark red
                            ]);
                            break;
                        case 'Missing':
                            $sheet->getStyle("G$row")->applyFromArray([
                                'font' => ['color' => ['rgb' => '856404'], 'bold' => true], // Dark yellow/brown
                            ]);
                            break;
                    }
                }
            },
        ];
    }
}