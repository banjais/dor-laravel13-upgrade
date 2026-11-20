@extends('layout.client.container_2021')
@section('content')
    <style>
        #gmap_canvas {
            position: relative;
            width: 100%;
            aspect-ratio: 1 / 1;
            min-height: 280px;
            max-height: 520px;
            overflow: hidden;
        }

        #gmap_canvas iframe {
            position: absolute;
            inset: 0;
            display: block;
            width: 100% !important;
            height: 100% !important;
            max-width: 100%;
            border: 0;
        }

        @media (max-width: 575.98px) {
            #gmap_canvas {
                min-height: 280px;
                max-height: 420px;
            }
        }
    </style>
    {{-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCibdBtd_WXNGP7yOF4oYnTtw_mEj7_OVg&sensor=false"></script>
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
    </script> --}}
    @php
    use App\Models\Home;
    $contact_details = $languageId == 1 ? 'सम्पर्क विवरण' : 'Contact Details';
    $follow_us = $languageId == 1 ? 'फलो गर्नुहोस्' : 'Follow Us';
    $quick_message = $languageId == 1 ? 'आफ्नो विवरण दिनुहोस्' : 'Your Details';
    $google_map = $languageId == 1 ? 'गुगल नक्शामा हामीलाई खोज्नुहोस्' : 'Find us on Google Map';
    $facebook = $languageId == 1 ? 'फेसबुक' : 'Facebook';
    $instagram = $languageId == 1 ? 'इन्स्टाग्राम' : 'Instagram';

    $facebookFeed = Home::getHomeDataFrontend('facebook-feed', $officeId, $languageId);
    $facebookUrl = trim((string) ($facebookFeed->data_2 ?? ''));
    $instagramFeed = Home::getHomeDataFrontend('instagram-feed', $officeId, $languageId);
    $instagramUrl = trim((string) ($instagramFeed->data_2 ?? ''));

    @endphp
    <main class="main-body">
        <section class="content-block dor-contact-page">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="bg-light py-3 px-3 contact-detail">
                            <div class="address-box">
                                <div>
                                    <div class="block-title">
                                        <h2>
                                            {{ $contact_details }}
                                        </h2>
                                        <div class="divider"></div>
                                    </div>
                                    <ul>
                                        <li>
                                            <div class="icon-container"><i class="fa fa-home"></i></div>
                                            {{ $officeData->office_name }}<br />
                                            {{ $officeData->office_address }}
                                        </li>
                                        <li>
                                            <div class="icon-container"><i class="fa fa-phone"></i></div>
                                            {{ $officeData->office_phone_number1 }}
                                        </li>
                                        @if (!empty(trim((string) ($officeData->office_phone_number2 ?? ''))))
                                            <li>
                                                <div class="icon-container"><i class="fa fa-fax"></i></div>
                                                {{ $officeData->office_phone_number2 }}
                                            </li>
                                        @endif
                                        <li>
                                            <div class="icon-container"><i class="fa fa-envelope"></i></div>
                                            <a href="mailto:{{ $officeData->office_email_address }}">{{ $officeData->office_email_address }}</a>
                                        </li>
                                        @if (!empty($officeData->office_alternative_email))
                                            @php
                                                $alternativeEmails = preg_split('/[;,]+/', (string) $officeData->office_alternative_email);
                                                $alternativeEmails = array_values(array_filter(array_map('trim', $alternativeEmails), fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL)));
                                            @endphp
                                            @if (count($alternativeEmails))
                                                <li>
                                                    <div class="icon-container"><i class="fa fa-envelope"></i></div>
                                                    @foreach ($alternativeEmails as $alternativeEmail)
                                                        <a href="mailto:{{ $alternativeEmail }}">{{ $alternativeEmail }}</a>@if (!$loop->last), @endif
                                                    @endforeach
                                                </li>
                                            @endif
                                        @endif
                                        @if ($languageId == 1)
                                            <li>
                                                <div class="icon-container"><i class="far fa-calendar"></i> </div>
                                                <p>सोमबार - शुक्रबार : ०९:०० बिहान - ०५:०० बेलुका</p>
						<p>शनिबार र आइतबार :   बिदा</p>
                                            </li>
                                        @else
                                            <li>
                                                <div class="icon-container"><i class="far fa-calendar"></i> </div>
                                                <p>Monday-Friday: 09:00 AM - 05:00 PM</p>
                                                <p>Saturday & Sunday: Closed</p>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                                <div class="mt-4">
                                    <div class="block-title">
                                        <h2>
                                            {{ $follow_us }}
                                        </h2>
                                        <div class="divider"></div>
                                    </div>
                                    <div class="social-shares">
                                        @if ($facebookUrl !== '')
                                            <div class="mt-1">
                                                <a href="{{ $facebookUrl }}" target="_blank" rel="noopener noreferrer">
                                                    <i class="fab fa-facebook me-2" aria-hidden="true"></i>
                                                    {{ $facebook }}
                                                </a>
                                            </div>
                                        @endif
                                        @if ($instagramUrl !== '')
                                            <div class="mt-1">
                                                <a href="{{ $instagramUrl }}" target="_blank" rel="noopener noreferrer">
                                                    <i class="fab fa-instagram me-2" aria-hidden="true"></i>
                                                    {{ $instagram }}
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="bg-light py-3 px-3 contact-detail quick-message-panel mt-md-0 mt-3">
                            <div class="block-title">
                                <h2>
                                    {{ $quick_message }}
                                </h2>
                                <div class="divider"></div>
                            </div>
                            @php
                                $showContactBcc = true;
                                $grmsBccSetting = \App\Models\Configuration::getValueFromName('contact_grs_bcc_enabled');
                                $grmsBccEnabled = $grmsBccSetting === null || (string) $grmsBccSetting === '1';
                            @endphp
                            @include('home.feedback_partial')

                        </div>
                    </div>
                    <div class="col-12 col-md-12 col-lg-4">
                        <div class="bg-light py-3 px-3 maps mt-lg-0 mt-3">
                            <div class="block-title">
                                <h2>
                                    {{ $google_map }}
                                </h2>
                                <div class="divider"></div>
                            </div>
                            <div id="gmap_canvas">
                                {!! $officeData->google_map_embed !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endsection
