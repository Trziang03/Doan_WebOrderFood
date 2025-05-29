@extends('layouts.layouts_user')
@section('title', 'Trang giới thiệu')
@section('content')
    <style>
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
                <h4>{{ DB::table('about')->select('name')->first()->name}} Shop</h4>
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequuntur optio amet laborum, 
                    libero dignissimos autem exercitationem maxime, dolorem sunt ex placeat culpa! Aspernatur 
                    ipsam harum nobis reprehenderit magni a doloremque!</p>
                <p>Địa chỉ: {{ DB::table('about')->select('address')->first()->address}}</p>
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
    </div>
  
@endsection
