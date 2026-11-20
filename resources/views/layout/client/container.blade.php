<!DOCTYPE html>
<html lang="en">

<head>
    @include('layout.client.head')
</head>

<!-- Preloader -->
<div id="preloader">
    <div id="status">&nbsp;</div>
</div>

<body>

    <div id="sb-site">
        <div class="boxed">

            <!-- header -->
            @include('layout.client.header')
            <!-- header -->

            @yield('content')

            <!-- footer -->
            @include('layout.client.footer')
            <!-- footer -->

        </div> <!-- boxed -->
    </div> <!-- sb-site -->

    <div class="sb-slidebar sb-right">
        <div class="input-group">
            <input type="text" class="form-control" placeholder="Search...">
            <span class="input-group-btn">
                <button class="btn btn-default" type="button"><i class="fa fa-search"></i></button>
            </span>
        </div><!-- /input-group -->
        <div class="input-group" style="width:100%;">
            <ul class="inline">
                <?php if(languageId() == 1){ ?>
                <li class="language"><a href="javascript:void(0)" class="change_language"
                        id="language_2">ENGLISH</a></li>
                <li class="language">| नेपाली</li>
                <?php }  else { ?>
                <li class="language">ENGLISH | </li>
                <li class="language"><a href="javascript:void(0)" class="change_language"
                        id="language_1">नेपाली</a></li>
                <?php }  ?>
            </ul>
        </div><!-- /input-group -->

        <h2 class="slidebar-header no-margin-bottom">Navigation</h2>

        <ul class="slidebar-menu">
            <li>
                <a href="{{ $abs_site_url . $subdomain }}"
                    class="animated fadeIn animation-delay-6 custom">{{ \Helper::getNameOftext('homepage', languageId()) }}</a>
            </li>
            <li>
                <a href="{{ $abs_site_url . $subdomain . '/feedback' }}"
                    class="animated fadeIn animation-delay-7 custom">{{ \Helper::getNameOftext('feedback', languageId()) }}</a>
            </li>
            <li>
                <a href="{{ $abs_site_url . $subdomain . '/laws' }}"
                    class="animated fadeIn animation-delay-8 custom">{{ \Helper::getNameOftext('laws', languageId()) }}</a>
            </li>
            <li>
                <a href="{{ $abs_site_url . $subdomain . '/page/links' }}"
                    class="animated fadeIn animation-delay-9 custom">
                    {{ \Helper::getNameOftext('links', languageId()) }}</a>
            </li>
            <li>
                <a href="{{ $abs_site_url . $subdomain . '/contact' }}"
                    class="animated fadeIn animation-delay-9 custom">{{ \Helper::getNameOftext('contact', languageId()) }}</a>
            </li>
            <li>
                <a href="http://mail.dor.gov.np/" target="_blank"
                    class="animated fadeIn animation-delay-9 custom">{{ \Helper::getNameOftext('check-mail', languageId()) }}</a>
            </li>
        </ul>
    </div> <!-- sb-slidebar sb-right -->

    <div id="back-top">
        <a href="#header"><i class="fa fa-chevron-up"></i></a>
    </div>

    <!-- Scripts -->
    @include('layout.client.foot')
    <!-- Scripts -->

</body>

</html>
