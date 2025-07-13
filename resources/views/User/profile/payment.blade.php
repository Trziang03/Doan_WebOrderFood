@extends('layouts.layouts_user')
@section('title', 'Danh sách đơn hàng')
<style>
    .pagination {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        list-style: none;
        gap: 6px;
        margin-top: 10px !important;
        padding-left: 0 !important;
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
    <div class="container my-5 px-3" style="max-width: 960px;">
        <div class="row text-center mb-3">
            <div class="col-12">
                <h5 class="fw-bold mb-2">
                    @if($latestOrder)
                        Thông tin đặt món: Bàn số {{ $latestOrder->table_id }} | Ngày:
                        {{ $latestOrder->created_at->format('d F, Y') }}
                    @else
                        Chưa có đơn hàng nào.
                    @endif
                </h5>
            </div>

            @if($latestOrder)
                    <div class="col-12" id="order-actions">
                        {!! view('User.partials.order_actions', [
                    'order' => $latestOrder,
                    'unpaidOrders' => $unpaidOrders ?? $orders->filter(fn($o) => $o->order_status_id == 2),
                    'orders' => $orders
                ]) !!}
                    </div>
            @endif
        </div>
        @forelse ($orders as $order)
            @php
                $color = match ($order->orderStatus->name) {
                    'Xác nhận' => 'primary',
                    'Đang chuẩn bị' => 'warning',
                    'Sẵn sàng phục vụ' => 'info',
                    'Chờ thanh toán' => 'danger',
                    'Đã thanh toán' => 'success',
                    'Đã hủy' => 'secondary',
                };
            @endphp
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-8 mb-3 mb-md-0">
                            <p class="mb-1"><strong>Mã đơn: {{ $order->order_code }}</strong></p>
                            <p class="mb-1">
                                <strong>Trạng thái:</strong>
                                <span id="order-status-{{ $order->id }}" class="badge bg-{{ $color }}">
                                    {{ $order->orderStatus->name }}
                                </span>
                            </p>
                            <p class="mb-1"><strong>Thời gian đặt món:</strong> {{ $order->created_at->format('H:i d/m/Y') }}
                            </p>
                            <p class="mb-1"><strong>Tổng số lượng món:</strong> {{ $order->orderItems->sum('quantity') }}</p>
                        </div>

                        <div class="col-12 col-md-4 text-md-end">
                            <h6 class="text-success mb-3">Tổng tiền: {{ number_format($order->total_price) }}đ</h6>

                            <div class="d-flex flex-column flex-md-row justify-content-md-end gap-2">
                                {{-- Nút xem chi tiết --}}
                                <a href="{{ route('user.payment.detail', ['order_code' => $order->order_code]) }}"
                                    class="btn btn-outline-primary btn-sm px-3 w-40 w-md-auto">
                                    Xem chi tiết
                                </a>

                                {{-- Nút huỷ nếu được phép --}}
                                @php
                                    $canCancel = now()->diffInMinutes($order->created_at) < 6 && $order->order_status_id == 0;
                                @endphp

                                @if ($canCancel)
                                    <div id="order-actions-{{ $order->id }}">
                                        <form method="POST" action="/payment/{{ $order->id }}/cancel" class="w-40 w-md-auto">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm px-3 w-md-auto"
                                                onclick="return confirm('Bạn có chắc muốn hủy đơn hàng?')">
                                                Hủy đơn
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-info">Chưa có đơn hàng nào.</div>
        @endforelse
    </div>
    <!-- Modal chọn phương thức thanh toán -->
    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="/payment/{{ $order->id }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Chọn phương thức thanh toán</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method_id" value="1" id="cod">
                            <label class="form-check-label" for="cod">💵 Thanh toán COD</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method_id" value="2" id="qr">
                            <label class="form-check-label" for="qr">🏦 Chuyển khoản QR</label>
                        </div>

                        <!-- Mã QR - sẽ hiển thị nếu chọn chuyển khoản -->
                        @php
                            $total = 0;
                            foreach ($order->orderItems as $item) {
                                $productPrice = $item->product->price;
                                if ($item->size && strtolower($item->size->name) !== 's') {
                                    $productPrice += $item->size->price ?? 0;
                                }
                                $base = $productPrice * $item->quantity;
                                $topping = $item->orderItemToppings->sum(fn($t) => $t->price * $t->quantity);
                                $total += $base + $topping;
                            }

                            $qrLink = "https://img.vietqr.io/image/VBA-6600029686868-compact.png?amount={$total}&addInfo=Thanh+toan+don+" . $order->order_code;
                        @endphp

                        <div id="qrImageContainer" class="mt-3 text-center d-none">
                            <img src="{{ $qrLink }}" alt="Mã QR chuyển khoản Agribank" class="img-fluid"
                                style="max-width: 250px;">
                            <p class="mt-2 mb-0 text-muted">Vui lòng quét mã để chuyển khoản Agribank</p>
                            <p class="text-muted small">Số tiền:
                                <strong>{{ number_format($total, 0, '.', '.') }}đ</strong><br>
                                Nội dung chuyển khoản: <strong>Thanh toan don {{ $order->order_code }}</strong>
                            </p>
                        </div>
                    </div>

                    <div class="modal-footer justify-content-center">
                        <button type="submit" class="btn btn-primary">Xác nhận thanh toán</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <!-- <script>
                                const statusColorMap = {
                                    'Xác nhận': 'primary',
                                    'Đang chuẩn bị': 'warning',
                                    'Sẵn sàng phục vụ': 'info',
                                    'Chờ thanh toán': 'danger',
                                    'Đã thanh toán': 'success',
                                    'Đã hủy': 'secondary'
                                };
                                const orders = @json($orders->map(fn($order) => [
                                    'id' => $order->id,
                                    'status' => $order->orderStatus->name,
                                ]));

                                orders.forEach(order => {
                                    let currentStatus = order.status;
                                    const statusEl = document.getElementById(`order-status-${order.id}`);

                                    if (!statusEl) return;

                                    setInterval(() => {
                                        fetch(`/api/order-status/${order.id}`)
                                            .then(res => res.json())
                                            .then(data => {
                                                if (data.status && data.status !== currentStatus) {
                                                    currentStatus = data.status;
                                                    statusEl.textContent = data.status;
                                                    const newColor = statusColorMap[data.status] || 'primary';
                                                    // Xóa tất cả các class màu hiện tại
                                                    statusEl.className = 'badge bg-' + newColor;
                                                    alertify.success(`Đơn #${order.id} đã cập nhật trạng thái`);

                                                    // Nếu là đơn mới nhất, cập nhật nút hành động
                                                    @if ($latestOrder)
                                                        if (order.id == {{ $latestOrder->id }}) {
                                                            fetch(`/admin/order/${order.id}/actions-html`)
                                                                .then(res => res.text())
                                                                .then(html => {
                                                                    document.getElementById('order-actions').innerHTML = html;
                                                                });
                                                        }
                                                    @endif
                                                    }
                                            })
                                            .catch(err => console.error(`Lỗi đơn hàng ${order.id}:`, err));
                                    }, 5000);
                                });
                            </script> -->
    <script>
        const statusColorMap = {
            'Xác nhận': 'primary',
            'Đang chuẩn bị': 'warning',
            'Sẵn sàng phục vụ': 'info',
            'Chờ thanh toán': 'danger',
            'Đã thanh toán': 'success',
            'Đã hủy': 'secondary'
        };

        const orders = @json($orders->map(fn($order) => [
            'id' => $order->id,
            'status' => $order->orderStatus->name,
        ]));

        orders.forEach(order => {
            let currentStatus = order.status;
            const statusEl = document.getElementById(`order-status-${order.id}`);

            if (!statusEl) return;

            setInterval(() => {
                fetch(`/api/order-status/${order.id}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.status && data.status !== currentStatus) {
                            currentStatus = data.status;
                            statusEl.textContent = data.status;

                            const newColor = statusColorMap[data.status] || 'primary';
                            statusEl.className = 'badge bg-' + newColor;

                            alertify.success(`Đơn #${order.id} đã cập nhật trạng thái`);

                            // Cập nhật lại toàn bộ nút hành động
                            fetch(`/order-actions-html`)
                                .then(res => res.text())
                                .then(html => {
                                    const actionsContainer = document.getElementById('order-actions');
                                    if (actionsContainer) {
                                        actionsContainer.innerHTML = html;
                                    }
                                });
                        }
                    })
                    .catch(err => console.error(`Lỗi đơn hàng ${order.id}:`, err));
            }, 5000);
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const codRadio = document.getElementById('cod');
            const qrRadio = document.getElementById('qr');
            const qrContainer = document.getElementById('qrImageContainer');

            codRadio.addEventListener('change', function () {
                if (codRadio.checked) {
                    qrContainer.classList.add('d-none');
                }
            });

            qrRadio.addEventListener('change', function () {
                if (qrRadio.checked) {
                    qrContainer.classList.remove('d-none');
                }
            });
        });
    </script>
@endsection
