@extends('layout.client.container_2021')
@section('content')

    <?php
    $languageId = languageId();
    $publishedDate = $languageId == 1 ? \Calendar::eng_to_nep_dor($noticeData->published_date) : $noticeData->en_published_date;
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
                                    href="{{ $abs_site_url . $subdomain . '/notices' }}">{{ \Helper::getNameOftext('notices', $languageId) }}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $noticeData->notice_heading }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mt-4">
                            <div class="block-title">
                                <h2>{{ $noticeData->notice_heading }}</h2>
                                <div class="divider"></div>
                            </div>
                            <div class="block-list">
                                <p><i class="far fa-calendar-alt" aria-hidden="true"></i> {{ $publishedDate }}</p>
                            </div>
                            <div>{!! $noticeData->notice_description !!}</div>
                            <br />

                            @if (file_exists('uploads/notice/' . $noticeData->notice_attachment) && $noticeData->notice_attachment)
                                <p><a
                                        href="/{{ $subdomain }}/notice/force/{{ $noticeData->notice_url }}"
                                        target="_blank"><i class="fa fa-download"></i>  {{ \Helper::getNameOftext('download', $languageId) }}</a></p>
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
