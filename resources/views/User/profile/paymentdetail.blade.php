@extends('layouts.layouts_user')
@section('title', 'Chi tiết đơn hàng')
<style>
.pagination {
    justify-content: center;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 10px !important;
    margin-bottom: 10px;
}

.page-link {
    position: relative;
    display: block;
    color: black !important;
    text-decoration: none;
    background-color: #fff;
    border: 1px solid #dee2e6;
    padding: 6px 12px;
    border-radius: 6px;
    transition: 0.3s;
    font-size: 14px;
}

.page-link:hover {
    color: white !important;
    background-color: orange !important;
    border-color: orange !important;
}

.page-item.active .page-link {
    color: white !important;
    background-color: orange !important;
    border-color: orange !important;
}

/* Ẩn số trang trên mobile, chỉ hiển thị Prev/Next */
@media (max-width: 576px) {
    .pagination li:not(.page-item:first-child):not(.page-item:last-child):not(.active) {
        display: none;
    }
}

    @media (max-width: 768px) {
        .table-responsive {
            display: none;
        }

        .order-card {
            display: block;
        }
    }

    @media (min-width: 769px) {
        .order-card {
            display: none;
        }
    }
</style>
@section('content')
<div class="order-container container py-4">
    <div class="row gy-4">
        <!-- Order Summary -->
        <div class="col-12 col-md-4">
            <div class="card h-100">
                <div class="card-header bg-light fw-bold d-flex justify-content-between">
                    <span>Mã đơn: {{ $order->order_code }}</span>
                </div>
                <div class="card-body">
                    <p><strong>Ngày:</strong> {{ $order->created_at->format('H:i d/m/Y') }}</p>
                    <p><strong>Bàn:</strong> {{ $order->table->name }}</p>
                    <p><strong>Tổng số lượng món:</strong> {{ $order->orderItems->sum('quantity') }}</p>
                    <p><strong>Trạng thái:</strong> <span id="order-status" data-order-id="{{ $order->id }}">{{ $order->orderStatus->name }}</span></p>
                    <h5 class="text-success">Tổng tiền: {{ number_format($order->total_price) }}đ</h5>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="col-12 col-md-8">
            <!-- Table View (Desktop only) -->
            <div class="table-responsive d-none d-md-block">
                <table class="table table-bordered align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th style="width: 40%;">Món ăn</th>
                            <th>Giá</th>
                            <th style="width: 20%;">Ghi chú</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orderItems as $item)
                            <tr>
                                <td class="d-flex align-items-center gap-2">
                                    <img src="{{ asset($item->product->image_food) }}" alt="{{ $item->product->name }}" style="width: 70px; height: 70px; object-fit: cover;">
                                    <div>
                                        <strong>{{ $item->product->name ?? 'Sản phẩm đã xoá' }} x {{ $item->quantity }}</strong><br>
                                        @if($item->size)
                                            Size: {{ $item->size->name }} (+{{ number_format($item->size->price) }}đ)<br>
                                        @endif
                                        @if($item->toppings->count())
                                            Topping:
                                            <ul class="mb-0 ps-3">
                                                @foreach($item->toppings as $topping)
                                                    <li>{{ $topping->name }} ({{ $topping->pivot->quantity }} x {{ number_format($topping->pivot->price) }}đ)</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">{{ number_format($item->product->price) }}đ</td>
                                <td class="text-center">{{ $item->note }}</td>
                                <td class="text-center"><strong>{{ number_format($item->total_price) }}đ</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile View (Card per item) -->
            <div class="d-md-none">
                @foreach ($orderItems as $item)
                    <div class="card mb-3">
                        <div class="card-body d-flex flex-column gap-2">
                            <div class="d-flex gap-3">
                                <img src="{{ asset($item->product->image_food) }}" alt="{{ $item->product->name }}"
                                     style="width: 80px; height: 80px; object-fit: cover;" class="rounded">
                                <div>
                                    <h6 class="fw-bold mb-1">{{ $item->product->name ?? 'Sản phẩm đã xoá' }} x {{ $item->quantity }}</h6>
                                    @if($item->size)
                                        <div>Size: {{ $item->size->name }} (+{{ number_format($item->size->price) }}đ)</div>
                                    @endif
                                    @if($item->toppings->count())
                                        <div>Topping:
                                            <ul class="mb-0 ps-3">
                                                @foreach($item->toppings as $topping)
                                                    <li>{{ $topping->name }} ({{ $topping->pivot->quantity }} x {{ number_format($topping->pivot->price) }}đ)</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <div class="text-muted">Giá: {{ number_format($item->product->price) }}đ</div>
                                <div class="fw-bold text-success">Tổng: {{ number_format($item->total_price) }}đ</div>
                            </div>
                            @if($item->note)
                                <div><strong>Ghi chú:</strong> {{ $item->note }}</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- Pagination -->
    <div class="mt-4">
        {{ $orderItems->withQueryString()->links() }}
    </div>
</div>
@endsection
@section('script')
<script>
    const orderId = {{ $order->id }};
    const statusEl = document.getElementById('order-status');
    let currentStatus = statusEl.textContent.trim();

    function updateOrderActionsHtml(orderId) {
        fetch(`/admin/order/${orderId}/actions-html`)
            .then(res => res.text())
            .then(html => {
                document.getElementById('order-actions').innerHTML = html;
            })
            .catch(err => {
                console.error('Lỗi khi cập nhật nút hành động:', err);
            });
    }

    setInterval(() => {
        fetch(`/api/order-status/${orderId}`)
            .then(res => res.json())
            .then(data => {
                if (data.status && data.status !== currentStatus) {
                    currentStatus = data.status;
                    statusEl.textContent = data.status;
                    alertify.success("Trạng thái đơn hàng đã được cập nhật!");

                    updateOrderActionsHtml(orderId); // cập nhật lại nút
                }
            })
            .catch(err => console.error("Lỗi khi lấy trạng thái:", err));
    }, 5000);
</script>
@endsection
