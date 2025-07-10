@extends('layouts.layouts_user')
@section('title', 'Vui lòng quét QR để đặt món')
@section('content')
    <div class="text-center py-5">
        <h2 style="height: 125px; margin-top: 122px;">Không thể truy cập</h2>
        @if(session('error'))
            <p class="text-danger mt-3">{{ session('error') }}</p>
        @endif
    </div>
@endsection
