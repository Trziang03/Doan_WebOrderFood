@extends('layouts.layouts_admin')
@section('title', 'Trang thêm món ăn')
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
            <div class="title">Thêm món ăn</div>
        </div>
        <div class="separator_x"></div>
        @if (session('message'))
            <script>
                alertify.success("{{ session('message') }}");
            </script>
        @endif
        <div class="row">
            <form action="{{ route('product.store') }}" method="POST" enctype="multipart/form-data" id="formAddProduct">
                @csrf
                <div class="form-group">
                    <div class="col">
                        <div class="form-group-row">
                            <div class="form-group-product">
                                <label>Tên món ăn</label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    name="name" 
                                    id="food_name" 
                                    oninput="validateFoodName()" 
                                    required 
                                    placeholder="Nhập tên món ăn">
                            
                                <span id="food_name_error" style="color: red; font-size: 12px;"></span>
                            </div>
                            
                            <div class="form-group-product">
                                <label>Danh mục</label>
                                <select name="category_id" required>
                                    @foreach ($danhSachPhanLoai as $phanLoai)
                                        <option value="{{ $phanLoai->id }}">{{ $phanLoai->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group-product">
                                <label>Trạng thái</label>
                                <select name="status" required>
                                    <option value="1">Hiển thị</option>
                                    <option value="0">Ẩn</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group-row">
                            <div class="form-group-product">
                                <label>Mô tả</label>
                                <textarea 
                                    name="description" 
                                    id="description" 
                                    rows="4" 
                                    required 
                                    oninput="validateDescription()"
                                    placeholder="Nhập mô tả món ăn"
                                ></textarea>
                            
                                <span id="description_error" style="color:red; font-size: 12px;"></span>
                            </div>
                            <div class="form-group-product short-input">
                                <label>Giá tiền</label>
                                <input 
                                    type="number" 
                                    class="form-control" 
                                    name="price" 
                                    id="price" 
                                    min="1000" 
                                    max="100000" 
                                    oninput="validatePrice()" 
                                    required 
                                    placeholder="Nhập giá từ 1.000 đến 100.000">
                            
                                <span id="price_error" style="color:red; font-size: 12px;"></span>
                            </div>
                            <div class="form-group-product short-input">
                                <label>Hình ảnh:</label>
                                <div id="image-products">
                                    <img id="preview-image" style="max-width: 150px; max-height: 150px; display: none;" />
                                    <input type="file" onchange="loadFile(event)" class="form-control"
                                        style="background-color:white" name="image" required>
                                </div>
                                @error('image')
                                    <span class="text-danger" style="color:red">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group-product">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <label style="margin: 0;">Toppings có sẵn</label>
                                <button type="button" class="btn" onclick="openOptionPopup()">+ Thêm
                                    Topping/Size</button>
                            </div>
                            <div class="topping-grid">
                                <!-- Toppings -->
                                @foreach ($toppings as $topping)
                                    <div class="topping-card">
                                        <input type="checkbox" name="toppings[]" value="{{ $topping->id }}"
                                            onchange="handleToppingChange(this, '{{ $topping->name }}', {{ $topping->price }})"
                                            class="topping-checkbox">
                                        <div class="topping-content">
                                            <div class="topping-name">{{ $topping->name }}</div>
                                            <div class="topping-price">
                                                {{ $topping->price > 0 ? '+' . number_format($topping->price, 0, ',', '.') . ' đ' : '+0 đ' }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <label style="margin-top: 20px;">Sizes có sẵn</label>
                            <div class="topping-grid">
                                <!-- Sizes -->
                                @foreach ($sizes as $size)
                                    <div class="topping-card">
                                        <input type="checkbox" name="sizes[]" value="{{ $size->id }}"
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
                            <!-- dùng cùng class cho đồng bộ -->
                        </div>
                        <div class="btn-goback">
                            <button>Xác nhận thêm</button>
                            <button type="reset">Hủy</button>
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
@endsection

@section('script')
    <script>
        function handleToppingChange(checkbox, name, price) {
            const container = document.getElementById("selectedToppings");

            if (checkbox.checked) {
                const item = document.createElement("div");
                item.className = "selected-item";
                item.dataset.id = checkbox.value;
                item.innerHTML = `${name} (+${price.toLocaleString('vi-VN')} đ)
                                    <button class="remove-btn" onclick="removeSelection(this, 'toppings')">×</button>`;
                container.appendChild(item);
            } else {
                const item = container.querySelector(`[data-id='${checkbox.value}']`);
                if (item) item.remove();
            }
        }

        function handleSizeChange(checkbox, name, price) {
            const container = document.getElementById("selectedSizes");

            if (checkbox.checked) {
                const item = document.createElement("div");
                item.className = "selected-item";
                item.dataset.id = checkbox.value;
                item.innerHTML = `${name} (+${price.toLocaleString('vi-VN')} đ)
                                    <button class="remove-btn" onclick="removeSelection(this, 'sizes')">×</button>`;
                container.appendChild(item);
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
    </script>
    <script>
        // Kiểm tra tên sản phẩm đã tồn tại
        function checkProduct(name) {
            let issetSpan = document.getElementById('isset-product');
            name = name.trim();
            if (name === "") {
                issetSpan.style.color = "red";
                issetSpan.textContent = "Tên sản phẩm không được bỏ trống";
            } else {
                $.ajax({
                    method: "POST",
                    url: '/admin/product/is_isset',
                    data: {
                        name,
                        _token: '{{ csrf_token() }}',
                    }
                }).done((data) => {
                    if (data == 0) {
                        issetSpan.style.color = "green";
                        issetSpan.textContent = "Tên sản phẩm hợp lệ!";
                    } else {
                        issetSpan.style.color = "red";
                        issetSpan.textContent = "Tên sản phẩm đã tồn tại!";
                    }
                });
            }
        }

        // Hiển thị preview ảnh sản phẩm
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

        // Cảnh báo nếu validate lỗi
        @if ($errors->any())
            alertify.alert('Vui lòng nhập đầy đủ các trường!');
        @endif

                                    // Cảnh báo HTML5 input name
                    const input = document.getElementById("name");
        input.addEventListener("invalid", function () {
            input.setCustomValidity("Vui lòng nhập tên sản phẩm vào đây!");
            input.addEventListener("input", function () {
                input.setCustomValidity("");
            });
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
        //kiểm tra giá ngay lúc nhập
        function validatePrice() {
            const input = document.getElementById('price');
            const error = document.getElementById('price_error');
            const value = parseInt(input.value);
        
            if (!value) {
                error.textContent = 'Vui lòng nhập giá tiền.';
                return false;
            }
        
            if (value < 1000) {
                error.textContent = 'Giá tiền tối thiểu là 1.000.';
                return false;
            }
        
            if (value > 100000) {
                error.textContent = 'Giá tiền tối đa là 100.000.';
                return false;
            }
        
            // ✅ Nếu hợp lệ
            error.textContent = '';
            return true;
        }
        document.getElementById('form-id').addEventListener('submit', function(event) {
            if (!validatePrice()) {
                event.preventDefault(); // Ngăn gửi nếu sai giá
            }
        });

        //kiểm tra tên món ngay lúc nhập
        function validateFoodName() {
            const input = document.getElementById('food_name');
            const error = document.getElementById('food_name_error');
            const value = input.value.trim();

            if (value === '') {
                error.textContent = 'Tên món ăn không được để trống.';
                return false;
            }

            if (value.length > 100) {
                error.textContent = 'Tên món ăn không được vượt quá 100 ký tự.';
                return false;
            }

            // ✅ Regex mới: KHÔNG CHO SỐ, chỉ cho chữ (có dấu), khoảng trắng và gạch ngang
            const regex = /^[\p{L}\s\-]+$/u; //được phép nhập tiếng Việt và dấu gạch ngang - 

            if (!regex.test(value)) {
                error.textContent = 'Tên món ăn chỉ được chứa chữ, dấu cách và gạch ngang (không số, không ký tự đặc biệt).';
                return false;
            }

            // ✅ Hợp lệ
            error.textContent = '';
            return true;
        }

        document.getElementById('form-id').addEventListener('submit', function(e) {
            if (!validateFoodName()) {
                e.preventDefault(); 
            }
        });

    </script>
    <script>
        function validateDescription() {
            const input = document.getElementById('description');
            const error = document.getElementById('description_error');
            const value = input.value.trim();
        
            if (value === '') {
                error.textContent = 'Mô tả không được để trống.';
                return false;
            }
        
            if (value.length > 255) {
                error.textContent = 'Mô tả không được vượt quá 255 ký tự.';
                return false;
            }
        
            // ✅ Cho phép chữ có dấu, số, dấu cách, chấm, phẩy, dấu gạch, ngoặc kép đơn giản
            const regex = /^[\p{L}0-9\s.,\-'"()!?]+$/u;
        
            if (!regex.test(value)) {
                error.textContent = 'Mô tả chỉ được chứa chữ, số, dấu cách và một số dấu cơ bản (, . - \' ")';
                return false;
            }
        
            error.textContent = '';
            return true;
        }

        
    </script>
            
@endsection
