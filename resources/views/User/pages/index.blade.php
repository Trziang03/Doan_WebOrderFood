@extends('layouts.layouts_user')
@section('title', 'Trang chủ')
@section('content')
    <!-- slideshow -->

     <style>
        :root {
            --orange: #ff6600;
            --white: #ffffff;
            --light-orange: #fff2e6;
            --text-dark: #333;
            --shadow: rgba(0, 0, 0, 0.05);
        }
    
        .product_best_seller {
            padding: 2rem 1rem;
            background-color: var(--light-orange);
            border-radius:10px; 
        }
        .product_best_seller h4 {
            text-align: center;
            font-weight: bold;
            margin-bottom: 2rem;
            font-size: 1.8rem;
            /* color: var(--orange); */
        }
        .product_best_seller_items {
            /* display: grid; */
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
        }
        .product_best_seller_item {
            background-color: var(--white);
            border: 1px solid #ffe0cc;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 6px var(--shadow);
            display: flex;
            flex-direction: column;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .product_best_seller_item:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 10px rgba(255, 102, 0, 0.2);
        }
        .product_best_seller_item img {
            width: 80%;
            height: auto;
            object-fit: cover;
            display: block;
            margin: 0 auto;
        }
        .product_best_seller_item_info {
            padding: 1rem;
            text-align: center;
        }
        .product_best_seller_item_info ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .product_best_seller_item_info li {
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }
        .product_best_seller_item_info li a {
            font-weight: 600;
            color: var(--text-dark);
            text-decoration: none;
        }
        .product_best_seller_item_info li a:hover {
            color: var(--orange);
        }
        .product_best_seller_item_info button {
            background-color: var(--orange);
            color: var(--white);
            border: none;
            border-radius: 4px;
            padding: 0.5rem 1rem;
            cursor: pointer;
            font-size: 0.9rem;
        }
        .product_best_seller_item_info button:hover {
            background-color: #e65c00;
        }
        @media only screen and (max-width: 768px){
            .product_best_seller_items {
                display: flex;
                flex-direction: row;
                overflow-x: auto;
                gap: 1rem;
                scroll-snap-type: x mandatory;
                padding-bottom: 1rem;
                /* Xóa khoảng cách giữa các sản phẩm */
                gap: 0;
                scrollbar-width: none; /* Firefox */
                -ms-overflow-style: none;  /* IE & Edge */
            }
            .product_best_seller_items::-webkit-scrollbar {
                display: none; /* Chrome, Safari */
            }

            .product_best_seller_item {
                flex: 0 0 auto;
                width: 50%; /* hoặc 220px tùy bạn muốn rộng bao nhiêu */
                scroll-snap-align: start;
                padding: 0;
            }
        }
        
        
    </style>
    <section class="container_css main_slideshow">
        <div class="main_slideshow_left">
            <div id="carouselExampleControls" class="carousel carousel-success slide" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active btn-border-radius"
                        aria-current="true" aria-label="Slide 1"></button>
                    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1"class="btn-border-radius"
                        aria-label="Slide 2"></button>
                    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" class="btn-border-radius"
                        aria-label="Slide 3"></button>
                    {{-- <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="3"
                        aria-label="Slide 4"></button> --}}
                </div>
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <a href="{{ route('timkiemsanpham', ['slug' => 'do-uong-cac-loai']) }}"><img
                                src="images/banner-tra-sua-7.jpg" class="d-block w-100 img-fluid rounded" style="" alt="Lỗi hiển thị"></a>
                    </div>
                    {{-- {{ route('timkiemsanpham', ['slug' => 'laptop', 'id' => 'Asus']) }} --}}
                    <div class="carousel-item">
                        <a href="{{ route('timkiemsanpham', ['slug' => 'do-uong-cac-loai']) }}"><img
                                src="images/banner-tra-sua-9.jpg" class="d-block w-100 img-fluid rounded" style=""
                                alt="Lỗi hiển thị"></a>
                                {{-- {{ route('timkiemsanpham', ['slug' => 'dien-thoai', 'id' => 'Apple']) }} --}}
                    </div>
                    <div class="carousel-item">
                        <a href="{{ route('timkiemsanpham', ['slug' => 'do-uong-cac-loai']) }}"><img
                                src="images/banner-tra-sua-8.jpg" class="d-block w-100 img-fluid rounded" style=""
                                alt="Lỗi hiển thị"></a>
                                {{-- {{ route('timkiemsanpham', ['slug' => 'dien-thoai', 'id' => 'Samsung']) }} --}}
                    </div>
                    {{-- <div class="carousel-item">
                        <a href=""><img src="images/banner-tra-sua-10.jpg"
                                class="d-block w-100" style="" alt="Lỗi hiển thị"></a>
                    </div> --}}
                        {{-- {{ route('timkiemsanpham', ['slug' => 'laptop']) }} --}}
                </div>
                <button class="carousel-control-prev btn-border-radius" type="button" data-bs-target="#carouselExampleControls"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next btn-border-radius" type="button" data-bs-target="#carouselExampleControls"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
        {{-- <div class="main_slideshow_right">
            <div class="main_slideshow_right_items">
                <div class="main_slideshow_right_item">
                    <div class="main_slideshow_right_item_img">
                        <a><img src="images/slider_right1.png" alt="Lỗi hiển thị"></a>
                    </div>
                    <div class="main_slideshow_right_item_img">
                        <a><img src="images/slide_right_2.png" alt="Lỗi hiển thị"></a>
                    </div>
                    <div class="main_slideshow_right_item_img">
                        <a><img src="images/slider_right3.png" alt="Lỗi hiển thị"></a>
                    </div>
                    <div class="main_slideshow_right_item_img">
                        <a><img src="images/slider_right4.png" alt="Lỗi hiển thị"></a>
                    </div>
                </div>
            </div>
        </div> --}}
    </section>

    <!-- Thương hiệu -->
    {{-- <section class="container_css main_branch ">
        <h4>THƯƠNG HIỆU NỔI BẬT</h4>
        <div class="list_branch">
            @foreach ($thuongHieu as $item)
                <a href="{{ route('timkiemsanpham', ['slug' => $item->name]) }}"><img
                        src="{{ asset('images/' . $item->image) }}" alt="Lỗi hiển thị"></a>
            @endforeach
        </div>
    </section> --}}
    <!-- Sản phẩm bán chạy -->
    <section class="container_css product_best_seller">
        <h4 style="color:white">Món ăn nổi bật</h4>
        
        <div id="carouselExampleIntervals" class="carousel slide carousel-dark" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active" data-bs-interval="10000">
                    <div class="product_best_seller_items">
                        @if (isset($danhSachBanChay))
                            @for ($i = 0; $i < count($danhSachBanChay); $i++)
                                @if ($i > 3)
                                @break
                            @endif
                            <div class="product_best_seller_item">
                                <a href="{{ route('detail', [$danhSachBanChay[$i]->slug]) }}"><img
                                        src="{{ asset('images/' .DB::table('image_products')->select('image')->where('product_id', $danhSachBanChay[$i]->id)->first()->image) }}"
                                        alt="Lỗi hiển thị"></a>
                                <div class="product_best_seller_item_info">
                                    <ul>
                                        <li><a
                                                href="{{ route('detail', [$danhSachBanChay[$i]->slug]) }}">{{ $danhSachBanChay[$i]->name }}</a>
                                        </li>
                                        <li>{{ number_format($danhSachBanChay[$i]->price, 0, ',', '.') }}<sup>đ</sup>
                                        </li>
                                        <li>{{ $danhSachBanChay[$i]->rating }} <i class="fas fa-star"></i></li>
                                            <li>
                                                <button onclick="buyNow({{ $danhSachBanChay[$i]->variants }})">Đặt món ngay</button>
                                            </li>
                                    </ul>
                                </div>
                            </div>
                        @endfor
                    @endif
                </div>
            </div>
            @if (isset($danhSachBanChay) && count($danhSachBanChay) > 4)
                <div class="carousel-item" data-bs-interval="2000">
                    <div class="product_best_seller_items">
                        @for ($i = 4; $i < count($danhSachBanChay); $i++)
                            <div class="product_best_seller_item">
                                <a href="{{ route('detail', [$danhSachBanChay[$i]->slug]) }}"><img
                                        src="{{ asset('images/' .DB::table('image_products')->select('image')->where('product_id', $danhSachBanChay[$i]->id)->first()->image) }}"
                                        alt="Lỗi hiển thị"></a>
                                <div class="product_best_seller_item_info">
                                    <ul>
                                        <li><a
                                                href="{{ route('detail', [$danhSachBanChay[$i]->slug]) }}">{{ $danhSachBanChay[$i]->name }}</a>
                                        </li>
                                        <li>{{ number_format($danhSachBanChay[$i]->price, 0, ',', '.') }}<sup>đ</sup>
                                        </li>
                                        <li>{{ $danhSachBanChay[$i]->rating }} <i class="fas fa-star"></i></li>
                                        <li>
                                            <button onclick="buyNow({{ $danhSachBanChay[$i]->variants }})">Đặt món ngay</button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIntervals"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIntervals"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            @else
        </div>
        @endif
    </section>

    <section class="container_css product_best_seller">
        <h4>CÁC LOẠI ĐỒ UỐNG NỔI BẬT</h4>
        <div id="carouselExampleControlsNoTouching" class="carousel slide carousel-dark" data-bs-touch="false">
            <div class="carousel-inner">
                <div class="carousel-item active" data-bs-interval="10000">
                    <div class="product_best_seller_items">
                        @if (isset($danhSachLapTopMoi))
                            @for ($i = 0; $i < count($danhSachLapTopMoi); $i++)
                                @if ($i > 3)
                                    @break
                                @endif
                                <div class="product_best_seller_item">
                                    <a href="{{ route('detail', [$danhSachLapTopMoi[$i]->slug]) }}"><img
                                            src="{{ asset('images/' . $danhSachLapTopMoi[$i]->image) }} "
                                            alt="Lỗi hiển thị"></a>
                                    <div class="product_best_seller_item_info">
                                        <ul>
                                            <li><a
                                                    href="{{ route('detail', [$danhSachLapTopMoi[$i]->slug]) }}">{{ $danhSachLapTopMoi[$i]->name }}</a>
                                            </li>
                                            <li>{{ number_format($danhSachLapTopMoi[$i]->price, 0, ',', '.') }}
                                                <sup>đ</sup>
                                            </li>
                                            <li>{{ $danhSachLapTopMoi[$i]->rating }}<i class="fas fa-star"></i></li>
                                            <li>
                                                <button onclick="buyNow({{ $danhSachLapTopMoi[$i]->variants }})">Đặt món ngay</button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            @endfor
                        @endif
                </div>
            </div>
            @if (isset($danhSachLapTopMoi) && count($danhSachLapTopMoi) > 4)
                <div class="carousel-item" data-bs-interval="2000">
                    <div class="product_best_seller_items">
                        @for ($i = 4; $i < count($danhSachLapTopMoi); $i++)
                            <div class="product_best_seller_item">
                                <a href=""><img src="{{ asset('images/' . $danhSachLapTopMoi[$i]->image) }} "
                                        alt="Lỗi hiển thị"></a>
                                <div class="product_best_seller_item_info">
                                    <ul>
                                        <li><a
                                                href="{{ route('detail', [$danhSachLapTopMoi[$i]->slug]) }}">{{ $danhSachLapTopMoi[$i]->name }}</a>
                                        </li>
                                        <li>{{ number_format($danhSachLapTopMoi[$i]->price, 0, ',', '.') }}
                                            <sup>đ</sup>
                                        </li>
                                        <li>{{ $danhSachLapTopMoi[$i]->rating }}<i class="fas fa-star"></i></li>
                                        <li>
                                            <button onclick="buyNow({{ $danhSachLapTopMoi[$i]->variants }})">
                                                Đặt món ngay</button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
                <button class="carousel-control-prev" type="button"
                    data-bs-target="#carouselExampleControlsNoTouching" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button"
                    data-bs-target="#carouselExampleControlsNoTouching" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            @else
            </div>
            @endif
        </div>
    </section>

<!-- Điện thoại mới nhất -->
<section class="container_css product_best_seller">
    <h4>CÁC LOẠI BÁNH TRÁNG</h4>
    <div id="carouselExampleInterval" class="carousel slide carousel-dark" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active" data-bs-interval="10000">
                <div class="product_best_seller_items">
                    @if (isset($danhSachDTHot))
                        @for ($i = 0; $i < count($danhSachDTHot); $i++)
                            @if ($i > 3)
                            @break
                        @endif
                        <div class="product_best_seller_item">
                            <a href="{{ route('detail', [$danhSachDTHot[$i]->slug]) }}"><img
                                    src="{{ asset('images/' . $danhSachDTHot[$i]->image) }}"
                                    alt="Lỗi hiển thị"></a>
                            <div class="product_best_seller_item_info">
                                <ul>
                                    <li><a
                                            href="{{ route('detail', [$danhSachDTHot[$i]->slug]) }}">{{ $danhSachDTHot[$i]->name }}</a>
                                    </li>
                                    <li>{{ number_format($danhSachDTHot[$i]->price, 0, ',', '.') }}<sup>đ</sup>
                                    </li>
                                    <li>{{ $danhSachDTHot[$i]->rating }} <i class="fas fa-star"></i></li>
                                    <li>
                                        <button onclick="buyNow({{ $danhSachDTHot[$i]->variants }})">Đặt món ngay</button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    @endfor
                @endif
            </div>
        </div>
        @if (isset($danhSachDTHot) && count($danhSachDTHot) > 2)
            <div class="carousel-item" data-bs-interval="2000">
                <div class="product_best_seller_items">
                    @for ($i = 2; $i < count($danhSachDTHot); $i++)
                        <div class="product_best_seller_item">
                            <a href="{{ route('detail', [$danhSachDTHot[$i]->slug]) }}"><img
                                    src="{{ asset('images/' . $danhSachDTHot[$i]->image) }}"
                                    alt="Lỗi hiển thị"></a>
                            <div class="product_best_seller_item_info">
                                <ul>
                                    <li><a
                                            href="{{ route('detail', [$danhSachDTHot[$i]->slug]) }}">{{ $danhSachDTHot[$i]->name }}</a>
                                    </li>
                                    <li>{{ number_format($danhSachDTHot[$i]->price, 0, ',', '.') }}<sup>đ</sup>
                                    </li>
                                    <li>{{ $danhSachDTHot[$i]->rating }} <i class="fas fa-star"></i></li>
                                    <li>
                                        <button onclick="buyNow({{ $danhSachDTHot[$i]->variants }})">Đặt món ngay</button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleInterval"
                data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleInterval"
                data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        @else
    </div>
    @endif
</div>
</section>



{{-- Hiển thị thông tin dịch vụ bán hàng, vận chuyển --}}
{{-- @include('user.partials.service') --}}
@guest
@if (session('error'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        handleLoginAuth();
    });
</script>
@endif
@endguest
@endsection
@section('script')
<script>
    function buyNow(variantId) {
        const quantity = 1;
        $.ajax({
                method: "GET",
                url: `/admin/check-stock-variant/${variantId}`
            })
            .done((data) => {
                if (data < quantity) {
                    alertify.alert('Thông báo', 'Sản phẩm không đủ số lượng!');
                } else {
                    $.ajax({
                        method: "POST",
                        url: '/order/buy-now',
                        data: {
                            id: variantId,
                            quantity,
                            _token: '{{ csrf_token() }}'
                        }
                    })
                    .done((data) => {
                        if(data.success===1){
                            window.location.href = data.url;
                        }else{
                            alertify.alert('Vui lòng đăng nhập để mua ngay');
                        }

                    })
                    .fail((data)=>{
                        console.log(data);
                    })

                }
            })
    }
</script>
@endsection
