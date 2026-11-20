@extends('layout.client.container_2021')
@section('content')
    @php

    $languageId = Session::get('language_id');
    $home = $languageId == 1 ? 'गृह पृष्‍ठ  ' : 'Home';
    $circularheading = $languageId == 1 ? 'सुचना' : 'Circulars';
    $fileNotfound = $languageId == 1 ? 'फाइल उपलब्ध छैन' : 'File Not found.';
    $download = $languageId == 1 ? 'डाउनलोड गर्नुहोस्' : 'Download';
    @endphp

    <main class="main-body">
        <section class="content-block">
            <div class="container">
                @if (!$auth)
                    <div class="block-title">
                        <h2>{{ \Helper::getNameOftext('circulars', $languageId) }}</h2>
                        <div class="divider"></div>
                        <div class="mt-4">
                            You should have office login privileges to view the circulars. <a
                                href="https://dor.gov.np/admin">Office Login</a>
                        </div>
                    </div>
                @else
                    @if ($circularData)
                        <div class="breadcrumb-wrap">
                            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a
                                            href="{{ $abs_site_url . $subdomain }}">{{ \Helper::getNameOftext('homepage', $languageId) }}
                                        </a></li>
                                    <li class="breadcrumb-item"><a
                                            href="{{ $abs_site_url . $subdomain . '/circulars' }}">{{ $circularheading }}</a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">
                                        {{ $circularData->circular_heading }}</li>
                                </ol>
                            </nav>
                        </div>
                        @php
                            $publishedDate = $languageId == 1 ? \Calendar::eng_to_nep_dor($circularData->published_date) : $circularData->en_published_date;
                        @endphp
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mt-4">
                                    <div class="block-title">
                                        <h2>{{ $circularData->circular_heading }}</h2>
                                        <div class="divider"></div>
                                    </div>
                                    <div class="block-list">
                                        <p><i class="far fa-calendar-alt" aria-hidden="true"></i> {{ $publishedDate }}</p>
                                    </div>
                                    <div>{!! $circularData->circular_description !!}</div>
                                    <br />

                                    @if (file_exists('uploads/circular/' . $circularData->circular_attachment))
                                        <p><a href="{{ $abs_site_url . $subdomain }}/circular/force/{{ $circularData->circular_url }}"
                                                target="_blank"><i class="fa fa-download"></i></a> {{ $download }}</p>
                                    @endif
                                </div>
                            </div>
                            {{-- <div class="col-md-4">
                                @include('layout.client.sidebar_2021')
                            </div> --}}
                        </div>
                    @else
                        <div class="breadcrumb-wrap">
                            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a
                                            href="{{ $abs_site_url . $subdomain }}">{{ \Helper::getNameOftext('homepage', $languageId) }}
                                        </a></li>
                                    <li class="breadcrumb-item"><a
                                            href="{{ $abs_site_url . $subdomain . '/circulars' }}">{{ $circularheading }}</a>
                                    </li>
                                </ol>
                            </nav>
                        </div>
                        <div class="row">
                            <div class="block-title">
                                {{ $fileNotfound }}
                            </div>
                            {{ $fileNotfound }}
                        </div>
                    @endif
                @endif
            </div>
        </section>
    </main>

@stop
