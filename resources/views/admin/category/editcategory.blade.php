@extends('layouts.layouts_admin')
@section('title', 'Trang cập nhật danh mục')
@section('active-category', 'active')
<style>
    .btn-goback>button>a {
        color: white;
    }

    #inputContainer .form-control {
        margin-bottom: 5px;
    }
    textarea.form-control {
        resize: vertical;
        overflow-y: auto;
        padding: 8px;
        line-height: 1.5;
        font-size: 14px;
        width: 40%;
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
                        <div class="col">
                            <textarea
                                id="description"
                                name="description"
                                class="form-control"
                                placeholder="Nhập mô tả"
                                rows="4"
                                maxlength="255"
                                oninput="validateDescription()"
                                value="{{ $danhMucTimKiem->description }}"
                            >{{ $danhMucTimKiem->description }}</textarea>
                            
                        </div>
                        <span id="description_error" class="text-danger" style="color:red"></span>
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
@section('script')
<script>
    function validateCategoryName() {
         const input = document.getElementById('name');
         const error = document.getElementById('name_error');
         const value = input.value.trim();

         if (value === '') {
             error.textContent = 'Tên danh mục không được để trống.';
             return;
         }

         if (value.length > 50) {
             error.textContent = 'Tên danh mục không được dài quá 50 ký tự.';
             return;
         }

         // ✅ Cho phép chữ có dấu, số, khoảng trắng và dấu gạch ngang
         const regex = /^[\p{L}0-9\s\-]+$/u;

         if (!regex.test(value)) {
             error.textContent = 'Chỉ cho phép chữ (có dấu), số, dấu cách và gạch ngang.';
             return;
         }

         // ✅ Hợp lệ
         error.textContent = '';
     }

 </script>
     
 <script>
     //kiểm tra định dạng mô tả ngay khi nhập
     function validateDescription() {
     const input = document.getElementById('description');
     const error = document.getElementById('description_error');
     const value = input.value.trim();

     if (value === '') {
         error.textContent = 'Mô tả không được để trống.';
         return;
     }

     if (value.length > 255) {
         error.textContent = 'Mô tả không được vượt quá 255 ký tự.';
         return;
     }

     // ✅ Cho phép chữ có dấu, số, khoảng trắng và một số ký tự thường dùng trong mô tả
     const regex = /^[\p{L}0-9\s.,\-–()!?]+$/u;

     if (!regex.test(value)) {
         error.textContent = 'Mô tả chỉ được chứa chữ, số, dấu cách, dấu chấm, phẩy, gạch, ngoặc và dấu chấm hỏi/cảm.';
         return;
     }

     error.textContent = ''; // ✅ Hợp lệ
 }

 </script>
@endsection