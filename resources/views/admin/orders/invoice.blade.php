<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $order->order_number }} — Mandira Foods</title>
    <link href="{{ asset('css/admin-invoice.css') }}" rel="stylesheet">
</head>
<body class="invoice-body">
    <div class="invoice-toolbar no-print">
        <button type="button" onclick="window.print()" class="invoice-btn">Print / Save PDF</button>
        <a href="{{ route('admin.orders.show', $order) }}" class="invoice-btn invoice-btn--muted">Back to order</a>
    </div>

    <article class="invoice">
        <header class="invoice__header">
            <div>
                <h1 class="invoice__brand">Mandira Foods</h1>
                <p class="invoice__tagline">Premium dried fruits &amp; snacks</p>
            </div>
            <div class="invoice__meta">
                <h2>Invoice</h2>
                <p><strong>{{ $order->order_number }}</strong></p>
                <p>Date: {{ $order->placed_at?->format('M j, Y') }}</p>
            </div>
        </header>

        <section class="invoice__parties">
            <div>
                <h3>Bill to</h3>
                <p>{{ $order->customer_name }}<br>
                {{ $order->customer_email }}<br>
                {{ $order->customer_phone }}</p>
            </div>
            <div>
                <h3>Ship to</h3>
                <p>{{ $order->formattedShippingAddress() }}</p>
            </div>
        </section>

        <table class="invoice__table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="num">Qty</th>
                    <th class="num">Unit price</th>
                    <th class="num">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                    <tr>
                        <td>{{ $item->product_title }}</td>
                        <td class="num">{{ $item->quantity }}</td>
                        <td class="num">Rs. {{ number_format((float) $item->unit_price, 2) }}</td>
                        <td class="num">Rs. {{ number_format((float) $item->line_total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="num">Subtotal</td>
                    <td class="num">Rs. {{ number_format((float) $order->subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="num">Shipping</td>
                    <td class="num">Rs. {{ number_format((float) $order->shipping_amount, 2) }}</td>
                </tr>
                <tr class="invoice__total-row">
                    <td colspan="3" class="num">Total ({{ $order->currency }})</td>
                    <td class="num">Rs. {{ number_format((float) $order->total, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <footer class="invoice__footer">
            <p>
                <strong>Payment:</strong>
                {{ $order->payment_method->value === 'cod' ? 'Cash on delivery' : ucfirst($order->payment_gateway ?? 'Online') }}
                — {{ ucfirst($order->payment_status->value) }}
            </p>
            <p>
                <strong>Order status:</strong> {{ ucfirst($order->status->value) }}
                &nbsp;|&nbsp;
                <strong>Delivery:</strong> {{ ucfirst($order->delivery_status->value) }}
            </p>
            @if ($order->notes)
                <p><strong>Notes:</strong> {{ $order->notes }}</p>
            @endif
            <p class="invoice__thanks">Thank you for your order.</p>
        </footer>
    </article>
</body>
</html>
