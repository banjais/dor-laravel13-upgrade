@php
$languageId = \Illuminate\Support\Facades\Session::get('language_id');
$officeId = \App\Models\Office::getIdfromSubdomain($subdomain);

$homespokeperson = \App\Models\Home::getHomeDataFrontend('dor-spoke-person', $officeId, $languageId);
$homegeneralinfo = \App\Models\Home::getHomeDataFrontend('general-info', $officeId, $languageId);
$homeInformationOfficer = \App\Models\Home::getHomeDataFrontend('information-officer', $officeId, $languageId);
$homeGrievanceOfficer = \App\Models\Home::getHomeDataFrontend('grievance-hearing-officer', $officeId, $languageId);
$homeAdditionalOfficer = \App\Models\Home::getHomeDataFrontend('additional-officer', $officeId, $languageId);

$otherOfficeOfficers = collect([
    $homespokeperson,
    $homegeneralinfo ?: $homeInformationOfficer,
    $homeGrievanceOfficer,
    $homeAdditionalOfficer,
])->filter()->take(4)->values();
@endphp

@if (isset($subdomain) && $subdomain != 'home' && $otherOfficeOfficers->isNotEmpty())
    <div class="person-block shadowed-box other-office-officers-frame" style="--officer-count: {{ $otherOfficeOfficers->count() }};">
        @foreach ($otherOfficeOfficers as $officer)
            @php
                $rawOfficerDescription = $officer->home_description ?? '';

                // नयाँ रेकर्डमा छुट्टै भाषाअनुसार सुरक्षित गरिएको नाम प्राथमिकता
                $officerName = trim($officer->officer_name ?? '');

                // पुराना रेकर्डका लागि पुरानो data_1 / data_2 नाम प्रयोग
                if ($officerName === '') {
                    $officerName = (int) $languageId === 1
                        ? trim($officer->data_1 ?? '')
                        : trim($officer->data_2 ?? '');
                }

                // पद सम्बन्धित भाषामा देखाउने
                $officerPosition = trim($officer->home_heading ?? '');

                // General Info पेजको पद निश्चित रूपमा सूचना अधिकारी हो
                if ($officer->home_url === 'general-info') {
                    $officerPosition = (int) $languageId === 1
                        ? 'सूचना अधिकारी'
                        : 'Information Officer';
                }

                // पुरानो General Info र Spokesperson रेकर्डमा नाम Description को
                // पहिलो अनुच्छेदमा हुन सक्छ; नयाँ नाम भए त्यसलाई नछुने
                if (
                    $officerName === '' &&
                    in_array($officer->home_url, ['general-info', 'dor-spoke-person'], true) &&
                    preg_match('/^\s*<p\b[^>]*>(.*?)<\/p>/is', $rawOfficerDescription, $firstParagraph)
                ) {
                    $officerName = trim(html_entity_decode(
                        strip_tags($firstParagraph[1]),
                        ENT_QUOTES | ENT_HTML5,
                        'UTF-8'
                    ));
                }

                // कुनै पुरानो रेकर्डमा छुट्टै नाम नभए मात्र अन्तिम fallback
                if ($officerName === '') {
                    $officerName = $officerPosition;
                }

                if (trim($officerPosition) === trim($officerName)) {
                    $officerPosition = '';
                }

                $cleanOfficerDescription = trim($rawOfficerDescription);

                // विवरणको सुरुमा नाम दोहोरिएको भए पहिलो अनुच्छेद हटाउने
                $cleanOfficerDescription = preg_replace_callback(
                    '/^\s*<p\b[^>]*>(.*?)<\/p>/is',
                    function ($match) use ($officerName) {
                        $firstText = html_entity_decode(
                            strip_tags($match[1]),
                            ENT_QUOTES | ENT_HTML5,
                            'UTF-8'
                        );

                        $firstText = preg_replace('/^(?:ई\.?|इ\.?\s*)/u', '', trim($firstText));
                        $nameText = preg_replace('/^(?:ई\.?|इ\.?\s*)/u', '', trim($officerName));

                        $firstText = preg_replace('/[\s\x{00A0}\x{200C}\x{200D}]+/u', '', $firstText);
                        $nameText = preg_replace('/[\s\x{00A0}\x{200C}\x{200D}]+/u', '', $nameText);

                        return $firstText === $nameText ? '' : $match[0];
                    },
                    $cleanOfficerDescription,
                    1
                );

                // खाली अनुच्छेद र अनुच्छेदको सुरुको अनावश्यक खाली ठाउँ हटाउने
                $cleanOfficerDescription = preg_replace(
                    '/<p\b[^>]*>(?:\s|&nbsp;|&#160;|&#xA0;|<br\s*\/?\s*>)*<\/p>/i',
                    '',
                    $cleanOfficerDescription
                );

                $cleanOfficerDescription = preg_replace_callback(
                    '/(<p\b[^>]*>)(.*?)(<\/p>)/is',
                    function ($match) {
                        $text = preg_replace('/^(?:\s|&nbsp;|&#160;|&#xA0;)+/i', '', $match[2]);
                        return $match[1] . $text . $match[3];
                    },
                    $cleanOfficerDescription
                );

                // HTML संरचना सुरक्षित राखेर विवरणको पाठ सामान्य बनाउने
                $cleanOfficerDescription = preg_replace_callback(
                    '/>([^<]+)</u',
                    function ($match) {
                        $text = html_entity_decode(
                            $match[1],
                            ENT_QUOTES | ENT_HTML5,
                            'UTF-8'
                        );

                        // सबै कार्यालयमा विवरणका अंक English मा देखाउने
                        $text = strtr($text, [
                            '०' => '0', '१' => '1', '२' => '2',
                            '३' => '3', '४' => '4', '५' => '5',
                            '६' => '6', '७' => '7', '८' => '8',
                            '९' => '9',
                        ]);

                        // इमेल भए त्यसअघिको Email / इमेल ठेगाना जस्ता लेबल हटाउने
                        if (preg_match('/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}/i', $text, $emailMatch, PREG_OFFSET_CAPTURE)) {
                            $text = substr($text, $emailMatch[0][1]);
                        }

                        // फोन नम्बर भएको विवरणबाट अगाडिका Contact / सम्पर्क / Mobile लेबल हटाउने
                        elseif (preg_match('/\d{7,}/u', $text, $numberMatch, PREG_OFFSET_CAPTURE)) {
                            $prefix = substr($text, 0, $numberMatch[0][1]);

                            if (preg_match('/(?:सम्पर्क|फोन|मोबाइल|मोबाईल|दूरभाष|contact|phone|mobile|tel|telephone)/iu', $prefix)) {
                                $text = substr($text, $numberMatch[0][1]);
                            }
                        }

                        // इमेल ठेगानालाई विरामचिह्न सफाइबाट सुरक्षित राख्ने
                        $emails = [];
                        $text = preg_replace_callback(
                            '/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}/i',
                            function ($email) use (&$emails) {
                                $key = 'EMAILTOKEN' . count($emails) . 'TOKEN';
                                $emails[$key] = $email[0];
                                return $key;
                            },
                            $text
                        );

                        // लगातार खाली ठाउँलाई एउटै बनाउने
                        $text = preg_replace('/[\s\x{00A0}\x{2000}-\x{200B}\x{202F}\x{205F}\x{3000}]+/u', ' ', $text);

                        // comma, full stop लगायतका चिह्नअघि खाली ठाउँ हटाउने
                        $text = preg_replace('/\s+([,;:!?।])/u', '$1', $text);

                        // विरामचिह्नपछि खाली ठाउँ नभए थप्ने
                        $text = preg_replace('/([,;:!?।])(?=\S)/u', '$1 ', $text);

                        // नेपाली/अंग्रेजी अक्षरपछि आएको full stop पछि खाली ठाउँ
                        $text = preg_replace('/(?<=[\p{L}])\.(?=[\p{L}])/u', '. ', $text);

                        // इमेल ठेगाना जस्ताको तस्तै फर्काउने
                        foreach ($emails as $key => $email) {
                            $text = str_replace($key, $email, $text);
                        }

                        return '>' . htmlspecialchars(
                            $text,
                            ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5,
                            'UTF-8'
                        ) . '<';
                    },
                    $cleanOfficerDescription
                );

                $cleanOfficerDescription = trim($cleanOfficerDescription);
                $descriptionText = trim(strip_tags($cleanOfficerDescription));
                $hasLongDescription = mb_strlen($descriptionText) > 250;
            @endphp

            <div class="media-block">
                <div class="rounded-fig-wrap">
                    <a href="{{ '/' . $subdomain . '/home/' . $officer->home_url }}">
                        @if ($officer->home_attachment)
                            <img
                                src="{{ asset('uploads/pages/' . $officer->home_attachment) }}"
                                alt="{{ $officerName }}"
                            />
                        @else
                            <span class="other-officer-no-photo">
                                <i class="fa fa-user" aria-hidden="true"></i>
                            </span>
                        @endif
                    </a>
                </div>

                <div class="media-desc">
                    <a href="{{ '/' . $subdomain . '/home/' . $officer->home_url }}">
                        @if ($officerPosition)
                            <p class="fw-bold other-officer-position">{{ $officerPosition }}</p>
                        @endif

                        <h5>{{ $officerName }}</h5>

                        @if ($officer->data_3)
                            @php
                                $englishPhone = strtr((string) $officer->data_3, [
                                    '०' => '0', '१' => '1', '२' => '2',
                                    '३' => '3', '४' => '4', '५' => '5',
                                    '६' => '6', '७' => '7', '८' => '8',
                                    '९' => '9',
                                ]);
                            @endphp
                            <p><i class="fa fa-phone-alt me-2"></i> {{ $englishPhone }}</p>
                        @endif

                        @if ($officer->data_4)
                            <p><i class="far fa-envelope me-2"></i> {{ $officer->data_4 }}</p>
                        @endif
                    </a>

                </div>
            </div>
        @endforeach
    </div>
@endif
