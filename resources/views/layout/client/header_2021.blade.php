@php
use App\Models\Office;
use App\Models\Content;
// print_r($subdomain);exit;
$languageId = Session::get('language_id');
$divisionOffices = [];

$officeData = Office::getOfficeDetailFromSubdomain($subdomain, $languageId);
$HeavydivisionOffices = Office::getOfficesByType($languageId, 4);
$MechanicalOffices = Office::getOfficesByType($languageId, 5);

$MainProjectOffices_1 = Office::getOfficesByType($languageId, 6);
$MainProjectOffices_2 = Office::getOfficesByType($languageId, 7);

$todaysDateInGregorian = \Carbon\Carbon::now('Asia/Kathmandu')->format('Y-m-d');
$nepalidate = Calendar::eng_to_nep_dor($todaysDateInGregorian);

$headerCalendarType = request()->route('type') ?? request()->segment(3);
if (!in_array($headerCalendarType, ['bs', 'ad', 'ns'], true)) {
    $headerCalendarType = 'ns';
}

$headerBsDate = $nepalidate;
$headerAdDate = \Carbon\Carbon::now('Asia/Kathmandu')->format('F j, Y');
$headerNsToday = \App\Services\NepalSambatService::today($languageId);
$headerNsDate = $headerNsToday
    ? ($languageId == 1 ? $headerNsToday['ne'] : $headerNsToday['en'])
    : ($languageId == 1 ? 'उपलब्ध छैन' : 'Unavailable');

$headerCalendarDate = $headerCalendarType === 'bs'
    ? $headerBsDate
    : ($headerCalendarType === 'ad' ? $headerAdDate : $headerNsDate);

$abs_site_url = '/';

$bandwidth = Session::get('bandwidth_code', 'high');
// dd($bandwidth);
if ($bandwidth == 'high') {
    // dd('we are high');
    $bandwidth_text = $languageId == 1 ? 'न्यून व्यान्डविथ' : 'Low Bandwidth';
} else {
    $bandwidth_text = $languageId == 1 ? 'उच्च व्यान्डविथ' : 'High Bandwidth';
}

$font_size_code = Session::get('font_size_code');

if (isset($is_home)) {
    $is_home = 1;
} else {
    $is_home = 0;
}
// dd($is_home);
@endphp
<!--header-->
<header class="main-header-wrap">
    <div class="top-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-xl-4 col-lg-4">
                    <div class="logo-wrap d-flex">
                        <div class="logo-img">
                            <a href="{{ route('frontend.homepage', $subdomain) }}">
                                <img class="no-bandwidth dor-government-emblem"
                                     src="/assets/pwa/government-emblem.png"
                                     alt="{{ $languageId == 1 ? 'नेपालको राष्ट्रिय प्रतीक' : 'National Emblem of Nepal' }}"
                                     width="512" height="429"
                                     loading="eager" decoding="async" />
                            </a>
                        </div>
                        <div class="logo-title">
                            <a href="{{ route('frontend.homepage', $subdomain) }}">
                                @if ($languageId == 1)
                                    <strong class="government-name">{{ $websiteIdentity['government_name_np'] ?? 'नेपाल सरकार' }}</strong>
                                    <small class="ministry-name">{{ $websiteIdentity['ministry_name_np'] ?? 'पूर्वाधार विकास मन्त्रालय' }}</small>
                                    <span class="department-name">{{ $websiteIdentity['department_name_np'] ?? 'सडक विभाग' }}</span>
                                @else
                                    <strong class="government-name">{{ $websiteIdentity['government_name_en'] ?? 'Government of Nepal' }}</strong>
                                    <small class="ministry-name">{{ $websiteIdentity['ministry_name_en'] ?? 'Ministry of Infrastructure Development' }}</small>
                                    <span class="department-name">{{ $websiteIdentity['department_name_en'] ?? 'Department of Roads' }}</span>
                                @endif
                                @if ($subdomain != 'home')
                                    <small>{{ $officeData ? $officeData->office_name : '' }}</small>
                                @endif
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-xl-8 col-lg-8">
                    <div class="row">
                        <div class="col-md-12 col-lg-11">
                            <div class="top-inline-items text-lg-end header-controls">
                                <ul>
                                    <li>
                                        <a href="javascript:void(0);" id="linkBandWidthSwitch" class="header-icon-control"
                                            aria-label="{{ $languageId == 1 ? 'कम ब्यान्डविथ' : 'Low bandwidth' }}"
                                            title="{{ $languageId == 1 ? 'कम ब्यान्डविथ' : 'Low bandwidth' }}">
                                            <i class="fa fa-tachometer" aria-hidden="true"></i>
                                        </a>
                                    </li>
                                    <li class="header-language-switch">
                                        <select id="headerLanguageSelect" class="form-select form-select-sm"
                                            aria-label="{{ $languageId == 1 ? 'भाषा छान्नुहोस्' : 'Select language' }}">
                                            <option value="1" {{ $languageId == 1 ? 'selected' : '' }}>NEP</option>
                                            <option value="2" {{ $languageId == 2 ? 'selected' : '' }}>ENG</option>
                                        </select>
                                    </li>
                                    <li>
                                        <button type="button" id="fontSizeToggle" class="header-icon-control"
                                            aria-label="{{ $languageId == 1 ? 'अक्षरको आकार' : 'Font size' }}"
                                            title="{{ $languageId == 1 ? 'अक्षरको आकार' : 'Font size' }}"
                                            aria-pressed="false">
                                            <i class="fa fa-font" aria-hidden="true"></i>
                                        </button>
                                    </li>
                                    <li class="header-date-time">
                                        <details class="header-calendar-dropdown">
    <summary class="header-calendar-toggle"
        aria-label="{{ $languageId == 1 ? 'मिति प्रणाली छान्नुहोस्' : 'Choose calendar system' }}">
        <span id="headerDateDisplay">{{ $headerCalendarDate }}</span>
        <i class="fa fa-chevron-down" aria-hidden="true"></i>
    </summary>
    <div class="header-calendar-menu">
        <div class="header-calendar-option">
            <button type="button" class="header-calendar-date {{ $headerCalendarType === 'bs' ? 'is-current' : '' }}"
                data-calendar-date="{{ e($headerBsDate) }}"
                aria-pressed="{{ $headerCalendarType === 'bs' ? 'true' : 'false' }}">BS</button>
        </div>
        <div class="header-calendar-option">
            <button type="button" class="header-calendar-date {{ $headerCalendarType === 'ad' ? 'is-current' : '' }}"
                data-calendar-date="{{ e($headerAdDate) }}"
                aria-pressed="{{ $headerCalendarType === 'ad' ? 'true' : 'false' }}">AD</button>
        </div>
        <div class="header-calendar-option">
            <button type="button" class="header-calendar-date {{ $headerCalendarType === 'ns' ? 'is-current' : '' }}"
                data-calendar-date="{{ e($headerNsDate) }}"
                aria-pressed="{{ $headerCalendarType === 'ns' ? 'true' : 'false' }}">NS</button>
        </div>
        <div class="header-calendar-source-row">
            <a class="header-calendar-source"
                href="{{ url('/' . $subdomain . '/calendar/ns') }}"
                aria-label="DoR Nepal Sambat Calendar"
                title="DoR Nepal Sambat Calendar">NS Calendar</a>
        </div>
    </div>
</details>
<script>
document.addEventListener('click', function (event) {
    document.querySelectorAll('.header-calendar-dropdown[open]').forEach(function (dropdown) {
        if (!dropdown.contains(event.target)) {
            dropdown.open = false;
        }
    });

    const button = event.target.closest('.header-calendar-date');
    if (!button) return;

    const dropdown = button.closest('.header-calendar-dropdown');
    if (!dropdown) return;

    dropdown.querySelector('#headerDateDisplay').textContent = button.dataset.calendarDate;

    dropdown.querySelectorAll('.header-calendar-date').forEach(function (item) {
        const selected = item === button;
        item.classList.toggle('is-current', selected);
        item.setAttribute('aria-pressed', selected ? 'true' : 'false');
    });

    dropdown.open = false;
});

document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
        document.querySelectorAll('.header-calendar-dropdown[open]').forEach(function (dropdown) {
            dropdown.open = false;
        });
    }
});
</script>
                                        <span class="header-clock">
                                            <span id="hours"></span>:<span id="min"></span>:<span id="sec"></span>
                                        </span>
                                    </li>
                                </ul>
                            </div>
                            <div class="top-inline-items header-utility-row">
                                <ul>
                                    <li class="header-admin-dropdown dropdown">
                                        <a href="#" id="headerAdminDropdown"
                                           class="dropdown-toggle"
                                           role="button"
                                           data-bs-toggle="dropdown"
                                           aria-expanded="false">
                                            {{ $languageId == 1 ? 'एडमिन' : 'Admin' }}
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end"
                                            aria-labelledby="headerAdminDropdown">
                                            <li>
                                                <a class="dropdown-item" href="/admin/login"><i class="fa fa-globe" aria-hidden="true"></i><span>Web/Circular</span></a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item"
                                                   href="https://mail.nepal.gov.np/"
                                                   target="_blank"
                                                   rel="noopener noreferrer">
                                                    <i class="fa fa-envelope" aria-hidden="true"></i>
                                                    <span>{{ $languageId == 1 ? 'मेल चेक' : 'Mail' }}</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="header-sitemap-link">
                                        <a href="{{ url('/' . $subdomain . '/sitemap') }}">
                                            {{ $languageId == 1 ? 'साइटम्याप' : 'Sitemap' }}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ '/' . $subdomain . '/contact' }}">
                                            {{ $languageId == 1 ? 'सम्पर्क' : 'Contact' }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-1 d-none d-lg-block">
                            <div class="flag-wrap"><img class="no-bandwidth"
                                    src="{{ asset('assets/img/nepal-flag.gif') }}" alt="" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="nav-wrap">
        <div class="container">
            <nav class="navbar navbar-expand-lg">
                <div class="container-fluid">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"><i class="fa fa-bars"></i></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            @include('layout.client.menu_items')
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </div>



</header>
