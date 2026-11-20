<?php
use App\Models\Office;
use App\Models\Content;
use App\Models\Home;
$languageId = Session::get('language_id', 1);
$footer_quote = Home::getHomeDetail(4, 'footer-quote', $languageId);
$officeId = Office::getIdfromSubdomain($subdomain);
if ($officeId == 0) {
    $officeId = Office::getIdfromSubdomain('home');
}
?>
<footer id="footer">
    <strong>{{ $footer_quote && $footer_quote->home_description && $footer_quote->home_description != '--' ? trim(html_entity_decode(strip_tags($footer_quote->home_description))) : ($languageId == 1 ? 'राष्ट्रिय सम्पर्क सञ्जाल र सामाजिक–आर्थिक समृद्धिका लागि सुरक्षित, भरपर्दो तथा दिगो सडक सञ्जालको विकास।' : 'Developing a Safe, Reliable and Sustainable Road Network for National Connectivity and Socio-Economic Prosperity.') }}</strong>
</footer>
<aside id="footer-widgets">
    <div class="container">

        <div class="row">
            <div class="col-md-2 col-xs-6">

                <h3 class="footer-widget-title">{{ \Helper::getNameOftext('quick-links', $languageId) }}</h3>
                <ul class="list-unstyled">
                    <li><a
                            href="{{ $abs_site_url . $subdomain . '/staffs' }}">{{ \Helper::getNameOftext('staffs', $languageId) }}</a>
                    </li>
                    <li><a
                            href="{{ $abs_site_url . $subdomain }}/news">{{ \Helper::getNameOftext('news', $languageId) }}</a>
                    </li>
                    <li><a
                            href="{{ $abs_site_url . $subdomain }}/notices">{{ \Helper::getNameOftext('notices', $languageId) }}</a>
                    </li>
                    <!-- <li><a href="{{ $abs_site_url . $subdomain }}/presses">{{ \Helper::getNameOftext('presses', $languageId) }}</a></li> -->
                    <!-- <li><a href="{{ $abs_site_url . $subdomain . '/equipments' }}">{{ \Helper::getNameOftext('equipments', $languageId) }}</a></li> -->
                    <!-- <li><a href="{{ $abs_site_url . $subdomain }}/bills">{{ \Helper::getNameOftext('bills', $languageId) }}</a></li>  -->
                    <li><a
                            href="{{ $abs_site_url . $subdomain }}/publications">{{ \Helper::getNameOftext('publications', $languageId) }}</a>
                    </li>
                    <li><a
                            href="{{ $abs_site_url . $subdomain }}/circulars">{{ \Helper::getNameOftext('circulars', $languageId) }}</a>
                    </li>
                    <li><a
                            href="{{ $abs_site_url . $subdomain }}/downloads">{{ \Helper::getNameOftext('downloads', $languageId) }}</a>
                    </li>
                    <li><a
                            href="{{ $abs_site_url . $subdomain . '/projects' }}">{{ \Helper::getNameOftext('projects', $languageId) }}</a>
                    </li>
                    <li><a
                            href="{{ $abs_site_url . $subdomain . '/vehicles' }}">{{ \Helper::getNameOftext('vehicles', $languageId) }}</a>
                    </li>
                    <li><a
                            href="{{ $abs_site_url . $subdomain }}/gallery">{{ \Helper::getNameOftext('gallery', $languageId) }}</a>
                    </li>
                </ul>

            </div>
            <div class="col-md-2 col-xs-6 ">
                <h3 class="footer-widget-title">{{ \Helper::getNameOftext('useful-links', $languageId) }}</h3>

                <ul class="list-unstyled">
                    <li><a
                            href="{{ $abs_site_url . $subdomain . '/tenders' }}">{{ \Helper::getNameOftext('tenders', $languageId) }}</a>
                    </li>
                    <li><a
                            href="{{ $abs_site_url . $subdomain }}/laws">{{ \Helper::getNameOftext('laws', $languageId) }}</a>
                    </li>
                    <li><a
                            href="{{ $abs_site_url . $subdomain }}/faqs">{{ \Helper::getNameOftext('faqs', $languageId) }}</a>
                    </li>
                    <li><a href="{{ $abs_site_url . $subdomain . '/page/links' }}"
                            class="animated fadeIn animation-delay-9 custom">
                            {{ \Helper::getNameOftext('links', $languageId) }}</a></li>

                    <?php
                    $footerContents = Content::getContentsPostionWise($languageId, $subdomain, 2);
                    foreach ($footerContents as $content) {
                        echo '<li><a href="' . $abs_site_url . $subdomain . '/page/' . $content->content_url . '">' . $content->content_heading . '</a></li>';
                    }
                    ?>
                </ul>
            </div>
            <div class="col-md-2 col-xs-6 clear">
                <h3 class="footer-widget-title">{{ \Helper::getNameOftext('regional-offices', $languageId) }}</h3>

                <ul class="list-unstyled">
                    <?php
                    $footerOffices = Office::getActiveRegionalOffices($languageId, $subdomain);
                    foreach ($footerOffices as $office) {
                        echo '<li><a href="' . $abs_site_url . $office->office_subdomain . '">' . $office->office_name . '</a></li>';
                    }
                    ?>
                </ul>
            </div>
            <div class="col-md-3 col-xs-6">
                <h3 class="footer-widget-title">{{ \Helper::getNameOftext('external-links', $languageId) }}</h3>

                <ul class="list-unstyled">
                    <li><a href="http://eproc.dor.gov.np/"
                            target="_blank">{{ \Helper::getNameOftext('eproc', $languageId) }}</a></li>
                    <li><a href="http://dryicesolutions.net/grms/"
                            target="_blank">{{ \Helper::getNameOftext('grms', $languageId) }}</a></li>
                    <li><a href="http://armp.aviyaan.com/bms/"
                            target="_blank">{{ \Helper::getNameOftext('bms', $languageId) }}</a></li>
                    <li><a href="https://ssrn.dor.gov.np/national"
                            target="_blank">{{ \Helper::getNameOftext('ssrn', $languageId) }}</a></li>
                </ul>
            </div>


            <div class="col-md-3 col-xs-12">
                <h3 class="footer-widget-title">{{ \Helper::getNameOftext('contact-us', $languageId) }}</h3>

                <?php
                    $officeData = Office::getOfficeDetailFromSubdomain($subdomain, $languageId);
                ?>
                <ul class="list-unstyled">

                    <li><i class="fa fa-home"></i>{{ $officeData->office_name }}</li>
                    <li><i class="fa fa-location-arrow"></i> {{ $officeData->office_address }}</li>
                    <?php
                    ?>
                    @if (!empty($officeData->office_email_address))
                        <li><i class="fa fa-envelope"></i> <a href="mailto:{{ $officeData->office_email_address }}">{{ $officeData->office_email_address }}</a></li>
                    @endif
                    @if (!empty($officeData->office_alternative_email))
                        <li><i class="fa fa-envelope"></i> <a href="mailto:{{ $officeData->office_alternative_email }}">{{ $officeData->office_alternative_email }}</a></li>
                    @endif
                    <li><i class="fa fa-phone"></i> {{ $officeData->office_phone_number1 }}</li>
                    @if ($officeData->office_phone_number2 != '')
                        <li><i class="fa fa-phone"></i> {{ $officeData->office_phone_number2 }}</li>
                    @endif
                </ul>
            </div>

        </div> <!-- row -->

    </div> <!-- container -->
</aside> <!-- footer-widgets -->

<footer id="footer">
    <p>&copy; {{ date('Y') }} {{ \Helper::getNameOftext('copy-right', $languageId) }}</p>


    Visitor:
    <!-- Start of StatCounter Code for Default Guide -->
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
        <div class="statcounter"><a title="shopify visitor
  statistics" href="http://statcounter.com/shopify/" target="_blank"><img class="statcounter"
                    src="http://c.statcounter.com/10655036/0/f1dd9ff8/0/" alt="shopify visitor statistics"></a></div>
    </noscript>
    <!-- End of StatCounter Code for Default Guide -->


</footer>
