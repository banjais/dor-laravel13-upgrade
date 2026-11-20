<?php
use Illuminate\Support\Str;
use App\Models\Office;
use App\Models\Distance;
use App\Models\Home;

$languageId = Session::get('language_id');
$officeId = Office::getIdfromSubdomain($subdomain);

$distance = new Distance();
// $test = $distance->RouteDijkstra();

$homeData3 = Home::getHomeDataFrontend('general-information', $officeId, $languageId);
$homegeneralinfo = Home::getHomeDataFrontend('general-info', $officeId, $languageId);
$homespokeperson = Home::getHomeDataFrontend('dor-spoke-person', $officeId, $languageId);

?>
<style type="text/css" media="screen">
    /* magnifying glass icon */
    .zoom:after {
        z-index: 1000;
        content: '';
        display: block;
        width: 33px;
        height: 33px;
        position: absolute;
        top: 0;
        right: 0;
        background: url(icon.png);
    }

    .zoom img {

        display: block;
    }

    .zoom img::selection {
        background-color: transparent;
    }

</style>

<div class="col-md-4 fadeInLeft animated animated" style="visibility: visible; animation-name: fadeInLeft;">
    <!--welcome to dor-->
    @if ($homeData3 != '')
        <div class="thumbnail pmmes">
            <h3 class="no-margin-top hentry__title">{{ $homeData3->home_heading }}</h3>
            <a href="{{ $abs_site_url . $subdomain }}" style="width:100px;">
                @if (isset($homeData3) && file_exists('uploads/pages/' . $homeData3->home_attachment) && $homeData3->home_attachment != '')
                    <img src="{{ $abs_site_url . 'uploads/pages/' . $homeData3->home_attachment }}">
                @endif
            </a>
            <div>

                @if (strlen($homeData3->home_description) != strlen(utf8_decode($homeData3->home_description)))
                    {{ Str::limit($homeData3->home_description, 600, '....') }}
                @else
                    {{ Str::limit($homeData3->home_description, 150, '....') }}
                @endif
            </div>
            @if (strlen($homeData3->home_description) != strlen(utf8_decode($homeData3->home_description)))
                @if (strlen($homeData3->home_description) > 250)
                    <p style="text-align:right;" class="btn float-right">
                        <a href="{{ $abs_site_url . $subdomain . '/home/' . $homeData3->home_url }}">
                            {{ \Helper::getNameOftext('read-more', $languageId) }}
                        </a>
                    </p>
                @endif
            @else
                @if (strlen($homeData3->home_description) > 150)
                    <p style="text-align:right;" class="btn float-right">
                        <a href="{{ $abs_site_url . $subdomain . '/home/' . $homeData3->home_url }}">
                            {{ \Helper::getNameOftext('read-more', $languageId) }}
                        </a>
                    </p>
                @endif
            @endif
        </div>
    @endif

    @if (isset($subdomain) && $subdomain != 'home')
        @if ($homegeneralinfo != '')
            <div class="thumbnail pmmes">
                <h3 class="no-margin-top hentry__title">{{ $homegeneralinfo->home_heading }}</h3>
                @if (strlen($homegeneralinfo->home_description) != strlen(utf8_decode($homegeneralinfo->home_description)))
                    {{ nl2br(Str::limit($homegeneralinfo->home_description, 250, '....')) }}
                @else
                    {{ nl2br(Str::limit($homegeneralinfo->home_description, 150, '....')) }}
                @endif

                @if (strlen($homegeneralinfo->home_description) != strlen(utf8_decode($homegeneralinfo->home_description)))
                    @if (strlen($homegeneralinfo->home_description) > 250)
                        <p style="text-align:right;" class="btn float-right">
                            <a href="{{ $abs_site_url . $subdomain . '/home/' . $homegeneralinfo->home_url }}">
                                {{ \Helper::getNameOftext('read-more', $languageId) }}
                            </a>
                        </p>
                    @endif
                @else
                    @if (strlen($homegeneralinfo->home_description) > 150)

                        <p style="text-align:right;" class="btn float-right">
                            <a href="{{ $abs_site_url . $subdomain . '/home/' . $homegeneralinfo->home_url }}">
                                {{ \Helper::getNameOftext('read-more', $languageId) }}
                            </a>
                        </p>
                    @endif
                @endif
            </div>
        @else
            <h3 class="no-margin-top">{{ \Helper::getNameOftext('general-info', $languageId) }}</h3>
            <div class="thumbnail pmmes">
                <p>{{ \Helper::getNameOftext('record-not-found', $languageId) }}</p>
            </div>
        @endif

        @if ($homespokeperson != '')
            <div class="thumbnail pmmes">
                <h3 class="no-margin-top hentry__title">{{ $homespokeperson->home_heading }}</h3>

                @if (strlen($homespokeperson->home_description) != strlen(utf8_decode($homespokeperson->home_description)))
                    {{ nl2br(Str::limit($homespokeperson->home_description, 250, '....')) }}
                @else
                    {{ nl2br(Str::limit($homespokeperson->home_description, 150, '....')) }}
                @endif


                @if (strlen($homespokeperson->home_description) != strlen(utf8_decode($homespokeperson->home_description)))
                    @if (strlen($homespokeperson->home_description) > 250)
                        <p style="text-align:right;" class="btn float-right">
                            <a href="{{ $abs_site_url . $subdomain . '/home/' . $homespokeperson->home_url }}">
                                {{ \Helper::getNameOftext('read-more', $languageId) }}
                            </a>
                        </p>
                    @endif
                @else
                    @if (strlen($homespokeperson->home_description) > 150)
                        <p style="text-align:right;" class="btn float-right">
                            <a href="{{ $abs_site_url . $subdomain . '/home/' . $homespokeperson->home_url }}">
                                {{ \Helper::getNameOftext('read-more', $languageId) }}
                            </a>
                        </p>
                    @endif
                @endif
            </div>
        @else
            <h3 class="no-margin-top">{{ \Helper::getNameOftext('spokes-person', $languageId) }}</h3>
            <div class="thumbnail pmmes">
                <p>{{ \Helper::getNameOftext('record-not-found', $languageId) }}</p>
            </div>
        @endif
    @endif

    <!--calculator-->


    <div class="col-md-12 thumbnail pmmes">
        <h3 class="hentry__title">{{ \Helper::getNameOftext('select-dor-office', $languageId) }}</h3>
        <div class="content-box box-default">
            <div id="container" style="min-width: 200px;margin: 0 auto">

            </div>
        </div>
    </div>



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


            // get the form data
            // there are many ways to get this data using jQuery (you can use the class or id also)
            var formData = {
                'start': $('select[name=start]').val(),
                'through': $('select[name=through]').val(),
                'end': $('select[name=end]').val()
            };
            // process the form
            $.ajax({
                    type: 'POST', // define the type of HTTP verb we want to use (POST for our form)
                    url: '{{ $abs_site_url }}home/distancecalc', // the url where we want to POST
                    data: formData, // our data object
                    dataType: 'json', // what type of data do we expect back from the server
                    encode: true
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
