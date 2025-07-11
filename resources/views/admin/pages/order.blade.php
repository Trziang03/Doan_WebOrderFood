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
        margin-right: 55px;
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

    <style>.tabs {
        display: flex;
        gap: 10px;
        margin-bottom: 10px;
    }

    .tabs>div {
        padding: 8px 16px;
        background-color: #eee;
        border-radius: 5px;
        cursor: pointer;
    }

    .tabs .active {
        background-color: #007bff;
        color: white;
        font-weight: bold;
    }

    .table {
        display: none;
    }

    .table.active {
        display: block;
    }

    .btn-action {
        margin: 0 5px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    table th,
    table td {
        padding: 8px;
        border: 1px solid #ddd;
        vertical-align: top;
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

        @if (request('keyword'))
            {{-- HIỂN THỊ KẾT QUẢ TÌM KIẾM --}}
            <div class="table active">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 12%;">Mã đơn</th>
                            <th style="width: 8%;">Mã bàn</th>
                            <th style="width: 25%;">Món ăn</th>
                            <th style="width: 14%;">Ghi chú</th>
                            <th style="width: 10%;">Tổng tiền</th>
                            <th style="width: 10%;">Trạng thái</th>
                            <th style="width: 13%;">Thời gian</th>
                            <th style="width: 8%;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($orders->count() > 0)
                            @foreach ($orders as $order)
                                <tr>
                                    <td>{{ $order->order_code }}</td>
                                    <td>{{ $order->table->name ?? '-' }}</td>
                                    <td>
                                        @foreach ($order->orderItems as $item)
                                            {{ $item->product->name }} (x{{ $item->quantity }})<br>
                                        @endforeach
                                    </td>
                                    <td>{{ $order->note }}</td>
                                    <td>{{ number_format($order->total_price) }}đ</td>
                                    <td>{{ $order->orderStatus->name }}</td>
                                    <td>{{ $order->updated_at->format('d/m/Y H:i') }}</td>
                                    <td style="text-align: center;">
                                        <a href="{{ route('admin.order.change-status', ['id' => $order->id]) }}"
                                            class="btn-action">
                                            <i class="fa-solid fa-sync-alt" style="color: #007bff;"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 20px; color: #a94442;">
                                    Không tìm thấy kết quả phù hợp với từ khóa "<strong>{{ request('keyword') }}</strong>".
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                <div class="pagination-wrapper">
                    {{ $orders->appends(request()->query())->links() }}
                </div>
            </div>
        @else
            {{-- HIỂN THỊ THEO TABS --}}
            @php
                $activeTab = session('tab') ?? (request('tab') ?? 'xacnhan');

                $statuses = [
                    ['id' => 0, 'name' => 'xacnhan', 'label' => 'Xác nhận'],
                    ['id' => 1, 'name' => 'dangchuanbi', 'label' => 'Đang chuẩn bị'],
                    ['id' => 2, 'name' => 'daphucvu', 'label' => 'Đã phục vụ'],
                    ['id' => 3, 'name' => 'chothanhtoan', 'label' => 'Chờ thanh toán'],
                    ['id' => 4, 'name' => 'dathanhtoan', 'label' => 'Đã thanh toán'],
                    ['id' => 5, 'name' => 'dahuy', 'label' => 'Đã hủy'],
                ];
            @endphp

            <div class="tabs">
                @foreach ($statuses as $status)
                    <div class="{{ $activeTab === $status['name'] ? 'active' : '' }}" data-target="{{ $status['name'] }}">
                        {{ $status['label'] }} ({{ $statusCounts[$status['name']] ?? 0 }})
                    </div>
                @endforeach
            </div>

            @foreach ($statuses as $status)
                <div class="table {{ $activeTab === $status['name'] ? 'active' : '' }}" id="{{ $status['name'] }}">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 12%;">Mã đơn</th>
                                <th style="width: 8%;">Mã bàn</th>
                                <th style="width: 25%;">Món ăn</th>
                                <th style="width: 14%;">Ghi chú</th>
                                <th style="width: 10%;">Tổng tiền</th>
                                @if ($status['id'] === 3)
                                    <th style="width: 10%;">Thanh toán</th>
                                @endif
                                <th style="width: 13%;">Thời gian</th>
                                <th style="width: 8%;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ordersByStatus[$status['name']] ?? [] as $index => $order)
                                <tr @if ($index === 0 && $status['id'] === 0) class="order-row" data-id="{{ $order->id }}" @endif>
                                    <td>{{ $order->order_code }}</td>
                                    <td>{{ $order->table->name ?? '-' }}</td>
                                    <td>
                                        @foreach ($order->orderItems as $item)
                                            <div style="margin-bottom: 8px;">
                                                <strong>
                                                    {{ $item->product->name }} (x{{ $item->quantity }})
                                                    @if ($item->size)
                                                        - Size: {{ $item->size->name }}
                                                    @endif
                                                </strong><br>
                                                @if ($item->toppings && $item->toppings->count() > 0)
                                                    <ul style="margin: 0 0 0 15px; padding-left: 0; list-style-type: disc;">
                                                        @foreach ($item->toppings as $topping)
                                                            <li>{{ $topping->name }} (x{{ $topping->pivot->quantity }})
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </div>
                                        @endforeach
                                    </td>
                                    <td>{{ $order->note }}</td>
                                    <td>{{ number_format($order->total_price) }}đ</td>
                                    @if ($status['id'] === 3)
                                        <td>{{ $order->paymentMethod->name_method ?? 'Chưa chọn' }}</td>
                                    @endif
                                    <td>{{ $order->updated_at->format('d/m/Y H:i') }}</td>
                                    <td style="text-align: center;">
                                        @if ($status['id'] === 5)
                                            {{-- Nút xoá đơn hàng --}}
                                            <button type="button" class="btn-action"
                                                onclick="showDeleteOrderPopup({{ $order->id }}, '{{ route('admin.order.delete', ['id' => $order->id]) }}')">
                                                <i class="fa-regular fa-trash-can" style="color: red; font-size: 18px;"></i>
                                            </button>
                                        @elseif ($status['id'] === 4)
                                            {{-- Nút xem chi tiết --}}
                                            <a href="javascript:void(0)" class="btn-action view-order-detail"
                                                data-id="{{ $order->id }}">
                                                <i class="fa-solid fa-eye" style="color: green;"></i>
                                            </a>
                                        @else
                                            {{-- Nút đổi trạng thái --}}
                                            <a href="{{ route('admin.order.change-status', ['id' => $order->id]) }}?tab={{ $status['name'] }}"
                                                class="btn-action">
                                                <i class="fa-solid fa-sync-alt" style="color: #007bff;"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if (isset($ordersByStatus[$status['name']]))
                        <div class="pagination-wrapper">
                            {{ $ordersByStatus[$status['name']]->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            @endforeach
        @endif

        <!-- Modal -->
        <div id="orderDetailModal" class="modal" style="display:none;">
            <div class="modal-content">
                <span class="close-modal">&times;</span>
                <div id="orderDetailContent">Đang tải...</div>
            </div>
        </div>
        <!-- Popup Xóa -->
        <div class="popup_admin" id="popupOrderDelete" style="display: none;">
            <h3 style="color: white;">Bạn có thật sự muốn xóa đơn hàng này?</h3>
            <p style="color: white;">* Đơn hàng bị xóa sẽ không thể khôi phục *</p>

            <label style="color:white;">
                Nhập từ <strong style="color: yellow;">XÓA</strong> để xác nhận:
            </label>
            <input type="text" id="orderConfirmInput" placeholder="Nhập XÓA..." />

            <div style="margin-top: 10px;">
                <input type="checkbox" id="orderConfirmCheckbox" />
                <label for="orderConfirmCheckbox" style="color: white;">Tôi đồng ý với hành động này</label>
            </div>

            <p id="orderAlert" style="color: red;"></p>

            <div class="button">
                <form id="orderDeleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="confirm" id="orderConfirmValue">
                    <button type="submit" id="orderDeleteBtn" disabled>Đồng ý</button>
                    <button type="button" onclick="cancel('order')">Hủy</button>
                </form>
            </div>
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
        $(document).ready(function() {
            $('.view-order-detail').click(function() {
                var id = $(this).data('id');
                $('#orderDetailModal').show();
                $('#orderDetailContent').html('Đang tải...');
                $.get('/admin/order/detail/' + id, function(data) {
                    $('#orderDetailContent').html(data);
                });
            });

            $('.close-modal').click(function() {
                $('#orderDetailModal').hide();
            });

            // Đóng khi bấm ra ngoài
            $(window).click(function(e) {
                if ($(e.target).is('#orderDetailModal')) {
                    $('#orderDetailModal').hide();
                }
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const activeTab = urlParams.get('tab');

            if (activeTab) {
                document.querySelectorAll('.tabs > div').forEach(tab => tab.classList.remove('active'));
                document.querySelectorAll('.table').forEach(table => table.classList.remove('active'));

                const selectedTab = document.querySelector(`.tabs > div[data-target="${activeTab}"]`);
                const selectedTable = document.getElementById(activeTab);

                if (selectedTab && selectedTable) {
                    selectedTab.classList.add('active');
                    selectedTable.classList.add('active');
                }
            }
        });
    </script>
    <script>
        document.querySelectorAll('.tabs > div').forEach(tab => {
            tab.addEventListener('click', function() {
                const target = this.getAttribute('data-target');
                const url = new URL(window.location.href);

                url.searchParams.set('tab', target);
                window.location.href = url.toString();
            });
        });
    </script>
    <script>
        function showDeleteOrderPopup(orderId, route) {
            const popup = document.getElementById('popupOrderDelete');
            const form = document.getElementById('orderDeleteForm');

            document.getElementById('orderConfirmInput').value = '';
            document.getElementById('orderConfirmCheckbox').checked = false;
            document.getElementById('orderDeleteBtn').disabled = true;
            document.getElementById('orderAlert').innerText = '';

            form.action = route;
            popup.style.display = 'block';
        }

        function cancel(type) {
            if (type === 'order') {
                document.getElementById('popupOrderDelete').style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('orderConfirmInput');
            const checkbox = document.getElementById('orderConfirmCheckbox');
            const deleteBtn = document.getElementById('orderDeleteBtn');
            const confirmValue = document.getElementById('orderConfirmValue');
            const alert = document.getElementById('orderAlert');

            function validateDelete() {
                const inputText = input.value.trim();
                const isConfirmed = inputText === 'XÓA';
                const isChecked = checkbox.checked;

                deleteBtn.disabled = !(isConfirmed && isChecked);
                alert.innerText = (!isConfirmed && inputText !== '') ? 'Bạn phải nhập đúng từ "XÓA"' : '';
                confirmValue.value = inputText;
            }

            input.addEventListener('input', validateDelete);
            checkbox.addEventListener('change', validateDelete);
        });
    </script>

<script>
    // B1: Lấy ID đơn hàng hiện tại
    let latestOrderId = localStorage.getItem('latestOrderId') ?? (document.querySelector('.order-row')?.dataset?.id ?? 0);
    console.log('ID đơn hiện tại:', latestOrderId);

    // B2: Đặt vòng lặp kiểm tra mỗi 10 giây
    setInterval(() => {
        fetch('/api/admin/orders/latest')
            .then(res => res.json())
            .then(data => {
                console.log('ID đơn mới từ server:', data.id);

                if (parseInt(data.id) > parseInt(latestOrderId)) {
                    latestOrderId = data.id;
                    localStorage.setItem('latestOrderId', data.id); // cập nhật lại

                    alertify.set('notifier', 'delay', 10);
                    alertify.success(`Đơn hàng mới: ${data.code}`);

                    setTimeout(() => {
                        location.reload();
                    }, 5000);
                }
            })
            .catch(() => {
                console.error("Không thể kiểm tra đơn hàng mới.");
            });
    }, 10000);
</script>
@endsection
