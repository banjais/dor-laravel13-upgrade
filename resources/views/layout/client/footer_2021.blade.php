@php
use App\Models\Office;
use App\Models\Content;
use App\Models\Home;
use App\Helpers\NepaliDateHelper;

$languageId = Session::get('language_id');
$officeId = Office::getIdfromSubdomain($subdomain);
if ($officeId == 0) {
    $officeId = Office::getIdfromSubdomain('home');
}

$abs_site_url = '/';

$last_update_date = lastUpdateDate($officeId);
// dd($last_update_date);
$h = new NepaliDateHelper();

$bs_date = $h->bsDate($last_update_date) . ' ' . dateFormat($last_update_date, 'h:i:s');
$last_update_date_np = devanagari($bs_date);
$last_update_date_en = dateFormat($last_update_date, 'Y-m-d h:i:s A');
$sessionLangId = Session::get('language_id', 1);
$officeData = Office::getOfficeDetailFromSubdomain($subdomain, $sessionLangId);

$facebookUrl = trim((string) ($officeData->office_facebook_page ?? ''));
$instagramUrl = trim((string) ($officeData->office_instagram_page ?? ''));

foreach (['facebookUrl', 'instagramUrl'] as $socialField) {
    $socialUrl = $$socialField;
    $parts = $socialUrl ? parse_url($socialUrl) : false;
    if (!$parts || !filter_var($socialUrl, FILTER_VALIDATE_URL) ||
        !in_array(strtolower($parts['scheme'] ?? ''), ['http', 'https'], true)) {
        $$socialField = '';
    }
}
$missingSocialText = $sessionLangId == 1 ? 'पेज राखिएको छैन' : 'Page not added yet';


$footer_col_1 = null;
$footer_col_2 = Home::getHomeDetail(4, 'footer-section-2', $sessionLangId);
$footer_col_3 = Home::getHomeDetail(4, 'footer-section-3', $sessionLangId);
$footer_quote = Home::getHomeDetail($officeId, 'footer-quote', $sessionLangId);
// dd($footer_col_2);
@endphp
<!-- Office quote: displayed between main content and footer -->
<section class="office-quote-section">
    <div class="container">
        <p>“{{ $footer_quote && $footer_quote->home_description && $footer_quote->home_description != '--' ? trim(html_entity_decode(strip_tags($footer_quote->home_description))) : ($sessionLangId == 1 ? 'राष्ट्रिय सम्पर्क सञ्जाल र सामाजिक–आर्थिक समृद्धिका लागि सुरक्षित, भरपर्दो तथा दिगो सडक सञ्जालको विकास।' : 'Developing a Safe, Reliable and Sustainable Road Network for National Connectivity and Socio-Economic Prosperity.') }}”</p>
    </div>
</section>
<!--footer-->
<footer class="main-footer">
    <div class="mid-footer">
        <div class="container">
            <div class="row">
                
<div class="col-md-3 col-sm-6">
    <h4>{{ $sessionLangId == 1 ? 'आन्तरिक लिंकहरु' : 'Internal Links' }}</h4>
    <div class="footer-list footer-internal-links">
        <ul>
            <li><a href="{{ url('/' . $subdomain . '/contact') }}">{{ $sessionLangId == 1 ? 'सम्पर्क' : 'Contact Us' }}</a></li>
            <li><a href="{{ url('/home/page/dor-office-contacts') }}">{{ $sessionLangId == 1 ? 'कार्यालय सम्पर्क' : 'DoR Contacts' }}</a></li>
        </ul>
        <div class="footer-social-links" aria-label="Social media">
            @if ($facebookUrl)
                <a href="{{ $facebookUrl }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook" title="Facebook"><i class="fab fa-facebook" aria-hidden="true"></i></a>
            @else
                <span class="footer-social-missing" title="{{ $missingSocialText }}"><i class="fab fa-facebook" aria-hidden="true"></i><small>{{ $missingSocialText }}</small></span>
            @endif
            @if ($instagramUrl)
                <a href="{{ $instagramUrl }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram" title="Instagram"><i class="fab fa-instagram" aria-hidden="true"></i></a>
            @else
                <span class="footer-social-missing" title="{{ $missingSocialText }}"><i class="fab fa-instagram" aria-hidden="true"></i><small>{{ $missingSocialText }}</small></span>
            @endif
        </div>
    </div>
</div>
<div class="col-md-3 col-sm-6">
    <h4>{{ $footer_col_2->home_heading ?? ($sessionLangId == 1 ? 'बाहिरी लिंकहरु' : 'External Links') }}</h4>
    <div class="footer-list">
        @if ($footer_col_2 && $footer_col_2->home_description && $footer_col_2->home_description != '--')
            {!! \Helper::normalizeFooterLinks($footer_col_2->home_description) !!}
        @else
            @include('layout.client.default_footer_col_2')
        @endif
    </div>
</div>
<div class="col-md-3 col-sm-6">
    <h4>{{ $footer_col_3->home_heading ?? ($sessionLangId == 1 ? 'महत्वपूर्ण लिंकहरु' : 'Important Links') }}</h4>
    <div class="footer-list">
        @php
            $importantDescription = $footer_col_3->home_description ?? '';
            $importantDescription = preg_replace_callback(
                '~<li\b[^>]*>.*?</li>~is',
                function ($match) {
                    return (stripos($match[0], 'facebook.com') !== false ||
                            stripos($match[0], 'instagram.com') !== false ||
                            stripos($match[0], 'socialMedia/') !== false) ? '' : $match[0];
                },
                $importantDescription
            );
        @endphp
        @if ($importantDescription && $importantDescription != '--')
            {!! \Helper::normalizeFooterLinks($importantDescription) !!}
        @else
            @include('layout.client.default_footer_col_3')
        @endif
    </div>
</div>

                <div class="col-md-3 col-sm-6">
                    <h4>{{ \Helper::getNameOftext('contact-us', $languageId) }}</h4>
                    <div class="address-list">
                        @if (isset($officeData))
                            @php
                                if (isset($officeData[0])) {
                                    $off_d = $languageId == 1 ? $officeData[0] : $officeData[1];
                                } else {
                                    $off_d = $officeData;
                                }
                            @endphp
                            @if ($off_d)
                                @php
                                    $footerEmails = [];
                                    foreach ([$off_d->office_alternative_email ?? '', $off_d->office_phone_number2 ?? ''] as $emailSource) {
                                        foreach (preg_split('/[;,]+/', $emailSource) as $email) {
                                            $email = trim($email);
                                            if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                                $footerEmails[strtolower($email)] = $email;
                                            }
                                        }
                                    }
                                    $secondaryPhone = trim((string) ($off_d->office_phone_number2 ?? ''));
                                    $secondaryPhone = strpos($secondaryPhone, '@') === false ? $secondaryPhone : '';
                                @endphp
                                <ul>
                                    <li><i class="fa fa-home"></i> {{ $off_d->office_name }}</li>
                                    <li><i class="fa fa-location-arrow"></i> {{ $off_d->office_address }}</li>
                                    @if (!empty($off_d->office_phone_number1))
                                        <li><i class="fa fa-phone"></i> <a href="tel:{{ $off_d->office_phone_number1 }}">{{ $off_d->office_phone_number1 }}</a></li>
                                    @endif
                                    @if ($secondaryPhone !== '')
                                        <li><span aria-hidden="true" style="display:inline-block;width:20px"></span><a href="tel:{{ $secondaryPhone }}">{{ $secondaryPhone }}</a></li>
                                    @endif
                                    @if (!empty($off_d->office_email_address))
                                        <li><i class="fa fa-envelope"></i> <a href="mailto:{{ $off_d->office_email_address }}">{{ $off_d->office_email_address }}</a></li>
                                    @endif
                                    @foreach ($footerEmails as $email)
                                        @if (strtolower($email) !== strtolower(trim((string) $off_d->office_email_address)))
                                            <li><span aria-hidden="true" style="display:inline-block;width:20px"></span><a href="mailto:{{ $email }}">{{ $email }}</a></li>
                                        @endif
                                    @endforeach
                                </ul>
                            @endif

                        @else

                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="bottom-footer">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="divider-list">
                        <ul>
                            <li class="footer-app-item">
                                <button type="button" class="footer-install-app-trigger"
                                    aria-expanded="false" aria-controls="dorAppPopover"
                                    title="DoR App">
                                    <i class="fa fa-mobile" aria-hidden="true"></i>
                                    {{ $languageId == 1 ? 'डोर एप' : 'DoR App' }}
                                </button>
                                <div class="footer-app-popover" id="dorAppPopover">
                                    <img src="{{ asset('assets/pwa/dor-website-qr.png') }}?v={{ filemtime(public_path('assets/pwa/dor-website-qr.png')) }}"
                                        alt="DoR App QR Code" width="140" height="140">
                                    <div class="footer-share-links">
                                        <button type="button" class="footer-native-share"
                                            aria-label="Share DoR website" title="Share">
                                            <svg viewBox="0 0 24 24" width="19" height="19"
                                                aria-hidden="true" focusable="false"
                                                fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <circle cx="18" cy="5" r="3"></circle>
                                                <circle cx="6" cy="12" r="3"></circle>
                                                <circle cx="18" cy="19" r="3"></circle>
                                                <path d="m8.7 10.7 6.6-4.4M8.7 13.3l6.6 4.4"></path>
                                            </svg>
                                        </button>
                                        <span class="visually-hidden" id="footerShareStatus" aria-live="polite"></span>
                                    </div>
                                </div>
                            </li>
                            <li><a href="/{{ $subdomain }}/home/screen-reader">{{ $languageId == 1 ? 'स्क्रीन रिडर' : 'Screen Reader' }}</a></li>
                            <li><a href="/{{ $subdomain }}/home/privacy-policy">{{ $languageId == 1 ? 'गोपनीयताको नीति' : 'Privacy Policy' }}</a></li>
                            <li><a href="/{{ $subdomain }}/home/terms-and-conditions">{{ $languageId == 1 ? 'प्रयोगका शर्तहरु' : 'Terms & conditions' }}</a></li>
                            <li><a href="/{{ $subdomain }}/gallery">{{ $languageId == 1 ? 'ग्यालरी' : 'Gallery' }}</a></li>
                            
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6 footer-meta text-lg-end mt-3 mt-lg-0">
                    <p>&copy; {{ date('Y') }} {{ \Helper::getNameOftext('copy-right', $languageId) }}</p>
                    <div class="visitor-no">
                        <span>Visitors: </span>
                        <!-- Start of StatCounter Code for Default Guide -->
                        <script>window.sc_remove_alt = true;</script>
                        <script type="text/javascript">
                            var sc_project = 10655036;
                            var sc_invisible = 0;
                            var sc_security = "f1dd9ff8";
                            var scJsHost = (("https:" == document.location.protocol) ?
                                "https://secure." : "http://www.");
                            document.write("<sc" + "ript type='text/javascript' src='" +
                                scJsHost +
                                "statcounter.com/counter/counter.js'></" + "script>");
                        </script>
                        <noscript>
                            <img class="statcounter"
                                src="http://c.statcounter.com/10655036/0/f1dd9ff8/0/"
                                alt="Visitor Counter">
                        </noscript>
                        <!-- End of StatCounter Code for Default Guide -->
                    </div>
                    <p>{{ $languageId == 1 ? 'पछिल्लो अपडेट :' : 'Last update' }}
                        {{ $languageId == 1 ? $last_update_date_np : $last_update_date_en }}</p>
                </div>
            </div>
        </div>
    </div>
</footer>
<!--/footer-->
