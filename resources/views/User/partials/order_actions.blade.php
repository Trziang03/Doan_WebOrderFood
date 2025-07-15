@php
    $orders = $orders ?? collect(); // tạo collection rỗng nếu chưa có

    // Lọc đơn chưa thanh toán (trạng thái 2)
    $unpaidOrders = $unpaidOrders ?? $orders->filter(fn($o) => $o->order_status_id == 2);

    // Lọc đơn có thể hủy (trạng thái 0 và chưa quá 3 phút)
    $cancelableOrders = $orders->filter(function ($order) {
        return now()->diffInMinutes($order->created_at) < 3 && $order->order_status_id == 0;
    });

    $hasCancelableOrders = $cancelableOrders->count() > 0;
@endphp

<div class="d-flex flex-column flex-md-row justify-content-between gap-2">
    <!-- @if ($hasCancelableOrders)
        <form method="POST" action="/payment/cancel-all" class="mt-2 mt-md-0">
            @csrf
            <button type="submit" class="btn btn-danger w-100 w-md-auto"
                onclick="return confirm('Bạn có chắc muốn hủy tất cả đơn')">
                Hủy tất cả
            </button>
        </form>
    @endif -->

    @if ($unpaidOrders->count())
        <button type="button" class="btn btn-success w-md-auto" data-bs-toggle="modal" data-bs-target="#paymentAllModal">
            Thanh toán tất cả ({{ $unpaidOrders->count() }})
        </button>
    @endif
</div>

<!-- Modal chọn phương thức thanh toán cho tất cả đơn -->
<div class="modal fade" id="paymentAllModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="/payment-all">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Chọn phương thức thanh toán</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body text-center">
                    @php
                        $totalAll = 0;
                        foreach ($unpaidOrders as $order) {
                            foreach ($order->orderItems as $item) {
                                $price = $item->product->price;
                                if ($item->size && strtolower($item->size->name) !== 's') {
                                    $price += $item->size->price ?? 0;
                                }
                                $base = $price * $item->quantity;

                                $toppingTotal = 0;
                                foreach ($item->orderItemToppings as $topping) {
                                    $toppingTotal += $topping->price * $topping->quantity;
                                }

                                $totalAll += $base + $toppingTotal;
                            }
                        }
                        $qrAll = "https://img.vietqr.io/image/VBA-6600029686868-compact.png?amount={$totalAll}&addInfo=Thanh+toan+tat+ca+don";
                    @endphp

                    <!-- COD -->
                    <div class="form-check d-flex justify-content-center align-items-center gap-2">
                        <input class="form-check-input" type="radio" name="payment_method_id" value="1" id="all_cod"
                            required>
                        <label class="form-check-label mb-0" for="all_cod">Thanh toán COD</label>
                    </div>
                    <div id="codAmount" class="text-muted small mb-3 d-none" style="font-size: 30px;">
                        Số tiền: <strong>{{ number_format($totalAll, 0, '.', '.') }}đ</strong>
                    </div>

                    <!-- QR -->
                    <div class="form-check d-flex justify-content-center align-items-center gap-2">
                        <input class="form-check-input" type="radio" name="payment_method_id" value="2" id="all_qr">
                        <label class="form-check-label mb-0" for="all_qr">Chuyển khoản QR</label>
                    </div>

                    <div id="qrImageAllContainer" class="mt-3 text-center d-none">
                        <img src="{{ $qrAll }}" class="img-fluid" style="max-width: 250px;">
                        <p class="text-muted mt-2">Số tiền:
                            <strong>{{ number_format($totalAll, 0, '.', '.') }}đ</strong>
                        </p>
                        <p class="text-muted small">Nội dung chuyển khoản: <strong>Thanh toan tat ca don</strong></p>
                    </div>
                </div>

                <div class="modal-footer justify-content-center">
                    <button type="submit" class="btn btn-primary">Xác nhận thanh toán</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const codRadio = document.getElementById('all_cod');
        const qrRadio = document.getElementById('all_qr');
        const codAmount = document.getElementById('codAmount');
        const qrContainer = document.getElementById('qrImageAllContainer');

        function togglePaymentInfo() {
            if (codRadio.checked) {
                codAmount.classList.remove('d-none');
                qrContainer.classList.add('d-none');
            } else if (qrRadio.checked) {
                codAmount.classList.add('d-none');
                qrContainer.classList.remove('d-none');
            } else {
                codAmount.classList.add('d-none');
                qrContainer.classList.add('d-none');
            }
        }

        codRadio.addEventListener('change', togglePaymentInfo);
        qrRadio.addEventListener('change', togglePaymentInfo);

        // Gọi toggle khi modal mở ra
        const modal = document.getElementById('paymentAllModal');
        modal.addEventListener('shown.bs.modal', function () {
            togglePaymentInfo();
        });
    });
</script>

