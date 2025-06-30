@extends('layouts.layouts_user')
@section('title', 'Thanh toán')
@section('content')
    <div class="popup_payment">
        <div class="overflow_payment"></div>
        <div class="popup_payment_base payment_cod">
            <p><i class="fas fa-times"></i></p>
            <h4>Xác nhận đơn hàng - COD</h4>
            <div>
                <p>Thực đơn của bạn đã được ghi nhận</p>
                <p>Cảm ơn bạn đã tin tưởng sử dụng dịch vụ tại GiDu Food</p>
            </div>
            <div>
                <button><a href="">Xem chi tiết Thực đơn</a></button>
                <button><a href="">Tiếp tục Chọn món</a></button>
            </div>
        </div>
        <div class="popup_payment_base payment_banking">
            <p><i class="fas fa-times"></i></p>
            <h4>Xác nhận đơn hàng - Banking</h4>
            <div>
                <p>Thực đơn của bạn đã được ghi nhận</p>
                <p>Cảm ơn bạn đã tin tưởng sử dụng dịch vụ tại GiDu Food</p>
                <div class="content_banking">
                        <a href=""><img src="./images/QR.jpg" alt="Lỗi" style="height: 100px;"></a>
                </div>
            </div>
            <div>
                <button><a href="">Xem chi tiết Thực đơn</a></button>
                <button><a href="">Tiếp tục Chọn món</a></button>
            </div>
        </div>
    </div>
    <div class="payments container_css">
        <div class="payment">
            <div class="payment_left">
                <div class="payment_products">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="card mb-3">
                        <div class="card-header">Mã đơn: <strong>{{ $order->order_code }}</strong></div>
                        <div class="card-body">
                            <p><strong>Bàn:</strong> {{ $order->table_id }}</p>
                            <p>Trạng thái: {{ $order->orderStatus->name }}</p>
                            <p><strong>Tổng tiền:</strong> {{ number_format($order->total_price, 0, ',', '.') }}đ</p>
                        </div>
                    </div>

                    <h4>Danh sách món:</h4>
                    <ul class="list-group mb-3">
                        @foreach($order->orderItems as $item)
                            <li class="list-group-item">
                                @foreach ($order->orderItems as $item)
                                    <div>
                                        <h4>{{ $item->product->name }}</h4>
                                        <p>Giá gốc: {{ number_format($item->product->price) }}đ</p>
                                        @if ($item->size)
                                            <p>Size: {{ $item->size->name }} (+ {{ number_format($item->size->price) }}đ)</p>
                                        @endif
                                        <p>Topping</p>
                                        @if ($item->toppings->count())
                                                @foreach ($item->toppings as $topping)
                                                    <li>
                                                        {{ $topping->name }} ({{ $topping->pivot->quantity }} x
                                                        {{ number_format($topping->pivot->price) }}đ)
                                                    </li>
                                                @endforeach
                                        @endif
                                        <p>Số lượng: {{ $item->quantity }}</p>
                                        <p>Ghi chú: {{ $item->note }}</p>
                                    </div>
                                @endforeach
                                <div><strong>Thành tiền:</strong> {{ number_format($item->total_price, 0, ',', '.') }}đ</div>
                            </li>
                        @endforeach
                    </ul>
                    <div class="method_payment">
                        <table>
                            <tbody>
                                <tr>
                                    <td colspan="3">
                                        <h6>Phương thức thanh toán</h6>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 10%;"><input id="cod" type="radio" style="font-size: 14px;" checked
                                            name="method_payment" value="COD"></td>
                                    <td style="width: 10%;"><img src="./images/iconcod.jpg" alt="Lỗi" style="height: 35px">
                                    </td>
                                    <td>
                                        <label for="cod" style="font-weight:500">Thanh toán khi nhận hàng
                                            (COD)</label>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 10%;"><input type="radio" style="font-size: 14px;" id="method_payment"
                                            name="method_payment" value="banking"></td>
                                    <td style="width: 10%;"><img src="./images/iconbanking.jpg" alt="Lỗi"
                                            style="height: 35px"></td>
                                    <td>
                                        <label for="method_payment" style="font-weight:500">Chuyển khoản qua ngân hàng
                                            (Banking)</label>
                                    </td>
                                </tr>
                               
                            </tbody>
                        </table>
                    </div>
                </div>
                </form>
                <button type="button" id="" onclick="order()">Gửi thực đơn</button>
            </div>
        </div>
    </div>
@endsection
@section('script')  

@endsection
