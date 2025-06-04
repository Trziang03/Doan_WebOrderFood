@extends('layouts.layouts_user')
@section('title', 'Trang giới thiệu')
@section('content')
    {{-- <style>
        .technology_search {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 12px;
        }

        .technology_search>form {
            width: 30%;
            display: flex;
            align-items: center;
        }

        .technology_search>form>input {
            border: none;
            padding: 5px 2px 5px 10px;
            outline: none;
            width: 300px;
            background-color: white;
            border-bottom-left-radius: 15px;
            border-top-left-radius: 15px;
        }

        .technology_search>form>button {
            padding: 5px 10px 5px 0;
            border: none;
            background-color: white;
            border-bottom-right-radius: 15px;
            border-top-right-radius: 15px;
        }

        .technology_content .pagination a {
            color: black;
            padding: 10px;
        }

        .technology_content .row_news .col_news>a {
            color: black;
        }
    </style>
    <div class="container_css" style="padding:10px;">
        <div class="content">
            <div class="header_content">
                <h3>Chào mừng Quý khách!</h3>
                <h4>GiDu Shop</h4>
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequuntur optio amet laborum, 
                    libero dignissimos autem exercitationem maxime, dolorem sunt ex placeat culpa! Aspernatur 
                    ipsam harum nobis reprehenderit magni a doloremque!</p>
                <p>Địa chỉ: Trường Cao Đẵng Kỹ Thuật Cao Thắng</p>
                <p>Chân thành cảm ơn và mong được phục vụ Quý khách!</p>
            </div>
            <div class="body_content">
                <div class="intro_content">
                    <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Quis, ex quos a in unde rem repellendus 
                        labore eveniet numquam iure expedita eligendi et ipsa dicta magni itaque alias. Debitis, optio.
                    </p>
                    <br>
                    <p style="text-align: center;"></p>
                </div>  
            </div>
        </div>
    </div> --}}
  

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
        color: white;
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
    
      <section id="gioithieu" class="intro">
        <h3>Chào mừng Quý khách!</h3>
                <h4>GiDu Shop</h4>
        <h2>Về Chúng Tôi</h2>
        <p>Gidu Food là cửa hàng chuyên cung cấp các món ăn nhanh hấp dẫn, phục vụ nhanh chóng, tiện lợi với giá cả hợp lý. Đội ngũ đầu bếp chuyên nghiệp và nguyên liệu sạch giúp bạn luôn an tâm khi thưởng thức.</p>
      </section>
    
      <section id="thucdon">
        <h2 style="text-align: center;">Thực Đơn Phổ Biến</h2>
        <div class="menu">
          <div class="item">
            <img src="images/banh-trang-tron-sate.jpg" alt="Bánh tráng trộn">
            <h3>Bánh Tráng Trộn</h3>
            <p>Bánh tráng mềm dẻo, đậm đà, topping đầy đủ: khô bò, trứng cút, rau răm, xoài bào.</p>
          </div>
          <div class="item">
            <img src="images/tra-sua-truyen-thong.jpg" alt="Trà sữa">
            <h3>Trà Sữa Trân Châu</h3>
            <p>Trà thơm béo kết hợp trân châu mềm dẻo, mát lạnh sảng khoái.</p>
          </div>
          <div class="item">
            <img src="images/pepsi.jpg" alt="Nước ngọt">
            <h3>Nước Ngọt Có Ga</h3>
            <p>Giải khát tức thì với Coca, Pepsi, Sting… luôn sẵn sàng phục vụ.</p>
          </div>
        </div>
      </section>
    
      
@endsection
