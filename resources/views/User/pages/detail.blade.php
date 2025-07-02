@extends('layouts.layouts_user')
@section('title', 'Trang chi tiết sản phẩm')
@section('content')
    @php
        $seach = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id') // ✔ rõ ràng
            ->select(
                'products.name as product',
                'categories.name as category',
                'categories.slug'
            )
            ->where('products.slug', $slug)
            ->first();

    @endphp
    <div style="background-color: rgb(241, 240, 241);">
        <div class="container_css product_detail_top_url">
            <ul>

                @if ($seach)
                    <li><a href="{{ route('user.index') }}">Trang chủ</a></li>
                    <li><a href="{{ route('timkiemsanphamtheodanhmuc', ['slug' => $seach->slug]) }}">{{ $seach->category }}</a></li>
                    <!-- Truy xuất đúng tên trường -->
                    <li><a href="{{ route('detail', $slug) }}">{{ $seach->product }}</a></li>
                @endif
            </ul>
        </div>
    </div>
    <!-- CSS để trong thẻ <style> -->
    <style>
        .topping-quantity-dot {
            position: absolute;
            top: -8px;
            right: -3px;
            background: red;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .topping-remove-dot {
            position: absolute;
            top: -8px;
            left: -3px;
            background: red;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 14px;
            text-align: center;
            line-height: 18px;
            cursor: pointer;
            display: none;
            /* Ẩn mặc định */
        }

        .product_detail {
            background-color: #fff2e6;
            display: flex;
            gap: 20px;
            padding: 20px;
            border-radius: 10px;
        }

        .product_detail_left {
            flex: 1;
        }

        .product_detail_left img {
            width: 100%;
            border-radius: 12px;
            object-fit: cover;
        }

        .product_detail_right {
            flex: 1;
        }

        .product_detail_right h3 {
            font-weight: bold;
        }

        .product_detail_right .price {
            font-size: 18px;
            color: red;
        }

        .product_detail_right .price strong {
            font-size: 20px;
        }

        .btn-border-radius {
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
            background-color: white;
            transition: all 0.2s ease-in-out;
        }

        .btn-border-radius:hover {
            background-color: #ffe6cc;
        }

        .color_active {
            background-color: #ffe6cc;
            color: #dd2f2c;
            border: 1px solid #dd2f2c !important;
            box-shadow: 0 .3rem .5rem rgba(0, 0, 0, .25);
        }

        .topping-list,
        .size-list {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 10px;
            margin-top: 10px;
        }

        .topping-item {
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 10px;
            text-align: center;
            min-width: 120px;
            max-width: 200px;
            margin-top: 10px;
        }

        .topping-item img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 8px;
        }

        .size-button {
            border: 1px solid #ddd;
            padding: 8px 12px;
            border-radius: 20px;
            background-color: white;
            width: 15%;
        }

        .size-button:hover {
            background-color: #ffe6cc;
        }

        .quantity-box {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .quantity-box button {
            background-color: white;
            padding: 5px 12px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .quantity-box input {
            background-color: white;
            width: 50px;
            text-align: center;
            margin: 0 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            padding: 2px;
        }

        .add-to-cart-btn {
            background-color: red;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 10px;
            font-weight: bold;
            cursor: pointer;
        }

        .add-to-cart-btn i {
            margin-left: 5px;
        }

        .food-description {
            margin-top: 10px;
            margin-bottom: 15px;
            font-size: 14px;
            color: #555;
            line-height: 1.6;
        }

        /* Note món ăn */
        .food-note {
            margin-top: 1rem;
        }

        .food-note label {
            font-weight: bold;
            color: #444;
            display: block;
            margin-bottom: 6px;
        }

        .food-note textarea {
            width: 95%;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            resize: vertical;
            min-height: 60px;
            max-height: 100px;
            /* không cho mở rộng quá mức */
            font-size: 14px;
            transition: border-color 0.2s ease;
            margin-bottom: 10px;
        }

        .food-note textarea:focus {
            outline: none;
        }

        @media (max-width: 768px) {
            .product_detail {
                flex-direction: column;
                align-items: center;
            }

            .product_detail_left,
            .product_detail_right {
                width: 100%;
            }

            .product_detail_left img {
                max-height: 250px;
                object-fit: cover;
            }

            .product_detail_right {
                text-align: left;
            }

            .topping-list,
            .size-list {
                justify-content: left;
            }

            .quantity-box {
                justify-content: left;
            }
        }

        @media (max-width: 768px) {

            .product_detail_left,
            .product_detail_right {
                flex: 1 1 48%;
            }
        }

        @media (min-width: 1024px) {
            .product_detail_right h4 {
                font-size: 28px;
            }
            .product_detail_right_price span {
                font-size: 24px;
            }
        }
    </style>

    <!-- Chi tiết sản phẩm -->
    <section class="container_css">
        <div class="product_detail">

            <!-- Bên trái: Hình ảnh sản phẩm -->
            <div class="product_detail_left">
                <img src="{{ asset($thongTinSanPham->image_food) }}" alt="Hình món ăn">
            </div>

            <!-- Bên phải: Thông tin và lựa chọn -->
            <div class="product_detail_right">
                <h3>{{ $thongTinSanPham->name }}</h3>

                <!-- Giá gốc ẩn -->
                <span id="giaGoc" data-base-price="{{ $thongTinSanPham->price }}" hidden></span>

                <!-- Giá bán động -->
                <p class="price">
                    Giá bán: <strong id="giaHienThi">{{ number_format($thongTinSanPham->price, 0, ',', '.') }} đ</strong>
                </p>

                <!-- Mô tả -->
                <p class="food-description">{{ $thongTinSanPham->description }}</p>

                <!-- Topping -->
                <h5 style="margin: 10px 0 5px; font-weight: 600;">Topping</h5>
                @foreach ($danhSachTopping as $topping)
                    <button class="btn-border-radius topping-item topping-button position-relative"
                        onclick="chonTopping({{ $topping->id }}, {{ $topping->price }}, this)"
                        data-topping-id="{{ $topping->id }}" data-price="{{ $topping->price }}">
                        {{ $topping->name }}
                        <span class="topping-quantity-dot" style="display:none;">1</span>
                        <span class="topping-remove-dot"
                            onclick="event.stopPropagation(); giamTopping({{ $topping->id }}, this)">−</span>
                    </button>
                @endforeach

                <!-- Size -->
                <h5 style="margin: 10px 0 5px; font-weight: 600;">Size</h5>
                <div class="size-list">
                    @foreach ($danhSachSize as $index => $size)
                        <button class="size-button {{ $index == 0 ? 'color_active' : '' }}"
                            onclick="chonSize({{ $size->id }}, {{ $size->price }}, this)" data-size-id="{{ $size->id }}"
                            data-price="{{ $size->price }}">
                            {{ $size->name }}
                        </button>
                    @endforeach
                </div>

                <!-- Số lượng -->
                <div class="quantity-box">
                    <button onclick="giamSoLuong()">-</button>
                    <input type="text" id="soLuong" min="1" max="5" value="1">
                    <button onclick="tangSoLuong()">+</button>
                </div>
                <!-- Thêm ghi chú -->
                <div class="food-note">
                    <label for="note">Ghi chú:</label>
                    <textarea id="note" maxlength="150"></textarea>
                </div>
                <!-- Thêm vào giỏ hàng -->
                <button class="add-to-cart-btn" id="add-to-cart" onclick="addToCart()"
                    data-product-id="{{ $thongTinSanPham->id }}">
                    Thêm vào thực đơn <i class="fas fa-cart-plus"></i>
                </button>
            </div>
        </div>
    </section>
@endsection
@section('script')
    <script>
    let selectedSizeId = null;
    let toppingQuantities = {};
    let sizePrices = {}, toppingPrices = {};
    let basePrice = 0;

    document.addEventListener("DOMContentLoaded", () => {
        const basePriceElement = document.getElementById('giaGoc');
        basePrice = parseFloat(basePriceElement?.dataset.basePrice || 0);

        document.querySelectorAll('.size-button').forEach(btn => {
            const id = btn.dataset.sizeId;
            sizePrices[id] = parseFloat(btn.dataset.price || 0);
        });

        document.querySelectorAll('.topping-button').forEach(btn => {
            const id = btn.dataset.toppingId;
            toppingPrices[id] = parseFloat(btn.dataset.price || 0);
        });

        const firstSize = document.querySelector('.size-button');
        if (firstSize) {
            selectedSizeId = firstSize.dataset.sizeId;
            firstSize.classList.add('color_active');
        }

        updatePrice();
    });

    // Size chọn
    function chonSize(id, price, btn) {
        selectedSizeId = id;
        document.querySelectorAll('.size-button').forEach(b => b.classList.remove('color_active'));
        btn.classList.add('color_active');
        updatePrice();
    }

    // Topping chọn
    function chonTopping(id, price, btn) {
        const current = toppingQuantities[id] || 0;
        if (current >= 3) {
            return alertify.alert('Thông báo', 'Bạn chỉ được chọn tối đa 3 phần cho mỗi loại topping!');
        }

        toppingQuantities[id] = current + 1;
        btn.classList.add('color_active');

        const dot = btn.querySelector('.topping-quantity-dot');
        if (dot) {
            dot.style.display = 'flex';
            dot.textContent = toppingQuantities[id];
        }

        const removeDot = btn.querySelector('.topping-remove-dot');
        if (removeDot) removeDot.style.display = 'block';

        updatePrice();
    }

    // Topping giảm
    function giamTopping(id, span) {
        if (!toppingQuantities[id]) return;

        toppingQuantities[id]--;
        const btn = span.closest('.topping-button');

        if (toppingQuantities[id] <= 0) {
            delete toppingQuantities[id];
            btn.classList.remove('color_active');
            btn.querySelector('.topping-quantity-dot')?.style.setProperty('display', 'none');
            btn.querySelector('.topping-remove-dot')?.style.setProperty('display', 'none');
        } else {
            btn.querySelector('.topping-quantity-dot').textContent = toppingQuantities[id];
        }

        updatePrice();
    }

    // Số lượng món
    function tangSoLuong() {
        const input = document.getElementById('soLuong');
        let value = parseInt(input.value) || 1;

        if (value >= 5) {
            return alertify.alert('Bạn chỉ được chọn tối đa 5 phần cho mỗi món!');
        }

        input.value = value + 1;
        updatePrice();
    }

    function giamSoLuong() {
        const input = document.getElementById('soLuong');
        let value = parseInt(input.value) || 1;

        if (value > 1) {
            input.value = value - 1;
            updatePrice();
        }
    }

    document.getElementById('soLuong').addEventListener('input', function () {
        let value = this.value.trim();
        if (!/^\d+$/.test(value)) {
            this.value = 1;
            alertify.alert('Số lượng phải là số nguyên từ 1 đến 5!');
        }

        let parsed = parseInt(this.value);
        if (parsed < 1) this.value = 1;
        else if (parsed > 5) {
            this.value = 5;
            alertify.alert('Bạn chỉ được chọn tối đa 5 phần cho mỗi món!');
        } else {
            this.value = parsed;
        }

        updatePrice();
    });

    // Tính tổng tiền
    function updatePrice() {
        const quantity = parseInt(document.getElementById('soLuong').value) || 1;
        const sizePrice = sizePrices[selectedSizeId] || 0;

        let toppingTotal = 0;
        for (let id in toppingQuantities) {
            toppingTotal += (toppingPrices[id] || 0) * toppingQuantities[id];
        }

        const unitPrice = basePrice + sizePrice + toppingTotal;
        const totalPrice = unitPrice * quantity;

        document.getElementById('giaHienThi').textContent = totalPrice.toLocaleString() + ' đ';
    }

    // Thêm vào giỏ hàng
    function addToCart() {
        const productId = document.getElementById('add-to-cart').dataset.productId;
        const quantity = parseInt(document.getElementById('soLuong').value) || 1;
        const note = document.getElementById('note').value.trim();

        if (!selectedSizeId) return alertify.alert('Vui lòng chọn size món ăn!');
        if (quantity < 1 || quantity > 5) return alertify.alert('Số lượng phải từ 1 đến 5!');

        // Tạo danh sách topping
        document.querySelectorAll('.topping-input').forEach(input => {
            const toppingId = input.dataset.toppingId;
            const qty = parseInt(input.value);
            if (qty > 0) {
                toppingQuantities[toppingId] = qty;
            }
        });

        const payload = {
            product_id: productId,
            size_id: selectedSizeId,
            topping_quantities: toppingQuantities,
            quantity: quantity,
            note: note,
            _token: '{{ csrf_token() }}'
        };

        $.post('/add-to-cart', payload)
            .done(data => {
                if (data.success) {
                    alertify.success(data.message);
                    if (data.cart?.totalQuantity !== undefined) {
                        $('#cart-quantity').text(data.cart.totalQuantity);
                    }
                } else {
                    alertify.alert(data.message);
                }
            })
            .fail(() => {
                alertify.alert('Không thể thêm vào giỏ hàng lúc này!');
            });
    }
</script>
@endsection
