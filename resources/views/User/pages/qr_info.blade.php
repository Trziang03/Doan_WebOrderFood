@extends('layouts.layouts_user')
@section('title', 'Trang xem QR')
@section('content')
<style>
    .qr-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 70vh; /* giúp căn giữa theo chiều dọc */
    }
    .qr-card {
        max-width: 400px;
        width: 100%;
        padding: 30px;
        border: 1px solid #ccc;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        text-align: center;
        background-color: #fff;
    }
    .qr-card img {
        max-width: 400px;
        margin-top: 20px;
    }
    .qr-card h2 {
        margin-bottom: 20px;
        font-size: 24px;
        color: #333;
    }
    .qr-card p {
        margin-bottom: 10px;
        font-size: 20px;
        color: #555;
    }
</style>

<div class="container qr-container">
    <div class="qr-card">
        <h2>Thông tin QR</h2>

        <p><strong>Tên bàn:</strong> {{ $table->name ?? 'Bàn số ' . $table->id }}</p>

        <img src="{{ asset('storage/qr-codes/' . $table->qr_code) }}" alt="QR Code">
    </div>
</div>
@endsection
