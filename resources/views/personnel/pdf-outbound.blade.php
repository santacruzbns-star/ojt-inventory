<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>INVENTORY REPORT</title>
    <style>
       /* 1. Reduced the top margin since the header is no longer fixed */
        @page {
            size: A4 portrait;
            margin: 30px 25px 80px 25px; 
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #232323;
            text-transform: uppercase;
        }

        /* 2. Removed fixed positioning and negative offsets */
        header {
            height: 90px;
            background-color: #ffffff;
            border-bottom: 6px solid #ffffff;
            /* Adjusted padding to sit nicely inside the new page margins */
            padding: 0 0 15px 0; 
            margin-bottom: 20px; /* Adds breathing room before the h1 and table */
        }

        .logo {
            height: 75px;
            float: left;
            object-fit: contain;
        }

        .header-content {
            float: right;
            text-align: right;
            padding-top: 5px;
        }

        .header-title {
            font-size: 24px;
            font-weight: bold;
            color: #1a252f;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }

        .header-address {
            font-size: 11px;
            color: #555;
            line-height: 1.5;
            text-transform: uppercase;
        }

        /* Clear floats */
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        h1 {
            font-size: 16px;
            margin-bottom: 12px;
            margin-top: 0;
            color: #1a252f;
            text-transform: uppercase;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        /* Target both th and td inside tbody */
        tbody th,
        tbody td {
            border: 1px solid #94a3b8;
            padding: 5px 4px;
            text-align: left;
            text-transform: uppercase;
        }

        tr.table-header-row th {
            background: #1a252f;
            font-weight: bold;
            color: #ffffff;
            text-transform: uppercase;
            font-size: 9px;
        }
    </style>
</head>

<body>
    <header class="clearfix">
        <img src="{{ public_path('storage/img/login-logo.png') }}" class="logo" alt="Goldtown Logo">
        <div class="header-content">
            <div class="header-title">Goldtown</div>
            <div class="header-address">
                <strong>Inventory Report</strong><br>
                National Highway, Lapasan, Cagayan De Oro City<br>
                9000 Misamis Oriental | (088) 856 7111
            </div>
        </div>
    </header>

    <main>
        <h1>Inventory Report</h1>
        <table>
            {{-- Removed <thead> to prevent repeating on new pages --}}
            <tbody>
                {{-- Placed the header row inside the body --}}
                <tr class="table-header-row">
                    <th>Asset Name</th>
                    <th>Product Name</th>
                    <th>Serial No.</th>
                    <th>Issued Date</th>
                    <th>Received Date</th>
                    <th>Custodian</th>
                    <th>Branch</th>
                    <th>Department</th>
                    <th>Status</th>
                </tr>

                @php
                    $groupColors = [];
                    $rowPalette = [
                        '#fef08a', '#dcfce7', '#e0f2fe', '#ffedd5', '#f3e8ff',
                        '#ccfbf1', '#fce7f3', '#fef9c3', '#dbeafe',
                    ];
                    $colorIndex = 0;
                @endphp

                @foreach ($outbounds as $row)
                    @php
                        $categoryName = $row->item?->category?->item_category_name ?? '-';
                        $branchName = $row->personnel?->branch?->branch_name ?? 'Unassigned';
                        $deptName = $row->personnel?->branch?->branch_department ?? 'Unassigned';

                        $comboKey = $branchName . '-' . $deptName . '-' . $categoryName;

                        if (!isset($groupColors[$comboKey])) {
                            $groupColors[$comboKey] = $rowPalette[$colorIndex % count($rowPalette)];
                            $colorIndex++;
                        }
                    @endphp

                    <tr style="background-color: {{ $groupColors[$comboKey] }};">
                        <td>{{ $categoryName }}</td>
                        <td>
                            {{ $row->item?->item_name ?? '-' }} 
                            <strong>(x{{ $row->personnel_item_quantity }})</strong>
                        </td>
                        <td>{{ $row->item?->item_serialno ?? '-' }}</td>
                        <td>{{ $row->personnel_date_issued ? \Carbon\Carbon::parse($row->personnel_date_issued)->setTimezone('Asia/Manila')->format('M d, Y ') : '-' }}</td>
                        <td>{{ $row->personnel_date_receive ? \Carbon\Carbon::parse($row->personnel_date_receive)->setTimezone('Asia/Manila')->format('M d, Y ') : '-' }}</td>
                        <td>{{ $row->personnel?->personnel_name ?? '-' }}</td>
                        <td>{{ $branchName }}</td>
                        <td>{{ $deptName }}</td>
                        <td>{{ $row->personnel_item_remarks ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </main>
</body>
</html>