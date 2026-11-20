@extends('layout.client.container')
@section('content')

    <?php
    $languageId = Session::get('language_id');
    $home = $languageId == 1 ? 'गृह पृष्‍ठ  ' : 'Home';
    $heading = $languageId == 1 ? 'सुझाब' : 'Feedback';

    $fullName = $languageId == 1 ? 'पुरा नाम' : 'Full Name';
    $emailAddress = $languageId == 1 ? 'ईमेल' : 'Email Address';
    $fullAddress = $languageId == 1 ? 'पुरा ठेगाना' : 'Full Address';
    $contactNumber = $languageId == 1 ? 'संपर्क न' : 'Contact Number';
    $message = $languageId == 1 ? 'सन्देश' : 'Message';
    $captchaPlaceHolder = $languageId == 1 ? 'क्यापचा ' : 'Captcha';
    $reloadCaptcha = $languageId == 1 ? 'रिलोड गर्नुहोस्  ' : 'Reload Captcha';
    $sendMessage = $languageId == 1 ? 'पठाउनुहोस्' : 'Send Message';

    ?>

    <div class="container">
        <section class="margin-bottom">
            <div class="breadcrum">
                <ul>
                    <li><a href="{{ $abs_site_url . $subdomain }}">{{ $home }} </a></li>
                    <li><i class="fa fa-chevron-right"></i></li>
                    <li>{{ $heading }}</li>
                </ul>
            </div>
            <div class="row">
                <div class="col-md-8 wow fadeInRight animated" style="visibility: visible; -webkit-animation: fadeInRight;">
                    <h3 class="hentry__title">{{ $heading }}</h3>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="textwidget panel widget">
                                <span class="icon-container"><span class="fa fa-home"></span></span>
                                <b>{{ $officeData->office_name }}</b><br>
                                {{ $officeData->office_address }} <br><br>
                                <span class="icon-container"><span class="fa fa-phone"></span></span>
                                <b>{{ $officeData->office_phone_number1 }}</b><br>
                                <span class="icon-container"><span class="fa fa-phone"></span></span>
                                <b>{{ $officeData->office_phone_number2 }}</b><br>

                                <span class="icon-container"><span class="fa fa-envelope"></span></span><a
                                    href="mailto:{{ $officeData->office_email_address }}">{{ $officeData->office_email_address }}</a>
                                <br><br>

                            </div>
                            <div class="panel widget widget_pt_social_icons panel-last-child">
                                <a class="social-icons__link" href="http://facebook.com/dor" target="_blank"><i
                                        class="fa  fa-facebook"></i></a>
                                <a class="social-icons__link" href="http://twitter.com" target="_blank"><i
                                        class="fa  fa-twitter"></i></a>
                                <a class="social-icons__link" href="http://youtube.com" target="_blank"><i
                                        class="fa  fa-youtube"></i></a>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="wpcf7" id="wpcf7-f5-o1" dir="ltr" lang="en-US">
                                <form method="post" action="" class="wpcf7-form">
                                    @csrf
                                    <?php if(Session::get('class')) { ?>
                                    <div class="portlet-body">
                                        <div class="<?php echo Session::get('class'); ?> col-md-12">
                                            <img class="close" data-dismiss="alert"
                                                style="float: right; margin-right: 10px;" data-dismiss="alert">
                                            <p><?php echo Session::get('message'); ?> </p>
                                        </div>
                                    </div>
                                    <?php } ?>

                                    <div class="row">
                                        <div class="col-xs-12  col-sm-5">
                                            <span class="wpcf7-form-control-wrap your-name">
                                                <input name="full_name" value="" size="40"
                                                    class="wpcf7-form-control wpcf7-text"
                                                    placeholder="{{ $fullName }} " type="text" autocomplete="off">
                                            </span><br>
                                            <span class="wpcf7-form-control-wrap your-email">
                                                <input name="email_address" value="" size="40"
                                                    class="wpcf7-form-control wpcf7-text wpcf7-email"
                                                    placeholder="{{ $emailAddress }}" type="email" autocomplete="off">
                                            </span><br>
                                            <span class="wpcf7-form-control-wrap your-contact">
                                                <input name="contact_number" value="" size="40"
                                                    class="wpcf7-form-control wpcf7-text"
                                                    placeholder="{{ $contactNumber }}" type="text" autocomplete="off">
                                            </span>
                                            <br>
                                            <span class="wpcf7-form-control-wrap your-address">
                                                <input name="address" value="" size="40"
                                                    class="wpcf7-form-control wpcf7-text"
                                                    placeholder="{{ $fullAddress }}" type="text" autocomplete="off">
                                            </span>
                                        </div>

                                        <div class="col-xs-12  col-sm-7">
                                            <span class="wpcf7-form-control-wrap your-message">
                                                <textarea name="message" cols="40" rows="5"
                                                    class="wpcf7-form-control wpcf7-textarea"
                                                    placeholder="{{ $message }}"></textarea>
                                            </span><br>
                                            <div id="captcha-image"><img src="<?php echo $captcha; ?>" /></div>
                                            <a href="javascript:void(0)"
                                                id="reload-captcha">{{ $reloadCaptcha }}</a><br />
                                            <input name="captcha" class="wpcf7-form-control wpcf7-text"
                                                placeholder="{{ $captchaPlaceHolder }}" type="text">
                                            <input value="{{ $sendMessage }}" name="btnFeedback"
                                                class="wpcf7-form-control wpcf7-submit btn btn-primary" type="submit">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
                @include('layout.client.sidebar')
            </div>
            <hr class="dotted">

        </section>
    </div>

@stop
