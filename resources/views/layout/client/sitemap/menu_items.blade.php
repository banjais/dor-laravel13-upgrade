@php
use App\Models\Content;
use App\Models\Office;
use App\Models\Home;
$headerContents = Content::getContentsPostionWise($languageId, $subdomain, 1, 0, 6);
$regionalHeading = $languageId == 1 ? 'क्षेत्रिय निर्देशानलय' : 'Regional Offices';

$projects_content = [];

$divisionOffices = [];

$officeId = Office::getIdfromSubdomain($subdomain);
$officeData = Office::getOfficeDetailFromSubdomain($subdomain, $languageId);

$facebookUrl = trim((string) ($officeData->office_facebook_page ?? ''));
$instagramUrl = trim((string) ($officeData->office_instagram_page ?? ''));

foreach (['facebookUrl', 'instagramUrl'] as $socialField) {
    $socialUrl = $$socialField;
    $parts = $socialUrl ? parse_url($socialUrl) : false;
    if (!$parts || !filter_var($socialUrl, FILTER_VALIDATE_URL) ||
        !in_array(strtolower($parts['scheme'] ?? ''), ['http', 'https'], true)) {
        $$socialField = '';
    }
}
$missingSocialText = $languageId == 1 ? 'पेज राखिएको छैन' : 'Page not added yet';

$HeavydivisionOffices = Office::getOfficesByType($languageId, 4);
$MechanicalOffices = Office::getOfficesByType($languageId, 5);

$MainProjectOffices_1 = Office::getOfficesByType($languageId, 6);
$MainProjectOffices_2 = Office::getOfficesByType($languageId, 7);

$footer_col_1 = null;
$footer_col_2 = Home::getHomeDetail(4, 'footer-section-2', $languageId);
$footer_col_3 = Home::getHomeDetail(4, 'footer-section-3', $languageId);

$abs_site_url = '/';
$subdomain == 'home';
@endphp
<h3 class="sitemap-section-title">{{ $languageId == 1 ? "हेडर मेनु" : "Header Menu" }}</h3>
<ol class="sitemap-numbered-list">
<li class="nav-item">
    <a class="nav-link active" href="{{ '/' . $subdomain }}">{{ \Helper::getNameOftext('homepage', $languageId) }}</a>
</li>
@if ($subdomain == 'home')
    @foreach ($headerContents as $content)
        @if ($content->content_url != 'contact')
            @php
                $subMenus = Content::getContentsPostionWise($languageId, $subdomain, 1, $content->id, 50);
            @endphp
            @if (count($subMenus) > 0)
                @if ($content->content_url == 'others')
                    <li>
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="true">
                            {{ $regionalHeading }}
                        </a>
                        <ul aria-labelledby="navbarDropdown">
                            <li class="has-sub"><a href="#">सम्भार महाशाखा</a>
                                <ul>
                                    @php
                                        $regionalOffices = Office::getActiveRegionalOffices($languageId, $subdomain);
                                    @endphp
                                    @foreach ($regionalOffices as $office)
                                        <li><a
                                                href="{{ $abs_site_url . $office->office_subdomain }}">{{ $office->office_name }}</a>
                                        </li>
                                    @endforeach

                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li class="">
                        <a href=" {{ $abs_site_url . $subdomain . '/DRO' }}">
                            {{ \Helper::getNameOftext('division-road-offices', $languageId) }}</a>
                    </li>
                @endif

                @if ($content->content_url == 'federal-road-supervision-monitoring-office')
                    @include(
                        'layout.client.sitemap.menu_supervision_monitoring'
                    )
                @elseif ($content->content_url == 'projects')
                    @php
                        $projects_content = $content;
                    @endphp
                @else
                    <li>
                        <a class="nav-link dropdown-toggle"
                            href="{{ $abs_site_url . $subdomain . '/page/' . $content->content_url }}"
                            id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ $content->content_heading }}
                        </a>
                        <ul aria-labelledby="navbarDropdown">
                            @foreach ($subMenus as $subContent)
                                @php
                                    $childMenus = Content::getContentsPostionWise($languageId, $subdomain, 1, $subContent->id, 50);
                                @endphp
                                @if (count($childMenus) > 0)
                                    <li class="has-sub"><a
                                            href="{{ $abs_site_url . $subdomain . '/page/' . $subContent->content_url }}">
                                            {{ $subContent->content_heading }}</a>
                                        <ul>
                                            @foreach ($childMenus as $child)
                                                @php
                                                    $subchildMenus = Content::getContentsPostionWise($languageId, $subdomain, 1, $child->id, 50);
                                                @endphp
                                                @if (count($subchildMenus) > 0)
                                                    <li class="has-sub"><a
                                                            href="{{ $abs_site_url . $subdomain . '/page/' . $child->content_url }}">
                                                            {{ $child->content_heading }}</a>
                                                        <ul>
                                                            @foreach ($subchildMenus as $subchild)
                                                                @php
                                                                    $subsubchildMenus = Content::getContentsPostionWise($languageId, $subdomain, 1, $subchild->id, 50);
                                                                @endphp
                                                                @if (count($subsubchildMenus) > 0)
                                                                    <li class="has-sub"><a
                                                                            href="{{ $abs_site_url . $subdomain . '/page/' . $subchild->content_url }}">
                                                                            {{ $subchild->content_heading }}</a>
                                                                        <ul>
                                                                            @foreach ($subsubchildMenus as $subsubchild)
                                                                                <li><a
                                                                                        href="{{ $abs_site_url . $subdomain . '/page/' . $subsubchild->content_url }}">
                                                                                        {{ $subsubchild->content_heading }}</a>
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    @else
                                                                    <li><a
                                                                            href="{{ $abs_site_url . $subdomain . '/page/' . $subchild->content_url }}">
                                                                            {{ $subchild->content_heading }}</a>
                                                                    </li>
                                                                @endif
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                    <li><a
                                                            href="{{ $abs_site_url . $subdomain . '/page/' . $child->content_url }}">
                                                            {{ $child->content_heading }}</a></li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    @else
                                    <li><a
                                            href="{{ $abs_site_url . $subdomain . '/page/' . $subContent->content_url }}">
                                            {{ $subContent->content_heading }}</a></li>
                                @endif
                            @endforeach

                        </ul>
                    </li>
                @endif
            @else
                <li class=""><a
                        href="{{ $abs_site_url . $subdomain . '/page/' . $content->content_url }}">
                        {{ $content->content_heading }}</a></li>
            @endif
        @endif
    @endforeach
@endif

@if ($subdomain == 'home')
    @include('layout.client.sitemap.menu_heavy_division_mechanical_division')
    @if (is_object($projects_content))
        @include('layout.client.sitemap.menu_road_projects', [
            'content' => $projects_content,
            'subMenus' => Content::getContentsPostionWise($languageId, $subdomain, 1, $projects_content->id, 50),
        ])
    @endif
@else
    <li><a href="{{ '/' . $subdomain . '/home/about-us' }}">{{ \Helper::getNameOftext('about-us', $languageId) }}</a></li>
    <li><a href="{{ '/' . $subdomain . '/home/working-area' }}">{{ \Helper::getNameOftext('working-area', $languageId) }}</a></li>
    <li><a href="{{ '/' . $subdomain . '/staffs' }}">{{ \Helper::getNameOftext('staffs', $languageId) }}</a></li>
    <li><a href="{{ '/' . $subdomain . '/projects' }}">{{ \Helper::getNameOftext('projects', $languageId) }}</a></li>
    <li><a href="{{ '/' . $subdomain . '/gallery' }}">{{ \Helper::getNameOftext('gallery', $languageId) }}</a></li>
@endif


<li class="sitemap-admin-item">
    <a>{{ $languageId == 1 ? 'एडमिन' : 'Admin' }}</a>
    <ul>
        <li><a href="/admin/login">Web/Circular</a></li>
        <li><a href="https://mail.nepal.gov.np/" target="_blank" rel="noopener noreferrer">{{ $languageId == 1 ? 'मेल चेक' : 'Mail' }}</a></li>
    </ul>
</li>
</ol>


<h3 class="sitemap-section-title">{{ $languageId == 1 ? 'फुटर लिङ्कहरू' : 'Footer Links' }}</h3>
<ol class="sitemap-numbered-list">
    <li><a class="sitemap-app-link" href="#dorAppPopover">{{ $languageId == 1 ? 'डोर एप' : 'DoR App' }}</a></li>
    <li><a href="{{ '/' . $subdomain . '/home/screen-reader' }}">{{ $languageId == 1 ? 'स्क्रीन रिडर' : 'Screen Reader' }}</a></li>
    <li><a href="{{ '/' . $subdomain . '/home/privacy-policy' }}">{{ $languageId == 1 ? 'गोपनीयताको नीति' : 'Privacy Policy' }}</a></li>
    <li><a href="{{ '/' . $subdomain . '/home/terms-and-conditions' }}">{{ $languageId == 1 ? 'प्रयोगका शर्तहरु' : 'Terms & conditions' }}</a></li>
    <li><a href="{{ '/' . $subdomain . '/gallery' }}">{{ \Helper::getNameOftext('gallery', $languageId) }}</a></li>
    <li class="sitemap-footer-group">
        <a>{{ $languageId == 1 ? 'आन्तरिक लिंकहरु' : 'Internal Links' }}</a>
        <ul>
            <li><a href="{{ '/' . $subdomain . '/contact' }}">{{ $languageId == 1 ? 'सम्पर्क' : 'Contact Us' }}</a></li>
            <li><a href="/home/page/dor-office-contacts">{{ $languageId == 1 ? 'कार्यालय सम्पर्क' : 'DoR Contacts' }}</a></li>
        </ul>
        <div class="footer-social-links sitemap-social-links">
            @if ($facebookUrl)
                <a href="{{ $facebookUrl }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="fab fa-facebook" aria-hidden="true"></i></a>
            @else
                <span class="footer-social-missing"><i class="fab fa-facebook" aria-hidden="true"></i><small>{{ $missingSocialText }}</small></span>
            @endif
            @if ($instagramUrl)
                <a href="{{ $instagramUrl }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="fab fa-instagram" aria-hidden="true"></i></a>
            @else
                <span class="footer-social-missing"><i class="fab fa-instagram" aria-hidden="true"></i><small>{{ $missingSocialText }}</small></span>
            @endif
        </div>
    </li>
    <li class="sitemap-footer-group">
        <a>{{ $footer_col_2->home_heading ?? ($languageId == 1 ? 'बाहिरी लिंकहरु' : 'External Links') }}</a>
        @if ($footer_col_2 && $footer_col_2->home_description && $footer_col_2->home_description != '--')
            {!! \Helper::normalizeFooterLinks($footer_col_2->home_description) !!}
        @else
            @include('layout.client.sitemap.default_footer_col_2')
        @endif
    </li>
    <li class="sitemap-footer-group">
        <a>{{ $footer_col_3->home_heading ?? ($languageId == 1 ? 'महत्वपूर्ण लिंकहरु' : 'Important Links') }}</a>
        @php
            $importantDescription = $footer_col_3->home_description ?? '';
            $importantDescription = preg_replace_callback(
                '~<li\b[^>]*>.*?</li>~is',
                function ($match) {
                    return (stripos($match[0], 'facebook.com') !== false ||
                            stripos($match[0], 'instagram.com') !== false ||
                            stripos($match[0], 'socialMedia/') !== false) ? '' : $match[0];
                },
                $importantDescription
            );
        @endphp
        @if ($importantDescription && $importantDescription != '--')
            {!! \Helper::normalizeFooterLinks($importantDescription) !!}
        @else
            @include('layout.client.sitemap.default_footer_col_3')
        @endif
    </li>
</ol>

