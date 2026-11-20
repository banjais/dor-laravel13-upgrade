@extends('layout.client.container_2021')
@section('content')

    @php
    $languageId = Session::get('language_id');
    $newsDate = $languageId == 1 ? \Calendar::eng_to_nep_dor($newsData->published_date) : $newsData->en_published_date;
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
                            <li class="breadcrumb-item"><a
                                    href="{{ $abs_site_url . $subdomain . '/news' }}">{{ \Helper::getNameOftext('news', $languageId) }}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $newsData->news_heading }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mt-4">
                            <div class="block-title">
                                <h2>{{ $newsData->news_heading }}</h2>
                                <div class="divider"></div>
                            </div>
                            <div class="block-list">
                                <p><i class="far fa-calendar-alt" aria-hidden="true"></i> {{ $newsDate }}</p>
                            </div>
                            <div>{!! $newsData->news_description !!}</div>
                            <br />

                            @if (is_file(public_path('uploads/news/' . $newsData->news_attachment)) && $newsData->news_attachment != '')
                                <p><a
                                        href="{{ $abs_site_url . $subdomain }}/news/force/{{ $newsData->news_url }}"
                                        target="_blank"><i class="fa fa-download"></i></a> {{ $download }}</p>
                            @endif
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
