@extends('layouts.layouts_user')
@section('title', 'Thanh toán')
@section('content')
    <div class="popup_payment">
        <div class="overflow_payment"></div>
        <div class="popup_payment_base payment_cod">
            <p><i class="fas fa-times"></i></p>
            <h4>Xác nhận đơn hàng - COD</h4>
            <div>
                <p>Đơn hàng của bạn đã được ghi nhận</p>
                <p>Cảm ơn bạn đã tin tưởng mua hàng tại Sinh Viên Nghiêm Túc shop</p>
            </div>
            <div>
                <button><a href="">Xem chi tiết đơn hàng</a></button>
                <button><a href="">Tiếp tục mua sắm</a></button>
            </div>
        </div>
        <div class="popup_payment_base payment_banking">
            <p><i class="fas fa-times"></i></p>
            <h4>Xác nhận đơn hàng - Banking</h4>
            <div>
                <p>Đơn hàng của bạn đã được ghi nhận</p>
                <p>Cảm ơn bạn đã tin tưởng mua hàng tại Sinh Viên Nghiêm Túc shop</p>
                <div class="content_banking">
                    <p>Nội dung chuyển khoản : 0123456789 HD001</p>
                    <p>Đơn hàng sẽ tự động hủy nếu chưa thanh toán trong vòng 3 ngày</p>
                </div>
            </div>
            <div>
                <button><a href="">Xem chi tiết đơn hàng</a></button>
                <button><a href="">Tiếp tục mua sắm</a></button>
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
                        @foreach($orderItems as $item)
                            <li class="list-group-item">
                                <div>
                                    <h4>{{ $item->product->name ?? 'Sản phẩm đã xoá' }}</h4>
                                    <p>Giá gốc: {{ number_format($item->product->price) }}đ</p>
                                    @if ($item->size)
                                        <p>Size: {{ $item->size->name }} (+ {{ number_format($item->size->price) }}đ)</p>
                                    @endif

                                    @if ($item->toppings->count())
                                        <p>Topping:</p>
                                        <ul>
                                            @foreach ($item->toppings as $topping)
                                                <li>
                                                    {{ $topping->name }} ({{ $topping->pivot->quantity }} x
                                                    {{ number_format($topping->pivot->price) }}đ)
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif

                                    <p>Số lượng: {{ $item->quantity }}</p>
                                    <p>Ghi chú: {{ $item->note }}</p>
                                    <strong>Thành tiền:</strong> {{ number_format($item->total_price, 0, ',', '.') }}đ
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <!-- Nút mở popup -->
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#paymentModal">
                    Thanh toán
                </button>
                <div class="pagination">
                    {{ $orderItems->links() }}
                </div>
            </div>
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
                        <input class="form-check-input" type="radio" name="payment_method_id" value="1" required>
                        <label class="form-check-label">💵 Thanh toán COD</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method_id" value="2" required>
                        <label class="form-check-label">🏦 Chuyển khoản QR</label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Xác nhận thanh toán</button>
                </div>

            </div>
        </div>
    </div>
@endsection
@section('script')

@endsection
