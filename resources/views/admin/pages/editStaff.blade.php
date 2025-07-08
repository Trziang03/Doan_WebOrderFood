@extends('layouts.layouts_admin')
@section('title', 'Trang cập nhật thông tin NV')
@section('content')
<div class="content" id="dashboard">
    <div class="head">
        <div class="title">Thông Tin Cá Nhân</div>
        <button><a href="{{route('admin.changepw')}}"><i class="fa-solid fa-lock"></i> Đổi mật khẩu</a></button>
    </div>
    <div class="separator_x"></div>
    <div class="area">
        <div class="infor">
            <form action="{{ route('admin.staff.update', ['id' => $staff->id]) }}"  method="POST">
                @csrf
                <div>
                    <p>Tài khoản : </p><input name="username" value="{{$staff->username}}">
                </div>
                @error('username')
                    <div class="alert_error_validate" style="margin-left: 15%">{{ $message }}</div>
                @enderror
                <div>
                    <p>Họ và tên : </p><input name="fullname" value="{{$staff->full_name}}">
                </div>
                @error('fullname')
                    <div class="alert_error_validate" style="margin-left: 15%">{{ $message }}</div>
                @enderror
                <div>
                    <p>Email : </p><input name="email" value="{{$staff->email}}">
                </div>
                @error('email')
                    <div class="alert_error_validate" style="margin-left: 15%">{{ $message }}</div>
                @enderror
                <div>
                    <p>Số điện thoại : </p><input name="phone" value="{{$staff->phone}}">
                </div>
                @error('phone')
                    <div class="alert_error_validate" style="margin-left: 15%">{{ $message }}</div>
                @enderror
                <div style="justify-content: start;">
                    <p>Giới tính :
                        <input type="radio" name="gender" value="male" {{($staff->gender == 'Nam') ? 'checked' : ''}}
                            style="width: 15px; margin-left: 55px;"> Nam
                        <input type="radio" name="gender" value="female" {{($staff->gender == 'Nữ') ? 'checked' : ''}}
                            style="width: 15px; margin-left: 55px;"> Nữ
                    </p>
                    <div style="margin-top: 10px">
                        <label style="margin-left: 20px;" for="role">Vai trò:</label><br>
                        <select style="margin-left: 20px;" name="role" required>
                            <option value="NV" {{ $staff->role == 'NV' ? 'selected' : '' }}>Nhân viên</option>
                            <option value="QL" {{ $staff->role == 'QL' ? 'selected' : '' }}>Quản trị viên</option>
                        </select>
                    </div>
                </div>
                @error('gender')
                    <div class="alert_error_validate" style="margin-left: 15%">{{ $message }}</div>
                @enderror
                <div>
                    <p>Ngày sinh : </p><input type="date" name="birthday" value="{{$staff->date_of_birth}}">
                </div>
                @error('birthday')
                    <div class="alert_error_validate" style="margin-left: 15%">{{ $message }}</div>
                @enderror
                <button type="submit" style="margin-top: 12px;">Lưu thông tin</button>
            </form>
        </div>
        <div class="separator"></div>
        <div class="avatar">
            <div>
                <img id="imagePreview" src="{{ asset('images/' . $staff->image) }}" alt="Image">
            </div>
            <form action="{{ route('admin.editAvatar') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input id="imageInput" type="file" name="image">
                <button type="submit">Đổi ảnh đại diện</button>
            </form>
        </div>
    </div>
</div>
<script>
    document.getElementById('imageInput').addEventListener('change', function (event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById('imagePreview').src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection
