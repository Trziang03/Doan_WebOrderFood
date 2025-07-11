@extends('layouts.layouts_user')
@section('title', 'Trang Menu')
@section('content')
    <style>
        button.buy_now {
            width: 100%;
            padding: 5px 0;
            font-size: 13px;
            border: none;
            font-weight: 800;
            color: #ffffff;
            background-color: rgb(240, 145, 55);
            cursor: pointer;
            border-radius: 10px;
            margin-top: 10px;
        }

        /* #productModal .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 20px;
            border-radius: 10px;
            max-width: 480px;
            width: 90%;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.3);
            position: relative;
            animation: slideIn 0.3s ease-in-out;
        }

        #productModal .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 24px;
            font-weight: bold;
            color: #333;
            cursor: pointer;
            transition: color 0.2s ease;
        }

        #productModal .close-btn:hover {
            color: #000;
        }

        .product-image img {
            width: 100%;
            border-radius: 8px;
        }

        .product-body {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .product-image {
            flex: 1 1 40%;
        }

        .product-info {
            flex: 1 1 55%;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .product-price {
            font-weight: bold;
            color: #d9534f;
            font-size: 18px;
        }

        .form-group label {
            font-weight: 600;
            display: inline-block;
            margin-bottom: 5px;
        }

        select {
            padding: 6px;
            border-radius: 5px;
            border: 1px solid #ccc;
            width: 50%;
        }

        input {
            padding: 4px;
            border: 1px solid #ccc;
            width: 30px;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .quantity-control button {
            width: 32px;
            height: 32px;
            background-color: #ddd;
            border: none;
            font-size: 18px;
            cursor: pointer;
            border-radius: 4px;
        }

        .quantity-control button:hover {
            background-color: #ccc;
        }

        .buy-btn {
            margin-top: 10px;
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            width: 85%;
            transition: background-color 0.2s ease;
        }

        .buy-btn:hover {
            background-color: #218838;
        }

        @media (max-width: 480px) {
            .product-body {
                flex-direction: column;
            }

            .product-image,
            .product-info {
                flex: 1 1 100%;
            }

            .buy-btn {
                font-size: 14px;
                padding: 10px;
            }

            #productModal .modal-content {
                padding: 15px;
            }
        } */
    </style>
    @php
        $danhSachDanhMuc = DB::table('categories')->where('status', 1)->select('name', 'slug', 'id')->get();
    @endphp
    <section class="container_css product_searchs">
        <div class="product_search_lists">
            <div class="product_search_list_left">
                <div>
                    <div style="text-align: center; margin-bottom: 10px;font-size: 25px;">
                        <strong>Bàn: {{ $table->name ?? ' ' }}</strong>
                    </div>
                </div>
                <div>
                    <h5><i class="fas fa-filter" style="margin-right: 5px;"></i>Bộ lọc tìm kiếm</h5>
                    <div class="product_search product_search_list_category">
                        <p class="category-toggle">Danh mục <i class="fas fa-sort-down"></i></p>
                        <div class="product_search_list_category_popup">
                            <a href="{{route('user.menu')}}">Tất cả</a>
                            @foreach ($danhSachDanhMuc as $danhMuc)
                                <a
                                    href="{{ route('timkiemsanphamtheodanhmuc', ['slug' => $danhMuc->slug]) }}">{{ $danhMuc->name }}</a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="product_search_list_right">
                <div class="product_search_list_right_items">
                    @if (isset($layTatCaSanPham) && $layTatCaSanPham->isNotEmpty())
                            @foreach ($layTatCaSanPham as $item)
                                <div class="product_search_list_right_item">
                                    <a href="{{ route('detail', [$item->slug]) }}">
                                        <img src="{{ asset(isset($item->image) ? $item->image : $item->image_food) }}"
                                            alt="Lỗi hiển thị">
                                    </a>
                                    <div class="product_search_list_item_info">
                                        <ul>
                                            <li><a href="{{ route('detail', $item->slug) }}">{{ $item->name }}</a></li>
                                            <li class="price">{{ number_format($item->price, 0, ',', '.') }}<sup>đ</sup></li>
                                            <li>
                                                <button class="buy_now" onclick="buyNowSearch({{$item->id}})">Thêm vào giỏ</button>
                                            </li>
                                            <!-- <li>
                                                <button class="buy_now" onclick="openModal({{ $item->id }})">Thêm vào giỏ</button>
                                            </li> -->
                                        </ul>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="page" id="page"></div>
                    @else
                    <div style="color: black; text-align:center; width:100%; margin-top:155px; height: 176px;">
                        <h3>Không tìm thấy món ăn nào</h3>
                    </div>
                @endif
            </div>
        </div>
    </section>
    <!-- <div id="productModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span onclick="closeModal()" class="close-btn">&times;</span>

            <h2 id="productName" class="product-name"></h2>

            <div class="product-body">

                <div class="product-image">
                    <img id="productImage" src="" alt="Ảnh món ăn">
                </div>

                <div class="product-info">

                    <p><strong>Giá:</strong> <span id="totalPrice" class="product-price">0đ</span></p>

                    <div class="form-group">
                        <label for="size_id"><strong>Chọn size:</strong></label>
                        <select id="size_id" onchange="calculateTotal()"></select>
                    </div>

                    <div class="form-group" id="topping_list">
                        <label><strong>Chọn topping:</strong></label>
                    </div>

                    <div class="form-group">
                        <label><strong>Số lượng:</strong></label>
                        <div class="quantity-control">
                            <button onclick="changeQuantity(-1)">-</button>
                            <input id="quantity" value="1" min="1" max="10" onchange="calculateTotal()">
                            <button onclick="changeQuantity(1)">+</button>
                        </div>
                    </div>

                    <button onclick="buyNow()" class="buy-btn">Thêm vào giỏ hàng</button>
                </div>
            </div>
        </div>
    </div> -->
@endsection
@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.querySelector('.category-toggle');
            const container = document.querySelector('.product_search_list_category');

            toggleBtn.addEventListener('click', function () {
                container.classList.toggle('active');
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            kt(); // Khởi tạo danh sách sản phẩm
            Page();
        });

        function kt() {
            const products = document.querySelectorAll('.product_search_list_right_item');
            return products;
        }

        function Page(itemsPage = 8) {
            const products = Array.from(kt());

            const countPage = Math.ceil(products.length / itemsPage);
            let index = 1;

            function LoadPage(page) {
                products.forEach(product => product.style.display = "none");
                const begin = (page - 1) * itemsPage;
                const end = begin + itemsPage;
                products.slice(begin, end).forEach(product => {
                    product.style.display = 'block';
                });
                LoadPageButton(countPage, page);
            }

            function LoadPageButton(countPage, index) {
                const page = document.getElementById('page');
                page.innerHTML = '';
                // Nút "Pre"
                const pre = document.createElement('button');
                pre.innerHTML = "Pre";
                pre.disabled = index === 1;
                pre.addEventListener('click', () => LoadPage(index - 1));
                page.appendChild(pre);
                // Nút số trang
                for (let i = 1; i <= countPage; i++) {
                    const button = document.createElement('button');
                    button.innerHTML = i;
                    button.className = i === index ? 'active' : '';
                    button.addEventListener('click', () => LoadPage(i));
                    page.appendChild(button);
                }
                // Nút "Next"
                const next = document.createElement('button');
                next.innerHTML = "Next";
                next.disabled = index === countPage;
                next.addEventListener('click', () => LoadPage(index + 1));
                page.appendChild(next);
            }

            if (products.length > 0) {
                LoadPage(index);
            } else {
                if (document.getElementById('page')) {
                    document.getElementById('page').innerHTML =
                        '<p>Không có sản phẩm nào phù hợp.</p>';
                }
            }
        }
    </script>
    <script>
        function buyNowSearch(productId) {
            const data = {
                product_id: productId,
                size_id: 1,       // mặc định size là 1
                quantity: 1,
                note: ''          // không cần ghi chú
                // Không gửi topping
                // Không cần gửi table_id nếu lưu trong session
            };

            fetch("/cart/add", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify(data)
            })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        // Cập nhật cả desktop và mobile icon
                        const desktopCart = document.getElementById('cart-quantity');
                        if (desktopCart) {
                            desktopCart.textContent = res.cart.totalQuantity;
                        }
                        const mobileCart = document.querySelector('.number_cart_mb_tl');
                        if (mobileCart) {
                            mobileCart.textContent = res.cart.totalQuantity;
                        }
                        return alertify.alert("Thông báo", res.message);
                    } else {
                        return alertify.alert("Thông báo", res.message);
                    }
                })
                .catch(err => {
                    console.error(err);
                    return alertify.alert("Thông báo", "Lỗi kết nối máy chủ!");
                });
        }
    </script>
    <script>
        function autoCheckTableStatus() {
            fetch('/table/check-status')
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'blocked') {
                        // Ẩn nút "Xem đơn hàng"
                        const container = document.getElementById('order-link-container');
                        if (container) container.innerHTML = '';

                        // Xóa giỏ hàng số lượng
                        document.getElementById('cart-quantity')?.textContent = '0';
                        document.querySelector('.number_cart_mb_tl')?.textContent = '0';

                        alertify.alert("Thông báo", res.message, function () {
                            window.location.href = '/404'; // Hoặc về trang chủ
                        });
                    }
                });
        }

        autoCheckTableStatus();
        setInterval(autoCheckTableStatus, 10000); // Kiểm tra mỗi 10s
    </script>
    <!-- <script>
                let currentProductId = null;
                let currentSizes = [];
                let currentToppings = [];
                let basePrice = 0;

                // Format tiền Việt
                function formatCurrency(value) {
                    return parseInt(value).toLocaleString('vi-VN') + 'đ';
                }

                // Mở popup sản phẩm
                function openModal(productId) {
                    currentProductId = productId;

                    fetch(`/product/info/${productId}`)
                        .then(res => res.json())
                        .then(data => {
                            if (!data.success) return alert(data.message || "Không tìm thấy sản phẩm");

                            const product = data.product;
                            basePrice = parseInt(product.price) || 0;
                            currentSizes = data.sizes || [];
                            currentToppings = data.toppings || [];

                            // Gán thông tin
                            document.getElementById('productName').innerText = product.name;
                            document.getElementById('productImage').src = product.image_food;
                            document.getElementById('quantity').value = 1;

                            // Render size
                            const sizeSelect = document.getElementById('size_id');
                            sizeSelect.innerHTML = '';
                            currentSizes.forEach((size, index) => {
                                sizeSelect.innerHTML += `
                                        <option value="${size.id}" ${index === 0 ? 'selected' : ''}>
                                            ${size.name} - ${formatCurrency(size.price)}
                                        </option>`;
                            });

                            // Render topping
                            const toppingDiv = document.getElementById('topping_list');
                            toppingDiv.innerHTML = '<label><strong>Chọn topping:</strong></label><br>';

                            currentToppings.forEach(top => {
                                toppingDiv.innerHTML += `
                                        <div class="topping-item" style="margin-bottom: 5px;">
                                            <label>
                                                <input type="checkbox" class="topping-checkbox" data-topping-id="${top.id}">
                                                ${top.name} (+${formatCurrency(top.price)})
                                            </label>
                                            <input type="number" class="topping-qty" value="0" min="1"
                                                data-price="${top.price}" data-topping-id="${top.id}"
                                                disabled style="width: 50px; margin-left: 10px; display: none;">
                                        </div>`;
                            });

                            // Gán sự kiện toggle checkbox
                            document.querySelectorAll('.topping-checkbox').forEach(checkbox => {
                                checkbox.addEventListener('change', () => {
                                    const toppingId = checkbox.dataset.toppingId;
                                    const qtyInput = document.querySelector(`.topping-qty[data-topping-id="${toppingId}"]`);
                                    if (checkbox.checked) {
                                        qtyInput.disabled = false;
                                        qtyInput.value = 1;
                                        qtyInput.style.display = "inline-block";
                                    } else {
                                        qtyInput.disabled = true;
                                        qtyInput.value = 0;
                                        qtyInput.style.display = "none";
                                    }
                                    calculateTotal();
                                });
                            });

                            // Hiển thị popup
                            document.getElementById('productModal').style.display = 'block';
                            calculateTotal();
                        })
                        .catch(() => alert("Không thể tải dữ liệu sản phẩm"));
                }

                // Đóng popup
                function closeModal() {
                    document.getElementById('productModal').style.display = 'none';
                }

                // Tăng/giảm số lượng
                function changeQuantity(change) {
                    const input = document.getElementById('quantity');
                    let val = parseInt(input.value) || 1;
                    val = Math.max(1, Math.min(10, val + change));
                    input.value = val;
                    calculateTotal();
                }

                // Tính tổng tiền
                function calculateTotal() {
                    const selectedSizeId = parseInt(document.getElementById('size_id').value);
                    const selectedSize = currentSizes.find(s => s.id == selectedSizeId);
                    const sizePrice = selectedSize ? parseInt(selectedSize.price) : 0;

                    let toppingTotal = 0;
                    document.querySelectorAll('.topping-qty').forEach(input => {
                        const quantity = parseInt(input.value) || 0;
                        const price = parseInt(input.dataset.price) || 0;
                        toppingTotal += quantity * price;
                    });

                    const quantity = parseInt(document.getElementById('quantity').value) || 1;
                    const unitPrice = basePrice + sizePrice + toppingTotal;
                    const totalPrice = unitPrice * quantity;

                    document.getElementById('totalPrice').textContent = formatCurrency(totalPrice);
                }

                // Gửi thêm vào giỏ hàng
                function buyNow() {
                    const size_id = parseInt(document.getElementById('size_id').value);
                    const quantity = parseInt(document.getElementById('quantity').value);
                    const toppings = {};

                    document.querySelectorAll('.topping-qty').forEach(input => {
                        const toppingId = input.dataset.toppingId;
                        const val = parseInt(input.value);
                        if (val > 0) toppings[toppingId] = val;
                    });

                    const data = {
                        product_id: currentProductId,
                        size_id: size_id,
                        quantity: quantity,
                        note: '',
                        topping_quantities: toppings
                    };

                    fetch("/cart/add", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(data)
                    })
                        .then(res => res.json())
                        .then(res => {
                            if (res.success) {
                                alertify.alert(res.message);
                                closeModal();
                                document.getElementById('cart-quantity').innerText = res.cart.totalQuantity || 0;
                            } else {
                                alertify.alert(res.message || "Thêm vào giỏ thất bại");
                            }
                        })
                        .catch(() => alertify.alert("Lỗi khi gửi dữ liệu lên máy chủ"));
                }
            </script> -->
@endsection
