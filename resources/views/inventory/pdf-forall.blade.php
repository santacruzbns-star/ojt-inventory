<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>ITEM INVENTORY REPORT</title>
    <style>
        /* 1. Reduced the top margin since the header is no longer fixed */
        @page {
            /* Forces Landscape Mode */
            size: A4 portrait;
            
            /* Reduced top margin to 30px so content flows naturally */
            margin: 30px 25px 80px 25px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 8px; /* Reduced to 8px for a highly compact layout */
            color: #232323;
            text-transform: uppercase;
        }

        /* 2. Removed fixed positioning and negative offsets */
        header {
            height: 90px;
            /* Taller header for the larger logo */
            background-color: #ffffff;
            border-bottom: 6px solid #ffffff;
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
            font-size: 22px; /* Slightly smaller header title */
            font-weight: bold;
            color: #1a252f;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }

        .header-address {
            font-size: 9px; /* Shrunk the address down */
            color: #555;
            line-height: 1.4;
            text-transform: uppercase;
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
            font-size: 8px; /* Shrunk footer */
            color: #777;
            text-transform: uppercase;
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
            font-size: 14px; /* Slightly smaller H1 */
            margin-bottom: 8px;
            margin-top: 0;
            color: #1a252f;
            text-transform: uppercase;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        /* Target both th and td inside tbody */
        tbody th,
        tbody td {
            border: 1px solid #94a3b8;
            padding: 4px 4px; /* Very tight padding for maximum rows */
            text-align: left;
            text-transform: uppercase; 
        }

        /* CRITICAL CHANGE: Style the specific row instead of a generic thead */
        tr.table-header-row th {
            background: #1a252f;
            font-weight: bold;
            color: #ffffff;
            text-transform: uppercase;
            font-size: 8px; /* Match body size */
        }

        /* Optional status colors for the remark text */
        .text-danger { color: #dc3545; font-weight: bold; }
        .text-warning { color: #313131; font-weight: bold; }
        .text-success { color: #155724; font-weight: bold; }
    </style>
</head>

<body>
    <header class="clearfix">
        <img src="{{ public_path('storage/img/login-logo.png') }}" class="logo" alt="Goldtown Logo">
        <div class="header-content">
            <div class="header-title">Goldtown</div>
            <div class="header-address">
                <strong>Items Inventory Report</strong><br>
                National Highway, Lapasan, Cagayan De Oro City<br>
                9000 Misamis Oriental | (088) 856 7111
            </div>
        </div>
    </header>
    <main>
        <h1>Items Inventory Report</h1>
        <table>
            {{-- Removed <thead> to prevent repeating on new pages --}}
            <tbody>
                {{-- Placed the header row inside the body --}}
                <tr class="table-header-row">
                    <th>Product</th>
                    <th>Category</th>
                    <th>Serial No.</th>
                    <th>UOM</th>
                    <th>Total Qty</th>
                    <th>Remaining Qty</th>
                    <th>Status</th>
                </tr>

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