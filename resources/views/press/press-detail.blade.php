@extends('layout.client.container')
@section('content')

    @php
    $languageId = Session::get('language_id');
    $home = $languageId == 1 ? 'गृह पृष्‍ठ  ' : 'Home';
    $pressheading = $languageId == 1 ? 'प्रेस विज्ञप्ति' : 'Press Release';
    $fileNotfound = $languageId == 1 ? 'फाइल उपलब्ध छैन' : 'File Not found.';
    $download = $languageId == 1 ? 'डाउनलोड गर्नुहोस्' : 'Download';
    $publishedDate = $languageId == 1 ? \Calendar::eng_to_nep_dor($pressData->published_date) : $pressData->en_published_date;
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
                                    href="{{ $abs_site_url . $subdomain . '/presses' }}">{{ $pressheading }}</a></li>
                            <li class="breadcrumb-item">{{ $pressData->press_heading }}
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mt-4">
                            <div class="block-title">
                                <h2>{{ $pressData->press_heading }}</h2>
                                <div class="divider"></div>
                            </div>
                            <p><i class="far fa-calendar-alt"></i>{{ $publishedDate }}</p>
                            <p>{!! $pressData->press_description !!}</p>
                            @if (file_exists('uploads/press/' . $pressData->press_attachment) && $pressData->press_attachment != '')
                                <p>{{ $download }}
                                    <a href="{{ $abs_site_url . $subdomain }}/press/force/{{ $pressData->press_url }}"
                                        target="_blank"><i class="fa fa-download"></i></a>
                                </p>

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
