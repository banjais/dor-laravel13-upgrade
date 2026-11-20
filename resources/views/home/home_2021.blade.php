@extends('layout.client.container_2021')
@section('more_css')
    <style>
        .homepage-content-section {
            padding-top: 12px;
        }
        .homepage-content-section > .container > .row {
            align-items: stretch;
        }
        .homepage-tabs,
        .homepage-flash-board {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
        }
        .homepage-tabs {
            min-height: 340px;
        }
        .homepage-flash-board {
            height: auto;
            min-height: 0;
            align-self: flex-start;
        }
        .homepage-tabs {
            padding: 0;
        }
        .homepage-flash-board {
            padding: 16px;
        }
        .homepage-flash-board .block-title {
            margin-bottom: 16px;
        }
        .homepage-flash-board .fixed-vdo {
            height: 168px;
        }
        .homepage-flash-board .fixed-vdo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        @media (max-width: 767.98px) {
            .homepage-content-section {
                padding-top: 8px;
            }
            .homepage-tabs,
            .homepage-flash-board {
                min-height: 0;
            }
            .homepage-flash-board {
                margin-top: 16px !important;
            }
        }
        .no-js #loader {
            display: none;
        }

        .js #loader {
            display: block;
            position: absolute;
            left: 100px;
            top: 0;
        }

        .se-pre-con {
            position: fixed;
            left: 0px;
            top: 0px;
            width: 100%;
            height: 100%;
            z-index: 9999;
            background: url('/assets/img_2021/loading.gif') center no-repeat #fff;
        }
    </style>
@endsection
@section('content')
    @php
    use Illuminate\Support\Facades\Auth;
    use App\Models\Office;
    use Illuminate\Support\Str;
    use App\Models\Home;
    use App\Models\FlashBoxSetting;
    use App\Models\Publication;
    use App\Models\Category;
    use App\Models\Download;

    $languageId = Session::get('language_id');
    $officeId = Office::getIdfromSubdomain($subdomain);

    // Dynamic Flash Box items: general-information, -2, -3, -4 ...
    $flashBoxItems = Home::join('home_detail', 'home.id', '=', 'home_detail.home_id')
        ->select('home.*', 'home_detail.home_heading')
        ->where('home.office_id', $officeId)
        ->where('home.home_url', 'REGEXP', '^general-information(-[0-9]+)?$')
        ->where('home_detail.language_id', $languageId)
        ->where('home.is_active', '1')
        ->where('home.is_deleted', '0')
        ->orderByRaw("CASE WHEN home_url = 'general-information' THEN 0 ELSE 1 END")
        ->orderByRaw("CAST(SUBSTRING_INDEX(home_url, '-', -1) AS UNSIGNED)")
        ->get();

    $flashBoxSettings = FlashBoxSetting::current();

    $bandwidth_code = Session::get('bandwidth_code', 'high');

    // dd($publications);

    @endphp

    <div class="se-pre-con"></div>
    <main class="main-body">
        <section class="news-ticker-block">
            <div class="container">
                <div class="news-ticker">
                    <div class="news-ticker-box">
                        <div class="divider-list mt-0 news-ticker-list">
                            <ul>
                                @foreach ($homepagenewstickers as $homepagenewsticker)
                                    <li><a
                                            href="{{ url('home/latest-news', [$homepagenewsticker->ticker_id]) }}">{{ $homepagenewsticker->ticker_title }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="banner-block">
            <div class="container">
                <div class="row align-items-stretch home-banner-officers-row">
                    <div class="col-xl-9 col-md-8">
                        <div class="slider-wrap mb-3 mb-lg-0" id="single-slider">
                            @foreach ($banners as $banner)
                                <div>
                                    <a class="banner-detail-link" href="{{ url($subdomain . '/banner/' . $banner->id) }}" aria-label="{{ $banner->banner_title }}">
                                    @if ($bandwidth_code == 'high')
                                        <div class="slider-fig slider-img-holder"
                                            data-image="{{ asset('uploads/banner/' . $banner->banner_attachment) }}">
                                        </div>
                                    @else
                                        <div class="slider-fig p-3">
                                            {{ $banner->banner_title }}
                                        </div>
                                    @endif
                                    <div class="slider-caption">
                                        <div class="container">
                                            <p>{{ $banner->banner_caption }}
                                            </p>
                                        </div>
                                    </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4 home-officers-column">
                        <div class="home-officers-panel">
                            @include('home.contact_officers')
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="utilities-block">
            <div class="container">
                @include('home.utility_tools')
            </div>
        </section>
        <section class="content-block homepage-content-section">
            <div class="container">
                <div class="row align-items-stretch">
                    <div class="col-lg-8 col-md-7">
                        <div class="tab-wrap homepage-tabs shadowed-box" id="download-tabs-section">
                            @include('home.home_page_tabs')
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-5">
                        <div class="video-block homepage-flash-board shadowed-box mt-3 mt-md-0">
                            @include('home.live', ['flashBoxItems' => $flashBoxItems, 'flashBoxSettings' => $flashBoxSettings])
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@stop
@section('more_scripts')
    <script>
        $(window).load(function() {
            // Animate loader off screen
            $(".se-pre-con").fadeOut("slow");;
        });
    </script>

@endsection
