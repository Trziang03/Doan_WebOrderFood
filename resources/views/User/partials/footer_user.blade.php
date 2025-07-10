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
                    <p>📍 Địa chỉ: 65 Huỳnh Thúc Kháng, P.Sài Gòn, Tp.HCM</p>
                    <p>📞 Hotline: 0909 123 456</p>
                    <p>📧 Email: gidufood@gmail.com</p>
                    <p>Copyright ©2025 - GiDuFood</p>
                  </section>
            </div>
        </div>
        <div class="footer_top_right">
            <div class="footer_top_logo">
                <a href=""><img src="{{asset('images/'.$lienKetWebsite->logo)}}" style="max-width: 100%" alt="Lỗi hiển thị"></a>
            </div>
            <div class="footer_top_slogan">
                <h1 class="slogan">NGON NHANH NO GỌN!</h1>
            </div>
        </div>
    </div>

</footer>
