@extends('layouts.layouts_admin')
@section('title', 'Trang quản lý danh mục')
@section('active-category', 'active')
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
    <div class="content" id="danhmuc">
        <div class="head">
            <div class="title">Quản Lý Danh Mục</div>
            <div class="search">
                <form action="{{ route('admin.category') }}" method="GET">
                    <input type="text" name="keyword" placeholder="Tìm danh mục..." value="{{ request('keyword') }}">
                    <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
            </div>
        </div>
        <div class="separator_x"></div>
        <a href="{{ route('admin.category.addcategory') }}" class="btn">
            <i class="fa-solid fa-plus"></i> Thêm danh mục
        </a>
            <table>
                <thead>
                    <tr>
                        <th style="width: 160px;">Tên danh mục</th>
                        <th style="width: 300px;">Mô tả</th>
                        <th style="width: 140px;">Trạng thái</th>
                        <th style="width: 48px;">Sửa</th>
                        <th style="width: 48px;">Xóa</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($danhSachDanhMuc as $item)
                        <tr>
                            <td style="text-align: center;">{{ $item->name }}</td>
                            <td style="text-align: center;">{{ $item->description }}</td>
                            <td style="text-align: center;">
                                {{ $item->status == 1 ? 'Hiện' : 'Ẩn' }}
                            </td>
                            <td style="text-align: center;">
                                <a href="{{ route('admin.category.editcategory', ['id' => $item->id]) }}">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </a>
                            </td>
                            <td style="text-align: center;">
                                <a onclick="popup('delete', {{ $item->id }})" data-id="{{ $item->id }}">
                                    <i class="fa-regular fa-trash-can"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align: center;">Không có danh mục nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="popup_admin" id="popupdm">
                <h3 style="color: white;">Bạn có thật sự muốn xóa danh mục này?</h3>
                <p style="color: white;">* Danh mục bị xóa sẽ không thể khôi phục *</p>
            
                <label style="color:white;">
                    Nhập từ <strong style="color: yellow;">XÓA</strong> để xác nhận:
                </label>
                <input type="text" id="confirmInput" placeholder="Nhập XÓA..." />
            
                <div style="margin-top: 10px;">
                    <input type="checkbox" id="confirmCheckbox" />
                    <label for="confirmCheckbox" style="color: white;">Tôi đồng ý với hành động này</label>
                </div>
            
                <p id="alert" style="color: red;"></p>
            
                <div class="button">
                    <button type="button" id="deleteBtn" disabled>Đồng ý</button>
                    <button onclick="cancel('dm')">Hủy</button>
                </div>
            </div>
            
        <div class="pagination">
            {{ $danhSachDanhMuc->links() }}
        </div>
    </div>
@endsection
@section('script')
    <script>
        document.getElementById('categoryFilter').addEventListener('change', function () {
            const categoryId = this.value;

            fetch(`{{ route('filter.category', ['id' => 'all']) }}?categoryFilter=${categoryId}`)
                .then(response => response.json())
                .then(data => {
                    const tbody = document.querySelector('table tbody');
                    tbody.innerHTML = '';

                    if (data.length === 0) {
                        tbody.innerHTML = `
                <tr>
                    <td colspan="5" style="text-align: center;">Không có danh mục nào để hiển thị.</td>
                </tr>`;
                    } else {
                        data.forEach(item => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                    <td style="text-align: center;">${item.name}</td>
                    <td style="text-align: center;">${item.description}</td>
                    <td style="text-align: center;">${item.status == 1 ? 'Hiển thị' : 'Ẩn'}</td>
                    <td style="text-align: center;">
                        <a href="/admin/category/edit/${item.id}"><i class="fa-solid fa-check"></i></a>
                    </td>
                    <td style="text-align: center;">
                        <a onclick="popup('dm', ${item.id})" data-id="${item.id}">
                            <i class="fa-solid fa-x"></i>
                        </a>
                    </td>
                `;
                            tbody.appendChild(row);
                        });
                    }
                });
            });

        const inputContainer = document.getElementById('inputContainer');

        btnAddSpecifications.addEventListener('click', function () {
            const newInput = document.createElement('input');
            newInput.type = 'text';
            newInput.className = 'form-control';
            inputContainer.appendChild(newInput);
        });

        function popup(action, id) {
            console.log('ID nhận được:', id);

            const popupElement = document.getElementById('popupdm');
            popupElement.style.display = 'block';

            const deleteButton = document.getElementById('deleteBtn');
            deleteButton.setAttribute('data-id', id);

            deleteButton.onclick = function () {
                const confirmInput = document.getElementById('confirm_input').value.trim().toUpperCase();

                if (confirmInput !== 'XÓA') {
                    document.getElementById('alert').textContent = 'Bạn phải nhập chính xác "XÓA" để thực hiện.';
                    return;
                }

                const categoryId = this.getAttribute('data-id');

                fetch(`/admin/deletecategory/${categoryId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        confirm: 'XÓA' // gửi xác nhận kèm
                    })
                })
                .then(response => response.json().then(data => ({ ok: response.ok, data })))
                .then(({ ok, data }) => {
                    if (ok) {
                        alertify.success(data.message);
                        window.location.href = '/admin/category'; // reload lại trang danh sách
                    } else {
                        alertify.error(data.message);
                    }
                })
                .catch(error => {
                    console.error('Lỗi khi gửi yêu cầu xóa:', error);
                });
            };
        }

        function cancel(action) {
            const popupElement = document.getElementById('popupdm');
            popupElement.style.display = 'none';
        }
    </script>
    
    {{-- Xác nhận bước 2 để xóa danh mục --}}
    <script>
        document.getElementById('confirm_input').addEventListener('input', function () {
            const input = this.value.trim().toUpperCase();
            const btn = document.getElementById('deleteBtn');
            const alert = document.getElementById('alert');
        
            if (input === 'XÓA') {
                btn.disabled = false;
                alert.textContent = '';
            } else {
                btn.disabled = true;
                alert.textContent = 'Bạn phải nhập chính xác "XÓA" để thực hiện.';
            }
        });
    </script>
        
@endsection
