@extends('layout.client.container_2021')

@section('more_css')
<style>
    .dor-calendar { max-width: 1050px; margin: 25px auto 40px; padding: 20px; background: #fff; border: 1px solid #e5e9ef; border-radius: 8px; }
    .dor-calendar h2 { color: #142D4E; font-size: 24px; margin: 0 0 18px; }
    .dor-calendar-nav { display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap; margin-bottom: 18px; }
    .dor-calendar-nav a { color: #1769AA; text-decoration: none; font-weight: 600; padding: 8px 12px; border: 1px solid #dce5ee; border-radius: 5px; }
    .dor-calendar-nav a:hover { background: #1769AA; color: #fff; }
    .dor-calendar-types { display: flex; align-items: center; gap: 5px; flex-wrap: wrap; margin-bottom: 16px; }
    .dor-calendar-select {
        width: 48px; min-width: 48px; max-width: 48px; height: 24px;
        margin: 0; padding: 1px 12px 1px 2px;
        border: 0; border-radius: 0; background: transparent;
        color: #142D4E; box-shadow: none; font-family: inherit;
        font-size: 10px; font-weight: 700; line-height: 1.2; cursor: pointer;
    }
    .dor-calendar-select:focus { outline: 1px solid rgba(23, 105, 170, .35); outline-offset: 1px; }
    .dor-calendar-source-links { display: inline-flex; align-items: center; gap: 3px; }
    .dor-calendar-source-links a {
        display: inline-flex; align-items: center; justify-content: center;
        width: 24px; height: 24px; border: 0; border-radius: 3px;
        background: transparent; color: #1769AA; text-decoration: none;
        font-size: 14px; font-weight: 700; line-height: 1;
    }
    .dor-calendar-source-links a:hover, .dor-calendar-source-links a:focus {
        background: rgba(23, 105, 170, .10); color: #142D4E; outline: none;
    }
    .dor-calendar-grid { display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); border-top: 1px solid #e5e9ef; border-left: 1px solid #e5e9ef; }
    .dor-calendar-grid > div { min-height: 72px; padding: 10px 5px; text-align: center; border-right: 1px solid #e5e9ef; border-bottom: 1px solid #e5e9ef; }
    .dor-calendar-grid .weekday { min-height: auto; background: #f2f5f8; color: #142D4E; font-weight: 700; }
    .dor-calendar-grid .today { background: #e8f2fc; color: #075da5; font-weight: 700; outline: 2px solid #1769AA; outline-offset: -2px; }
    .dor-calendar-empty { background: #fafbfc; }
    .dor-calendar-message { padding: 25px; background: #f7f9fb; border-left: 4px solid #1769AA; color: #394b5c; }
    @media (max-width: 600px) {
        .dor-calendar { margin: 12px 8px 25px; padding: 12px; }
        .dor-calendar h2 { font-size: 20px; }
        .dor-calendar-grid > div { min-height: 52px; padding: 7px 2px; font-size: 13px; }
        .dor-calendar-grid .weekday { font-size: 11px; }
    }
</style>
@endsection

@section('content')
@php
    $isBs = $type === 'bs';
    $isNs = $type === 'ns';

    $bsMonthsNe = ['बैशाख', 'जेठ', 'असार', 'साउन', 'भदौ', 'असोज', 'कार्तिक', 'मंसिर', 'पुष', 'माघ', 'फागुन', 'चैत'];
    $bsMonthsEn = ['Baisakh', 'Jestha', 'Ashadh', 'Shrawan', 'Bhadra', 'Ashwin', 'Kartik', 'Mangsir', 'Poush', 'Magh', 'Falgun', 'Chaitra'];
    $adMonthsEn = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    $adMonthsNe = ['जनवरी', 'फेब्रुअरी', 'मार्च', 'अप्रिल', 'मे', 'जुन', 'जुलाई', 'अगस्ट', 'सेप्टेम्बर', 'अक्टोबर', 'नोभेम्बर', 'डिसेम्बर'];

    $weekdaysEn = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    $weekdaysNe = ['आइत', 'सोम', 'मङ्गल', 'बुध', 'बिहि', 'शुक्र', 'शनि'];

    $calendarMonth = $calendarData['month'] ?? 1;
    $calendarYear = $calendarData['year'] ?? now('Asia/Kathmandu')->year;
    $calendarFirstWeekday = $calendarData['firstWeekday'] ?? 0;

    $nsMonthsNe = ['कछलाथ्व', 'थिंलाथ्व', 'प्वन्हेलाथ्व', 'सिल्लाथ्व', 'चिल्लाथ्व', 'चौलाथ्व', 'बछलाथ्व', 'तछलाथ्व', 'दिल्लाथ्व', 'गुंलाथ्व', 'यँलाथ्व', 'कौलाथ्व'];
    $nsMonthsEn = ['Kachhalā', 'Thinlā', 'Pwanhelā', 'Sillā', 'Chillā', 'Chaulā', 'Bachhalā', 'Tachhalā', 'Dillā', 'Gunlā', 'Yanlā', 'Kaulā'];

    $monthName = $isNs
        ? ($languageId === 1 ? $nsMonthsNe[$calendarMonth - 1] : $nsMonthsEn[$calendarMonth - 1])
        : ($isBs
            ? ($languageId === 1 ? $bsMonthsNe[$calendarMonth - 1] : $bsMonthsEn[$calendarMonth - 1])
            : ($languageId === 1 ? $adMonthsNe[$calendarMonth - 1] : $adMonthsEn[$calendarMonth - 1]));

    $displayYear = $languageId === 1
        ? strtr((string) $calendarYear, ['0'=>'०','1'=>'१','2'=>'२','3'=>'३','4'=>'४','5'=>'५','6'=>'६','7'=>'७','8'=>'८','9'=>'९'])
        : $calendarYear;

    if ($isNs) {
        $previousYear = $calendarMonth === 1 ? $calendarYear - 1 : $calendarYear;
        $previousMonth = $calendarMonth === 1 ? 12 : $calendarMonth - 1;
        $nextYear = $calendarMonth === 12 ? $calendarYear + 1 : $calendarYear;
        $nextMonth = $calendarMonth === 12 ? 1 : $calendarMonth + 1;
        $canPrevious = $calendarYear > 1060 || $calendarMonth > 1;
        $canNext = $calendarYear < 1913;
    } else {
        $previous = \Carbon\Carbon::create($calendarYear, $calendarMonth, 1)->subMonth();
        $next = \Carbon\Carbon::create($calendarYear, $calendarMonth, 1)->addMonth();
        $canPrevious = $type === 'ad' || $previous->year >= 2000;
        $canNext = $type === 'ad' || $next->year <= 2089;
    }
@endphp

<div class="container">
    <div class="dor-calendar">
        <h2>{{ $calendarTitle }}</h2>

        <div class="dor-calendar-types">
            <select class="dor-calendar-select" aria-label="{{ $languageId === 1 ? 'मिति प्रणाली छान्नुहोस्' : 'Choose calendar system' }}" onchange="if (this.value) window.location.href = this.value">
                <option value="{{ route('frontend.calendar', ['subdomain' => $subdomain, 'type' => 'bs']) }}" {{ $type === 'bs' ? 'selected' : '' }}>BS</option>
                <option value="{{ route('frontend.calendar', ['subdomain' => $subdomain, 'type' => 'ad']) }}" {{ $type === 'ad' ? 'selected' : '' }}>AD</option>
                <option value="{{ route('frontend.calendar', ['subdomain' => $subdomain, 'type' => 'ns']) }}" {{ $type === 'ns' ? 'selected' : '' }}>NS</option>
            </select>
            <span class="dor-calendar-source-links" aria-label="{{ $languageId === 1 ? 'बाह्य पात्रो स्रोतहरू' : 'External calendar references' }}">
                <a href="https://nepalipatro.com.np/" target="_blank" rel="noopener noreferrer" title="{{ $languageId === 1 ? 'नेपाली पात्रो — विक्रम संवत्' : 'Nepali Patro — Bikram Sambat' }}" aria-label="{{ $languageId === 1 ? 'नेपाली पात्रो — विक्रम संवत्' : 'Nepali Patro — Bikram Sambat' }}"><i class="fas fa-external-link-alt" aria-hidden="true"></i></a>
                <a href="https://nepalipatro.com.np/" target="_blank" rel="noopener noreferrer" title="{{ $languageId === 1 ? 'नेपाली पात्रो — ईस्वी संवत्' : 'Nepali Patro — Gregorian calendar' }}" aria-label="{{ $languageId === 1 ? 'नेपाली पात्रो — ईस्वी संवत्' : 'Nepali Patro — Gregorian calendar' }}"><i class="fas fa-external-link-alt" aria-hidden="true"></i></a>
                <a href="https://trisambat.com" target="_blank" rel="noopener noreferrer" title="{{ $languageId === 1 ? 'Trisambat — नेपाल संवत्' : 'Trisambat — Nepal Sambat' }}" aria-label="{{ $languageId === 1 ? 'Trisambat — नेपाल संवत्' : 'Trisambat — Nepal Sambat' }}"><i class="fas fa-external-link-alt" aria-hidden="true"></i></a>
            </span>
        </div>

        @if($calendarUnavailable)
            <div class="dor-calendar-message">
                {{ $languageId === 1
                    ? 'नेपाल संवत् पात्रोको प्रमाणित मिति विवरण हाल उपलब्ध छैन। सही मिति सुनिश्चित भएपछि पात्रो देखाइनेछ।'
                    : 'Verified Nepal Sambat calendar data is not currently available. The calendar will be displayed when accurate date data is available.' }}
            </div>
        @else
            <div class="dor-calendar-nav">
                @if($canPrevious)
                    <a href="{{ route('frontend.calendar', ['subdomain' => $subdomain, 'type' => $type]) }}?year={{ $isNs ? $previousYear : $previous->year }}&month={{ $isNs ? $previousMonth : $previous->month }}">&#8592; {{ $languageId === 1 ? 'अघिल्लो' : 'Previous' }}</a>
                @else
                    <span></span>
                @endif

                <strong>{{ $monthName }} {{ $displayYear }}</strong>

                @if($canNext)
                    <a href="{{ route('frontend.calendar', ['subdomain' => $subdomain, 'type' => $type]) }}?year={{ $isNs ? $nextYear : $next->year }}&month={{ $isNs ? $nextMonth : $next->month }}">{{ $languageId === 1 ? 'अर्को' : 'Next' }} &#8594;</a>
                @else
                    <span></span>
                @endif
            </div>

            <div class="dor-calendar-grid">
                @foreach(($languageId === 1 ? $weekdaysNe : $weekdaysEn) as $weekday)
                    <div class="weekday">{{ $weekday }}</div>
                @endforeach

                @for($blank = 0; $blank < $calendarFirstWeekday; $blank++)
                    <div class="dor-calendar-empty"></div>
                @endfor

                @foreach($calendarData['days'] as $day)
                    <div class="{{ $day['isToday'] ? 'today' : '' }}">
                        @if($isNs)
                            <strong>{{ $day['nsDate'] }}</strong>
                            @if(!empty($day['tithiName']))
                                <small style="display:block; margin-top:4px;">{{ $day['tithiName'] }}</small>
                            @endif
                            <small style="display:block; margin-top:4px; color:#64748b;">{{ $day['date'] }}</small>
                            @if(!empty($day['holiday']))
                                <small style="display:block; color:#a32626; font-weight:700;">{{ $languageId === 1 ? 'बिदा' : 'Holiday' }}</small>
                            @endif
                            @foreach($day['events'] as $event)
                                <small style="display:block; color:#8b2635;">{{ $event['name'][$languageId === 1 ? 'ne' : 'en'] ?? '' }}</small>
                            @endforeach
                        @else
                            {{ $isBs && $languageId === 1
                                ? $day['bsDate']
                                : ($languageId === 1
                                    ? strtr((string) $day['day'], ['0'=>'०','1'=>'१','2'=>'२','3'=>'३','4'=>'४','5'=>'५','6'=>'६','7'=>'७','8'=>'८','9'=>'९'])
                                    : $day['day']) }}
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        @if($isNs && !$calendarUnavailable)
            <p style="margin-top:18px; font-size:12px; color:#64748b;">
                {{ $languageId === 1 ? 'नेपाल संवत् मिति तथा तिथि स्रोत:' : 'Nepal Sambat date and tithi source:' }}
                <a href="https://trisambat.com" target="_blank" rel="noopener noreferrer">Trisambat</a>
                · {{ $languageId === 1 ? 'बीटा स्रोत; सरकारी आधिकारिक पात्रो होइन।' : 'Beta source; not an official government calendar.' }}
            </p>
        @endif
    </div>
</div>
@endsection
