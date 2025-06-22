@extends('layouts.layouts_admin')
@section('title', 'Trang cập nhật danh mục')
@section('active-category', 'active')
<style>
    .btn-goback>button>a {
        color: white;
    }

</style>
@section('content')
    <div class="separator"></div>
    <div class="content">
        <div class="head">
            <div class="title">Sửa danh mục</div>
        </div>
        <div class="separator_x"></div>
        <div class="row">
            <form action="{{ route('admin.category.updatecategory', ['id' => $danhMucTimKiem->id]) }}" method="POST"
                id="formEditCategory">
                @csrf
                <div class="form-group">
                    <div class="col">
                        <label for="name">Tên danh mục:</label>
                    </div>
                    <div class="col">
                        <input type="text" class="form-control" id="name" name="name"
                            value="{{ $danhMucTimKiem->name }}">
                        @error('name')
                            <span class="text-danger" style="color:red">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <div class="col">
                        <label for="description">Mô tả:</label>
                    </div>
                    <div class="col">
                        <input type="text" class="form-control input-description" id="description" name="description"
                            value="{{ $danhMucTimKiem->description }}">
                        @error('description')
                            <span class="text-danger" style="color:red">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <div class="col">
                        <label for="status">Trạng thái:</label>
                    </div>
                    <div class="col">
                        <select name="status" id="status" class="form-control mt-1">
                            <option value="1" {{ $danhMucTimKiem->status == 1 ? 'selected' : '' }}>Hiển thị</option>
                            <option value="0" {{ $danhMucTimKiem->status == 0 ? 'selected' : '' }}>Ẩn</option>
                        </select>
                        @error('status')
                            <span class="text-danger" style="color:red">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="btn-goback">
                    <button type="submit">Xác nhận</button>
                    <button>Hủy</button>
                    <button type="button" class="btn-height"><a href="{{ route('admin.category') }}">&laquo;Trở
                            lại</a></button>
                </div>
            </form>
        </div>
    </div>
    </div>
@endsection
