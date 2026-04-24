<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>INVENTORY REPORT</title>
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

        /* Optional status colors for the remark text */
        .text-danger { color: #dc3545; font-weight: bold; }
        .text-warning { color: #856404; font-weight: bold; }
        .text-success { color: #155724; font-weight: bold; }
    </style>
</head>

<body>
    <header class="clearfix">
        <img src="{{ public_path('storage/img/login-logo.png') }}" class="logo" alt="Goldtown Logo">
        <div class="header-content">
            <div class="header-title">Goldtown</div>
            <div class="header-address">
                <strong>Master Inventory Report</strong><br>
                National Highway, Lapasan, Cagayan De Oro City<br>
                9000 Misamis Oriental | (088) 856 7111
            </div>
        </div>
    </header>
    <main>
        <h1>Master Inventory</h1>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Serial No.</th>
                    <th>UOM</th>
                    <th>Total Qty</th>
                    <th>Remaining Qty</th>
                    <th>Remark / Status</th>
                </tr>
            </thead>
            <tbody>
                @php
                    // Tracking colors to group items by their Category
                    $categoryColors = [];

                    // The same pastel palette
                    $rowPalette = [
                        '#e0f2fe', // Light Blue
                        '#fef08a', // Light Yellow
                        '#dcfce7', // Light Green
                        '#f3e8ff', // Light Purple
                        '#ffedd5', // Light Orange/Peach
                        '#ccfbf1', // Light Teal
                        '#fce7f3', // Light Pink
                        '#fef9c3', // Pale Yellow
                        '#dbeafe', // Pale Blue
                    ];
                    $colorIndex = 0;
                @endphp

                @foreach ($allItems as $item)
                    @php
                        $catName = $item->category?->item_category_name ?? 'Uncategorized';

                        // Assign a background color based on the Category
                        if (!isset($categoryColors[$catName])) {
                            $categoryColors[$catName] = $rowPalette[$colorIndex % count($rowPalette)];
                            $colorIndex++;
                        }
                        
                        // Text styling for remarks
                        $remarkClass = '';
                        if ($item->item_remark === 'Damaged') $remarkClass = 'text-danger';
                        if ($item->item_remark === 'Missing') $remarkClass = 'text-warning';
                        if ($item->item_remark === 'Good') $remarkClass = 'text-success';
                    @endphp

                    <tr style="background-color: {{ $categoryColors[$catName] }};">
                        <td><strong>{{ $item->item_name }}</strong></td>
                        <td>{{ $catName }}</td>
                        <td>{{ $item->item_serialno ?? '-' }}</td>
                        <td>{{ $item->uom?->item_uom_name ?? '-' }}</td>
                        <td>{{ $item->item_quantity ?? '0' }}</td>
                        <td>
                            <strong>{{ $item->item_quantity_remaining ?? '0' }}</strong>
                        </td>
                        <td class="{{ $remarkClass }}">{{ $item->item_remark ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </main>
</body>

</html>