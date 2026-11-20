@extends('layout.client.container_2021')
@section('content')

    @php
    use Illuminate\Support\Facades\Auth;
    use App\Models\Office;
    use App\Models\Circular;
    $languageId = Session::get('language_id');
    $officeId = Office::getIdfromSubdomain($subdomain);
    // $circulars = Circular::getMainHomePageCirculars($officeId, $languageId);
    @endphp
    <!--Main-->
    <main class="main-body">
        <section class="banner-block">
            <div class="container">
                <div class="row align-items-stretch home-banner-officers-row">
                    <div class="col-xl-9 col-md-8">
                        <div class="slider-wrap mb-3 mb-lg-0" id="single-slider">
                            @foreach ($banners as $banner)
                                <div>
                                    <a class="banner-detail-link" href="{{ url($subdomain . '/banner/' . $banner->id) }}" aria-label="{{ $banner->banner_title }}">
                                        <div class="slider-fig">
                                            <img src="{{ asset('uploads/banner/' . $banner->banner_attachment) }}" alt="" />
                                        </div>
                                        <div class="slider-caption">
                                            <div class="container">
                                                <p>{{ $banner->banner_caption }}</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4 home-officers-column">
                        <div class="home-officers-panel">
                            @include('home.other_office_officers')
                        </div>
                        @include('layout.client.sidebar_2021')
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
                <div class="row">
                    <div class="col-12">
                        <div class="tab-wrap mt-3" id="download-tabs-section">
                            @include('home.home_page_tabs')
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>
    <!--/Main-->
@stop
