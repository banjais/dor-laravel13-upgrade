@extends('layout.client.container')
@section('content')

    @php
    $languageId = Session::get('language_id');
    $home = $languageId == 1 ? 'गृह पृष्‍ठ  ' : 'Home';
    $heading = $languageId == 1 ? 'कर्मचारी को विवरण' : 'Staff Profile';
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
                            @if (file_exists('uploads/staffs/' . $staffData->staff_attachment) && $staffData->staff_attachment != '')
                                {
                                <img src="{{ $abs_site_url }}uploads/staffs/{{ $staffData->staff_attachment }}"
                                    alt="{{ $staffData->staff_full_name }}" class="img-responsive">
                            @else
                                <img src="{{ $abs_site_url }}assets/img/noimage.jpg"
                                    alt="{{ $staffData->staff_full_name }}" class="img-responsive">
                            @endif
                        </div>

                        @if ($languageId == 1)
                            <address>
                                <p> <strong>नाम : </strong> <span>{{ $staffData->staff_full_name }}</span> </p>
                                <p> <strong>पद : </strong> <span>{{ $staffData->staff_designation }}</span> </p>
                                <p> <strong>ईमेल : </strong> <span><a
                                            href="mailto:{{ $staffData->staff_email_address }}">{{ $staffData->staff_email_address }}</a></span>
                                </p>
                                <p> <strong>फोन :</strong> <span><a
                                            href="tel:+17079217269">{{ $staffData->staff_contact_number }}</a></span>
                                </p>
                            </address>
                        @else
                            <address>
                                <p> <strong>Name :</strong> <span>{{ $staffData->staff_full_name }}</span> </p>
                                <p> <strong>Position :</strong> <span>{{ $staffData->staff_designation }}</span> </p>
                                <p> <strong>E-Mail :</strong> <span><a
                                            href="mailto:{{ $staffData->staff_email_address }}">{{ $staffData->staff_email_address }}</a></span>
                                </p>
                                <p> <strong>Tel :</strong> <span><a href="tel:+17079217269">+1 707 921 7269</a></span> </p>
                            </address>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </main>

@stop
