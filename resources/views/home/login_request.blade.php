@extends('layout.client.container_2021')
@section('content')

@php
$languageId = Session::get('language_id');
$home = $languageId == 1 ? 'गृह पृष्‍ठ ' : 'Home';
$heading = $languageId == 1 ? 'Request for Login Credentials (DoR office use only)' : 'Request for Login Credentials
(DoR office use only)';

$fullName = $languageId == 1 ? 'Your Full Name' : 'Your Full Name';
$emailAddress = $languageId == 1 ? 'Your Email Address' : 'Your Email Address';
$officeName = $languageId == 1 ? 'Your Office Name' : 'Your Office Name';
$designation = $languageId == 1 ? 'Your Designation' : 'Your Designation';
$fullAddress = $languageId == 1 ? 'Full Address of Your Office' : 'Full Address of Your Office';
$contactNumber = $languageId == 1 ? 'Landline Number of Your Office' : 'Landline Number of Your Office';

$emailLogin = $languageId == 1 ? 'Email Login' : 'Email Login';
$webLogin = $languageId == 1 ? 'Web Page Login' : 'Web Page Login';
$circularLogin = $languageId == 1 ? 'Circular Page Login' : 'Circular Page Login';

$captchaPlaceHolder = $languageId == 1 ? 'Captcha' : 'Captcha';
$reloadCaptcha = $languageId == 1 ? 'Reload Captcha' : 'Reload Captcha';
$sendMessage = $languageId == 1 ? 'Send Message' : 'Send Message';

$help_text = $languageId == 1 ? 'To get login account for your office, please fill the form below and click Send Message
button' : 'To get login account for your office, please fill the form below and click Send Message button';

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
                        <div class="col-lg-12">
                            <p>{{ $help_text }}</p>
                            <form method="post" action="/home/login-credentials-request/post">
                                @csrf
                                @if (Session::get('class'))
                                <div class="portlet-body">
                                    <div class="<?php echo Session::get('class'); ?> col-md-12">
                                        <img class="close" data-dismiss="alert"
                                            style="float: right; margin-right: 10px;" data-dismiss="alert">
                                        <p><?php echo Session::get('message'); ?> </p>
                                    </div>
                                </div>
                                @endif

                                @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                        <li class="text-danger">{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <input name="full_name" value="{{ old('full_name') }}" size="40"
                                                class="form-control" placeholder="{{ $fullName }} " type="text"
                                                autocomplete="off" />

                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3"><input name="email" value="{{ old('email') }}"
                                                size="40" class="form-control email" placeholder="{{ $emailAddress }}"
                                                type="email" autocomplete="off" />
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group mb-3"><input name="office_name"
                                                value="{{ old('office_name') }}" size="40" class="form-control"
                                                placeholder="{{ $officeName }} " type="text" autocomplete="off" />
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group mb-3"><input name="office_address"
                                                value="{{ old('office_address') }}" size="40" class="form-control"
                                                placeholder="{{ $fullAddress }} " type="text" autocomplete="off" />
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group mb-3"><input name="office_phone"
                                                value="{{ old('office_phone') }}" size="40" class="form-control"
                                                placeholder="{{ $contactNumber }} " type="text" autocomplete="off" />
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group mb-3"><input name="designation"
                                                value="{{ old('designation') }}" size="40" class="form-control"
                                                placeholder="{{ $designation }} " type="text" autocomplete="off" />
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <input type="checkbox" name="is_circular_login" id="is_circular_login"
                                                @if(old('is_circular_login')) checked @endif />
                                            <label for="is_circular_login">{{ $circularLogin }}</label>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <input type="checkbox" name="is_email_login" id="is_email_login"
                                                @if(old('is_email_login')) checked @endif />
                                            <label for="is_email_login">{{ $emailLogin }}</label>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <input type="checkbox" name="is_web_login" id="is_web_login"
                                                @if(old('is_web_login')) checked @endif />
                                            <label for="is_web_login">{{ $webLogin }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <div id="captcha-image">
                                                <img src="{{ $captcha }}" />
                                            </div>
                                            <p class="mt-2"><a href="javascript:void(0)"
                                                    id="reload-captcha">{{ $reloadCaptcha }}</a>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <input name="captcha" class="form-control"
                                                placeholder="{{ $captchaPlaceHolder }}" type="text" />
                                        </div>
                                    </div>
                                    <div class="form-group mt-2">
                                        <input value="{{ $sendMessage }}" name="btnFeedback" class="submit-btn"
                                            type="submit" />
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
    </section>
</main>
@stop