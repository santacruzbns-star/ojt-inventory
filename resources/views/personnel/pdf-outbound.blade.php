<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Outbound Records</title>
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
    <h1>Personnel &amp; Outbound</h1>
    <table>
        <thead>
            <tr>
                <th>Custodian</th>
                <th>Item</th>
                <th>Serial</th>
                <th>Qty</th>
                <th>Issued</th>
                <th>Received</th>
                <th>Branch</th>
                <th>Dept</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($outbounds as $row)
                <tr>
                    <td>{{ $row->personnel?->personnel_name ?? '-' }}</td>
                    <td>{{ $row->item?->item_name ?? '-' }}</td>
                    <td>{{ $row->item?->item_serialno ?? '-' }}</td>
                    <td>{{ $row->personnel_item_quantity }}</td>
                    <td>{{ $row->personnel_date_issued ? \Carbon\Carbon::parse($row->personnel_date_issued)->format('Y-m-d') : '-' }}
                    </td>
                    <td>{{ $row->personnel_date_receive ? \Carbon\Carbon::parse($row->personnel_date_receive)->format('Y-m-d') : '-' }}
                    </td>
                    <td>{{ $row->personnel?->branch?->branch_name ?? '-' }}</td>
                    <td>{{ $row->personnel?->branch?->branch_department ?? '-' }}</td>
                    <td>{{ $row->personnel_item_remarks ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
