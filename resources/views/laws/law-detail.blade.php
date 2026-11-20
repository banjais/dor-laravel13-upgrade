@extends('layout.client.container_2021')
@section('content')

    <?php
    $languageId = Session::get('language_id');
    $home = $languageId == 1 ? 'गृह पृष्‍ठ  ' : 'Home';
    $lawheading = $languageId == 1 ? 'नियम कानुन' : 'Laws';
    $download = $languageId == 1 ? 'डाउनलोड गर्नुहोस्' : 'Download';
    $lawDate = $languageId == 1 ? \Calendar::eng_to_nep_dor($lawData->published_date) : $lawData->en_published_date;
    ?>
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
                                    href="{{ $abs_site_url . $subdomain . '/laws' }}">{{ $lawheading }}</a></li>
                            <li class="breadcrumb-item">{{ $lawData->law_heading }}
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mt-4">
                            <div class="block-title">
                                <h2>{{ $lawData->law_heading }}</h2>
                                <div class="divider"></div>
                            </div>
                            <div>{!! $lawData->law_description !!}</div>
                            @if (file_exists('uploads/law/' . $lawData->law_attachment) && $lawData->law_attachment != '')
                                <p>{{ $download }}<a
                                        href="/{{ $subdomain }}/law/force/{{ $lawData->law_url }}"
                                        target="_blank"><i class="fa fa-download"></i></a></p>
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
