@php
use Illuminate\Support\Str;
use App\Models\Office;
use App\Models\Distance;
use App\Models\Home;

$languageId = Session::get('language_id');
$officeId = Office::getIdfromSubdomain($subdomain);

$distance = new Distance();
// $test = $distance->RouteDijkstra();

//live data
$homeData3 = Home::getHomeDataFrontend('general-information', $officeId, $languageId);
$homeData3_2 = Home::getHomeDataFrontend('general-information-2', $officeId, $languageId);
$homeData3_3 = Home::getHomeDataFrontend('general-information-3', $officeId, $languageId);

$homeData2 = Home::getHomeDataFrontend('message-from-director-general', $officeId, $languageId);
$homegeneralinfo = Home::getHomeDataFrontend('general-info', $officeId, $languageId);
$homespokeperson = Home::getHomeDataFrontend('dor-spoke-person', $officeId, $languageId);
// dd($homespokeperson);

$homeInformationOfficer = Home::getHomeDataFrontend('information-officer', $officeId, $languageId);
$homeGrievanceOfficer = Home::getHomeDataFrontend('grievance-hearing-officer', $officeId, $languageId);
$homeAdditionalOfficer = Home::getHomeDataFrontend('additional-officer', $officeId, $languageId);

// Other Office का अधिकतम चार Officer एउटै फ्रेममा
$otherOfficeOfficers = collect([
    $homespokeperson,
    $homegeneralinfo ?: $homeInformationOfficer,
    $homeGrievanceOfficer,
    $homeAdditionalOfficer,
])->filter()->take(4)->values();

$homeDirectorGeneral = Home::getHomePageDirectorGeneral($officeId, $officeId, $languageId);

// $facebookFeed = Home::getHomeDataFrontend('facebook-feed', $officeId, $languageId);


// dd($homegeneralinfo);

@endphp
@if ($homeData3 && $subdomain != 'home')
    <div class="shadowed-box p-3">
        <div class="block-title">
            <h2>{{ isset($homeData3) ? $homeData3->home_heading : '' }}</h2>
            <div class="divider"></div>
        </div>
        <div class="block-list">
            @if (isset($homeData3) && $homeData3->home_attachment)
                <a href="{{ $abs_site_url . $subdomain }}"><img
                        src="{{ $abs_site_url . 'uploads/pages/' . $homeData3->home_attachment }}" /></a>
            @endif
        </div>
        <div>
            @if (strlen($homeData3->home_description) != strlen(utf8_decode($homeData3->home_description)))
                {!! Str::limit($homeData3->home_description, 600, '....') !!}
            @else
                {!! Str::limit($homeData3->home_description, 150, '....') !!}
            @endif
        </div>


        @if (strlen($homeData3->home_description) != strlen(utf8_decode($homeData3->home_description)))
            @if (strlen($homeData3->home_description) > 250)
                <div class="text-end mt-1">
                    <a class="more-btn"
                        href="{{ $abs_site_url . $subdomain . '/home/' . $homeData3->home_url }}">
                        {{ \Helper::getNameOftext('read-more', $languageId) }}
                    </a>
                </div>
            @endif
        @else
            @if (strlen($homeData3->home_description) > 150)
                <div class="text-end mt-1">
                    <a class="more-btn"
                        href="{{ $abs_site_url . $subdomain . '/home/' . $homeData3->home_url }}">
                        {{ \Helper::getNameOftext('read-more', $languageId) }}
                    </a>
                </div>
            @endif
        @endif
    </div>
@elseif($homeData3)
    <div class="shadowed-box p-3">
        @include('home.live', ['homeData3' => $homeData3])
    </div>
@endif



@if (isset($subdomain) && $subdomain == 'home')
    <div class="mt-2">@include('home.contact_officers')</div>
@endif

{{-- @if ($facebookFeed && $facebookFeed->data_1)
    <div class="shadowed-box p-3 mt-2">
        <div>
            {!! $facebookFeed->data_1 !!}
        </div>
    </div>
@endif
 --}}

<style>
    .fb-page,
    .fb-page span,
    .fb-page span iframe[style] {
        width: 100% !important;
    }
</style>
