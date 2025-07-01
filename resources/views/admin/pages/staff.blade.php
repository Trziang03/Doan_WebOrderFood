@extends('layouts.layouts_admin')
@section('title', 'Trang quản lý thống kê')
@section('active-staff', 'active')
@section('content')

<div class="content" id="dashboard">
    <div class="head">
        <div class="title">Quản lý nhân viên</div>
        {{-- <button><a href="{{route('admin.changepw')}}"><i class="fa-solid fa-lock"></i> Đổi mật khẩu</a></button> --}}
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
</div>
@endsection