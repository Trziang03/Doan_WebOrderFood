<footer>
    @php
         $lienKetWebsite = DB::table('about')->first();
         $danhSachDanhMuc = DB::table('categories')->select('categories.name','categories.slug')->get();
    @endphp
    <div class="footer_top container_css">
        <div class="footer_top_left_items">
            <div class="footer_top_item">
                <p style="text-align: center;padding:0 0 10px 0;font-size: 20px; font-weight: bold;">Thanh Toán</p>
                <div style=" display: flex; justify-content: space-around;">
                    <img style="width: 80px; background-color: rgb(233, 239, 236); padding: 5px;"
                        src="{{asset('/images/banking.png')}}" alt="Lỗi hiển thị">
                    <img style="width: 85px; background-color: rgb(233, 239, 236); padding: 2px;" src="{{asset('/images/cod.png')}}"
                        alt="Lỗi hiển thị">
                </div>
                {{-- <p style="text-align: center; padding:10px 0;font-size: 20px; font-weight: bold;">Đơn vị vận chuyển
                </p>
                <div class="footer_bottom_logastic"
                    style="display: flex; justify-content: space-around;flex-wrap: wrap;">
                    <div>
                        <img style="width: 90px;" src="{{asset('images/spx.png')}}" alt="Lỗi hiển thị">
                        <img style="width: 90px;" src="{{asset('/images/jt.png')}}" alt="Lỗi hiển thị">
                    </div>
                    <div class="footer_bottom_logastic_item" style="margin-top: 5px;">
                        <img style="width: 90px;" src="{{asset('/images/be.png')}}" alt="Lỗi hiển thị">
                        <img style="width: 90px;" src="{{asset('/images/alo.png')}}" alt="Lỗi hiển thị">
                    </div>
                </div> --}}
            </div>
            <div class="footer_top_item">
                <p style="text-align: center;font-size: 20px; font-weight: bold;">Kết nối với chúng tôi</p>
                <ul>
                    <li><a href="{{$lienKetWebsite->facebook}}"><i class="fab fa-facebook"></i>Facebook</a>
                    </li>
                    <li><a href="{{$lienKetWebsite->youtube}}"><i class="fab fa-youtube"></i>Youtube</a></li>
                </ul>
            </div>
            <div class="footer_bottom">
                <section id="lienhe" class="intro">
                    <h2>Liên Hệ Với Gidu Food</h2>
                    <p>📍 Địa chỉ: 65 Huỳnh Thúc Kháng, P.Bến Nghé, Q.1, Tp.HCM</p>
                    <p>📞 Hotline: 0909 123 456</p>
                    <p>📧 Email: lienhe@gidufood.vn</p>
                    <p>©2025 - Bản quyền thuộc về GiDu Food</p>
                  </section>
            </div>
            {{-- <div class="footer_top_item">
                <p style="text-align: center;font-size: 20px; font-weight: bold;">Danh mục sản phẩm</p>
                <ul>
                    @foreach ($danhSachDanhMuc as $item)
                        <li><a href="{{ route('timkiemsanpham', ['slug' => $item->slug])}}"><i
                                    class="fas fa-{{ $item->name == 'Điện Thoại' ? 'mobile' : strtolower($item->name) }}"></i>{{ $item->name }}</a>
                        </li>
                    @endforeach
                </ul>
            </div> --}}
            {{-- <div class="footer_top_item">
                <p style="text-align: center;font-size: 19px; font-weight: bold;">Thương hiệu mới nhất</p>
                <ul>
                    @foreach ($danhSachPhanLoai as $item)
                        <li><a href="{{ route('timkiemsanpham', ['slug' => $item->name])}}"><i class="fas fa-angle-right"></i>{{ $item->name }}</a></li>
                    @endforeach
                </ul>
            </div> --}}
        </div>
        <div class="footer_top_right">
            <div class="footer_top_logo">
                <a href=""><img src="{{asset('images/'.$lienKetWebsite->logo)}}" style="max-width: 100%" alt="Lỗi hiển thị"></a>
            </div>
            <div class="footer_top_slogan">
                <h1 class="slogan">NGON NHANH <br>- NO GỌN!</h1>
            </div>
        </div>
    </div>

</footer>
