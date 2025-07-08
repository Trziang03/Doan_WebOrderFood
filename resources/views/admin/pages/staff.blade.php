@extends('layouts.layouts_admin')
@section('title', 'Trang quản lý nhân viên')
@section('active-staff', 'active')
<style>
    .btn.add {
        background-color: rgb(240, 145, 55);
        color: white;
        text-align: center;
        padding: 10px;
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

    .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 999;
    }

    .modal-content {
        background: white;
        padding: 25px;
        border-radius: 10px;
        width: 80%;
        max-width: 800px;
    }

    .form-grid {
        display: flex;
        gap: 20px;
    }

    .form-col {
        flex: 1;
    }

    .form-control {
        width: 100%;
        padding: 10px 12px;
        margin-bottom: 15px;
        border-radius: 5px;
        border: 1px solid #ccc;
        font-size: 14px;
    }

    .form-actions {
        text-align: center;
    }

    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        cursor: pointer;
    }

    .btn-success {
        background-color: #28a745;
        color: white;
        margin-right: 10px;
    }

    .btn-danger {
        background-color: #dc3545;
        color: white;
    }
</style>
@section('content')
    <div class="content" id="staffmanagement">
        <div class="head">
            <div class="title">Quản lý nhân viên</div>
            <div class="search">
                <form action="{{ route('admin.staff') }}" method="GET">
                    <input type="text" name="keyword" placeholder="Tìm nhân viên..." value="{{ request('keyword') }}">
                    <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
            </div>
        </div>
        <div class="separator_x"></div>
        <button onclick="openModal()" class="btn add">
            <i class="fa-solid fa-plus"></i> Thêm nhân viên
        </button>
        <table>
            <thead>
                <tr>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>SĐT</th>
                    <th>Trạng thái</th>
                    <th>Vai trò</th>
                    <th style="width: 48px;">Sửa</th>
                    <th style="width: 48px;">Xóa</th>
                </tr>
            </thead>
            <tbody>
                @foreach($staffs as $staff)
                    <tr>
                        <td>{{ $staff->full_name }}</td>
                        <td>{{ $staff->email }}</td>
                        <td>{{ $staff->phone }}</td>
                        <td>{{ $staff->status == 1 ? 'Kích hoạt' : 'Tạm khóa' }}</td>
                        <td>{{ $staff->role }}</td>
                        <td style="text-align: center;">
                            <button onclick="openEditModal({{ $staff->id }})" class="btn btn-warning"><i class="fa-regular fa-pen-to-square"></i></button>
                        </td>
                        <td style="text-align: center;">
                            <a onclick="popup('delete', {{ $staff->id }})" data-id="{{ $staff->id }}">
                                <i class="fa-regular fa-trash-can"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Popup --}}
    <div class="modal" id="addUserModal" style="display: none;">
        <div class="modal-content">
            <h2>THÊM NHÂN VIÊN</h2>
            <form id="addUserForm" method="POST" action="{{ route('admin.staff.store') }}">
                @csrf
                <div class="form-grid">
                    <div class="form-col">
                        <input type="text" name="username" class="form-control" placeholder="Tài Khoản">
                        <input type="text" name="full_name" class="form-control" placeholder="Họ và tên">
                        <select name="gender" class="form-control">
                            <option value="">-- Giới tính --</option>
                            <option value="Nam">Nam</option>
                            <option value="Nữ">Nữ</option>
                        </select>
                        <input type="date" name="date_of_birth" class="form-control">
                        <input type="text" name="phone" class="form-control" placeholder="Số điện thoại">
                    </div>

                    <div class="form-col">
                        <input type="email" name="email" class="form-control" placeholder="Email">
                        <input type="password" name="password" class="form-control" placeholder="Mật khẩu">
                        <input type="password" name="password_confirmation" class="form-control"
                            placeholder="Xác nhận password">
                        <select name="role" class="form-control">
                            <option value="">-- Vai trò --</option>
                            <option value="QL">Admin</option>
                            <option value="NV">Nhân viên</option>
                        </select>
                        <select name="status" class="form-control">
                            <option value="">-- Trạng thái --</option>
                            <option value="1">Kích hoạt</option>
                            <option value="0">Tạm khóa</option>
                        </select>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-success">Lưu</button>
                    <button type="button" class="btn btn-danger" onclick="closeModal()">Đóng</button>
                </div>
            </form>
        </div>
    </div>
    {{-- Popup Sửa --}}
<div class="modal" id="editUserModal" style="display: none;">
    <div class="modal-content">
        <h2>SỬA NHÂN VIÊN</h2>
        <form id="editUserForm" method="POST">
            @csrf
            <input type="hidden" id="edit_id">

            <div class="form-grid">
                <div class="form-col">
                    <input type="text" name="username" id="edit_username" class="form-control" placeholder="Tài Khoản">
                    <input type="text" name="full_name" id="edit_full_name" class="form-control" placeholder="Họ và tên">
                    <select name="gender" id="edit_gender" class="form-control">
                        <option value="">-- Giới tính --</option>
                        <option value="Nam">Nam</option>
                        <option value="Nữ">Nữ</option>
                    </select>
                    <input type="date" name="date_of_birth" id="edit_date_of_birth" class="form-control">
                </div>

                <div class="form-col">
                    <input type="email" name="email" id="edit_email" class="form-control" placeholder="Email">
                    <input type="text" name="phone" id="edit_phone" class="form-control" placeholder="Số điện thoại">
                    <select name="role" id="edit_role" class="form-control">
                        <option value="">-- Vai trò --</option>
                        <option value="QL">Admin</option>
                        <option value="NV">Nhân viên</option>
                    </select>
                    <select name="status" id="edit_status" class="form-control">
                        <option value="">-- Trạng thái --</option>
                        <option value="1">Kích hoạt</option>
                        <option value="0">Tạm khóa</option>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Cập nhật</button>
                <button type="button" class="btn btn-danger" onclick="closeEditModal()">Đóng</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
    <script>
        function openModal() {
            document.getElementById('addUserModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('addUserModal').style.display = 'none';
        }
    </script>
    <script>
    function openEditModal(id) {
        fetch('/admin/staff/' + id + '/edit')
            .then(res => res.json())
            .then(data => {
                document.getElementById('edit_id').value = data.id;
                document.getElementById('edit_username').value = data.username;
                document.getElementById('edit_full_name').value = data.full_name;
                document.getElementById('edit_gender').value = data.gender;
                document.getElementById('edit_date_of_birth').value = data.date_of_birth;
                document.getElementById('edit_phone').value = data.phone;
                document.getElementById('edit_email').value = data.email;
                document.getElementById('edit_role').value = data.role;
                document.getElementById('edit_status').value = data.status;

                document.getElementById('editUserModal').style.display = 'block';
            });
    }

    function closeEditModal() {
        document.getElementById('editUserModal').style.display = 'none';
    }

    document.getElementById('editUserForm').addEventListener('submit', function (e) {
        e.preventDefault();
        let id = document.getElementById('edit_id').value;
        let form = new FormData(this);

        fetch('/admin/staff/' + id + '/update', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: form
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Cập nhật thành công!');
                closeEditModal();
                location.reload();
            } else {
                alert('Có lỗi xảy ra!');
            }
        });
    });
</script>
@endsection
