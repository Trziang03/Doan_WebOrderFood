@extends('layouts.layouts_admin')
@section('title', 'Trang quản lý bàn ăn')
@section('active-table', 'active')
<style>
    .btn {
        background-color: rgb(240, 145, 55);
        color: white !important;
        text-align: center;
        padding: 8px;
        width: 100px;
        margin-bottom: 12px;
        border-radius: 4px;
        float: right;
        margin-right: 0px;
        margin-top: 1px;
    }

    .modal {
        position: fixed;
        z-index: 1000;
        padding-top: 50px;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-content {
        margin: auto;
        background-color: #fff;
        padding: 20px;
        border: 1px solid #888;
    }

    .close-btn {
        font-size: 28px;
        background: none;
        border: none;
        color: #000;
        cursor: pointer;
        transition: color 0.3s;
        padding: 0 10px;
    }

    .close-btn:hover {
        color: red;
    }
</style>
@section('content')
    <div class="content" id="banan">
        <div class="head">
            <div class="title">Quản Lý bàn ăn</div>
            <button id="toggleForm" class="btn-toggle">Thêm bàn</button>
        </div>
        <div class="separator_x"></div>
        <!-- Form nhập bàn ăn -->
        <form method="POST" action="{{ route('admin.table.store') }}">
            @csrf
            <div id="tableForm" class="form-section" style="display: none;">
                <div class="form-group-tablee">
                    <div class="form-group-row-table">
                        <div class="form-group-table">
                            <label for="tableNumber">Số bàn</label>
                            <input type="text" id="tableNumber" name="name" class="form-control" placeholder="Nhập số bàn">
                        </div>
                        <div class="form-group-table">
                            <label for="statusSelect">Trạng thái</label>
                            <select name="table_status_id" id="statusSelect" class="form-control">
                                @foreach ($statuses as $status)
                                    <option value="{{ $status->id }}">{{ $status->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group-row-table buttons">
                        <button class="btn" type="submit">Thêm</button>
                        <button class="btn" type="button" onclick="hideForm()">Hủy</button>
                    </div>
                </div>
            </div>
        </form>

        @php
            $totalSlots = 12;
            $tableCount = count($tables);
            $emptySlots = $totalSlots - $tableCount;
        @endphp

        <div class="grid-container">
            {{-- Hiển thị bàn thật --}}
            @foreach ($tables as $table)
                <div class="table-box">
                    <div class="table-title">{{ $table->name }}</div>
                    <div class="table-status">{{ $table->status->name }}</div>
                    @if ($table->qr_image_path)
                        <div class="table-qr">
                            <img src="{{ asset('storage/' . $table->qr_image_path) }}" width="80">
                        </div>
                    @endif
                    <div class="table-actions">
                        <button class="btn-action" onclick='openEditPopup(@json($table))'><i
                                class="fa-regular fa-pen-to-square"></i></button>
                        <button onclick="showQR({{ $table->id }})">📷</button>
                    </div>
                </div>
            @endforeach

            {{-- Thêm ô trống nếu còn thiếu --}}
            @for ($i = 1; $i <= $emptySlots; $i++)
                <div class="table-box"></div>
            @endfor
        </div>
    </div>

    <!-- Popup sửa bàn ăn -->
    <div id="optionPopup" style="display: none;">
        <div class="popup-content" style="padding: 5px 20px;">
            <button class="btn-close-popup" onclick="closeEditPopup()">×</button>
            <h4>Cập nhật bàn ăn</h4>

            <form id="editTableForm" method="POST" action="{{ route('admin.table.update', ['id' => $table->id]) }}">
                @csrf
                <label for="editName">Số hiệu bàn</label>
                <input type="text" id="editName" name="name" value="{{ $table->name }}">

                <label for="editStatus">Trạng thái</label>
                <select id="editStatus" name="table_status_id">
                    @foreach ($statuses as $status)
                        <option value="{{ $status->id }}" {{ $table->table_status_id == $status->id ? 'selected' : '' }}>
                            {{ $status->name }}
                        </option>
                    @endforeach
                </select>

                <div class="form-check">
                    <label for="editQR">Đổi mã QR</label>
                    <input type="checkbox" id="editQR" name="regen_qr">
                </div>

                <label>URL gọi món</label>
                <p id="editQrUrl" style="font-size: 13px; word-break: break-word;">
                    {{ asset('storage/qr-codes/' . $table->qr_code) }}
                </p>

                <button type="submit" style="margin-left: 105px; margin-top: 10px;">Lưu thay đổi</button>
            </form>
        </div>
    </div>

    <!-- Modal hiển thị QR -->
    <div id="qrModal" class="modal" style="display: none;">
        <div class="modal-content" style="width: 350px; border-radius: 10px;">
            <div style="text-align: right;">
                <button class="close-btn" onclick="closeQRModal()">×</button>
            </div>
            <h3 style="text-align: center; margin-top: 0; font-size: 24px; margin-bottom: 10px;">Mã QR</h3>
            <div style="text-align: center; padding: 10px;">
                <div style="border: 10px solid #ff9900; padding: 15px; background: #fff8e7; border-radius: 20px;">
                    <img id="qrCode" src="" alt="QR Code" style="width: 200px; height: 200px;" />
                    <div style="font-weight: bold; margin-top: 10px;" id="qrTableName">Bàn ...</div>
                </div>
            </div>

            <div style="display: flex; justify-content: center; gap: 10px; margin-top: 15px;">
                <a id="downloadQR" class="btn btn-success" download target="_blank">Tải QR Code</a>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        const btn = document.getElementById('toggleForm');
        const form = document.getElementById('tableForm');

        btn.addEventListener('click', () => {
            if (form.style.display === 'none') {
                form.style.display = 'block';
                btn.textContent = 'Ẩn thêm';
            } else {
                form.style.display = 'none';
                btn.textContent = 'Thêm bàn';
            }
        });

        function openEditPopup(table) {
            const form = document.getElementById('editTableForm');
            form.action = '/admin/table/update/' + table.id;

            document.getElementById('editName').value = table.name;
            document.getElementById('editStatus').value = table.table_status_id;
            document.getElementById('editQrUrl').innerText = table.qr_table ?? 'Chưa có';

            document.getElementById('optionPopup').style.display = 'block';
        }

        function closeEditPopup() {
            document.getElementById('optionPopup').style.display = 'none';
        }

        function hideForm() {
            document.getElementById('tableForm').style.display = 'none';
            document.getElementById('toggleForm').textContent = 'Thêm bàn';
        }
    </script>

    <script>
        function showQR(tableId) {
            const table = window.tables.find(t => t.id === tableId);

            if (!table || !table.qr_code) {
                alert('Không tìm thấy QR code cho bàn này');
                return;
            }

            const qrImageUrl = '/storage/qr-codes/' + table.qr_code;

            document.getElementById('qrCode').src = qrImageUrl;
            document.getElementById('qrTableName').innerText = 'Bàn ' + table.name;
            document.getElementById('downloadQR').href = qrImageUrl;

            document.getElementById('qrModal').style.display = 'block';
        }

        function closeQRModal() {
            document.getElementById('qrModal').style.display = 'none';
        }

        // Dữ liệu bàn từ Laravel truyền vào JavaScript
        window.tables = @json($tables);
    </script>
@endsection
