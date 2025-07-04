@extends('layouts.layouts_admin')
@section('title', 'Trang quản lý đơn hàng')
@section('active-order', 'active')
<style>
    .btn {
        background-color: rgb(240, 145, 55);
        color: white !important;
        text-align: center;
        padding: 6px;
        width: 130px;
        margin-bottom: 12px;
        border-radius: 4px;
        float: right;
        margin-right: 0px;
        margin-top: 1px;
    }

    .filter {
        margin-top: 3px;
        margin-right: 5px;
        padding: 7px 30px;
        border-radius: 5px;
    }

    td,
    th {
        word-wrap: break-word;
        word-break: break-word;
    }
</style>
@section('content')
    <div class="content" id="donhang">
        <div class="head">
            <div class="title">Quản Lý Đơn Hàng</div>
            <div class="search">
                <form action="{{ route('admin.order') }}" method="GET">
                    <input type="text" name="keyword" placeholder="Tìm mã đơn hoặc bàn..." value="{{ request('keyword') }}">
                    <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
            </div>
        </div>

        <div class="separator_x"></div>

        <div class="table">
            <table>
                <thead>
                    <tr>
                        <th style="width: 25px;">Mã đơn</th>
                        <th style="width: 10px;">Mã bàn</th>
                        <th style="width: 60px;">Món ăn</th>
                        <th style="width: 15px;">Tổng tiền</th>
                        <th style="width: 30px;">Trạng thái</th>
                        <th style="width: 20px;">Thời gian</th>
                        <th style="width: 10px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td style="text-align: center;">{{ $order->order_code }}</td>
                            <td style="text-align: center;">{{ $order->table->name ?? 'N/A' }}</td>
                            <td>
                                @if (!empty($order->orderItems))
                                    @foreach ($order->orderItems as $item)
                                        {{ $item->product->name ?? '---' }} - "Size {{ $item->size->name ?? '' }}"
                                        x{{ $item->quantity }}
                                    @endforeach
                                @endif
                            </td>
                            <td style="text-align: center;">
                                @php
                                    $total = 0;

                                    foreach ($order->orderItems as $item) {
                                        // Nếu size là S hoặc không có size, giữ nguyên giá
                                        $productPrice = $item->product->price;
                                        if ($item->size && strtolower($item->size->name) !== 's') {
                                            $productPrice += $item->size->price ?? 0; // thêm tiền size nếu có
                                        }

                                        $basePrice = $productPrice * $item->quantity;

                                        $toppingTotal = 0;
                                        foreach ($item->orderItemToppings as $topping) {
                                            $toppingTotal += $topping->price * $topping->quantity;
                                        }

                                        $total += $basePrice + $toppingTotal;
                                    }
                                @endphp
                                {{ number_format($total, 0, '.', '.') }}đ
                            </td>
                            <td style="text-align: center;">{{ $order->orderStatus->name }}</td>
                            <td style="text-align: center;">{{ $order->created_at->format('d/m/Y H:i:s') }}</td>
                            <td style="text-align: center;">
                                <!-- Nút 1: Thay đổi trạng thái -->
                                <a href="{{ route('admin.order.change-status', $order->id) }}" class="btn-action">
                                    <i class="fa-solid fa-sync-alt" style="color: #007bff;"></i>
                                </a>

                                <a href="javascript:void(0)" class="btn-action view-order-detail" data-id="{{ $order->id }}">
                                    <i class="fa-solid fa-eye" style="color: green;"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center;">Không có đơn hàng nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Modal -->
        <div id="orderDetailModal" class="modal" style="display:none;">
            <div class="modal-content">
                <span class="close-modal">&times;</span>
                <div id="orderDetailContent">Đang tải...</div>
            </div>
        </div>
        <!-- Popup Xóa -->
        <!-- <div class="popup_admin" id="popupxoa" style="display: none;">
                    <h3 style="color: white;">Bạn có thật sự muốn xóa đơn hàng ... ?</h3>
                    <p style="color: white;">* Đơn hàng bị xóa sẽ không thể khôi phục nữa *</p>
                    <p id="alert"></p>
                    <div class="button">
                        <button onclick="deleteOrder(this.dataset.id)">Đồng ý</button>
                        <button onclick="cancel('xoa')">Hủy</button>
                    </div>
                </div> -->
        <div class="pagination">
            {{ $orders->links() }}
        </div>  
    </div>
@endsection

@section('script')
    <!-- <script>
                function showDeletePopup(full_name, id) {
                    let popup = document.getElementById('popupxoa');
                    popup.children[0].textContent = `Bạn có thật sự muốn xóa đơn hàng của khách hàng ${full_name} ?`;
                    popup.querySelector("button[onclick^='deleteOrder']").dataset.id = id;
                    popup.style.display = "block";
                }

                function deleteOrder(id) {
                    $.ajax({
                        type: "POST",
                        url: `/admin/order/delete/${id}`,
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (data) {
                            alert(data);
                            location.reload();
                        },
                        error: function (xhr) {
                            alert('Có lỗi xảy ra: ' + xhr.responseText);
                        }
                    });
                    document.getElementById('popupxoa').style.display = "none";
                }

                function cancel(type) {
                    document.getElementById(`popup${type}`).style.display = "none";
                }
            </script> -->
    <script>
        $(document).ready(function () {
            $('.view-order-detail').click(function () {
                var id = $(this).data('id');
                $('#orderDetailModal').show();
                $('#orderDetailContent').html('Đang tải...');
                $.get('/admin/order/detail/' + id, function (data) {
                    $('#orderDetailContent').html(data);
                });
            });

            $('.close-modal').click(function () {
                $('#orderDetailModal').hide();
            });

            // Đóng khi bấm ra ngoài
            $(window).click(function (e) {
                if ($(e.target).is('#orderDetailModal')) {
                    $('#orderDetailModal').hide();
                }
            });
        });
    </script>
@endsection
