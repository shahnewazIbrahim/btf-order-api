@component('mail::message')
# Low Stock Alert

Variant: **{{ $inventory->variant->sku }}**
Product: **{{ $inventory->variant->product->name }}**

**Current Stock: {{ $inventory->stock }}**
Threshold: {{ $inventory->low_stock_threshold }}

Please restock soon.

Thanks,
{{ config('app.name') }}
@endcomponent
