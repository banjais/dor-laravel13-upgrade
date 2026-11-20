@php
$fullName = $languageId == 1 ? 'पुरा नाम' : 'Full Name';
$emailAddress = $languageId == 1 ? 'ईमेल' : 'Email Address';
$ccEmail = $languageId == 1 ? 'प्रतिलिपि इमेल (वैकल्पिक)' : 'CC Email (Optional)';
$fullAddress = $languageId == 1 ? 'पुरा ठेगाना' : 'Full Address';
$contactNumber = $languageId == 1 ? 'संपर्क न' : 'Contact Number';
$message = $languageId == 1 ? 'सन्देश' : 'Message';
$captchaPlaceHolder = $languageId == 1 ? 'क्यापचा ' : 'Captcha';
$reloadCaptcha = $languageId == 1 ? 'रिलोड गर्नुहोस्  ' : 'Reload Captcha';
$sendMessage = $languageId == 1 ? 'पठाउनुहोस्' : 'Send Message';

$help_text = !empty($showContactBcc)
    ? ($languageId == 1
        ? 'कृपया आफ्नो विवरण र सन्देश भर्नुहोस्।'
        : 'Please provide your details and message.')
    : ($languageId == 1
        ? 'आफ्नो सुझाब पठाउन तलको फर्म भरेर "पठाउनुहोस्" बटनमा थिच्नुहोस । '
        : 'To send your feedback please fill the form below and click Send Message button');

@endphp
<p>{{ $help_text }}</p>
<form method="post" action="{{route('public.feedback.post', $subdomain)}}">
    @csrf
    @if (!empty($showContactBcc))
        <input type="hidden" name="form_context" value="contact">
    @endif
    @if (Session::get('class'))
        <div class="portlet-body">
            <div class="<?php echo Session::get('class'); ?> col-md-12">
                <img class="close" data-dismiss="alert" style="float: right; margin-right: 10px;"
                    data-dismiss="alert">
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
        <div class="form-group">
            <p><input name="full_name" value="{{ old('full_name') }}" size="40" class="form-control"
                    placeholder="{{ $fullName }} " type="text" autocomplete="off" />
            </p>

            <p><input name="email_address" value="{{ old('email_address') }}" size="40" class="form-control email"
                    placeholder="{{ $emailAddress }}" type="email" autocomplete="off" />
            </p>
            <p><input name="cc_email" value="{{ old('cc_email') }}" size="40"
                    class="form-control email" placeholder="{{ $ccEmail }}"
                    type="email" autocomplete="off" /></p>

            <p><input name="contact_number" value="{{ old('contact_number') }}" placeholder="{{ $contactNumber }}"
                    size="40" class="form-control" /></p>
            <p><input name="address" value="{{ old('address') }}" size="40" class="form-control"
                    placeholder="{{ $fullAddress }}" type="text" autocomplete="off" />
            </p>
        </div>

        <div class="form-group">
            <p><textarea name="message" cols="40" rows="5" class="form-control"
                    placeholder="{{ $message }}">{{ old('message') }}</textarea></p>

            @if (!empty($showContactBcc))
                <div class="form-group mt-2">
                    <label class="d-block">
                        {{ $languageId == 1 ? 'यो सन्देशको प्रतिलिपि कसलाई पठाउने? (वैकल्पिक)' : 'Who should receive a copy of this message? (Optional)' }}
                    </label>
                    <div class="form-check">
                        <input type="checkbox"
                            name="contact_bcc[]"
                            id="contact-bcc-dg"
                            value="director_general"
                            class="form-check-input"
                            {{ in_array('director_general', old('contact_bcc', [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="contact-bcc-dg">
                            {{ $languageId == 1 ? 'महानिर्देशक' : 'Director General' }}
                        </label>
                    </div>
                    @if (!empty($grmsBccEnabled))
                        <div class="form-check">
                            <input type="checkbox"
                                name="contact_bcc[]"
                                id="contact-bcc-grho"
                                value="grievance_hearing_officer"
                                class="form-check-input"
                                {{ in_array('grievance_hearing_officer', old('contact_bcc', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="contact-bcc-grho">
                                {{ $languageId == 1 ? 'गुनासो सुन्ने अधिकारी' : 'Grievance Hearing Officer' }}
                            </label>
                        </div>
                    @endif
                </div>
            @endif
            <div id="captcha-image">
                <img src="{{ $captcha }}" />
            </div>
            <p class="mt-2"><a href="javascript:void(0)" id="reload-captcha">{{ $reloadCaptcha }}</a>
            </p>
            <p><input name="captcha" class="form-control" placeholder="{{ $captchaPlaceHolder }}" type="text" />
            </p>
            <p><input value="{{ $sendMessage }}" name="btnFeedback" class="submit-btn w-100"
                    type="submit" /></p>
        </div>
    </div>
</form>
