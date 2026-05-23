@props(['orders', 'emptyMessage' => 'No orders yet. When you place an order, it will appear here.'])

@if ($orders->isEmpty())
    <p class="account-empty">{{ $emptyMessage }}</p>
    <a href="{{ route('shop') }}" class="account-btn account-btn--primary account-empty__action">Start shopping</a>
@else
    <ul class="account-orders">
        @foreach ($orders as $order)
            @php
                $statusClass = 'account-orders__status--' . $order->status->value;
            @endphp
            <li class="account-orders__item">
                <div class="account-orders__main">
                    <a href="{{ route('account.orders.show', $order->order_number) }}" class="account-orders__link">
                        <strong>{{ $order->order_number }}</strong>
                    </a>
                    <span>{{ $order->placed_at->format('M j, Y') }} &middot; {{ $order->items->count() }}
                        item{{ $order->items->count() === 1 ? '' : 's' }}</span>
                </div>
                <div class="account-orders__meta">
                    <span class="account-orders__price">Rs. {{ number_format((float) $order->total, 0) }}</span>
                    <span class="account-orders__status {{ $statusClass }}">
                        {{ ucfirst(str_replace('_', ' ', $order->status->value)) }}
                    </span>
                </div>
            </li>
        @endforeach
    </ul>
@endif
