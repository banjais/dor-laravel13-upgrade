<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->

<head>
    @include('layout.admin.head')
</head>

<body class="fixed-top">
    @php
        $user = Auth::user();
        $is_circular_login = 0;
        if ($user && $user->user_type_id == 13) {
            $is_circular_login = 1;
        }
    @endphp
    @include('layout.admin.header')
    <div id="container" class="row-fluid">
        @if ($is_circular_login == 0)
            @include('layout.admin.sidebar')
        @endif
        @yield('content')

    </div>

    @include('layout.admin.footer')
    @include('layout.admin.foot')

    @yield('footer_js')

</body>

</html>
