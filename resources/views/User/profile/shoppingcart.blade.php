@extends('layouts.layouts_user')
@section('title', 'Trang giỏ hàng')
@section('content')
    <div class="shopping_cart container_css" id="shopping_cart">
        @if ($cartItems->isEmpty())
            <h3 style="height:183px; text-align:center; margin-top:105px">Chưa có món ăn nào <br>
            <a href="{{ route('user.menu') }}" style="display:inline-block; margin-top:10px;">Đặt món ngay ?</a></h3>
        @else
            <div class="shopping_cart_main" id="cart-main">
                <div class="shopping_cart_items" id="list-product">
                    @foreach ($cartItems as $item)
                        @php
                            $sizePrice = $item->size ? $item->size->price : 0;
                            $productPrice = $item->product->price + $sizePrice;
                            $toppingTotal = $item->toppings->sum(function ($t) {
                                return $t->topping->price * $t->quantity;
                            });
                            $totalPrice = ($productPrice + $toppingTotal) * $item->quantity;
                        @endphp

                        <div id="cart-item-{{ $item->id }}" class="shopping_cart_item"
                            style="display: flex; gap: 10px; background-color: #FFF3E8; border-radius: 10px; padding: 12px; margin-bottom: 12px;">
                            <div class="cart_item_img" style="flex-shrink: 0;">
                                <img src="{{ asset($item->product->image_food) }}" alt="">
                            </div>
                            <div class="cart_item_info"
                                style="flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                                <div class="cart_item_info_top"
                                    style="display: flex; justify-content: space-between; align-items: flex-start;">
                                    <h4 style="font-size: 16px;">{{ $item->product->name }} - Size {{ $item->size->name }}</h4>
                                    <button class="btn-delete-item" data-id="{{ $item->id }}"
                                        style="border: none; background: none;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>

                                @if ($item->toppings->isNotEmpty())
                                    <div>
                                        <p style="margin: 0;">Topping:</p>
                                        <ul class="topping-list" style="margin-left: 15px; font-weight: bold; padding-left: 15px;">
                                            @foreach ($item->toppings as $topping)
                                                @if ($topping->topping)
                                                    <li>{{ $topping->topping->name }} x {{ $topping->quantity }}</li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @if (!empty($item->note))
                                    <p style="margin: 0;">Ghi chú: {{ $item->note }}</p>
                                @endif

                                <div class="cart_item_footer"
                                    style="display: flex; justify-content: space-between; align-items: center; padding: 6px 12px; margin-top: auto; margin-bottom: 10px; width: 100%;">

                                    {{-- Cột bên trái: Giá --}}
                                    <span style="font-weight: bold; font-size: 20px;">Giá: {{ number_format($totalPrice) }} <sup>đ</sup></span>

                                    {{-- Cột bên phải: Nút tăng giảm --}}
                                    <div style="display: flex; align-items: center; gap: 10px; background-color: #F7F7F7; border-radius: 15px;">
                                        <button class="btn-decrease" data-id="{{ $item->id }}"
                                            style="border: none; background: none;"><i class="fas fa-minus"></i></button>
                                        <input class="amount" disabled type="text" value="{{ $item->quantity }}"
                                            style="width: 30px; text-align: center;">
                                        <button class="btn-increase" data-id="{{ $item->id }}"
                                            style="border: none; background: none;"><i class="fas fa-plus"></i></button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="shopping_cart_bottom" id="cart-bottom">
                    <div class="shopping_cart_bottom_left">
                        <button id="btn-delete-all">Xóa tất cả</button>
                    </div>

                    <div class="shopping_cart_bottom_right_voucher">
                        <div class="shopping_cart_bottom_price">
                            <h4>Tổng cộng:</h4>
                            <p id="item-total">
                                {{ number_format(
                $cartItems->sum(function ($item) {
                    $sizePrice = $item->size ? $item->size->price : 0;
                    $productPrice = $item->product->price + $sizePrice;

                    $toppingTotal = $item->toppings->sum(function ($t) {
                        return $t->topping->price * $t->quantity;
                    });
                    return ($productPrice + $toppingTotal) * $item->quantity;
                })
            ) }} <sup>đ</sup>
                            </p>
                            <form action="{{ route('cart.submit') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn" style="background-color: rgb(240, 145, 55);
                                                        width: 100%;
                                                        border: none;
                                                        background-color: rgb(240, 145, 55);
                                                        color: white;
                                                        padding: 10px;
                                                        border-radius: 5px;
                                                        transition: all linear 0.3s;
                                                        margin-left: 28px">Gửi đơn hàng</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function () {
            // CSRF token setup
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Xóa một item
            $('.btn-delete-item').click(function () {
                const id = $(this).data('id');
                $.ajax({
                    url: `/cart/delete-item/${id}`,
                    type: 'DELETE',
                    success: res => {
                        if (res.success) {
                            alertify.success(res.message);
                            $(`#cart-item-${id}`).remove();
                            updateAfterChange(res.cart);
                            checkIfEmpty();
                        } else alertify.error(res.message);
                    },
                    error: () => alertify.error("Xóa không thành công!")
                });
            });

            // Xóa toàn bộ giỏ hàng
            $('#btn-delete-all').click(function () {
                if (!confirm('Xóa toàn bộ giỏ hàng?')) return;
                $.ajax({
                    url: '/cart/delete-all',
                    type: 'DELETE',
                    success: res => {
                        if (res.success) {
                            alertify.success(res.message);
                            afterDeleteAll();
                        } else alertify.error(res.message);
                    },
                    error: () => alertify.error("Không thể xóa giỏ hàng!")
                });
            });

            // Giảm số lượng
            $('.btn-decrease').click(function () {
                const id = $(this).data('id');
                $.ajax({
                    url: `/cart/minus/${id}`,
                    type: 'PATCH',
                    success: res => {
                        if (res.success) {
                            if (res.item.quantity <= 0) {
                                $(`#cart-item-${id}`).remove();
                                checkIfEmpty();
                            } else {
                                updateItem(id, res.item);
                            }
                            updateAfterChange(res.cart);
                        } else alertify.error(res.message);
                    },
                    error: () => alertify.error("Không giảm được!")
                });
            });

            // Tăng số lượng
            $('.btn-increase').click(function () {
                const id = $(this).data('id');
                $.ajax({
                    url: `/cart/increase/${id}`,
                    type: 'PATCH',
                    success: res => {
                        if (res.success) {
                            updateItem(id, res.item);
                            updateAfterChange(res.cart);
                        } else alertify.error(res.message);
                    },
                    error: () => alertify.error("Không tăng được!")
                });
            });

            // Cập nhật số lượng + tổng tiền của từng item
            function updateItem(id, item) {
                // Tổng tiền mỗi sản phẩm
                const total = (item.price + item.topping_total) * item.quantity;
                const itemRow = $(`#cart-item-${id}`);
                itemRow.find('.amount').val(item.quantity);
                itemRow.find('.item-total').text(formatNumber(total) + ' đ');
            }

            function updateAfterChange(cart) {
                $('#cart-quantity').text(cart.totalQuantity);
                $('#item-total').text(formatNumber(Number(cart.totalPrice)) + ' đ');
            }

            // Hiển thị giao diện trống nếu không còn item nào
            function checkIfEmpty() {
                if ($('.shopping_cart_item').length === 0) {
                    afterDeleteAll();
                }
            }

            // Hiển thị lại khi giỏ trống
            function afterDeleteAll() {
                $('#cart-quantity').text(0);
                $('#cart-main').empty().append('<h3 style="text-align:center;margin-top: 118px;height: 150px;">Thực đơn trống</h3>');
            }

            // Format tiền
            function formatNumber(number) {
                return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            }
        });
    </script>
    <script>
        // Phân trang
        document.addEventListener("DOMContentLoaded", () => {
            kt(); // Khởi tạo danh sách sản phẩm
            Page();
        });

        function kt() {
            const products = document.querySelectorAll('.shopping_cart_item');
            return products;
        }

        function Page(itemsPage = 4) {
            const products = Array.from(kt());

            const countPage = Math.ceil(products.length / itemsPage);
            let index = 1;

            function LoadPage(page) {
                const container = document.querySelector("#list-product-variant");
                container.innerHTML = "";
                const begin = (page - 1) * itemsPage;
                const end = begin + itemsPage;
                products.slice(begin, end).forEach(product => {
                    container.appendChild(product);
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

            if (products.length > itemsPage) {
                LoadPage(index);
            }
        }
    </script>
@endsection
