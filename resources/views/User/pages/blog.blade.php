@extends('layouts.layouts_user')
@section('title', 'Trang giới thiệu')
@section('content')
    {{-- giới thiệu website GiDu Food --}}
<style>
:root {
        --orange: #ff6600;
        --light-orange: #fff2e6;
        --white: #ffffff;
        --text-dark: #333;
      }

      body {
        font-family: 'Segoe UI', sans-serif;
        background-color: var(--white);
        color: var(--text-dark);
        margin: 0;
        padding: 0;
      }

      /* Nội dung chính */
      section {
        padding: 40px 20px;
      }

      /* Giới thiệu */
      .intro {
        text-align: center;
        max-width: 800px;
        margin: auto;
      }

      /* Tiêu đề */
      h2 {
        margin-bottom: 15px;
      }

      /* Thực đơn */
      .menu {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 25px;
        margin-top: 30px;
      }

      .item {
        background-color: var(--light-orange);
        border-radius: 12px;
        width: 260px;
        padding: 15px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        text-align: center;
        transition: transform 0.3s;
      }

      .item:hover {
        transform: translateY(-5px);
      }

      .item img {
        max-width: 100%;
        border-radius: 10px;
        margin-bottom: 10px;
      }

      /* Responsive nhỏ */
      @media (max-width: 600px) {
        .menu {
          flex-direction: column;
          align-items: center;
        }
      }

</style>

      <section id="gioithieu" class="intro" style="background-color:#fff2e6;
      ">
        <h3>Chào mừng Quý khách!</h3>
                <h4>GiDu Shop</h4>
        <h2>Về Chúng Tôi</h2>
        <p>Gidu Food là cửa hàng chuyên cung cấp các món ăn nhanh hấp dẫn, phục vụ nhanh chóng, tiện lợi với giá cả hợp lý. Đội ngũ đầu bếp chuyên nghiệp và nguyên liệu sạch giúp bạn luôn an tâm khi thưởng thức.</p>
        <p>GiDu Food – Thế giới ăn vặt đậm chất Việt!
          Chuyên bán bánh tráng, trà sữa các loại, giá bình dân, phục vụ nhanh – chuẩn vị học trò, sinh viên và dân văn phòng.  </p>
      </section>

      <section id="thucdon">
        <h2 style="text-align: center;">Thực Đơn Phổ Biến</h2>
        <div class="menu">
          <div class="item">
            <img src="images/banh-trang-tron-sate.jpg" alt="Bánh tráng trộn">
            <h3 >Bánh Tráng Trộn</h3>
            <p>Bánh tráng mềm dẻo, đậm đà, topping đầy đủ: khô bò, trứng cút, rau răm, xoài bào.</p>
          </div>
          <div class="item">
            <img src="images/tra-sua-truyen-thong.jpg" alt="Trà sữa">
            <h3>Trà Sữa Trân Châu</h3>
            <p>Trà thơm béo kết hợp trân châu mềm dẻo, mát lạnh sảng khoái.</p>
          </div>
          <div class="item">
            <img src="images/tra-mang-cau.jpg" alt="Nước ngọt">
            <h3>Trà trái cây</h3>
            <p>Thức uống thanh mát, kết hợp giữa vị trà nhẹ và hương chua ngọt đặc trưng của trái cây.</p>
          </div>
        </div>
      </section>


@endsection
