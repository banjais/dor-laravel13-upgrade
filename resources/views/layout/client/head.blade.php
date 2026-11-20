
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <title>{{ isset($pageTitle) ? $pageTitle : '' }} || {{ $site_name }}</title>

    <!--<link rel="shortcut icon" href="{{ $abs_site_url }}assets/img/favicon.png">-->
     <link rel="icon" href="http://mof.gov.np/images/favicon.ico" />


    <meta name="keywords" content="{{ isset($metaKeywords) ? $metaKeywords : '' }}" />
	<meta name="description" content="{{ isset($metaDescription) ? $metaDescription : '' }}">
	<meta name="copyright" content="© {{ date('Y').' , ' . $site_name }}" />
    <meta name="author" content="Dry Ice Solutions P. Ltd." />
    <meta name="email" content="info@dryicesolutions.net" />
    <meta name="Distribution" content="Global" />

    <!-- CSS -->
    <link href="{{ asset('assets/css/preload.css') }}" rel="stylesheet" media="screen">
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" media="screen">
    <link href="{{ asset('assets/css/yamm.css') }}" rel="stylesheet" media="screen">
    <link href="{{ asset('assets/css/bootstrap-switch.min.css') }}" rel="stylesheet" media="screen">
    <link href="{{ asset('assets/css/font-awesome.min.css') }}" rel="stylesheet" media="screen">
    <link href="{{ asset('assets/css/animate.min.css') }}" rel="stylesheet" media="screen">
    <link href="{{ asset('assets/css/lightbox.css') }}" rel="stylesheet" media="screen">
    <link href="{{ asset('assets/css/jquery.bxslider.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/syntaxhighlighter/shCore.css') }}" rel="stylesheet" media="screen">
	<link href="{{ asset('assets/css/dropdown-styles.css') }}" rel="stylesheet" media="screen">

    <link href="{{ asset('assets/css/style-blue.css') }}" rel="stylesheet" media="screen" title="default">
    <link href="{{ asset('assets/css/width-full.css') }}" rel="stylesheet" media="screen" title="default">

    <link href="{{ asset('assets/css/buttons.css') }}" rel="stylesheet" media="screen">
    <link href="{{ asset('assets/css/datepicker.css') }}" rel="stylesheet" media="screen">
	
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/jquery.fancybox.css') }}?v=2.1.5" media="screen" />
    <link href="{{ asset('assets/css/website-theme.css') }}" rel="stylesheet" media="screen">
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


<!-- <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script> -->
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
 <!-- load jquery via CDN -->
<script src="{{ asset('assets/js/jquery.cookie.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap-switch.min.js') }}"></script>
<script src="{{ asset('assets/zoom/jquery.elevatezoom.js') }}"></script> 
<!-- <script src="{{ asset('assets/js/ticker.js') }}"></script> -->
<script type="text/javascript" src="{{ asset('assets/js/endlessRiver.js') }}"></script>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-85788045-1', 'auto');
  ga('send', 'pageview');

</script> 
