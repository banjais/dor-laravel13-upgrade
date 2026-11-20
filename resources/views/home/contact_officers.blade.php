<div class="person-block shadowed-box head-office-contact-officers">
    <div class="person-header">
        {{-- <p>{{ $languageId == 1 ? 'सम्पर्क अधिकारीहरु' : 'Contact Officers' }}
        </p> --}}
    </div>
    @php
        $englishDigits = [
                            '०' => '0', '१' => '1', '२' => '2',
                            '३' => '3', '४' => '4', '५' => '5',
                            '६' => '6', '७' => '7', '८' => '8',
                            '९' => '9',
                        ];
        // dd($homeData2);
    @endphp
    @if ($homeDirectorGeneral)
        <div class="media-block">
            <div class="rounded-fig-wrap">
                <a href="{{ '/home/home/' . $homeDirectorGeneral->home_url }}">
                    <img src="{{ asset('uploads/pages/' . $homeDirectorGeneral->home_attachment) }}" alt="" />
                </a>
            </div>
            <div class="media-desc">
                <a href="{{ '/home/home/' . $homeDirectorGeneral->home_url }}">
                    <h5>{{ $languageId == 1 ? $homeDirectorGeneral->data_1 : $homeDirectorGeneral->data_2 }}</h5>
                    <p class="fw-bold">{!! $homeDirectorGeneral->home_heading !!}</p>
                    @if ($homeDirectorGeneral->data_3)
                        <p><i class="fa fa-phone-alt me-2"></i> {{ strtr((string) $homeDirectorGeneral->data_3, $englishDigits) }}</p>
                    @endif
                    @if ($homeDirectorGeneral->data_4)
                        <p><i class="far fa-envelope me-2"></i> {{ $homeDirectorGeneral->data_4 }}</p>
                    @endif
                </a>
            </div>
        </div>
        @if ($homespokeperson)
            <div class="media-block">
                <div class="rounded-fig-wrap">
                    <a href="{{ '/home/home/' . $homespokeperson->home_url }}">
                        <img src="{{ asset('uploads/pages/' . $homespokeperson->home_attachment) }}" alt="" />
                    </a>
                </div>
                <div class="media-desc">
                    <a href="{{ '/home/home/' . $homespokeperson->home_url }}">
                        <h5>{{ $languageId == 1 ? $homespokeperson->data_1 : $homespokeperson->data_2 }}</h5>
                        <p class="fw-bold">{!! $homespokeperson->home_heading !!}</p>
                        @if ($homespokeperson->data_3)
                            <p><i class="fa fa-phone-alt me-2"></i> {{ strtr((string) $homespokeperson->data_3, $englishDigits) }}</p>
                        @endif
                        @if ($homespokeperson->data_4)
                            <p><i class="far fa-envelope me-2"></i> {{ $homespokeperson->data_4 }}</p>
                        @endif
                    </a>
                </div>
            </div>
        @endif
        @if ($homeInformationOfficer)
            <div class="media-block">
                <div class="rounded-fig-wrap">
                    <a href="{{ '/home/home/' . $homeInformationOfficer->home_url }}">
                        <img src="{{ asset('uploads/pages/' . $homeInformationOfficer->home_attachment) }}" alt="" />
                    </a>
                </div>
                <div class="media-desc">
                    <a href="{{ '/home/home/' . $homeInformationOfficer->home_url }}">
                        <h5>{{ $languageId == 1 ? $homeInformationOfficer->data_1 : $homeInformationOfficer->data_2 }}
                        </h5>
                        <p class="fw-bold">{!! $homeInformationOfficer->home_heading !!}</p>
                        @if ($homeInformationOfficer->data_3)
                            <p><i class="fa fa-phone-alt me-2"></i> {{ strtr((string) $homeInformationOfficer->data_3, $englishDigits) }}
                            </p>
                        @endif
                        @if ($homeInformationOfficer->data_4)
                            <p><i class="far fa-envelope me-2"></i> {{ $homeInformationOfficer->data_4 }}
                            </p>
                        @endif
                    </a>
                </div>
            </div>
        @endif
    @endif

    @if (isset($is_home) && $is_home && !empty($homeGrievanceOfficer))
        @php
            $grievanceName = (int) $languageId === 1
                ? $homeGrievanceOfficer->data_1
                : $homeGrievanceOfficer->data_2;
            $grievanceEmail = \App\Models\Configuration::getValueFromName('contact_grs_bcc_email');
        @endphp
        <div class="media-block">
            <div class="rounded-fig-wrap">
                <a href="{{ '/home/home/' . $homeGrievanceOfficer->home_url }}">
                    <img src="{{ asset('uploads/pages/' . $homeGrievanceOfficer->home_attachment) }}"
                        alt="{{ $homeGrievanceOfficer->home_heading }}" />
                </a>
            </div>
            <div class="media-desc">
                <a href="{{ '/home/home/' . $homeGrievanceOfficer->home_url }}">
                    <h5>{{ $grievanceName ?: $homeGrievanceOfficer->home_heading }}</h5>
                </a>
                @if ($grievanceName)
                    <p class="fw-bold">{{ $homeGrievanceOfficer->home_heading }}</p>
                @endif
                @if ($homeGrievanceOfficer->data_3)
                    <p><i class="fa fa-phone-alt me-2"></i>
                        <a href="tel:{{ strtr((string) $homeGrievanceOfficer->data_3, $englishDigits) }}">{{ strtr((string) $homeGrievanceOfficer->data_3, $englishDigits) }}</a>
                    </p>
                @endif
                @if ($grievanceEmail)
                    <p><i class="far fa-envelope me-2"></i>
                        <a href="mailto:{{ $grievanceEmail }}">{{ $grievanceEmail }}</a>
                    </p>
                @endif
            </div>
        </div>
    @endif
</div>
