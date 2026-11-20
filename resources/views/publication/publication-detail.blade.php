@extends('layout.client.container_2021')
@section('content')

    @php
    $languageId = Session::get('language_id');
    $home = $languageId == 1 ? 'गृह पृष्‍ठ  ' : 'Home';
    $publicationheading = $languageId == 1 ? 'प्रकाशनहरु' : 'Publications';
    $fileNotfound = $languageId == 1 ? 'फाइल उपलब्ध छैन' : 'File Not found.';
    $download = $languageId == 1 ? 'डाउनलोड गर्नुहोस्' : 'Download';
    $publishedDate = $languageId == 1 ? \Calendar::eng_to_nep_dor($publicationData->published_date) : $publicationData->en_published_date;

    // dd($publicationData);

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
                            <li><a
                                    href="{{ $abs_site_url . $subdomain . '/publications' }}">{{ $publicationheading }}</a>
                            </li>
                            <li><a
                                    href="{{ $abs_site_url . $subdomain . '/publication/' . $categoryUrl }}">{{ $publicationCategory }}</a>
                            </li>
                            <li>{{ $publicationData->publication_heading }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mt-4">
                            <div class="block-title">
                                <h2>{{ $publicationData->publication_heading }}</h2>
                                <div class="divider"></div>
                            </div>
                            <div class="block-list">
                                <p><i class="far fa-calendar-alt" aria-hidden="true"></i> {{ $publishedDate }}</p>
                            </div>
                            <div>{!! $publicationData->publication_description !!}</div>
                            <br />

                            @if (file_exists('uploads/publication/' . $publicationData->publication_attachment) && $publicationData->publication_attachment)
                                <p><a href="/{{ $subdomain }}/publication/{{ $categoryUrl.'/force/'.$publicationData->publication_url }}" target="_blank"><i class="fa fa-download"></i> {{ $download }}</a></p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@stop
