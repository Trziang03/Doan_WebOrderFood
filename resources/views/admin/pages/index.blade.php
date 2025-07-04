@extends('layouts.layouts_admin')
@section('title', 'Trang Dashboard')
@section('active-dashboard', 'active')
@section('content')
<div class="content" id="dashboard">
    <div class="head">
        <div class="title">Trang Dashboard</div>
    </div>
    <div class="separator_x"></div>
    <div class="static">
        <div class="count">
            <div class="see">
                <div class="icon">
                    <img src="/images/iconfood2.png" alt="icon">
                </div>
                <div class="number">
                    <p class="num">{{count(App\Models\Product::all())}}</p>
                    <p>Món ăn</p>
                </div>
            </div>
            <div class="go">
                <a href="{{route('product.index')}}">Xem chi tiết <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>
        <div class="count">
            <div class="see">
                <div class="icon">
                    <img src="/images/icontable.png" alt="icon">
                </div>
                <div class="number">
                    <p class="num">{{count(App\Models\Table::all())}}</p>
                    <p>Bàn ăn</p>
                </div>
            </div>
            <div class="go">
                <a href="{{route('admin.table')}}">Xem chi tiết <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>
        <div class="count">
            <div class="see">
                <div class="icon">
                    <img src="/images/icondonhang.png" alt="icon">
                </div>
                <div class="number">
                    <p class="num">{{count(App\Models\Order::all())}}</p>
                    <p>Đơn hàng</p>
                </div>
            </div>
            <div class="go">
                <a href="{{route('admin.order')}}">Xem chi tiết <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
    <div class="static">
        @if ( Auth::user()->role === 'QL')
        <div class="count">
            <div class="see">
                <div class="icon">
                    <img src="/images/nv2.png" alt="icon">
                </div>
                <div class="number">
                    <p class="num">{{ \App\Models\User::where('role', 'NV')->count() }}</p>
                    <p>Nhân viên</p>
                </div>
            </div>
            <div class="go">
                <a href="{{route('admin.staff')}}">Xem chi tiết <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>
        
        <div class="count">
            <div class="see">
                <div class="icon">
                    <img src="/images/icondanhmuc.png" alt="icon">
                </div>
                <div class="number">
                    <p class="num">{{count(App\Models\Category::all())}}</p>
                    <p>Danh mục</p>
                </div>
            </div>
            <div class="go">
                <a href="{{route('admin.category')}}">Xem chi tiết <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>

        <div class="count">
            <div class="see">
                <div class="icon">
                    <img src="/images/icon-thong-ke.jpg" alt="icon">
                </div>
                <div class="number">
                    <p>Thống kê</p>
                </div>
            </div>
            <div class="go">
                <a href="{{ route('admin.static') }}">Xem chi tiết <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>
        @endif
    </div>
    <div class="separator_x"></div>
    <div class="area">
        <div class="infor">
            <form action="{{ route('admin.editWebsite') }}" method="POST">
                @csrf
                <div>
                    <p>Tên : </p><input name="name" value="{{App\Models\About::first()->name}}">
                </div>
                <div>
                    <p>Facebook : </p><input name="facebook" value="{{App\Models\About::first()->facebook}}">
                </div>
                <div>
                    <p>Youtube : </p><input name="youtube" value="{{App\Models\About::first()->youtube}}">
                </div>
                <div>
                    <p>Số điện thoại : </p><input name="phone" value="{{App\Models\About::first()->phone}}">
                </div>
                <div>
                    <p>Email : </p><input name="email" value="{{App\Models\About::first()->email}}">
                </div>
                <div>
                    <p>Địa chỉ : </p><input name="address" value="{{App\Models\About::first()->address}}">
                </div>
                <button type="submit">Lưu thông tin</button>
            </form>
        </div>
        <div class="separator"></div>
        <div class="avatar">
            <div>
                <img id="logoPreview" src="{{ asset('images/' . App\Models\About::first()->logo) }}" alt="Logo">
            </div>
            <form action="{{ route('admin.editLogo') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input id="logoInput" type="file" name="logo">
                <button type="submit">Đổi logo</button>
            </form>
        </div>
    </div>
</div>
<script>
    document.getElementById('logoInput').addEventListener('change', function (event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById('logoPreview').src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection
