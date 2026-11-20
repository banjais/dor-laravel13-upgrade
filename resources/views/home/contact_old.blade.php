@extends('layout.client.container')
@section('content')

    <?php
    $languageId = Session::get('language_id');
    $home = $languageId == 1 ? 'गृह पृष्‍ठ  ' : 'Home';
    $heading = $languageId == 1 ? 'सम्पर्क' : 'Contact Us';
    ?>
    <style>
        #gmap_canvas img {
            max-width: none !important;
            background: none !important
        }

    </style>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCibdBtd_WXNGP7yOF4oYnTtw_mEj7_OVg&sensor=false"></script>
    <script type="text/javascript">
        var latitude = '<?php echo $officeData->office_latitude; ?>';
        var longitude = '<?php echo $officeData->office_longitude; ?>';
        var name = '<?php echo $officeData->office_name; ?>';
        var address = '<?php echo $officeData->office_address; ?>';

        function init_map() {
            var myOptions = {
                zoom: 17,
                center: new google.maps.LatLng(latitude, longitude),
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };
            map = new google.maps.Map(document.getElementById("gmap_canvas"), myOptions);
            marker = new google.maps.Marker({
                map: map,
                position: new google.maps.LatLng(latitude, longitude)
            });
            infowindow = new google.maps.InfoWindow({
                content: "<b>" + name + " </b><br/>" + address
            });
            google.maps.event.addListener(marker, "click", function() {
                infowindow.open(map, marker);
            });
            infowindow.open(map, marker);
        }
        google.maps.event.addDomListener(window, 'load', init_map);
    </script>

    <div class="container">
        <section class="margin-bottom">
            <div class="breadcrum">
                <ul>
                    <li><a href="{{ $abs_site_url . $subdomain }}">{{ $home }} </a></li>
                    <li><i class="fa fa-chevron-right"></i></li>
                    <li>{{ $heading }}</li>
                </ul>
            </div>
            <div class="row">
                <div class="col-md-8 wow fadeInRight animated" style="visibility: visible; -webkit-animation: fadeInRight;">
                    <h3 class="hentry__title">{{ $heading }}</h3>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="textwidget panel widget">
                                <span class="icon-container"><span class="fa fa-home"></span></span>
                                <b>{{ $officeData->office_name }}</b><br>
                                {{ $officeData->office_address }} <br><br>
                                <span class="icon-container"><span class="fa fa-phone"></span></span>
                                <b>{{ $officeData->office_phone_number1 }}</b><br>
                                <span class="icon-container"><span class="fa fa-fax"></span></span>
                                <b>{{ $officeData->office_phone_number2 }}</b><br>

                                <span class="icon-container"><span class="fa fa-envelope"></span></span><a
                                    href="mailto:{{ $officeData->office_email_address }}">{{ $officeData->office_email_address }}</a>
                                <br><br>
                                @if (isset($contactData))
                                    <?php //echo $contactData->content_heading;
                                    if (file_exists('uploads/pages/' . $contactData->content_attachment) && $contactData->content_attachment != '') {
                                        echo '<span><a href="' .
                                            $abs_site_url .
                                            'home/page/mainforce/' .
                                            $contactData->content_url .
                                            '/' .
                                            $officeId .
                                            '" target="_blank">
                                                                        											<i class="glyphicon glyphicon-download mr6"></i>Download File</a>
                                                                        										</span>';
                                    } ?>
                                @endif
                            </div>
                            <div class="panel widget widget_pt_social_icons panel-last-child">
                                <a class="social-icons__link" href="http://facebook.com/dor" target="_blank"><i
                                        class="fa  fa-facebook"></i></a>
                                <a class="social-icons__link" href="http://twitter.com" target="_blank"><i
                                        class="fa  fa-twitter"></i></a>
                                <a class="social-icons__link" href="http://youtube.com" target="_blank"><i
                                        class="fa  fa-youtube"></i></a>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="wpcf7" id="wpcf7-f5-o1" dir="ltr" lang="en-US">
                                <div style="overflow:hidden;height:400px;width:100%;">
                                    <div id="gmap_canvas" style="height:400px;width:100%;"></div>
                                </div>
                            </div>

                            <div>
                                @if ($contactData)
                                    <?php echo Helper::GetSubContentDoclist($contactData->content_url, $contactData->id, '1'); ?>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @include('layout.client.sidebar')
            </div>
            <hr class="dotted" />
        </section>
    </div>
@stop
