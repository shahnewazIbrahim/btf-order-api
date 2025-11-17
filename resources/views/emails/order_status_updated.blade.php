@component('mail::message')
# Order Status Updated

Hello {{ $order->customer->name ?? 'Customer' }},

Your order **#{{ $order->id }}** status has changed.

- Previous status: **{{ ucfirst($oldStatus) }}**
- New status: **{{ ucfirst($newStatus) }}**

@component('mail::button', ['url' => url('/')])
View Order
@endcomponent

Thanks,
{{ config('app.name') }}
@endcomponent
