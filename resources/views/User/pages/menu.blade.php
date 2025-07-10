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

        .product_search_list_size_popup button {
            margin: 4px;
            padding: 5px 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            cursor: pointer;
        }

        .product_search_list_size_popup button.active_price {
            background-color: rgb(240, 145, 55);
            color: white;
        }

        .active_size {
            background-color: rgb(240, 145, 55);
            color: #fff;
            border: 1px solid #ff6600;
        }

        /* ========== Đặt trong file home.css hoặc user_min.css ========== */

        .product_search {
            margin-bottom: 20px;
        }

        .product_search_list_size_popup,
        .product_search_list_price_popup {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }

        .product_search_list_size_popup button,
        .product_search_list_price_popup button {
            padding: 10px 14px;
            border: 2px solid #ffa34d;
            border-radius: 20px;
            background-color: #fff;
            color: #ffa34d;
            font-weight: 600;
            transition: all 0.3s ease;
            min-width: 100px;
            text-align: center;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .product_search_list_size_popup button:hover,
        .product_search_list_price_popup button:hover {
            background-color: #ffe0c1;
        }

        .product_search_list_size_popup button.active_size,
        .product_search_list_price_popup button.active_price {
            background-color: #ffa34d;
            color: #fff;
            border-color: #ffa34d;
        }

        .btn-toggle-filter {
            top: 80px ;
            right: 10px;
            left: auto;
            background-color: #ffa34d;
            color: white;
            padding: 10px 12px;
            border-radius: 50px;
            border: none;
            font-size: 14px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        }


        .filter-panel {
            position: fixed;
            top: 0;
            left: -70%;
            /* Trượt ra từ bên trái */
            width: 60%;
            max-width: 320px;
            height: 100%;
            background: rgba(255, 255, 255, 0.95);
            /* nền mờ nhẹ */
            backdrop-filter: blur(50px);
            z-index: 9999;
            transition: left 0.3s ease;
            padding: 20px;
            box-shadow: -2px 0 8px rgba(0, 0, 0, 0.15);
        }

        /* Khi mở */
        .filter-panel.open {
            left: 0;
        }



        .filter-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 15px;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 26px;
            cursor: pointer;
        }



        /* Mobile view */
        @media screen and (max-width: 576px) {

            .product_search_list_size_popup button,
            .product_search_list_price_popup button {
                min-width: 30%;
                font-size: 14px;
            }

            .product_search {
                text-align: center;
            }

            .product_search p {
                font-weight: bold;
                margin-bottom: 8px;
                font-size: 16px;
            }
        }
    </style>
    @php
        $danhSachDanhMuc = DB::table('categories')->where('status', 1)->select('name', 'slug', 'id')->get();
    @endphp

    <section class="container_css product_searchs">
        <div class="product_search_lists">
            <div class="product_search_list_left">
                <div >
                    <div style="text-align: center; margin-bottom: 10px;font-size: 30px;">
                        <strong style="margin-top: 10px;">Bàn: {{ $table->name }}</strong>
                        <div>
                            <button onclick="toggleFilterPanel()" class="btn-toggle-filter">
                                <i class="fas fa-filter"></i> Bộ lọc
                            </button>
        
                        </div>
    
                    </div>
                </div>
                    <div class="filter-panel" id="filterPanel">
                        <div class="filter-header">
                            <span>Bộ lọc</span>
                            <button onclick="toggleFilterPanel()" class="close-btn">&times;</button>
                        </div>
                        <!-- Bên dưới là nội dung bộ lọc như bạn đang có -->
                        <div class="filter-body">
                            <!-- Danh mục, Size, Mức giá ... giữ nguyên như bạn có -->
                            <div class="product_search product_search_list_category">
                                <p class="category-toggle">Danh mục <i class="fas fa-sort-down"></i></p>
                                <div class="product_search_list_category_popup">
                                    <a href="{{ route('user.menu') }}">Tất cả</a>
                                    @foreach ($danhSachDanhMuc as $danhMuc)
                                        <a
                                            href="{{ route('timkiemsanphamtheodanhmuc', ['slug' => $danhMuc->slug]) }}">{{ $danhMuc->name }}</a>
                                    @endforeach
                                </div>
                            </div>
                            <div class="product_search product_search_list_size">
                                <p>Kích cỡ</p>
                                <div class="product_search_list_size_popup">
                                    <button onclick="searchBySize(undefined, this)">Tất cả</button>
                                    <button onclick="searchBySize('S', this)">Size S</button>
                                    <button onclick="searchBySize('M', this)">Size M</button>
                                    <button onclick="searchBySize('L', this)">Size L</button>
                                </div>
                            </div>
                            <div class="product_search product_search_list_price">
                                <p>Mức giá</p>
                                <div class="product_search_list_price_popup">
                                    <button id ="searchall"onclick="searchProduct(undefined,undefined,undefined,this)"
                                        class="active_price">Tất cả</button>
                                    <button onclick="searchProduct(0,10000,undefined,this)">Từ dưới 10K</button>
                                    <button onclick="searchProduct(10000,25000,undefined,this)">Từ 10 đến 25K</button>
                                    <button onclick="searchProduct(25000,35000,undefined,this)">Từ 25 đến 35k</button>
                                    <button onclick="searchProduct(35000,undefined,undefined,this)">Trên 35K</button>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="product_search_list_right">
                <div class="product_search_list_right_items">
                    @if (isset($layTatCaSanPham) && $layTatCaSanPham->isNotEmpty())
                        @foreach ($layTatCaSanPham as $item)
                            @php
                                $dsSize = $item->sizes
                                    ->pluck('name')
                                    ->map(function ($val) {
                                        return strtolower($val);
                                    })
                                    ->implode(',');
                            @endphp
                            <div class="product_search_list_right_item" data-size="{{ $dsSize }}">
                                <a href="{{ route('detail', [$item->slug]) }}">
                                    <img src="{{ asset(isset($item->image) ? $item->image : $item->image_food) }}"
                                        alt="Lỗi hiển thị">
                                </a>
                                <div class="product_search_list_item_info">
                                    <ul>
                                        <li><a href="{{ route('detail', $item->slug) }}">{{ $item->name }}</a></li>
                                        <li class="price">{{ number_format($item->price, 0, ',', '.') }}<sup>đ</sup></li>
                                        <li>
                                            <button class="buy_now" onclick="buyNowSearch({{ $item->id }})">Thêm vào
                                                giỏ</button>
                                        </li>
                                    </ul>
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
@endsection
@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.querySelector('.category-toggle');
            const container = document.querySelector('.product_search_list_category');

            toggleBtn.addEventListener('click', function() {
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
    <!-- <script>
        function autoCheckTableStatus() {
            fetch('admin/table/check-status')
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'blocked') {
                        // Reset icon giỏ hàng về 0
                        const desktopCart = document.getElementById('cart-quantity');
                        if (desktopCart) desktopCart.textContent = '0';

                        const mobileCart = document.querySelector('.number_cart_mb_tl');
                        if (mobileCart) mobileCart.textContent = '0';

                        alertify.alert("Thông báo", res.message, function () {
                            window.location.href = '/404';
                        });
                    }
                });
        }
        // Gợi ý: 5s kiểm tra 1 lần
        setInterval(autoCheckTableStatus, 5000);
    </script> -->
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
    <script>
        function searchProduct(min = 0, max = Infinity, itemsPage = 8, btn = null) {
            const active_color_price = document.querySelectorAll('.product_search_list_price_popup button');
            if (active_color_price) {
                active_color_price.forEach((element) => {
                    active_color_price.forEach(btn => btn.classList.remove('active_price'));
                });
                btn.classList.add('active_price');
                const products = Array.from(kt());
                const searchProduct = [];
                products.forEach(function(product) {
                    const priceText = product.querySelector('.price').innerHTML;
                    const price = parseInt(priceText.replace(/[^0-9]/g, ''));
                    if (price >= min && price <= max) {
                        searchProduct.push(product);
                    } else {
                        product.style.display = "none";
                    }
                });
                const countPage = Math.ceil(searchProduct.length / itemsPage);
                let index = 1;

                function LoadPage(page) {
                    searchProduct.forEach(product => product.style.display = "none");
                    const begin = (page - 1) * itemsPage;
                    const end = begin + itemsPage;
                    searchProduct.slice(begin, end).forEach(product => {
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

                if (searchProduct.length > 0) {
                    LoadPage(index);
                } else {
                    if (document.getElementById('page')) {
                        document.getElementById('page').innerHTML =
                            '<p>Không có sản phẩm nào phù hợp.</p>';
                    }
                }
            }
        }
    </script>


    <script>
        let selectedSize = undefined;
        let selectedPrice = {
            min: 0,
            max: Infinity
        };
        let activeSizeBtn = null;
        let activePriceBtn = null;



        function searchBySize(size, btn) {
            if (activeSizeBtn === btn) {
                selectedSize = undefined;
                activeSizeBtn.classList.remove('active_size');
                activeSizeBtn = null;
            } else {
                if (activeSizeBtn) activeSizeBtn.classList.remove('active_size');
                btn.classList.add('active_size');
                selectedSize = size;
                activeSizeBtn = btn;
            }

            applyFilters();
        }

        function applyFilters() {
            const products = Array.from(kt());
            const searchProduct = [];
            // Xóa active class khỏi các nút giá
            document.querySelectorAll('.product_search_list_price_popup button').forEach(btn => btn.classList.remove(
                'active_price'));
            products.forEach(function(product) {
                // --- Xử lý giá ---
                const priceText = product.querySelector('.price').innerHTML;
                const price = parseInt(priceText.replace(/[^0-9]/g, ''));
                const matchPrice = price >= selectedPrice.min && price <= selectedPrice.max;

                // --- Xử lý size ---
                const productSize = product.getAttribute('data-size');
                const sizeList = productSize ? productSize.split(',') : [];
                const matchSize = selectedSize === undefined || sizeList.includes(selectedSize.toLowerCase());

                // --- Kiểm tra đủ điều kiện ---
                if (matchSize && matchPrice) {
                    searchProduct.push(product);
                } else {
                    product.style.display = "none";
                }
            });

            const itemsPerPage = 8;
            const countPage = Math.ceil(searchProduct.length / itemsPerPage);
            let index = 1;

            function LoadPage(page) {
                searchProduct.forEach(p => p.style.display = "none");
                const begin = (page - 1) * itemsPerPage;
                const end = begin + itemsPerPage;
                searchProduct.slice(begin, end).forEach(p => {
                    p.style.display = 'block';
                });
                LoadPageButton(countPage, page);
            }

            function LoadPageButton(countPage, index) {
                const page = document.getElementById('page');
                page.innerHTML = '';

                const pre = document.createElement('button');
                pre.innerHTML = "Pre";
                pre.disabled = index === 1;
                pre.addEventListener('click', () => LoadPage(index - 1));
                page.appendChild(pre);

                for (let i = 1; i <= countPage; i++) {
                    const button = document.createElement('button');
                    button.innerHTML = i;
                    button.className = i === index ? 'active' : '';
                    button.addEventListener('click', () => LoadPage(i));
                    page.appendChild(button);
                }

                const next = document.createElement('button');
                next.innerHTML = "Next";
                next.disabled = index === countPage;
                next.addEventListener('click', () => LoadPage(index + 1));
                page.appendChild(next);
            }

            if (searchProduct.length > 0) {
                LoadPage(index);
            } else {
                document.getElementById('page').innerHTML = '<p>Không có sản phẩm nào phù hợp.</p>';
            }
        }
    </script>


    <script>
        function toggleFilterPanel() {
            const panel = document.getElementById('filterPanel');
            panel.classList.toggle('open');
        }
    </script>


@endsection
