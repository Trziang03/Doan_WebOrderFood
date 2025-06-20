@extends('layouts.layouts_admin')
@section('title', 'Trang thêm danh mục')
@section('active-category', 'active')
<style>
    .btn-goback>button>a {
        color: white;
    }

    #inputContainer .form-control {
        margin-bottom: 5px;
    }
</style>
@section('content')
    <div class="separator"></div>
    <div class="content">
        <div class="head">
            <div class="title">Thêm danh mục</div>
        </div>
        <div class="separator_x"></div>
            @if (session('message'))
                <script>
                    alertify.success("{{ session('message') }}");
                </script>
            @endif
            <div class="row">
                <form action="{{ route('admin.category.storecategory') }}" method="POST" id="formAddCategory">
                    <div class="form-group">
                        <div class="col">
                            <label>Tên danh mục:</label>
                        </div>
                        <div class="col">
                            <input type="text" class="form-control" id="name" name="name" placeholder="Nhập tên danh mục">
                            @error('name')
                                <span class="text-danger" style="color:red">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col">
                            <label>Mô tả:</label>
                        </div>
                        <div class="col">
                            <input type="text" class="form-control input-description" id="description" name="description" placeholder="Nhập mô tả">
                            @error('description')
                                <span class="text-danger" style="color:red">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col">
                            <label for="status">Trạng thái:</label>
                            <select name="status" id="status" class="form-control mt-1">
                                <option value="1">Hiển thị</option>
                                <option value="0">Ẩn</option>
                            </select>
                        </div>
                        <div class="col" id="inputContainer">
                            @error('status')
                                <span class="text-danger" style="color:red">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="btn-goback">
                        @csrf
                        <button>Xác nhận thêm</button>
                        <button>Hủy</button>
                        <button type="button"><a href="{{ route('admin.category') }}">&laquo; Trở lại</a></button>
                    </div>
                </form>
            </div>
    </div>
@endsection
@section('script')
    <script>
        const inputContainer = document.getElementById('inputContainer');

        btnAddSpecifications.addEventListener('click', function () {
            const newInput = document.createElement('input');
            newInput.type = 'text';
            newInput.className = 'form-control';
            inputContainer.appendChild(newInput);
        });
        function popup(action, id) {
            console.log('ID nhận được:', id);

            // Hiển thị popup
            const popupElement = document.getElementById('popupdm');
            popupElement.style.display = 'block';

            // Lưu ID vào nút "Đồng ý"
            const deleteButton = document.getElementById('deleteBtn');
            deleteButton.setAttribute('data-id', id);
        }

        document.getElementById('deleteBtn').addEventListener('click', function () {
            const categoryId = this.getAttribute('data-id');

            fetch(`/admin/deletecategory/${categoryId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
                .then(response => {
                    return response.json().then(data => {
                        return {
                            ok: response.ok,
                            data: data
                        }; // Trả về một đối tượng chứa cả ok và data
                    });
                })
                .then(({
                    ok,
                    data
                }) => {
                    if (ok) {
                        alertify.success(data.message);
                        window.location.href = '/admin/addcategory';
                    } else {
                        alert('Đã có lỗi xảy ra: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });
    </script>
@endsection
