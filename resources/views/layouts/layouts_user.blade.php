<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/alertify.min.js"></script>

    <!-- CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/css/alertify.min.css" />
    <!-- Default theme -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/css/themes/default.min.css" />
    <!-- Semantic UI theme -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/css/themes/semantic.min.css" />
    <!-- Bootstrap theme -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/css/themes/bootstrap.min.css" />
    {{--<link rel="shortcut icon" href="{{ asset('images/favicon.svg') }}" type="image/x-icon"> --}}
    <link rel="stylesheet" href="{{ secure_asset('bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ secure_asset('fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ secure_asset('css/layout_user.css') }}">
    <link rel="stylesheet" href="{{ secure_asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ secure_asset('css/user_min.css') }}">
    <link rel="stylesheet" href="{{ secure_asset('css/user_min_two.css') }}">
    <link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout_user.css') }}">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/user_min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/user_min_two.css') }}">
    <title>@yield('title', 'Trang chủ') - GiDu Food</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

</head>

<body>
    <!-- Header -->
    @include('user.partials.header_user')
    <!-- Header -->
    <div class="cskh">
        <i id="scroll" class="far fa-hand-point-up"></i>

    </div>
    {{-- login register --}}
    @include('user.partials.login_register')
    {{-- login register --}}
    <main>
        @yield('content')
    </main>
    <!-- Footer -->
    @include('user.partials.footer_user')
    <!-- Footer -->

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('js/layout_user.js') }}"></script>
    <script src="https://a77f-113-185-64-1.ngrok-free.app/js/layout_user.js"></script>
    @yield('script')
    {{-- ấn nút tìm kiếm thanh input sẽ trượt ra --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.querySelector('.search-toggle');
            const searchInput = document.querySelector('.search-input');

            if (toggleBtn && searchInput) {
                toggleBtn.addEventListener('click', function () {
                    searchInput.classList.toggle('active');
                    if (searchInput.classList.contains('active')) {
                        searchInput.focus();
                    }
                });
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('.search-form');
            const input = document.querySelector('.search-input');

            form.addEventListener('submit', function (e) {
                // Nếu input chưa mở hoặc chưa nhập gì thì chỉ mở ra thôi
                if (!input.classList.contains('active') || input.value.trim() === '') {
                    e.preventDefault(); // Không gửi form
                    input.classList.add('active');
                    input.focus();
                }
            });
        });
        </script>
</body>
<script>
    function Login() {
        $('.alert_error_validate').text('');
        $.ajax({
            'url': "{{ route('dangnhap') }} ",
            'type': "POST",
            'data': {
                _token: '{{ csrf_token() }}',
                email_login: $('#email_login').val(),
                password_login: $('#password_login').val()
            },
            success: function(response) {
                if (response.message) {
                    alertify.success(response.message);
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }else{
                    window.location="/admin";
                }


            },
            error: function(xhr) {
                const error = xhr.responseJSON.errors;
                if (error) {
                    if (error.email_login)
                        $('#email_login_error').text(error.email_login);
                    if (error.password_login)
                        $('#password_login_error').text(error.password_login);
                } else if (xhr.responseJSON.msg_error)
                    alertify.error(xhr.responseJSON.msg_error)
            }
        })
    }

    function Register() {
        $('.alert_error_validate').text('');
        if ($('#password_register').val() !== $('#pwd_comfirm').val()) {
            $('#pwd_comfirm_error').text('Xác nhận password sai');
            return;
        }
        $('#pwd_comfirm_error').text('');
        $.ajax({
            'url': "{{ route('dangky') }}",
            'type': "POST",
            'data': {
                _token: '{{ csrf_token() }}',
                username: $('#username_register').val(),
                full_name: $('#full_name_register').val(),
                phone: $('#phone_register').val(),
                email_register: $('#email_register').val(),
                password_register: $('#password_register').val(),
            },
            success: function(response) {
                alertify.success(response.message);
                handleTargetLogin();
                document.querySelectorAll('.form_register .form_ground input').forEach(element => {
                    element.value = '';
                });
            },
            error: function(xhr) {
                const error = xhr.responseJSON.errors;
                if (error.username)
                    $('#username_register_error').text(error.username);
                if (error.full_name)
                    $('#full_name_register_error').text(error.full_name);
                if (error.email_register)
                    $('#email_register_error').text(error.email_register);
                if (error.phone)
                    $('#phone_register_error').text(error.phone);
                if (error.password_register)
                    $('#password_register_error').text(error.password_register);
            }
        });
    }
</script>

</html>
