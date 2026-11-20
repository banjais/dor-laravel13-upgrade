@php
use App\Models\Content;
use App\Models\Office;
$headerContents = Content::getContentsPostionWise($languageId, $subdomain, 1, 0, 6);
$regionalHeading = $languageId == 1 ? 'क्षेत्रिय निर्देशानलय' : 'Regional Offices';

$projects_content=[];

$abs_site_url = '/';
@endphp
<li class="nav-item">
    <a class="nav-link active" aria-current="page"
        href="{{ '/' . $subdomain }}">{{ \Helper::getNameOftext('homepage', $languageId) }}</a>
</li>
@if ($subdomain == 'home')
    @foreach ($headerContents as $content)
        @if ($content->content_url != 'contact')
            @php
                $subMenus = Content::getContentsPostionWise($languageId, $subdomain, 1, $content->id, 50);
            @endphp
            @if (count($subMenus) > 0)
                @if ($content->content_url == 'others')
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            {{ $regionalHeading }}
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li class="has-sub"><a class="dropdown-item" href="#">सम्भार महाशाखा</a>
                                <ul class="submenu dropdown-menu">
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
                    @include('layout.client.menu_supervision_monitoring')
                @elseif ($content->content_url == 'projects')
                    @php
                        $projects_content = $content;
                    @endphp
                @else
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle"
                            href="{{ $abs_site_url . $subdomain . '/page/' . $content->content_url }}"
                            id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ $content->content_heading }}
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            @foreach ($subMenus as $subContent)
                                @php
                                    $childMenus = Content::getContentsPostionWise($languageId, $subdomain, 1, $subContent->id, 50);
                                @endphp
                                @if (count($childMenus) > 0)
                                    <li class="has-sub"><a class="dropdown-item"
                                            href="{{ $abs_site_url . $subdomain . '/page/' . $subContent->content_url }}">
                                            {{ $subContent->content_heading }}</a>
                                        <ul class="submenu dropdown-menu">
                                            @foreach ($childMenus as $child)
                                                @php
                                                    $subchildMenus = Content::getContentsPostionWise($languageId, $subdomain, 1, $child->id, 50);
                                                @endphp
                                                @if (count($subchildMenus) > 0)
                                                    <li class="has-sub"><a class="dropdown-item"
                                                            href="{{ $abs_site_url . $subdomain . '/page/' . $child->content_url }}">
                                                            {{ $child->content_heading }}</a>
                                                        <ul class="submenu dropdown-menu">
                                                            @foreach ($subchildMenus as $subchild)
                                                                @php
                                                                    $subsubchildMenus = Content::getContentsPostionWise($languageId, $subdomain, 1, $subchild->id, 50);
                                                                @endphp
                                                                @if (count($subsubchildMenus) > 0)
                                                                    <li class="has-sub"><a class="dropdown-item"
                                                                            href="{{ $abs_site_url . $subdomain . '/page/' . $subchild->content_url }}">
                                                                            {{ $subchild->content_heading }}</a>
                                                                        <ul class="submenu dropdown-menu">
                                                                            @foreach ($subsubchildMenus as $subsubchild)
                                                                                <li><a class="dropdown-item"
                                                                                        href="{{ $abs_site_url . $subdomain . '/page/' . $subsubchild->content_url }}">
                                                                                        {{ $subsubchild->content_heading }}</a>
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    @else
                                                                    <li><a class="dropdown-item"
                                                                            href="{{ $abs_site_url . $subdomain . '/page/' . $subchild->content_url }}">
                                                                            {{ $subchild->content_heading }}</a>
                                                                    </li>
                                                                @endif
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                    <li><a class="dropdown-item"
                                                            href="{{ $abs_site_url . $subdomain . '/page/' . $child->content_url }}">
                                                            {{ $child->content_heading }}</a></li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    @else
                                    <li><a class="dropdown-item"
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
    @include('layout.client.menu_heavy_division_mechanical_division')
    @include('layout.client.menu_road_projects', $projects_content)
@else
    <li class="nav-item"><a class="nav-link"
            href="{{ $abs_site_url . $subdomain . '/home/about-us' }}">{{ \Helper::getNameOftext('about-us', $languageId) }}</a>
    </li>
    <li class="nav-item"><a class="nav-link"
            href="{{ $abs_site_url . $subdomain . '/home/working-area' }}">{{ \Helper::getNameOftext('working-area', $languageId) }}</a>
    </li>
    <li class="nav-item"><a class="nav-link"
            href="{{ $abs_site_url . $subdomain . '/staffs' }}">{{ \Helper::getNameOftext('staffs', $languageId) }}</a>
    </li>
    <li class="nav-item"><a class="nav-link"
            href="{{ $abs_site_url . $subdomain . '/projects' }}">{{ \Helper::getNameOftext('projects', $languageId) }}</a>
    </li>
    {{-- <li class="nav-item"><a class="nav-link"
            href="{{ $abs_site_url . $subdomain . '/vehicles' }}">{{ \Helper::getNameOftext('vehicles', $languageId) }}</a>
    </li> --}}
    <li class="nav-item"><a class="nav-link"
            href="{{ $abs_site_url . $subdomain . '/gallery' }}">{{ \Helper::getNameOftext('gallery', $languageId) }}</a>
    </li>
@endif
</ul>
@if ($subdomain != 'home')
    <a href="/home" class="back-btn"><span> <i class="fa fa-arrow-left"></i>
            {{ \Helper::getNameOftext('switch-to-head-office', $languageId) }}</span></a>
@endif
