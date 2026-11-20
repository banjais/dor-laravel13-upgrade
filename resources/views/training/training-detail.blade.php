@extends('layout.client.container_2021')
@section('content')
    @php
    $languageId = Session::get('language_id');
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
                                    href="{{ $abs_site_url . $subdomain . '/trainings' }}">{{ \Helper::getNameOftext('trainings', $languageId) }}</a>
                            </li>
                            <li class="breadcrumb-item">{{ $trainingData->training_heading }}
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mt-4">
                            <div class="block-title">
                                <h2>{{ $trainingData->training_heading }}</h2>
                                <div class="divider"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div>
                                <div class="col-lg-12">
                                    <p> <strong>{{ \Helper::getNameOftext('start_date', $languageId) }} : </strong>
                                        @if ($trainingData->training_start_date)
                                            <span><?php echo \Calendar::eng_to_nep_dor($trainingData->training_start_date); ?></span>
                                        @else
                                            <i>N/A`</i>
                                        @endif
                                    </p>
                                    <p> <strong>{{ \Helper::getNameOftext('end_date', $languageId) }} : </strong>
                                        @if ($trainingData->training_end_date)
                                            <span><?php echo \Calendar::eng_to_nep_dor($trainingData->training_end_date); ?></span>
                                        @else
                                            <i>N/A`</i>
                                        @endif
                                    </p>
                                    <p> <strong>{{ \Helper::getNameOftext('contact_person', $languageId) }} : </strong>
                                        <span>{{ $trainingData->training_contact_person }}</span>
                                    </p>
                                    <p> <strong>{{ \Helper::getNameOftext('contact_number', $languageId) }} : </strong>
                                        <span>{{ $trainingData->training_contact_number }}</span>
                                    </p>
                                    <p> <strong>{{ \Helper::getNameOftext('number_of_trainees', $languageId) }} :
                                        </strong> <span
                                            id="number_of_traniees">{{ $trainingData->number_of_traniees }}</span> </p>
                                    <p> <strong>{{ \Helper::getNameOftext('minimum_qualification', $languageId) }} :
                                        </strong> <span>{{ $trainingData->minimum_qualification }}</span> </p>

                                    <div>{!! $trainingData->training_description !!}</div>
                                    @if ($trainingData->training_remarks != '')
                                        <p> <strong>{{ \Helper::getNameOftext('remarks', $languageId) }} : </strong>
                                            <span></span>
                                        </p>
                                        <div>{!! $trainingData->training_remarks !!}</div>
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>
        </section>
    </main>
@stop
