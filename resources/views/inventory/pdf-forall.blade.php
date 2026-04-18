<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Inventory</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
        }

        h1 {
            font-size: 16px;
            margin-bottom: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 4px 6px;
            text-align: left;
        }

        th {
            background: #eee;
        }
    </style>
</head>

<body>
    <h1>Inventory</h1>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Category</th>
                <th>Serial</th>
                <th>UOM</th>
                <th>Total</th>
                <th>Remaining</th>
                <th>Remark</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($allItems as $item)
                <tr>
                    <td>{{ $item->item_name }}</td>
                    <td>{{ $item->category?->item_category_name ?? '-' }}</td>
                    <td>{{ $item->item_serialno ?? '-' }}</td>
                    <td>{{ $item->uom?->item_uom_name ?? '-' }}</td>
                    <td>{{ $item->item_quantity ?? '-' }}</td>
                    <td>{{ $item->item_quantity_remaining ?? '-' }}</td>
                    <td>{{ $item->item_remark ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>