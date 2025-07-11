@php
    $canCancel = now()->diffInMinutes($order->created_at) < 3 && $order->order_status_id == 0;
@endphp

@if ($order->order_status_id == 2)
    <button class="btn btn-success mt-2 mt-md-0" data-bs-toggle="modal" data-bs-target="#paymentModal">
        Thanh toán
    </button>
@else
    <button class="btn btn-secondary mt-2 mt-md-0" disabled>
        Thanh toán
    </button>
@endif

@if ($canCancel)
    <form method="POST" action="/payment/{{ $order->id }}/cancel" class="mt-2 mt-md-0">
        @csrf
        <button type="submit" class="btn btn-danger" onclick="return confirm('Bạn có chắc muốn hủy đơn hàng?')">
            Hủy đơn
        </button>
    </form>
@endif
