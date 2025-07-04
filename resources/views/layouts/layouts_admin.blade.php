<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    {{-- <link rel="shortcut icon" href="{{ asset('images/favicon.svg') }}" type="image/x-icon"> --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('css')

    <title>@yield('title') - GiDu Food</title>
</head>

<body>
    {{-- header --}}
    @include('admin.partials.header_admin')
    {{-- header --}}
    <div class="container">

        {{-- sidebar --}}
        @include('admin.partials.sidebar')
        {{-- sidebar --}}

        {{-- main --}}
        <div class="main">
            <div class="separator"></div>
            @yield('content')
        </div>
        {{-- main --}}
    </div>
    <script src="{{ asset('js/admin.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Alertify JS -->
    <script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
    @yield('script')
    <script>
        @if (session('message'))
            alertify.alert('{{ session('message') }}');
        @endif
    </script>
</body>

</html>
