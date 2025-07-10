<div id="order-detail-popup-content">
    <div id="chitietdonhang">
        <div class="head">
            <div class="title">Chi Tiết Đơn Hàng: <strong>{{ $order->order_code }}</strong></div>
        </div>

        <div class="separator_x"></div>

        <div class="order-info" id="printArea">
            <div class="bill-container" id="bill-content">
                <h2 style="text-align: center">HÓA ĐƠN</h2>

                <table class="food-table" style="margin-bottom: 15px; font-size: 14px;">
                    <tr>
                        <td><strong>Mã đơn:</strong> {{ $order->order_code }}</td>
                        <td><strong>Bàn:</strong> {{ $order->table->name ?? 'Không có' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Phương thức thanh toán:</strong>
                            {{ $order->paymentMethod->name_method ?? '---' }}
                        </td>
                        <td><strong>Trạng thái đơn:</strong> {{ $order->orderStatus->name ?? '---' }}</td>
                    </tr>
                    <tr>
                        <td colspan="2"><strong>Thời gian tạo:</strong>
                            {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i:s') }}</td>
                    </tr>
                </table>

                <table class="food-table" cellpadding="6">
                    <thead>
                        <tr>
                            <th>Món ăn</th>
                            <th>Size</th>
                            <th>Số lượng</th>
                            <th>Đơn giá</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderItems as $item)
                            <tr style="text-align: center;">
                                <td style="width: 150px;">{{ $item->product->name ?? '[Đã xóa]' }}</td>
                                <td>{{ $item->size->name ?? '---' }}</td>
                                <td style="width: 100px;">{{ $item->quantity }}</td>
                                <td>
                                    {{ number_format($item->product->price, 0, '.', '.') }}đ
                                    @if(!empty($item->size) && $item->size->price > 0)
                                        + {{ number_format($item->size->price, 0, '.', '.') }}đ (Size)
                                    @endif
                                </td>
                            </tr>

                            @if($item->toppings && $item->toppings->count())
                                <tr>
                                    <td colspan="6" style="padding: 10px 10px; background: #f9f9f9;">
                                        <strong>Topping đã chọn:</strong>
                                        <table style="margin-top: 5px; border-collapse: collapse">
                                            <thead style="background: #eaeaea;">
                                                <tr>
                                                    <th style="padding: 4px;">Tên topping</th>
                                                    <th style="padding: 4px;">Số lượng</th>
                                                    <th style="padding: 4px;">Giá</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($item->toppings as $topping)
                                                    <tr style="text-align: center;">
                                                        <td style="padding: 5px;">{{ $topping->name }}</td>
                                                        <td style="padding: 5px;">{{ $topping->pivot->quantity }}</td>
                                                        <td style="padding: 5px;">
                                                            {{ number_format($topping->pivot->price, 0, '.', '.') }}đ
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>

                {{-- Tổng tiền --}}
                <div style="text-align: center; font-size: 25px; margin-top: 5px; font-weight: bold">
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
                    @endphp
                    <strong>Tổng tiền:</strong> {{ number_format($total, 0, '.', '.') }}đ
                </div>
            </div>
        </div>
        <div class="text-center" style="margin-bottom: 25px; margin-right: 70px; margin-top: 5px;">
            <button id="btnPrint" class="btn btn-warning">🖨️ In đơn hàng</button>
        </div>
    </div>
</div>
<script>
    document.getElementById("btnPrint").addEventListener("click", function () {
        var printContents = document.getElementById("printArea").innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        location.reload(); // reload lại để trở về như cũ (nếu cần)
    });
</script>
<script>
    window.addEventListener("paymentMethodUpdated", function(e) {
        document.getElementById("paymentMethodDisplay").textContent = e.detail.method_name;
    });
</script>
