<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PersonnelItemsExport implements FromCollection, WithHeadings, WithMapping
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
            'ID',
            'Personnel Name',
            'Department',
            'Branch',
            'Item Name',
            'Quantity',
            'Date Receive',
            'Date Issued',
            'Remarks',
            'Return reason (code)',
            'Return details',
        ];
    }

    public function map($outbound): array
    {
        return [
            $outbound->personnel_item_id,
            $outbound->personnel->personnel_name ?? '',
            $outbound->personnel->branch->branch_department ?? '',
            $outbound->personnel->branch->branch_name ?? '',
            $outbound->item->item_name ?? '',
            $outbound->personnel_item_quantity,
            $outbound->personnel_date_receive,
            $outbound->personnel_date_issued,
            $outbound->personnel_item_remarks,
            $outbound->return_reason_preset ?? '',
            $outbound->return_reason_detail ?? '',
        ];
    }
}