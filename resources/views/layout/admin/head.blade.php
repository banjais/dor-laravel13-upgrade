    <meta charset="utf-8" />
    <title>{{ $site_name ?? config('app.name', 'Department of Roads') }}</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta content="" name="description" />
    <meta content="" name="author" />
    <link href="{{ asset('assets/admin/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/admin/bootstrap/css/bootstrap-responsive.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/admin/font-awesome/css/font-awesome.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/style.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/style_responsive.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/admin/bootstrap/css/bootstrap-fileupload.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/style_default.css') }}" rel="stylesheet" id="style_color" />

    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/gritter/css/jquery.gritter.css') }}" />

    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/uniform/css/uniform.default.css') }}" />
    <link href="{{ asset('assets/admin/fancybox/source/jquery.fancybox.css') }}" rel="stylesheet" />

    <link rel="stylesheet" type="text/css"
        href="{{ asset('assets/admin/bootstrap-datepicker/css/datepicker.css') }}" />

    <!-- jquery slider -->
    <link rel="stylesheet" href="{{ asset('assets/admin/jslider/css/jslider.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('assets/admin/jslider/css/jslider.blue.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('assets/admin/jslider/css/jslider.plastic.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('assets/admin/jslider/css/jslider.round.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('assets/admin/jslider/css/jslider.round.plastic.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('assets/admin/data-tables/DT_bootstrap.css') }}" />
    <link rel="stylesheet"
        href="{{ asset('assets/admin/bootstrap-toggle-buttons/static/stylesheets/bootstrap-toggle-buttons.css') }}" />
    <link rel="stylesheet" type="text/css"
        href="{{ asset('assets/admin/bootstrap-colorpicker/css/colorpicker.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/chosen_bootstrap/chosen.css') }}" />
    <link rel="stylesheet" type="text/css"
        href="{{ asset('assets/admin/jquery-ui/jquery-ui-1.10.1.custom.min.css') }}" />
    <!-- end -->

    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css_2021/spectrum.css') }}" />


    <script src="{{ asset('assets/admin/js/jquery-1.8.3.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/js/jquery.validate.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/js/additional-methods.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/ckeditor/ckeditor.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/js/jquery-ui.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/admin/js/jquery-ui-1.7.1.custom.min.js') }}"></script>

    <script src="{{ asset('assets/admin/bootstrap/js/bootstrap.min.js') }}"></script>

    <script type="text/javascript" src="{{ asset('assets/js_2021/spectrum.js') }}"></script>

    <script type="text/javascript">
        var sitePath = @json($abs_site_url ?? url('/'));
    </script>
