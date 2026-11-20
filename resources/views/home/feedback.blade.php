@extends('layout.client.container_2021')
@section('content')

@php
use App\Models\Office;
use App\Models\Home;

$languageId = Session::get('language_id');
$home = $languageId == 1 ? 'गृह पृष्‍ठ ' : 'Home';
$heading = $languageId == 1 ? 'सुझाब' : 'Feedback';

$officeData = Office::getOfficeDetailFromSubdomain($subdomain, $languageId);

$facebookFeed = Home::getHomeDataFrontend('facebook-feed', $officeId, $languageId);
$instagramFeed = Home::getHomeDataFrontend('instagram-feed', $officeId, $languageId);
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
                        <li class="breadcrumb-item">{{ $heading }}
                        </li>
                    </ol>
                </nav>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="mt-4">
                        <div class="block-title">
                            <h2>{{ $heading }}</h2>
                            <div class="divider"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="address-box">
                                <ul>
                                    <li>
                                        <div class="icon-container"><i class="fa fa-home"></i></div>
                                           <p> {{ $officeData->office_name }}</p>
                                        <p>{{ $officeData->office_address }}</p>
                                    </li>
                                    <li>
                                        <div class="icon-container"><i class="fa fa-phone"></i></div>
                                        <p>{{ $officeData->office_phone_number1 }}</p>
                                    </li>
                                    <li>
                                        <div class="icon-container"><i class="fa fa-fax"></i></div>
                                        <p>{{ $officeData->office_phone_number2 }}</p>
                                    </li>
                                    <li>
                                        <div class="icon-container"><i class="fa fa-envelope"></i></div>
                                        <p><a href="mailto:{{ $officeData->office_email_address }}">
                                            {{ $officeData->office_email_address }}</a></p>
                                    </li>
                                </ul>
                            </div>
                            <hr>
                            <div class="social-shares">
                                @if ($facebookFeed && $facebookFeed->data_2)
                                    <a class="me-2" href="{{ $facebookFeed->data_2 }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                                @endif
                                @if ($instagramFeed && $instagramFeed->data_2)
                                    <a href="{{ $instagramFeed->data_2 }}" target="_blank" rel="noopener noreferrer" aria-label="X"><strong>X</strong></a>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-9">
                            @include('home.feedback_partial')
                        </div>
                    </div>
                </div>
    </section>
</main>
@stop
