@extends('layouts.layouts_user')
@section('title', 'Vui lòng quét QR để đặt món')
@section('content')
    <div class="text-center py-5">
        <h2 style="height: 77px; margin-top: 80px;">Không thể truy cập</h2>
        @if(session('error'))
            <p class="text-danger mt-3">{{ session('error') }}</p>
        @endif
        <a href="/" class="btn btn-primary mt-3">Về trang chủ</a>
    </div>
@endsection
