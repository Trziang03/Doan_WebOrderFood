@extends('layouts.layouts_admin')
@section('title', 'Trang cập nhật món ăn')
@section('active-product', 'active')
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
        <div class="title">Chỉnh sửa món ăn</div>
    </div>
    <div class="separator_x"></div>

    @if (session('msg'))
        <script>
            alertify.success("{{ session('msg') }}");
        </script>
    @endif

    <div class="row">
        <form action="{{ route('product.update', $sanPham->id) }}" method="POST" enctype="multipart/form-data" id="formEditProduct">
            @csrf
            @method('PUT')

            <div class="form-group">
                <div class="col">
                    <div class="form-group-row">
                        <div class="form-group-product">
                            <label>Tên món ăn</label>
                            <input type="text" class="form-control" name="name" value="{{ $sanPham->name }}" required>
                        </div>
                        <div class="form-group-product">
                            <label>Danh mục</label>
                            <select name="category_id" required>
                                @foreach ($danhSachPhanLoai as $phanLoai)
                                    <option value="{{ $phanLoai->id }}" {{ $phanLoai->id == $sanPham->category_id ? 'selected' : '' }}>
                                        {{ $phanLoai->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group-product">
                            <label>Trạng thái</label>
                            <select name="status" required onchange="handleChangeStatus(this)">
                                <option value="1" {{ $sanPham->status == 1 ? 'selected' : '' }}>Hiển thị</option>
                                <option value="0" {{ $sanPham->status == 0 ? 'selected' : '' }}>Ẩn</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group-row">
                        <div class="form-group-product">
                            <label>Mô tả</label>
                            <textarea name="description" rows="4" required>{{ $sanPham->description }}</textarea>
                        </div>
                        <div class="form-group-product short-input">
                            <label>Giá tiền</label>
                            <input type="text" class="form-control" name="price" value="{{ $sanPham->price }}" required>
                        </div>
                        <div class="form-group-product short-input">
    <label>Hình ảnh hiện tại:</label>
    <div id="image-products">
        @if ($sanPham->image_food)
            <img id="preview-image" src="{{ asset($sanPham->image_food) }}" style="max-width: 150px; max-height: 150px;" />
        @else
            <img id="preview-image" style="max-width: 150px; max-height: 150px; display: none;" />
        @endif

        <input type="file" onchange="loadFile(event)" class="form-control"
            style="background-color:white" name="image">
    </div>
    @error('image')
        <span class="text-danger" style="color:red">{{ $message }}</span>
    @enderror
</div>
                    </div>
                    <div class="form-group-product">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <label style="margin: 0;">Toppings có sẵn</label>
                            <button type="button" class="btn" onclick="openOptionPopup()">+ Thêm Topping/Size</button>
                        </div>
                        <div class="topping-grid">
                            @foreach ($toppings as $topping)
                                @php
                                    $isChecked = in_array($topping->id, $sanPham->toppings->pluck('id')->toArray());
                                @endphp
                                <div class="topping-card">
                                    <input type="checkbox" name="toppings[]" value="{{ $topping->id }}" data-name="{{ $topping->name }}"
       data-price="{{ $topping->price }}"
       {{ $isChecked ? 'checked' : '' }}
       onchange="handleToppingChange(this, '{{ $topping->name }}', {{ $topping->price }})"
       class="topping-checkbox">
                                    <div class="topping-content">
                                        <div class="topping-name">{{ $topping->name }}</div>
                                        <div class="topping-price"> {{ $topping->price > 0 ? '+' . number_format($topping->price, 0, ',', '.') . ' đ' : '+0 đ' }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <label style="margin-top: 20px;">Sizes có sẵn</label>
                        <div class="topping-grid">
                            @foreach ($sizes as $size)
                                <div class="topping-card">
                                    <input type="checkbox" name="sizes[]" value="{{ $size->id }}"
                                        {{ in_array($size->id, $sanPham->sizes->pluck('id')->toArray()) ? 'checked' : '' }}
                                        onchange="handleSizeChange(this, '{{ $size->name }}', {{ $size->price }})"
                                        class="topping-checkbox">
                                    <div class="topping-content">
                                        <div class="topping-name">Size {{ $size->name }}</div>
                                        <div class="topping-price">
                                            {{ $size->price > 0 ? '+' . number_format($size->price, 0, ',', '.') . ' đ' : '+0 đ' }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Danh sách đã chọn -->
                    <div class="form-group-product" style="margin-top: 20px;">
                        <label>Tùy chọn đã chọn:</label>
                        <div id="selectedToppings" class="selected-toppings-list"></div>
                        <div id="selectedSizes" class="selected-toppings-list"></div>
                    </div>

                    <div class="btn-goback">
                        <button>Cập nhật</button>
                        <button type="reset">Đặt lại</button>
                        <button type="button"><a href="{{ route('admin.product') }}">&laquo; Trở lại</a></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<div id="optionPopup">
    <div class="popup-content">
        <h4>Thêm Topping hoặc Size</h4>

        <form action="{{ route('admin.topping.store') }}" method="POST" style="margin-bottom: 20px;">
            @csrf
            <label for="toppingName">Tên Topping</label>
            <input type="text" id="toppingName" name="name" required>

            <label for="toppingPrice">Giá Topping</label>
            <input type="text" id="toppingPrice" name="price" required>

            <button type="submit">Thêm Topping</button>
        </form>

        <form action="{{ route('admin.size.store') }}" method="POST">
            @csrf
            <label for="sizeName">Tên Size</label>
            <input type="text" id="sizeName" name="name" required>

            <label for="sizePrice">Giá Size</label>
            <input type="text" id="sizePrice" name="price" required>

            <button type="submit">Thêm Size</button>
        </form>

        <button onclick="closeOptionPopup()" class="btn-close-popup">×</button>
    </div>
</div>
<div class="popup_admin" id="popup-confirm-hide">
    <h3 style="color: white;">Bạn có chắc muốn ẩn sản phẩm này không?</h3>
    <div class="button">
        <button onclick="confirmHide()">Xác nhận</button>
        <button onclick="cancelHide()">Hủy</button>
    </div>
</div>
@endsection
@section('script')
<script>
    function handleToppingChange(checkbox, name, price) {
        const container = document.getElementById("selectedToppings");

        if (checkbox.checked) {
            if (!container.querySelector(`[data-id='${checkbox.value}']`)) {
                const item = document.createElement("div");
                item.className = "selected-item";
                item.dataset.id = checkbox.value;
                item.innerHTML = `${name} (+${price.toLocaleString('vi-VN')} đ)
                    <button class="remove-btn" onclick="removeSelection(this, 'toppings')">×</button>`;
                container.appendChild(item);
            }
        } else {
            const item = container.querySelector(`[data-id='${checkbox.value}']`);
            if (item) item.remove();
        }
    }

    function handleSizeChange(checkbox, name, price) {
        const container = document.getElementById("selectedSizes");

        if (checkbox.checked) {
            if (!container.querySelector(`[data-id='${checkbox.value}']`)) {
                const item = document.createElement("div");
                item.className = "selected-item";
                item.dataset.id = checkbox.value;
                item.innerHTML = `${name} (+${price.toLocaleString('vi-VN')} đ)
                    <button class="remove-btn" onclick="removeSelection(this, 'sizes')">×</button>`;
                container.appendChild(item);
            }
        } else {
            const item = container.querySelector(`[data-id='${checkbox.value}']`);
            if (item) item.remove();
        }
    }

    function removeSelection(button, type) {
        const item = button.parentElement;
        const id = item.dataset.id;
        item.remove();

        const checkbox = document.querySelector(`input[name='${type}[]'][value='${id}']`);
        if (checkbox) checkbox.checked = false;
    }

    //Tự động load các tùy chọn đã được check sẵn khi vào trang sửa
    window.addEventListener('DOMContentLoaded', function () {
        // Toppings
        document.querySelectorAll("input[name='toppings[]']:checked").forEach(checkbox => {
            const name = checkbox.closest('.topping-card')?.querySelector('.topping-name')?.innerText || 'Không tên';
            const priceText = checkbox.closest('.topping-card')?.querySelector('.topping-price')?.innerText || '0';
            const price = parseInt(priceText.replace(/[^\d]/g, '') || 0);
            handleToppingChange(checkbox, name, price);
        });

        // Sizes
        document.querySelectorAll("input[name='sizes[]']:checked").forEach(checkbox => {
            const name = checkbox.closest('.topping-card')?.querySelector('.topping-name')?.innerText || 'Size';
            const priceText = checkbox.closest('.topping-card')?.querySelector('.topping-price')?.innerText || '0';
            const price = parseInt(priceText.replace(/[^\d]/g, '') || 0);
            handleSizeChange(checkbox, name, price);
        });

        // Gắn sự kiện khi thay đổi checkbox toppings
        document.querySelectorAll("input[name='toppings[]']").forEach(checkbox => {
            checkbox.addEventListener("change", function () {
                const name = checkbox.closest('.topping-card')?.querySelector('.topping-name')?.innerText || 'Không tên';
                const priceText = checkbox.closest('.topping-card')?.querySelector('.topping-price')?.innerText || '0';
                const price = parseInt(priceText.replace(/[^\d]/g, '') || 0);
                handleToppingChange(checkbox, name, price);
            });
        });

        // Gắn sự kiện khi thay đổi checkbox sizes
        document.querySelectorAll("input[name='sizes[]']").forEach(checkbox => {
            checkbox.addEventListener("change", function () {
                const name = checkbox.closest('.topping-card')?.querySelector('.topping-name')?.innerText || 'Size';
                const priceText = checkbox.closest('.topping-card')?.querySelector('.topping-price')?.innerText || '0';
                const price = parseInt(priceText.replace(/[^\d]/g, '') || 0);
                handleSizeChange(checkbox, name, price);
            });
        });
    });

</script>
    <script>
        // Thêm ảnh mới
        function loadFile(event) {
    const image = document.getElementById('preview-image');
    const file = event.target.files[0];

    if (file) {
        image.src = URL.createObjectURL(file);
        image.style.display = 'block';
    } else {
        image.src = '#';
        image.style.display = 'none';
    }
}

    // Ẩn thông báo sau 3 giây nếu có phần tử #message
    window.addEventListener('DOMContentLoaded', function () {
        const message = document.getElementById('message');
        if (message) {
            setTimeout(() => {
                message.style.display = 'none';
            }, 3000);
        }

        // Hiển thị thông báo lỗi hoặc thành công (nếu có)
        @if (session('msg'))
            alertify.alert('{{ session('msg') }}');
        @endif

        @if ($errors->any())
            alertify.alert('Vui lòng nhập đầy đủ các trường!');
        @endif
    });
</script>

<script>
        // đóng mở popup
        function openOptionPopup() {
            document.getElementById('optionPopup').style.display = 'block';
        }

        function closeOptionPopup() {
            document.getElementById('optionPopup').style.display = 'none';
        }
    </script>

<script>
    let selectedSelect = null;

    function handleChangeStatus(select) {
        if (select.value == "0") {
            selectedSelect = select;
            // Lưu lại trạng thái cũ
            select.dataset.old = "1";
            document.getElementById('popup-confirm-hide').style.display = 'block';
        } else {
            select.dataset.old = "0";
        }
    }

    function confirmHide() {
        document.getElementById('popup-confirm-hide').style.display = 'none';
        // Không cần làm gì thêm vì đã chọn 0
    }

    function cancelHide() {
        if (selectedSelect) {
            selectedSelect.value = selectedSelect.dataset.old;
        }
        document.getElementById('popup-confirm-hide').style.display = 'none';
    }
</script>
@endsection
