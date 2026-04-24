<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>OUTBOUND REPORT</title>
    <style>
        /* Define page margins to accommodate header and footer */
        @page {
            /* Forces Landscape Mode */
            size: A4 portrait;
            
            /* Increased top margin to 130px to fit the larger logo and header */
            margin: 130px 25px 80px 25px;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #232323;
        }

        /* Clean White Header */
        header {
            position: fixed;
            top: -130px;
            /* Match the top page margin */
            left: -25px;
            right: -25px;
            height: 90px;
            /* Taller header for the larger logo */
            background-color: #ffffff;
            border-bottom: 6px solid #ffffff;
            padding: 15px 25px 5px 25px;
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
        }

        /* Fixed Footer */
        footer {
            position: fixed;
            bottom: -50px;
            left: 0px;
            right: 0px;
            height: 30px;
            border-top: 1px solid #ccc;
            padding-top: 5px;
            font-size: 10px;
            color: #777;
        }

        .footer-date {
            float: left;
        }

        .footer-page {
            float: right;
        }

        /* Automatically increments page number */
        .page-number:before {
            content: counter(page);
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

        th,
        td {
            border: 1px solid #94a3b8;
            padding: 9px 8px; /* Breathing room */
            text-align: left;
        }

        th {
            background: #1a252f;
            font-weight: bold;
            color: #ffffff;
            text-transform: uppercase;
            font-size: 10px;
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
            <thead>
                <tr>
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
            </thead>
            <tbody>
                @php
                    // Tracking colors for the combination of Branch AND Department
                    $branchDeptColors = [];

                    // A varied palette of light colors: Yellows, Greens, Blues, Peaches, Purples
                    $rowPalette = [
                        '#fef08a', // Light Yellow
                        '#dcfce7', // Light Green
                        '#e0f2fe', // Light Blue
                        '#ffedd5', // Light Orange/Peach
                        '#f3e8ff', // Light Purple
                        '#ccfbf1', // Light Teal
                        '#fce7f3', // Light Pink
                        '#fef9c3', // Pale Yellow
                        '#dbeafe', // Pale Blue
                    ];
                    $colorIndex = 0;
                @endphp

                @foreach ($outbounds as $row)
                    @php
                        // Get branch and department names
                        $branchName = $row->personnel?->branch?->branch_name ?? 'Unassigned';
                        $deptName = $row->personnel?->branch?->branch_department ?? 'Unassigned';

                        // Create a unique key for this specific combination
                        $comboKey = $branchName . '-' . $deptName;

                        // Assign a background color for the row based on the branch+dept combination
                        if (!isset($branchDeptColors[$comboKey])) {
                            $branchDeptColors[$comboKey] = $rowPalette[$colorIndex % count($rowPalette)];
                            $colorIndex++;
                        }
                    @endphp

                    <tr style="background-color: {{ $branchDeptColors[$comboKey] }};">
                        <td>{{ $row->item?->category?->item_category_name ?? '-' }}</td>
                        
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