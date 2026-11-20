@extends('layout.client.container_2021')
@section('content')
    @php
    $languageId = Session::get('language_id');
    $home = $languageId == 1 ? 'गृह पृष्‍ठ  ' : 'Home';
    $projectheading = $languageId == 1 ? 'रोड प्रोजेक्टस' : 'Road Projects';
    // dd($projectData->google_map_embed);
    @endphp
    <style>
        #gmap_canvas img {
            max-width: none !important;
            background: none !important
        }

    </style>


    {{-- <script src="https://maps.googleapis.com/maps/api/js?sensor=false&key=AIzaSyCibdBtd_WXNGP7yOF4oYnTtw_mEj7_OVg"></script>
    <script type="text/javascript">
        var latitude = '<?php echo $projectData->project_latitude; ?>';
        var longitude = '<?php echo $projectData->project_longitude; ?>';
        var zoom = '<?php echo $projectData->project_zoom; ?>';
        var name = '<?php echo $projectData->project_heading; ?>';
        var address = '<?php echo $projectData->project_location; ?>';

        function init_map() {
            var myOptions = {
                zoom: 10,
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
    <main class="main-body">
        <section class="content-block">
            <div class="container">
                <div class="breadcrumb-wrap">
                    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a
                                    href="{{ $abs_site_url . $subdomain }}">{{ \Helper::getNameOftext('homepage', $languageId) }}
                                </a></li>
                            <li class="breadcrumb-item"><a
                                    href="{{ $abs_site_url . $subdomain . '/projects' }}">{{ $projectheading }}</a>
                            </li>
                            <li class="breadcrumb-item">{{ $projectData->project_heading }}
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mt-4">
                            <div class="block-title">
                                <h2>{{ $projectData->project_heading }}</h2>
                                <div class="divider"></div>
                            </div>
                            <div id="gmap_canvas" style="height:400px;width:100%;">
                                {!!$projectData->google_map_embed !!}
                            </div>
                            <div>{!! $projectData->project_description !!}</div>
                        </div>

                    </div>
                    {{-- <div class="col-md-4">
                        @include('layout.client.sidebar_2021')
                    </div> --}}
                </div>
            </div>
        </section>
    </main>


@stop
