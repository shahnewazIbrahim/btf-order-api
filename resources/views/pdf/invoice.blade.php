<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $order->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { margin-bottom: 20px; }
        .items table { width: 100%; border-collapse: collapse; }
        .items th, .items td { border: 1px solid #ddd; padding: 6px; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
<div class="header">
    <h2>Invoice #{{ $order->id }}</h2>
    <p>Date: {{ $order->created_at->format('Y-m-d H:i') }}</p>
    <p>Customer: {{ $order->customer->name ?? 'N/A' }} ({{ $order->customer->email ?? '' }})</p>
</div>

<div class="items">
    <table>
        <thead>
        <tr>
            <th>Product</th>
            <th>Variant</th>
            <th class="text-right">Qty</th>
            <th class="text-right">Price</th>
            <th class="text-right">Total</th>
        </tr>
        </thead>
        <tbody>
        @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product->name ?? 'N/A' }}</td>
                <td>{{ $item->variant->sku ?? '-' }}</td>
                <td class="text-right">{{ $item->quantity }}</td>
                <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                <td class="text-right">{{ number_format($item->total_price, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<p class="text-right" style="margin-top: 10px;">
    <strong>Subtotal:</strong> {{ number_format($order->subtotal, 2) }}<br>
    <strong>Discount:</strong> {{ number_format($order->discount, 2) }}<br>
    <strong>Total:</strong> {{ number_format($order->total, 2) }}
</p>
</body>
</html>
