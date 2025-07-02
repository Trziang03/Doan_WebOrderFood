@extends('layouts.layouts_user')
@section('title', 'Thanh toán')
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
    <div class="container my-4">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
    <h5 class="fw-bold mb-0">
        Thông tin đặt món: Bàn số {{ $order->table_id }} | Ngày: {{ $order->created_at->format('d F, Y') }}
    </h5>

    <button class="btn btn-success mt-2 mt-md-0" data-bs-toggle="modal" data-bs-target="#paymentModal">
        Thanh toán
    </button>
</div>
        <div class="row mt-3 gy-3">
            <!-- Order Summary -->
            <div class="col-12 col-md-4">
                <div class="card">
                    <div class="card-header bg-light fw-bold d-flex justify-content-between">
                        <span>Order ID: {{ $order->order_code }}</span>
                    </div>
                    <div class="card-body">
                        <p><strong>Ngày:</strong> {{ $order->created_at->format('H:i d/m/Y') }}</p>
                        <p><strong>Bàn:</strong> {{ $order->table->name }}</p>
                        <p><strong>Tổng số lượng món:</strong> {{ $order->orderItems->sum('quantity') }}</p>
                        <p><strong>Trạng thái:</strong> {{ $order->orderStatus->name }}</p>
                        <h5 class="text-success">Tổng tiền: {{ number_format($order->total_price) }}đ</h5>
                    </div>
                </div>
            </div>
            <!-- Product List -->
            <div class="col-12 col-md-8">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="text-align: center; width: 40%;">Món ăn</th>
                                <th style="text-align: center;">Giá</th>
                                <th style="text-align: center;width: 20%;">Ghi chú</th>
                                <th style="text-align: center;">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orderItems as $item)
                                <tr>
                                    <td class="d-flex align-items-center gap-2">
                                        <img src="{{ asset($item->product->image_food) }}" alt="{{ $item->product->name }}"
                                            style="width: 70px; height: 70px; object-fit: cover;">
                                        <div>
                                            <strong>{{ $item->product->name ?? 'Sản phẩm đã xoá' }} x
                                                {{ $item->quantity }}</strong><br>
                                            @if($item->size)
                                                Size: {{ $item->size->name }} (+{{ number_format($item->size->price) }}đ)<br>
                                            @endif
                                            @if($item->toppings->count())
                                                Topping:
                                                <ul class="mb-0 ps-3">
                                                    @foreach($item->toppings as $topping)
                                                        <li>{{ $topping->name }} ({{ $topping->pivot->quantity }} x
                                                            {{ number_format($topping->pivot->price) }}đ)</li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </div>
                                    </td>
                                    <td style="text-align: center;">{{ number_format($item->product->price) }}đ</td>
                                    <td style="text-align: center;">{{ $item->note }}</td>
                                    <td style="text-align: center;"><strong>{{ number_format($item->total_price) }}đ</strong>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Mobile-friendly order display -->
                <div class="order-card">
                    @foreach($orderItems as $item)
                        <div class="card mb-3">
                            <div class="card-body d-flex flex-column gap-2">
                                <div class="d-flex gap-3">
                                    <img src="{{ asset($item->product->image_food) }}" alt="{{ $item->product->name }}"
                                        style="width: 80px; height: 80px; object-fit: cover;" class="rounded">
                                    <div>
                                        <h6 class="fw-bold mb-1">{{ $item->product->name ?? 'Sản phẩm đã xoá' }} x
                                            {{ $item->quantity }}</h6>
                                        @if($item->size)
                                            <div>Size: {{ $item->size->name }} ({{ number_format($item->size->price) }}đ)</div>
                                        @endif
                                        @if($item->toppings->count())
                                            <div>Topping:
                                                <ul class="mb-0 ps-3">
                                                    @foreach($item->toppings as $topping)
                                                        <li>{{ $topping->name }} ({{ $topping->pivot->quantity }} x
                                                            {{ number_format($topping->pivot->price) }}đ)</li>
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
        <div class="d-flex justify-content-center mt-4">
            {{ $orderItems->links() }}
        </div>
    </div>

    <!-- Modal chọn phương thức thanh toán -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">🔐 Chọn phương thức thanh toán</h5>
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

                <!-- Mã QR - Ẩn ban đầu -->
                <div id="qrImageContainer" class="mt-3 d-none text-center">
                    <img src="{{ asset('images/QR.png') }}" alt="Mã QR chuyển khoản" class="img-fluid" style="max-width: 250px;">
                    <p class="mt-2 mb-0 text-muted">Vui lòng quét mã để chuyển khoản</p>
                </div>
            </div>

            <div class="modal-footer justify-content-center">
                <button type="submit" class="btn btn-primary">Xác nhận thanh toán</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const codRadio = document.getElementById('cod');
        const qrRadio = document.getElementById('qr');
        const qrContainer = document.getElementById('qrImageContainer');

        function toggleQR() {
            if (qrRadio.checked) {
                qrContainer.classList.remove('d-none');
            } else {
                qrContainer.classList.add('d-none');
            }
        }

        codRadio.addEventListener('change', toggleQR);
        qrRadio.addEventListener('change', toggleQR);
    });
</script>
@endsection
