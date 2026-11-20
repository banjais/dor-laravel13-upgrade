<meta charset="utf-8">
<title>{{ isset($pageTitle) ? $pageTitle : '' }} || {{ $site_name }}</title>

<!--<link rel="shortcut icon" href="{{ $abs_site_url }}assets/img/favicon.png">-->
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}?v={{ filemtime(public_path('favicon.png')) }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}?v={{ filemtime(public_path('apple-touch-icon.png')) }}">

<meta name="description" content="{{ isset($metaKeywords) ? $metaKeywords : '' }}" />
<meta name="keywords" content="{{ isset($metaDescription) ? $metaDescription : '' }}">
<meta name="copyright" content="© {{ date('Y') . ', ' . $site_name }}" />
<meta name="author" content="Department of Roads, Government of Nepal" />
<meta name="email" content="dgdor@dor.gov.np" />
<meta name="Distribution" content="Global" />

<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="manifest" href="{{ asset('manifest.webmanifest') }}?v={{ filemtime(public_path('manifest.webmanifest')) }}">
<meta name="theme-color" content="#142D4E">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<!--fonts-->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<!--/.fonts-->
<!-- Bootstrap -->
<link href="{{ asset('assets/css_2021/bootstrap.min.css') }}?v={{ filemtime(public_path('assets/css_2021/bootstrap.min.css')) }}" rel="stylesheet" />
<link href="{{ asset('assets/css_2021/fontawesome-all.min.css') }}?v={{ filemtime(public_path('assets/css_2021/fontawesome-all.min.css')) }}" rel="stylesheet" />
<link href="{{ asset('assets/css_2021/slick.min.css') }}?v={{ filemtime(public_path('assets/css_2021/slick.min.css')) }}" rel="stylesheet" />
<link href="{{ asset('assets/css_2021/slick-theme.min.css') }}?v={{ filemtime(public_path('assets/css_2021/slick-theme.min.css')) }}" rel="stylesheet" />
<link href="{{ asset('assets/css_2021/style.css') }}?v={{ filemtime(public_path('assets/css_2021/style.css')) }}" rel="stylesheet" />
<link href="{{ asset('assets/css_2021/responsive.css') }}?v={{ filemtime(public_path('assets/css_2021/responsive.css')) }}" rel="stylesheet" />
<style>
    /* .form-switch .form-check-input {
        height: 24px;
        width: 48px;
        background-color: #0099DA;
        border-color: #0099DA;
        border: none;
        background-image: url("data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='rgba(255,255,255,1.0)'/></svg>");
    }

    .form-switch .form-check-input:focus {
        border-color: rgba(0, 0, 0, 0.25);
        outline: 0;
        box-shadow: 0 0 0 0 rgba(0, 0, 0, 0);
        background-image: url("data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='rgba(0,0,0,0.25)'/></svg>");
    } */

    /* .form-switch .form-check-input:checked {
        background-color: #0099DA;
        border-color: #0099DA;
        border: none;
        background-image: url("data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='rgba(255,255,255,1.0)'/></svg>");
    } */

</style>
<link href="{{ asset('assets/css_2021/website-theme.css') }}?v={{ filemtime(public_path('assets/css_2021/website-theme.css')) }}" rel="stylesheet" />
<style>
:root {
    --theme-page-bg: {{ $websiteTheme['page_background'] ?? '#F5F6F4' }};
    --theme-card-bg: {{ $websiteTheme['card_background'] ?? '#FFFFFF' }};
    --theme-header-bg: {{ $websiteTheme['header_background'] ?? '#142D4E' }};
    --theme-nav-bg: {{ $websiteTheme['nav_background'] ?? '#1769AA' }};
    --theme-accent: {{ $websiteTheme['accent_color'] ?? '#8B2635' }};
    --theme-footer-bg: {{ $websiteTheme['footer_background'] ?? '#263746' }};
    --theme-text: {{ $websiteTheme['text_color'] ?? '#263238' }};
    --theme-border: {{ $websiteTheme['border_color'] ?? '#E5E8E6' }};
}
</style>
