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
<<<<<<< HEAD
            <div class="col-lg-8">

                <div class="row">
                    <form action="{{ route('product.store') }}" method="POST" enctype="multipart/form-data"
                        id="formAddProduct" class="form-product">
                        <div class="col">
                            <div class="form-groups">
                                <div class="form-group-product">
                                    <div class="col"><label>Tên Món ăn:</label></div>
                                    <div class="col"><input type="text" onkeyup="checkProduct(this.value)"
                                            class="form-control" id="name" name="name" required></div>
                                    <span id="isset-product"></span>
                                    @error('name')
                                        <span class="text-danger" style="color:red">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group-product">
                                    <div class="col"><label>Mô tả:</label></div>
                                    <textarea name="description" required></textarea>
                                    @error('description')
                                        <span class="text-danger" style="color:red">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group-product">
                                    <div>
                                        <div class="col"><label>Danh mục:</label></div>
                                        <div class="col">
                                            <select name="category" onchange="loadBrandAndCategorySpe(this)">
                                                @foreach ($danhSachPhanLoai as $phanLoai)
                                                    <option value="{{ $phanLoai->id }}">{{ $phanLoai->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    {{-- <div>
                                        <div class="col"><label>Thương hiệu:</label></div>
                                        <div class="col">
                                            <select name="brand" id="brands">
                                                @foreach ($danhSachThuongHieu as $thuongHieu)
                                                    <option value="{{ $thuongHieu->id }}">{{ $thuongHieu->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div> --}}
                                </div>
                                <div class="form-group-product">
                                    <div class="col">
                                        <label>Hình ảnh:</label>
                                    </div>
                                    <div> <button type="button" data-idx=1 onclick="addImage(this)">Thêm hình ảnh</button>
                                    </div>
                                    <div class="col" id="image-products">
                                        <img id="output-1" />
                                        <input type="file" data-index=1 onchange="loadFile(event,this)"
                                            class="form-control" style="background-color:white" name="image[0]" required>
                                    </div>
                                    @error('image')
                                        <span class="text-danger" style="color:red">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            {{-- <div class="form-groups" id="category-specification">
                                @foreach ($danhSachThongTinKyThuat as $index => $thongTinKyThuat)
                                    <div class=" form-group-product">
                                        <div class="col">
                                            <label>{{ $thongTinKyThuat->name }}</label>
                                        </div>
                                        <div class="col">
                                            <input type="hidden" name="specification[{{ $index }}]"
                                                value="{{ $thongTinKyThuat->id }}">
                                            <input type="text" class="form-control" name="value[{{ $index }}]" required>

                                        </div>
                                    </div>
                                @endforeach
                                 Hiển thị xong 

                            </div> --}}
                            <div class="row">
                                <p>Thêm các biến thể</p>
                                <div>
                                    <button type="button" data-index=0 onclick="addVariant(this)">Thêm biến thể</button>
                                </div>
                                <div id="variants">
                                    <div>
                                        <p>Biến thể 1</p>
                                        <span>
                                            Màu sắc
                                            <input type="text" name="variants[0][color]" required>
                                        </span>
                                        <span>
                                            Dung lượng
                                            <input type="text" name="variants[0][internal_memory]" required>
                                        </span>
                                        <span>
                                            Giá
                                            <input type="number" min="0" name="variants[0][price]" required>
                                        </span>
                                        <div>
                                            <span>
                                                Số lượng
                                                <input type="number" min="0" name="variants[0][stock]" required>
                                            </span>
                                            <span>
                                                Hình ảnh
                                                <input type="file" name="variants[0][image_variant]" required>
                                            </span>
=======
            <form action="{{ route('product.store') }}" method="POST" enctype="multipart/form-data" id="formAddProduct">
                @csrf
                <div class="form-group">
                    <div class="col">
                        <div class="form-group-row">
                            <div class="form-group-product">
                                <label>Tên món ăn</label>
                                <input type="text" class="form-control" name="name" required>
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
                                <textarea name="description" rows="4" required></textarea>
                            </div>
                            <div class="form-group-product short-input">
                                <label>Giá tiền</label>
                                <input type="text" class="form-control" name="price" required>
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
>>>>>>> 521da537225f710a7f10e4c2ea3d0c804cd43cb5
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
                <input type="number" id="toppingPrice" name="price" required>

                <button type="submit">Thêm Topping</button>
            </form>

            <form action="{{ route('admin.size.store') }}" method="POST">
                @csrf
                <label for="sizeName">Tên Size</label>
                <input type="text" id="sizeName" name="name" required>

                <label for="sizePrice">Giá Size</label>
                <input type="number" id="sizePrice" name="price" required>

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
                issetSpan.textContent = "Tên món ăn không được bỏ trống";
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
                        issetSpan.textContent = "Tên món ăn hợp lệ!";
                    } else {
                        issetSpan.style.color = "red";
                        issetSpan.textContent = "Tên món ăn đã tồn tại!";
                    }
                });
            }
<<<<<<< HEAD

        }
    </script>
    <script>
        var loadFile = function(event, img) {
            const idx = img.dataset.index;
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('output-' + idx);
                output.src = reader.result;
                output.style.width = "150px";
                output.style.height = "150px";
            };
            reader.readAsDataURL(event.target.files[0]);
        };
    </script>
    <script>
        function notification() {
            setTimeout(() => {
                message.style.display = 'none';
            }, 3000);
        }
    </script>

    <script>
        // function addVariant(btn) {
        //     btn.dataset.index++;
        //     const variants = document.getElementById('variants');
        //     const variant = `<div>
        //                     <p> Biến thể ${Number(btn.dataset.index)+1}</p>
        //                     <span>
        //                         Màu sắc
        //                         <input type="text" name="variants[${Number(btn.dataset.index)}][color]" required>
        //                     </span>
        //                     <span>
        //                         Dung lượng
        //                         <input type="text" name="variants[${Number(btn.dataset.index)}][internal_memory]" required>
        //                     </span>
        //                     <span>
        //                         Giá
        //                         <input type="number" min="0" name="variants[${Number(btn.dataset.index)}][price]" required>
        //                     </span>
        //                     <div>
        //                         <span>
        //                             Số lượng
        //                             <input type="number" min="0" name="variants[${Number(btn.dataset.index)}][stock]" required>
        //                         </span>
        //                         <span>
        //                             Hình ảnh
        //                             <input type="file" name="variants[${Number(btn.dataset.index)}][image_variant]" required>
        //                         </span>
        //                     </div>

        //                 </div>`;
        //     variants.insertAdjacentHTML('beforeend', variant);
        // }
    </script>
    <script>
        function loadBrandAndCategorySpe(category) {
            loadCategorySpecification(category);
            loadBrands(category);
=======
>>>>>>> 521da537225f710a7f10e4c2ea3d0c804cd43cb5
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
<<<<<<< HEAD
    </script>
    <script>
            const input = document.getElementById("name");
            input.addEventListener("invalid", function () {
            input.setCustomValidity("Vui lòng nhập tên món ăn vào đây!");
=======

                                    // Cảnh báo HTML5 input name
                    const input = document.getElementById("name");
        input.addEventListener("invalid", function () {
            input.setCustomValidity("Vui lòng nhập tên sản phẩm vào đây!");
>>>>>>> 521da537225f710a7f10e4c2ea3d0c804cd43cb5
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
@endsection
