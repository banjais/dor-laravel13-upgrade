@extends('layout.client.container_2021')
@section('content')

    @php
    $languageId = Session::get('language_id');
    $home = $languageId == 1 ? 'गृह पृष्‍ठ  ' : 'Home';
    $tenderheading = $languageId == 1 ? 'बोलपत्र/दरभाउपत्र ' : 'Tender Notice';
    $fileNotfound = $languageId == 1 ? 'फाइल उपलब्ध छैन' : 'File Not found.';
    $download = $languageId == 1 ? 'डाउनलोड गर्नुहोस्' : 'Download';
    $publishedDate = $languageId == 1 ? 'प्रकाशित मिति : ' : 'Published Date : ';
    $expiryDate = $languageId == 1 ? 'अन्तिम मिति : ' : 'Expiry Date : ';
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
                                    href="{{ $abs_site_url . $subdomain . '/tenders' }}">{{ $tenderheading }}</a></li>
                            <li class="breadcrumb-item">{{ $tenderData->tender_heading }}
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mt-4">
                            <div class="block-title">
                                <h2>{{ $tenderData->tender_heading }}</h2>
                                <div class="divider"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div>
                                <div class="col-lg-12">

                                    @if ($languageId == 1)
                                        <p>{{ $publishedDate . \Calendar::eng_to_nep_dor($tenderData->published_date) }}
                                        </p>
                                        <p>{{ $expiryDate . \Calendar::eng_to_nep_dor($tenderData->expiry_date) }}</p>
                                    @else
                                        <p>{{ $publishedDate . $tenderData->en_published_date }}</p>
                                        <p>{{ $expiryDate . $tenderData->en_expiry_date }}</p>
                                    @endif

                                    <div>{!! $tenderData->tender_description !!}</div>

                                    <br />
                                    @if (file_exists('uploads/tender/' . $tenderData->tender_attachment) && $tenderData->tender_attachment != '')
                                        <p>{{ $download }}
                                            <a href="{{ $abs_site_url . $subdomain }}/tender/force/{{ $tenderData->tender_url }}"
                                                target="_blank"><i class="fa fa-download"></i></a>
                                        </p>
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>
        </section>
    </main>

@stop
