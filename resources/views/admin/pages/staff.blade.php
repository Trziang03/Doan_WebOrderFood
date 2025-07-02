@extends('layouts.layouts_admin')
@section('title', 'Trang quản lý nhân viên')
@section('active-staff', 'active')
@section('content')
<style>
    .popup-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }
    
    .popup-content {
        background: #fff;
        padding: 30px;
        border-radius: 16px;
        width: 40%;
        max-width: 500px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        animation: fadeIn 0.3s ease;
    }
    .alert_error_validate {
    color: red;
    font-size: 13px;
    margin-top: 4px;
}

    
    @keyframes fadeIn {
        from {
            transform: translateY(-10px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    
    .popup-content h3 {
        text-align: center;
        font-size: 22px;
        font-weight: bold;
        margin-bottom: 20px;
        color: #333;
    }
    
    .popup-content input {
        width: 95%;
        padding: 10px 14px;
        margin-bottom: 12px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 15px;
        transition: all 0.2s ease;
        outline: none;
    }
    
    .popup-content input:focus {
        border-color: #ff9900;
        box-shadow: 0 0 0 2px rgba(255, 153, 0, 0.2);
    }
    
    .popup-content .btn {
        padding: 8px 16px;
        border: none;
        border-radius: 8px;
        font-weight: bold;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .popup-content .btn-add {
        background-color: #ff9900;
        color: white;
        margin-right: 8px;
    }
    
    .popup-content .btn-add:hover {
        background-color: #e68a00;
    }
    
    .popup-content .btn-close {
        background-color: #ccc;
        color: #333;
    }
    
    .popup-content .btn-close:hover {
        background-color: #b3b3b3;
    }
    </style>
    
    
<div class="content" id="dashboard">
    <div class="head">
        <div class="title">Quản lý nhân viên</div>
        <button onclick="openPopup()" class="btn btn-primary">+ Thêm nhân viên</button>
    </div>
    <div class="separator_x"></div>
    <div class="area">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Tên</th>
                    <th>Email</th>
                    <th>Số điện thoại</th>
                    <th>Giới tính</th>
                    <th>Ngày sinh</th>
                    <th>Vai trò</th>
                    <th>Trạng thái</th>
                    <th>Sửa</th>
                </tr>
            </thead>
            <tbody>
                @foreach($staffs as $staff)
                    <tr>
                        <td>{{ $staff->full_name }}</td>
                        <td>{{ $staff->email }}</td>
                        <td>{{ $staff->phone ?? 'Không có' }}</td>
                        <td>{{ $staff->gender}}</td>
                        <td>{{ $staff->date_of_birth }}</td>
                        <td>{{ $staff->role }}</td>
                        <td>{{ $staff->status == 1 ? 'Đang hoạt động' : 'Tạm khóa' }}</td>
                        <td>
                            <a href="{{ route('admin.staff.profile', ['id' => $staff->id]) }}">
                                <i class="fa-regular fa-pen-to-square"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

{{-- popup thêm nhân viên khi thêm có role mặc định là NV --}}
    <div id="popupForm" class="popup-overlay" style="display:none;">
        <div class="popup-content">
            <h3>THÊM NHÂN VIÊN</h3>
            <form action="{{ route('admin.staff.store') }}" method="POST" id="addStaffForm" enctype="multipart/form-data">
                @csrf
                <input name="username" placeholder="Tài khoản.." value="{{ old('username') }}" required>
                <input name="fullname" placeholder="Họ và tên.." value="{{ old('fullname') }}" required>
                <input name="phone" placeholder="Số điện thoại.." value="{{ old('phone') }}" required>
                <input name="email" placeholder="Email.." type="email" value="{{ old('email') }}" required>
                <input name="password" placeholder="Mật khẩu.." type="password" required>
                <input name="password_confirmation" placeholder="Xác nhận mật khẩu.." type="password" required>

                <button type="submit" class="btn btn-add">Thêm</button>
                <button type="button" onclick="closePopup()" class="btn btn-close">Đóng</button>
            </form>
        </div>
    </div>

    
</div>
@endsection
@section('script')
    <script>
        function openPopup() {
            document.getElementById('popupForm').style.display = 'flex';
        }
        function closePopup() {
            document.getElementById('popupForm').style.display = 'none';
        }
    </script>
    <script>
        document.getElementById('addStaffForm').addEventListener('submit', function (e) {
            e.preventDefault();
        
            const form = e.target;
            const formData = new FormData(form);
            
            // Xóa lỗi cũ
            document.querySelectorAll('.alert_error_validate').forEach(el => el.remove());
        
            fetch("{{ route('admin.staff.ajaxStore') }}", {
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(async response => {
                if (!response.ok) {
                    const data = await response.json();
                    if (data.errors) {
                        for (const [key, messages] of Object.entries(data.errors)) {
                            const input = form.querySelector(`[name="${key}"]`);
                            const error = document.createElement('div');
                            error.className = 'alert_error_validate';
                            error.style.color = 'red';
                            error.textContent = messages[0];
                            input.parentNode.insertBefore(error, input.nextSibling);
                        }
                    }
                    throw new Error('Validation failed');
                }
        
                return response.json();
            })
            .then(data => {
                alert(data.message);
                closePopup();
                location.reload(); // refresh danh sách nếu có
            })
            .catch(error => {
                console.error(error);
            });
        });
    </script>
        
@endsection