@extends('layout.client.container')
@section('content')
    <?php
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
    ?>
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

                    <!--welcome to dor-->
                    <div class=" calculator">
                        <h3 class="no-margin-top hentry__title">
                            Distance Calculator
                        </h3>
                        <form action="#" method="POST" id="distance">
                            @csrf
                            <ul>
                                <li><label for="">From</label>

                                <?php echo $distance->getSelectHtml('start', $test); ?>

                                <li><label for="">Via</label><?php echo $distance->getSelectHtml('through', $test, 'TRUE'); ?></li>
                                <li><label for="">To</label> <?php echo $distance->getSelectHtml('end', $test); ?></li>
                                <li> <button type="submit" class="btn btn-success">Find <span
                                            class="fa fa-arrow-right"></span></button>
                                </li>
                            </ul>
                        </form>
                    </div>
                    <!--calculator-->
                    <?php if (isset($homeMap) &&  file_exists('uploads/offices/'.$homeMap->office_attachment) && $homeMap->office_attachment != '' ): ?>
                    <div class="col-md-12">
                        <h3 class="hentry__title">Map</h3>
                        <div class="content-box box-default">

                            <img id="zoom_01" src="{{ $rel_site_url }}uploads/offices/{{ $homeMap->office_attachment }}"
                                class="img-responsive animated bounceInLeft animation-delay-1" alt="Image"
                                data-zoom-image="{{ $rel_site_url }}uploads/offices/{{ $homeMap->office_attachment }}">


                        </div>
                    </div>
                    <?php endif ?>

                </div>
                @include('layout.client.sidebar')
            </div>
            <hr class="dotted">

        </section>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="top:250px;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Distance Calculator</h4>
                </div>
                <div class="modal-body">
                    <div id="msgdisplay"></div>
                    <div id="msgtext"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        $('#zoom_01').elevateZoom({
            zoomType: "inner",
            cursor: "crosshair",
            zoomWindowFadeIn: 900,
            zoomWindowFadeOut: 1150,
            scrollZoom: true
        });
    </script>
    <script type="text/javascript">
        // magic.js
        $(document).ready(function() {


            // process the form
            $('form#distance').submit(function(event) {
                // alert(0);

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
                        url: 'http://localhost:8000/home/distancecalc', // the url where we want to POST
                        data: formData, // our data object
                        dataType: 'json', // what type of data do we expect back from the server
                        encode: true,
                        '_token': '{{ csrf_token() }}'
                    })
                    // using the done promise callback
                    .done(function(data) {


                        $('#msgdisplay').empty();
                        $('#msgtext').empty();
                        $('#myModal').modal('show');
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


@stop
