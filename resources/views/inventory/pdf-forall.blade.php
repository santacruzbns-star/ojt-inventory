<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>ITEM INVENTORY REPORT</title>
    <style>
        /* 1. Page Margins */
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

        header {
            height: 90px;
            background-color: #ffffff;
            border-bottom: 6px solid #ffffff;
            padding: 0 0 15px 0;
            margin-bottom: 20px;
        }

        .logo {
            height: 110px;
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
                <strong>Items Inventory Report</strong><br>
                National Highway, Lapasan, Cagayan De Oro City<br>
                9000 Misamis Oriental | (088) 856 7111
            </div>
        </div>
    </header>
    <main>
        <h1>Items Inventory Report</h1>
        <table>
            <tbody>
                <tr class="table-header-row">
                    <th>No.</th>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Serial No.</th>
                    <th>UOM</th>
                    <th>Total Qty</th>
                    <th>Remaining Qty</th>
                    <th>Status</th>
                </tr>

                @foreach ($allItems as $item)
                    @php
                        $remarkClass = '';
                        if ($item->item_remark === 'Damaged') {
                            $remarkClass = 'text-danger';
                        } elseif ($item->item_remark === 'Missing') {
                            $remarkClass = 'text-warning';
                        } elseif ($item->item_remark === 'Good') {
                            $remarkClass = 'text-success';
                        }
                    @endphp

                    {{-- Removed the style attribute here --}}
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><strong>{{ $item->item_name }}</strong></td>
                        <td>{{ $item->category?->item_category_name ?? 'Uncategorized' }}</td>
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
