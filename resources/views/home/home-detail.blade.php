@extends('layout.client.container_2021')
@section('content')

    <?php
    $languageId = Session::get('language_id');
    $home = $languageId == 1 ? 'गृह पृष्‍ठ  ' : 'Home';

    $download_exclude_url = ['dor-spoke-person', 'general-info', 'message-from-director-general', 'information-officer', 'grievance-hearing-officer'];
    $current_url = $homeData->home_url;
    $should_exclude_download = in_array($current_url, $download_exclude_url);
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
                            <li class="breadcrumb-item active" aria-current="page">{{ $homeData->home_heading }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mt-4">
                            <div class="block-title">
                                <h2>{{ $homeData->home_heading }}</h2>
                                <div class="divider"></div>
                            </div>
                            @if ($should_exclude_download)
                                <div class="text-left mb-3">
                                    <img
                                        src="{{ '/uploads/pages/' . $homeData->home_attachment }}" width="150" />
                                </div>
                            @endif
                            <div>{!! $homeData->home_description !!}</div>
                            @if ($current_url === 'grievance-hearing-officer')
                                <p class="mt-3">
                                    <a href="https://grms.dor.gov.np" target="_blank" rel="noopener noreferrer">
                                        {{ $languageId == 1 ? 'गुनासो व्यवस्थापन प्रणाली (GRMS)' : 'Grievance Redress Management System (GRMS)' }}
                                    </a>
                                </p>
                            @endif
                            <br />
                            @if (!$should_exclude_download)
                                @if ($homeData->home_attachment)
                                    <hr />
                                    <p><a href="/uploads/pages/{{ $homeData->home_attachment }}" target="_blank"><i
                                                class="fa fa-download"></i>
                                            {{ $languageId == 1 ? 'डाउनलोड' : 'Download' }}</a></p>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@stop
