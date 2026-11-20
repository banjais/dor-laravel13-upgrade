@extends('layout.client.container_2021')
@section('content')

    <?php
    $languageId = languageId();
    $publishedDate = $languageId == 1 ? \Calendar::eng_to_nep_dor($guidelineData->published_date) : $guidelineData->en_published_date;
    // dd($guidelineData->guideline_description);
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
                                    href="{{ $abs_site_url . $subdomain . '/guidelines' }}">{{ \Helper::getNameOftext('guideline', $languageId) }}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $guidelineData->guideline_heading }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mt-4">
                            <div class="block-title">
                                <h2>{{ $guidelineData->guideline_heading }}</h2>
                                <div class="divider"></div>
                            </div>
                            <div class="block-list">
                                <p><i class="far fa-calendar-alt" aria-hidden="true"></i> {{ $publishedDate }}</p>
                            </div>
                            <div>{!! $guidelineData->guideline_description !!}</div>
                            <br />

                            @if (file_exists('uploads/guideline/' . $guidelineData->guideline_attachment) && $guidelineData->guideline_attachment)
                                <p><a
                                        href="/{{ $subdomain }}/guideline/force/{{ $guidelineData->guideline_url }}"
                                        target="_blank"><i class="fa fa-download"></i>  {{ \Helper::getNameOftext('download', $languageId) }}</a></p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@stop
