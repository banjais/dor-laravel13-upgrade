@extends('layout.client.container_2021')
@section('content')
    @php
    use App\Models\Distance;
    $languageId = Session::get('language_id');
    $home = $languageId == 1 ? 'गृह पृष्‍ठ  ' : 'Home';
    $heading = $languageId == 1 ? 'Distance Calculator' : 'Distance Calculator';
    $lowerHeading = $languageId == 1 ? 'क्षेत्रीय कार्यालयहरु' : 'Regional Offices';
    $lowerHeading2 = $languageId == 1 ? 'हेअवी एकुइप्मेन्त डिभिजनस' : 'Heavy Equipment Divisions';
    $lowerHeading3 = $languageId == 1 ? 'मेशिनसम्बन्धी कार्यालयहरु' : 'Mechanical offices';
    $notAvailableMessage = $languageId == 1 ? 'कुनैपनि रेकर्ड पाइएन ।' : 'Records are Not Available.';
    $distance = new Distance();
    $test = $distance->RouteDijkstra();
    @endphp
    <main class="main-body">
        <section class="content-block">
            <div class="container">
                <div class="breadcrumb-wrap">
                    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a
                                    href="{{ $abs_site_url . $subdomain }}">{{ \Helper::getNameOftext('homepage', $languageId) }}
                                </a></li>
                            <li class="breadcrumb-item">{{ $heading }}
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mt-4">
                            <div class="block-title">
                                <h2>{{ $heading }}</h2>
                                <div class="divider"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div>
                                <div class="col-lg-6">
                                    <form action="#" method="POST" id="distance">
                                        @csrf
                                        <div>
                                            <p>
                                                <label class="mb-2">From:</label>
                                                <?php echo $distance->getSelectHtml('start', $test); ?>
                                            </p>
                                            <p>
                                                <label class="mb-2">Via:</label>
                                                <?php echo $distance->getSelectHtml('through', $test, 'TRUE'); ?>
                                            </p>
                                            <p>
                                                <label class="mb-2">To:</label>
                                                <?php echo $distance->getSelectHtml('end', $test); ?>
                                            </p>
                                            <p>
                                                <button type="submit" class="btn btn-success">Find <span
                                                        class="fa fa-arrow-right"></span></button>
                                            </p>
                                        </div>
                                    </form>
                                    <!--calculator-->
                                    {{-- @if (isset($homeMap) && file_exists('uploads/offices/' . $homeMap->office_attachment))
                                        <div class="col-md-12">
                                            <div class="block-title">
                                                <h2>Map</h2>
                                                <div class="divider"></div>
                                            </div>
                                            <div class="content-box box-default">

                                                <img id="zoom_01"
                                                    src="{{ $rel_site_url }}uploads/offices/{{ $homeMap->office_attachment }}"
                                                    class="img-responsive animated bounceInLeft animation-delay-1"
                                                    alt="Image"
                                                    data-zoom-image="{{ $rel_site_url }}uploads/offices/{{ $homeMap->office_attachment }}">


                                            </div>
                                        </div>
                                    @endif --}}
                                </div>

                            </div>
                        </div>
                    </div>
        </section>
    </main>

    <!-- Modal -->
    <div class="modal" tabindex="-1" id="modalDistanceResult" style="top:250px;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Distance Calculator</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="msgdisplay"></p>
                    <p id="msgtext"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    <script>
        // $('#zoom_01').elevateZoom({
        //     zoomType: "inner",
        //     cursor: "crosshair",
        //     zoomWindowFadeIn: 900,
        //     zoomWindowFadeOut: 1150,
        //     scrollZoom: true
        // });
    </script>
    <script type="text/javascript">
        // magic.js
        $(document).ready(function() {
            // process the form
            $('form#distance').submit(function(event) {
                // get the form data
                // there are many ways to get this data using jQuery (you can use the class or id also)
                var formData = {
                    'start': $('select[name=start]').val(),
                    'through': $('select[name=through]').val(),
                    'end': $('select[name=end]').val()
                };
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                // process the form
                $.ajax({
                        type: 'POST', // define the type of HTTP verb we want to use (POST for our form)
                        url: '/home/distancecalc', // the url where we want to POST
                        data: formData, // our data object
                        dataType: 'json', // what type of data do we expect back from the server
                        encode: true,
                        '_token': '{{ csrf_token() }}'
                    })
                    // using the done promise callback
                    .done(function(data) {
                        $('#msgdisplay').empty();
                        $('#msgtext').empty();
                        $('#modalDistanceResult').modal('show');
                        $('#msgdisplay').append('<strong>Distance: </strong> <label >' + data.Distance +
                            ' KM</label >');
                        $('#msgtext').append('<strong>Route: </strong><label >' + data.RouteFromOrigin +
                            '</label >');
                        // here we will handle errors and validation messages
                    });

                // stop the form from submitting the normal way and refreshing the page
                event.preventDefault();
            });

        });
    </script>
@endsection
