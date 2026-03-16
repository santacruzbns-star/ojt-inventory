<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Item PDF</title>
    <style>
      
    </style>
</head>
<body>

    <h1>Inventory Item Details</h1>
    <p>Date: {{ now()->format('F j, Y') }}</p>

    <table>
        <tr>
            <th>Product Name</th>
            <td>{{ $item->item_name }}</td>
        </tr>
        <tr>
            <th>Category</th>
            <td>{{ $item->category->item_category_name ?? '-' }}</td>
        </tr>
        <tr>
            <th>Brand</th>
            <td>{{ $item->brand->item_brand_name ?? '-' }}</td>
        </tr>
        <tr>
            <th>Serial Number</th>
            <td>{{ $item->item_serialno ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Unit of Measure</th>
            <td>{{ $item->uom->item_uom_name ?? '-' }}</td>
        </tr>
        <tr>
            <th>Quantity</th>
            <td>{{ $item->item_quantity ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>{{ ucfirst($item->item_remark) ?? 'N/A' }}</td>
        </tr>
    </table>

</body>
</html>