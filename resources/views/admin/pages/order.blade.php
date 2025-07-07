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
    /* css cái chuông thông báo */
    .bell-notification {
        position: relative;
        display: inline-block;
        margin-right: 16px;
        cursor: pointer;
        font-size: 20px;
        color: #333;
        transition: color 0.3s ease;
    }

    .bell-notification:hover {
        color: #ff6f00;
    }

    .bell-badge {
        position: absolute;
        top: -6px;
        right: -10px;
        background-color: red;
        color: white;
        font-size: 11px;
        font-weight: bold;
        padding: 2px 6px;
        border-radius: 50%;
        box-shadow: 0 0 3px rgba(0,0,0,0.3);
        animation: pulse 1s infinite;
    }

    /* Hiệu ứng rung nhẹ */
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.15); }
        100% { transform: scale(1); }
    }

</style>
</style>
@section('content')
    <div class="content" id="donhang">
        <div class="head">
            <div class="title">Quản Lý Đơn Hàng</div>
            <div class="search">   
                <div class="bell-notification">
                    <i class="fas fa-bell"></i>
                    @if ($pendingCount > 0)
                        <span class="bell-badge">{{ $pendingCount }}</span>
                    @endif
                </div>       
                <form action="{{ route('admin.order') }}" method="GET">
                    <input type="text" name="keyword" placeholder="Tìm mã đơn hoặc bàn..." value="{{ request('keyword') }}">
                    <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
            </div>
        </div>

        <div class="separator_x"></div>

        {{-- ACTIVE TAB --}}
        @php
            $activeTab = session('tab') ?? (request('tab') ?? 'xacnhan');
        @endphp

        {{-- <div class="tabs">
            <div class="{{ $activeTab === 'xacnhan' ? 'active' : '' }}" data-target="xacnhan">Xác nhận</div>
            <div class="{{ $activeTab === 'dangchuanbi' ? 'active' : '' }}" data-target="dangchuanbi">Đang chuẩn bị</div>
            <div class="{{ $activeTab === 'daphucvu' ? 'active' : '' }}" data-target="daphucvu">Đã phục vụ</div>
            <div class="{{ $activeTab === 'chuathanhtoan' ? 'active' : '' }}" data-target="chuathanhtoan">Chưa thanh toán
            </div>
            <div class="{{ $activeTab === 'dathanhtoan' ? 'active' : '' }}" data-target="dathanhtoan">Đã thanh toán</div>
        </div> --}}
        <div class="tabs">
            <div class="{{ $activeTab === 'xacnhan' ? 'active' : '' }}" data-target="xacnhan">
                Xác nhận ({{ $statusCounts['xacnhan'] ?? 0 }})
            </div>
            <div class="{{ $activeTab === 'dangchuanbi' ? 'active' : '' }}" data-target="dangchuanbi">
                Đang chuẩn bị ({{ $statusCounts['dangchuanbi'] ?? 0 }})
            </div>
            <div class="{{ $activeTab === 'daphucvu' ? 'active' : '' }}" data-target="daphucvu">
                Đã phục vụ ({{ $statusCounts['daphucvu'] ?? 0 }})
            </div>
            <div class="{{ $activeTab === 'chuathanhtoan' ? 'active' : '' }}" data-target="chuathanhtoan">
                Chưa thanh toán ({{ $statusCounts['chuathanhtoan'] ?? 0 }})
            </div>
            <div class="{{ $activeTab === 'dathanhtoan' ? 'active' : '' }}" data-target="dathanhtoan">
                Đã thanh toán ({{ $statusCounts['dathanhtoan'] ?? 0 }})
            </div>
        </div>

        @php
            $statuses = [
                ['id' => 0, 'name' => 'xacnhan'],
                ['id' => 1, 'name' => 'dangchuanbi'],
                ['id' => 2, 'name' => 'daphucvu'],
                ['id' => 3, 'name' => 'chuathanhtoan'],
                ['id' => 4, 'name' => 'dathanhtoan'],
            ];
        @endphp

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
                            <th style="width: 10%;">Trạng thái</th>
                            <th style="width: 13%;">Thời gian</th>
                            <th style="width: 8%;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $orders = App\Models\Order::where('order_status_id', $status['id'])
                                ->orderBy('updated_at', 'desc')
                                ->paginate(7, ['*'], $status['name']);
                        @endphp

                        @foreach ($orders as $order)
                            <tr>
                                <td style="text-align: center;">{{ $order->order_code }}</td>
                                <td style="text-align: center;">{{ $order->table->name ?? 'N/A' }}</td>
                                <td>
                                    @foreach ($order->orderItems as $item)
                                        {{ $item->product->name ?? '---' }} - "Size {{ $item->size->name ?? '' }}"
                                        x{{ $item->quantity }}<br>
                                    @endforeach
                                </td>
                                <td style="text-align: center;">{{ $order->note }}</td>
                                <td style="text-align: center;">
                                    @php
                                        $total = 0;
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
                                            $total += $base + $toppingTotal;
                                        }
                                    @endphp
                                    {{ number_format($total, 0, '.', '.') }}đ
                                </td>
                                <td style="text-align: center;">{{ $order->orderStatus->name }}</td>
                                <td style="text-align: center;">{{ $order->created_at->format('d/m/Y H:i:s') }}</td>
                                <td style="text-align: center;">
                                    @if ($status['id'] !== 4)
                                        <a href="{{ route('admin.order.change-status', ['id' => $order->id]) }}?tab={{ $status['name'] }}"
                                            class="btn-action">
                                            <i class="fa-solid fa-sync-alt" style="color: #007bff;"></i>
                                        </a>
                                    @endif

                                    @if ($status['id'] === 4)
                                        <a href="javascript:void(0)" class="btn-action view-order-detail"
                                            data-id="{{ $order->id }}">
                                            <i class="fa-solid fa-eye" style="color: green;"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- PHÂN TRANG GIỮ TAB --}}
                <div style="text-align: center;">
                    {{ $orders->appends(['tab' => $status['name']])->links() }}
                </div>
            </div>
        @endforeach

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
                success: function(data) {
                    alert(data);
                    location.reload();
                },
                error: function(xhr) {
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
@endsection
