@extends('layouts.layouts_admin')
@section('title', 'Trang quản lý món ăn')
@section('active-product', 'active')
<style>
    .btn {
        background-color: rgb(240, 145, 55);
        color: white !important;
        text-align: center;
        padding: 8px;
        width: 130px;
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
</style>
@section('content')
    <div class="content" id="sanpham">
        <div class="head">
            <div class="title">Quản Lý Món Ăn</div>
            <div class="search">
                <form action="{{ route('admin.product') }}" method="GET">
                    <input type="text" name="keyword" placeholder="Tìm món ăn..." value="{{ request('keyword') }}">
                    <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
            </div>
        </div>
        <div class="separator_x"></div>
        @if ( Auth::user()->role === 'QL')
            <a href="{{ route('product.create') }}" class="btn">
                <i class="fa-solid fa-plus"></i> Thêm món ăn</a>
        @endif
        <select onchange="findProduct(this)">
            <option value="all" {{ request()->is('admin/products') ? 'selected' : '' }}>Danh mục</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" {{ request()->is('admin/products/category/' . $category->id) ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        <table>
            <thead>
                <tr>
                    <th style="width: 20px;">Hình ảnh</th>
                    <th style="width: 40px;">Tên món</th>
                    <th style="width: 25px;">Danh mục</th>
                    <th style="width: 100px;">Mô tả</th>
                    <th style="width: 20px;">Trạng thái</th>
                    <th style="width: 20px;">Giá</th>
                    <th style="width: 10px;">Sửa</th>
                    <!-- <th style="width: 10px;">Ẩn</th> -->
                </tr>
            </thead>
<tbody>
    @if ($danhSachSanPham->count())
        @foreach ($danhSachSanPham as $sanPham)
            <tr>
                <td style="text-align: center;"><img src="{{ asset($sanPham->image_food) }}" width="70"></td>
                <td style="text-align: center;">{{ $sanPham->name }}</td>
                <td style="text-align: center;">
                    {{ $sanPham->category->name ?? 'Chưa có danh mục' }}
                </td>
                <td style="text-align: center;">{{ $sanPham->description }}</td>
                <td style="text-align: center;">{{ $sanPham->status == 1 ? 'Hiện' : 'Ẩn' }}</td>
                <td style="text-align: center;">{{ number_format($sanPham->price, 0, '.', '.') }}đ</td>
                <td style="text-align: center;">
                    <a href="{{ route('product.edit', ['product' => $sanPham->id]) }}">
                        <i class="fa-regular fa-pen-to-square"></i>
                    </a>
                </td>
                <!-- <td style="text-align: center;">
                    <a onclick="showPopupProduct({id:{{ $sanPham->id }},name:'{{ $sanPham->name }}'})" class="cursor">
                        <i class="fa-regular fa-trash-can"></i>
                    </a>
                </td> -->
            </tr>
        @endforeach
    @else
        <tr>
            <td colspan="8" style="text-align: center;">Không có món nào để hiển thị.</td>
        </tr>
    @endif
</tbody>
        </table>
        <div class="pagination">
            {{ $danhSachSanPham->links() }}
        </div>
    </div>
@endsection
@section('script')
    <script>
        function findProduct(select) {
            const categoryId = select.value;

            if (categoryId === "all") {
                window.location.href = "{{ url('/admin/products') }}";
            } else {
                window.location.href = "{{ url('/admin/products/category') }}/" + categoryId;
            }
        }
    </script>

    <script>
        @if (session('msg'))

            alertify
                .alert("Thông báo", "{{ session('msg') }}");
        @endif
    </script>
@endsection
