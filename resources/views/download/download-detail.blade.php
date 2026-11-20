@extends('layout.client.container_2021')
@section('content')

<?php
	$languageId = Session::get('language_id');
	$home = ($languageId == 1) ? 'गृह पृष्‍ठ  ': 'Home';
	$downloadheading	= ($languageId == 1) ? 'डाउनलोड': 'Downloads';
	$download	= ($languageId == 1) ? 'डाउनलोड गर्नुहोस्': 'Download';
	$publishedDate = ($languageId == 1) ? \Calendar::eng_to_nep_dor($downloadData->published_date) : $downloadData->en_published_date ;
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
                                href="{{ $abs_site_url . $subdomain . '/downloads' }}">{{ $downloadheading }}</a></li>
                        <li class="breadcrumb-item">{{ $downloadData->download_heading }}
                        </li>
                    </ol>
                </nav>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="mt-4">
                        <div class="block-title">
                            <h2>{{ $downloadData->download_heading }}</h2>
                            <div class="divider"></div>
                        </div>
                        <div>{!! $downloadData->law_description !!}</div>
                        @if (file_exists('uploads/download/' . $downloadData->download_attachment) && $downloadData->download_attachment != '')
                            <p><a
                                    href="/{{ $subdomain }}/download/force/{{ $downloadData->download_url }}"
                                    target="_blank"><i class="fa fa-download"></i>&nbsp;{{ $download }}</a></p>
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
