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

    /* popup cập nhật bàn */

    /* Overlay nền xám mờ */
    #optionPopup {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background-color: rgba(0, 0, 0, 0.4);
        z-index: 999;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Nội dung popup */
    #optionPopup .popup-content {
        background-color: white;
        /* cam đậm */
        padding: 25px 30px;
        border-radius: 12px;
        width: 350px;
        position: relative;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
        color: #212121;
        font-family: 'Segoe UI', sans-serif;
    }

    /* Tiêu đề */
    #optionPopup h4 {
        margin-top: 0;
        margin-bottom: 20px;
        font-size: 22px;
        font-weight: bold;
        text-align: center;
        color: #212121;
    }

    /* Nút đóng */
    .btn-close-popup {
        position: absolute;
        top: 12px;
        right: 15px;
        background: #ef6c00;
        color: white;
        border: none;
        font-size: 18px;
        font-weight: bold;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        line-height: 26px;
        text-align: center;
        cursor: pointer;
        transition: 0.2s;
    }

    .btn-close-popup:hover {
        background-color: #e65100;
    }

    /* Input và select */
    #optionPopup input,
    #optionPopup select {
        width: 100%;
        padding: 8px 10px;
        margin-bottom: 15px;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        box-shadow: inset 0 0 0 1px #ccc;
        transition: 0.2s;
    }

    #optionPopup input:focus,
    #optionPopup select:focus {
        outline: none;
        box-shadow: inset 0 0 0 2px #29b6f6;
    }

    /* Label */
    #optionPopup label {
        font-weight: 500;
        margin-bottom: 5px;
        display: block;
    }

    /* Nút lưu */
    #optionPopup button[type="submit"] {
        background-color: #43a047;
        /* xanh lá */
        color: white;
        border: none;
        padding: 10px 20px;
        font-weight: bold;
        font-size: 14px;
        border-radius: 6px;
        cursor: pointer;
        margin-top: 10px;
        transition: 0.2s;
        display: block;
        margin-left: auto;
        margin-right: auto;
    }

    #optionPopup button[type="submit"]:hover {
        background-color: #2e7d32;
    }

    /* Đoạn URL */
    #optionPopup p#editQrUrl {
        font-size: 13px;
        color: #fff;
        word-break: break-word;
        margin-bottom: 0;
    }


    /* popup thêm bàn */
    /* Nền overlay */
    .popup-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.3);
        z-index: 999;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Nội dung popup */
    .popup-content {
        background: #fff;
        border-radius: 12px;
        padding: 30px 35px;
        width: 250px;
        position: relative;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        font-family: 'Segoe UI', sans-serif;
    }

    /* Nút đóng */
    .btn-close-popup {
        position: absolute;
        top: 12px;
        right: 15px;
        background: #f57c00;
        color: white;
        border: none;
        font-size: 18px;
        font-weight: bold;
        cursor: pointer;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        line-height: 26px;
        text-align: center;
        transition: 0.2s;
    }

    .btn-close-popup:hover {
        background: #e65100;
    }

    /* Tiêu đề */
    .popup-content h4 {
        margin-top: 0;
        margin-bottom: 20px;
        font-size: 20px;
        font-weight: 600;
        color: #333;
    }

    /* Form labels */
    .popup-content label {
        display: block;
        margin-bottom: 6px;
        font-weight: 500;
        color: #333;
    }

    /* Form inputs */
    .popup-content input,
    .popup-content select {
        width: 100%;
        padding: 8px 10px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 14px;
        transition: 0.2s;
    }

    .popup-content input:focus,
    .popup-content select:focus {
        border-color: #f57c00;
        outline: none;
        box-shadow: 0 0 0 2px rgba(245, 124, 0, 0.2);
    }

    /* Buttons */
    .popup-content .btn {
        background-color: #f57c00;
        color: white;
        border: none;
        padding: 10px 0;
        width: 100%;
        font-size: 15px;
        font-weight: 500;
        border-radius: 6px;
        cursor: pointer;
        margin-top: 5px;
        transition: 0.2s;
    }

    .popup-content .btn:hover {
        background-color: #e65100;
    }

    .popup-content .form-buttons {
        display: flex;
        margin-top: 10px;
    }

    .popup-content .btn {
        background-color: #f57c00;
        color: white;
        border: none;
        padding: 8px 16px;
        font-size: 14px;
        font-weight: 500;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.2s;
        width: auto;
    }

    .popup-content .btn:hover {
        background-color: #e65100;
    }
</style>
@section('content')
    <div class="content" id="banan">
        <div class="head">
            <div class="title">Quản Lý bàn ăn</div>
            <!--<button id="toggleForm" class="btn-toggle">Thêm bàn</button>-->
        </div>
        <div class="separator_x"></div>

        <!-- Popup thêm bàn -->
        <div id="addTablePopup" class="popup-overlay" style="display: none;">
            <div class="popup-content">
                <button class="btn-close-popup" onclick="hideForm()">×</button>
                <h4>Thêm bàn ăn</h4>

                <form method="POST" action="{{ route('admin.table.store') }}" id="addTableForm">
                    @csrf
                    <div class="form-group-table">
                        <label for="tableNumber">Số bàn</label>
                        <input type="text" name="name" id="tableNumber" class="form-control" placeholder="Nhập số bàn"
                            required oninput="validateNewTableName()">
                        <span id="tableNameError" style="color: red; font-size: 12px;"></span>
                    </div>

                    <div class="form-group-table">
                        <label for="statusSelect">Trạng thái</label>
                        <select name="table_status_id" id="statusSelect" class="form-control" required>
                            @foreach ($statuses as $status)
                                <option value="{{ $status->id }}">{{ $status->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-buttons">
                        <button class="btn" type="submit">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>


        @php
            $totalSlots = 12;
            $tableCount = count($tables);
            $emptySlots = $totalSlots - $tableCount;
        @endphp

        <div class="grid-container">
            {{-- Hiển thị bàn thật --}}
            @foreach ($tables as $table)
                <div class="table-box" data-id="{{ $table->id }}">
                    <div class="table-title">{{ $table->name }}</div>
                    <div class="table-status">{{ $table->status->name }}</div>
                    @if ($table->qr_image_path)
                        <div class="table-qr">
                            <img src="{{ asset('storage/qr-codes/' . $table->qr_code) }}" width="80">
                        </div>
                    @endif
                    <div class="table-actions">
                        <button class="btn-action" onclick='openEditPopup(@json($table))'><i
                                class="fa-regular fa-pen-to-square"></i></button>
                        <button onclick="showQR({{ $table->id }})"><i class="fa fa-qrcode"></i></button>
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
        <div class="popup-content" style="padding: 5px 20px;" id="qr_container">
            <button class="btn-close-popup" onclick="closeEditPopup()">×</button>
            <h4>Cập nhật bàn ăn</h4>

            <form id="editTableForm" method="POST" action="">
                @csrf
                <label for="editName">Số hiệu bàn</label>
                <div class="col">
                    <input type="text" id="editName" name="name" placeholder="Tên bàn" oninput="validateFormat()">
                    <div class="alert_error_validate">
                        <span id="name_error" style="color: red; font-size:12px;margin-left: 10px">
                            @error('name')
                                {{ $message }}
                            @enderror
                        </span>
                    </div>
                </div>

                <label for="editStatus">Trạng thái</label>
                <select id="editStatus" name="table_status_id">
                    @foreach ($statuses as $status)
                        <option value="{{ $status->id }}" @if ($table->table_status_id == $status->id) selected @endif>
                            {{ $status->name }}
                        </option>
                    @endforeach
                </select>

                <div class="form-check">
                    <label for="editQR">Kích hoạt bàn</label>
                    <input type="checkbox" id="editQR" name="regen_qr">
                </div>

                <label>URL gọi món</label>
                <p id="editQrUrl" style="font-size: 13px; word-break: break-word; margin-top: 0px;">{{ $table->qr_url }}</p>

                <button type="submit" style="margin-left: 78px; margin-top: 20px;">Lưu thay đổi</button>
            </form>
        </div>
    </div>

    <!-- Modal hiển thị QR -->
    <div id="qrModal" class="modal" style="display: none;">
        <div class="modal-content" style="width: 280px; border-radius: 10px;">
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
        function updateTableStatuses() {
            fetch('/admin/table/status')
                .then(response => response.json())
                .then(data => {
                    data.forEach(table => {
                        const tableBox = document.querySelector(`.table-box[data-id='${table.id}']`);
                        if (tableBox) {
                            const statusEl = tableBox.querySelector('.table-status');
                            if (statusEl) {
                                statusEl.innerText = table.status_name;
                            }
                            // Optional: Thêm class theo status_id (ví dụ đổi màu)
                            tableBox.classList.remove('status-1', 'status-2', 'status-3');
                            tableBox.classList.add(`status-${table.status_id}`);
                        }
                    });
                })
                .catch(error => console.error('Lỗi khi cập nhật trạng thái:', error));
        }
        // Cập nhật mỗi 10000 giây
        setInterval(updateTableStatuses, 10000);
        updateTableStatuses(); // gọi lần đầu khi load
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
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
        });

        function openEditPopup(table) {
            const form = document.getElementById('editTableForm');
            form.action = '/admin/table/update/' + table.id;

            document.getElementById('editName').value = table.name;
            document.getElementById('editStatus').value = table.table_status_id;
            document.getElementById('editQrUrl').innerText = table.qr_url;

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

    {{-- kiểm tra dữ liệu ngay khi nhập --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('updateTableForm').addEventListener('submit', validateName);
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('addTableForm');

            form.addEventListener('submit', function (event) {
                event.preventDefault();

                const isValid = validateNewTableName();
                if (!isValid) return;

                checkNewTableNameExists(function (isAvailable) {
                    if (isAvailable) {
                        form.submit();
                    }
                });
            });
        });

        function validateNewTableName() {
            const input = document.getElementById('tableNumber');
            const error = document.getElementById('tableNameError');
            const value = input.value.trim();

            if (value === '') {
                error.textContent = 'Tên bàn không được để trống.';
                return false;
            }

            if (value.length > 50) {
                error.textContent = 'Tên bàn không được dài quá 50 ký tự.';
                return false;
            }

            if (!/^[a-zA-Z0-9\s\-]+$/.test(value)) {
                error.textContent = 'Tên bàn chỉ được chứa chữ, số, dấu cách và gạch ngang.';
                return false;
            }

            error.textContent = '';
            return true;
        }

        function checkNewTableNameExists(callback) {
            const input = document.getElementById('tableNumber');
            const value = input.value.trim();

            fetch(`/check-table-name?name=${encodeURIComponent(value)}`)
                .then(response => {
                    if (!response.ok) throw new Error('Lỗi mạng');
                    return response.json();
                })
                .then(data => {
                    if (data.exists) {
                        alertify.error('Tên bàn đã tồn tại.');
                        callback(false);
                    } else {
                        callback(true);
                    }
                })
                .catch(() => {
                    alertify.error('Lỗi khi kiểm tra tên bàn.');
                    callback(false);
                });
        }
    </script>
    <script>
        function validateFormat() {
            const input = document.getElementById('editName');
            const error = document.getElementById('name_error');
            const value = input.value.trim();

            if (value === '') {
                error.textContent = 'Tên bàn không được để trống.';
                error.style.display = 'inline';
                return false;
            }

            if (value.length > 50) {
                error.textContent = 'Tên bàn không được dài quá 50 ký tự.';
                error.style.display = 'inline';
                return false;
            }

            if (!/^[a-zA-Z0-9\s\-]+$/.test(value)) {
                error.textContent = 'Tên bàn chỉ được chứa chữ, số, dấu cách và gạch ngang.';
                error.style.display = 'inline';
                return false;
            }

            // Không có lỗi
            error.textContent = '';
            error.style.display = 'none';
            return true;
        }

        function checkNameExists(callback) {
            const input = document.getElementById('editName');
            const value = input.value.trim();
            const id = input.dataset.id;

            // Ẩn lỗi dưới input nếu có từ trước
            const errorElement = document.getElementById('name_error');
            errorElement.textContent = '';
            errorElement.style.display = 'none';

            fetch(`/check-table-name?name=${encodeURIComponent(value)}&exclude_id=${id}`)
                .then(response => {
                    if (!response.ok) throw new Error('Lỗi mạng');
                    return response.json();
                })
                .then(data => {
                    if (data.exists) {
                        alertify.error('Tên bàn đã tồn tại.');
                        callback(false);
                    } else {
                        callback(true);
                    }
                })
                .catch(() => {
                    alertify.error('Lỗi khi kiểm tra tên.');
                    callback(false);
                });
        }


        document.getElementById('updateTableForm').addEventListener('submit', function (event) {
            event.preventDefault();

            if (!validateFormat()) return;

            checkNameExists(function (isValid) {
                if (isValid) {
                    document.getElementById('updateTableForm').submit();
                }
            });
        });
    </script>

    {{-- kiểm tra duplicate name --}}
    <script></script>
    <script>
        document.getElementById("toggleForm").onclick = function () {
            document.getElementById("addTablePopup").style.display = "flex";
        }

        function hideForm() {
            document.getElementById("addTablePopup").style.display = "none";
        }
    </script>
@endsection
