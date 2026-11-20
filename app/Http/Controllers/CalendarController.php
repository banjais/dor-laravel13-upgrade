<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Services\NepalSambatService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    private function nepaliDigits(string|int $value): string
    {
        return strtr((string) $value, [
            '0' => '०', '1' => '१', '2' => '२', '3' => '३',
            '4' => '४', '5' => '५', '6' => '६', '7' => '७',
            '8' => '८', '9' => '९',
        ]);
    }

    public function index(Request $request, string $subdomain, string $type)
    {
        $type = strtolower($type);

        abort_unless(in_array($type, ['bs', 'ad', 'ns'], true), 404);

        $languageId = Language::getIdFromCode();
        $sessionLangId = Session::get('language_id') ?: $languageId;
        Session::put('language_id', $sessionLangId);
        $languageId = (int) $sessionLangId;
        $today = Carbon::now('Asia/Kathmandu');

        if ($type === 'ns') {
            $nsToday = NepalSambatService::today($languageId);

            if (!$nsToday) {
                return view('home.calendar', [
                    'subdomain' => $subdomain,
                    'abs_site_url' => ConfigurationController::abs_site_url(),
                    'rel_site_url' => ConfigurationController::rel_site_url(),
                    'site_name' => ConfigurationController::site_name(),
                    'type' => $type,
                    'languageId' => $languageId,
                    'calendarUnavailable' => true,
                    'calendarTitle' => $languageId === 1
                        ? 'नेपाल संवत् पात्रो'
                        : 'Nepal Sambat Calendar',
                    'calendarData' => [],
                ]);
            }

            $year = (int) $request->query('year', $nsToday['year']);
            $month = (int) $request->query('month', $nsToday['month']);
            $leapMonth = filter_var(
                $request->query('leapMonth', $nsToday['leapMonth'] ? '1' : '0'),
                FILTER_VALIDATE_BOOLEAN
            );

            abort_if($year < 1060 || $year > 1913 || $month < 1 || $month > 12, 404);

            $nsMonthData = NepalSambatService::month($year, $month, $leapMonth);

            if (!$nsMonthData || empty($nsMonthData['days'])) {
                return view('home.calendar', [
                    'subdomain' => $subdomain,
                    'abs_site_url' => ConfigurationController::abs_site_url(),
                    'rel_site_url' => ConfigurationController::rel_site_url(),
                    'site_name' => ConfigurationController::site_name(),
                    'type' => $type,
                    'languageId' => $languageId,
                    'calendarUnavailable' => true,
                    'calendarTitle' => $languageId === 1
                        ? 'नेपाल संवत् पात्रो'
                        : 'Nepal Sambat Calendar',
                    'calendarData' => [],
                ]);
            }

            $days = [];

            foreach ($nsMonthData['days'] as $nsDay) {
                $days[] = [
                    'day' => $nsDay['tithi'] ?? '',
                    'weekday' => $nsDay['weekday'],
                    'date' => $nsDay['date'],
                    'isToday' => $nsDay['date'] === $nsToday['date'],
                    'nsDate' => $nsDay[$languageId === 1 ? 'ne' : 'en'],
                    'tithiName' => $languageId === 1
                        ? ($nsDay['tithiNe'] ?? '')
                        : ($nsDay['tithiEn'] ?? ''),
                    'paksha' => $nsDay['paksha'],
                    'holiday' => $nsDay['holiday'],
                    'events' => $nsDay['events'],
                    'provisional' => $nsDay['provisional'],
                ];
            }

            return view('home.calendar', [
                'subdomain' => $subdomain,
                'abs_site_url' => ConfigurationController::abs_site_url(),
                'rel_site_url' => ConfigurationController::rel_site_url(),
                'site_name' => ConfigurationController::site_name(),
                'type' => $type,
                'languageId' => $languageId,
                'calendarUnavailable' => false,
                'calendarTitle' => $languageId === 1
                    ? 'नेपाल संवत् पात्रो'
                    : 'Nepal Sambat Calendar',
                'calendarData' => [
                    'year' => $year,
                    'month' => $month,
                    'leapMonth' => $leapMonth,
                    'daysInMonth' => count($days),
                    'firstWeekday' => $days[0]['weekday'],
                    'today' => $nsToday['date'],
                    'days' => $days,
                ],
            ]);
        }

        $year = (int) $request->query('year', $type === 'bs'
            ? (int) strtr(
                \Calendar::eng_to_nep((int) $today->format('Y'), (int) $today->format('m'), (int) $today->format('d'))['nepali_year'],
                array_flip(['०','१','२','३','४','५','६','७','८','९'])
            )
            : $today->year
        );

        $month = (int) $request->query('month', $type === 'bs'
            ? (int) strtr(
                \Calendar::eng_to_nep((int) $today->format('Y'), (int) $today->format('m'), (int) $today->format('d'))['nepali_month'],
                array_flip(['०','१','२','३','४','५','६','७','८','९'])
            )
            : $today->month
        );

        abort_if($month < 1 || $month > 12, 404);

        if ($type === 'ad') {
            abort_if($year < 1 || $year > 9998, 404);

            $firstDay = Carbon::create($year, $month, 1, 0, 0, 0, 'Asia/Kathmandu');
            $daysInMonth = $firstDay->daysInMonth;
            $days = [];

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $date = $firstDay->copy()->day($day);

                $days[] = [
                    'day' => $day,
                    'weekday' => $date->dayOfWeek,
                    'date' => $date->toDateString(),
                    'isToday' => $date->isSameDay($today),
                ];
            }

            $calendarTitle = $languageId === 1
                ? 'अंग्रेजी पात्रो'
                : 'Gregorian Calendar';
        } else {
            abort_if($year < 2000 || $year > 2089, 404);

            $yearRow = collect(config('calender'))->first(
                fn ($row) => (int) $row[0] === $year
            );

            abort_unless($yearRow, 404);

            $daysInMonth = (int) $yearRow[$month];
            $targetYear = $this->nepaliDigits($year);
            $targetMonth = $this->nepaliDigits(str_pad((string) $month, 2, '0', STR_PAD_LEFT));

            $scanStart = Carbon::create(max(1944, $year - 57), 1, 1, 0, 0, 0, 'Asia/Kathmandu');
            $scanEnd = Carbon::create(min(2033, $year - 56), 12, 31, 0, 0, 0, 'Asia/Kathmandu');

            $firstDay = null;

            for ($date = $scanStart->copy(); $date->lte($scanEnd); $date->addDay()) {
                $result = \Calendar::eng_to_nep(
                    (int) $date->format('Y'),
                    (int) $date->format('m'),
                    (int) $date->format('d')
                );

                if (
                    ($result['type'] ?? false) &&
                    ($result['nepali_year'] ?? '') === $targetYear &&
                    ($result['nepali_month'] ?? '') === $targetMonth &&
                    ($result['nepali_date'] ?? '') === '०१'
                ) {
                    $firstDay = $date->copy();
                    break;
                }
            }

            abort_unless($firstDay, 404);

            $days = [];

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $date = $firstDay->copy()->addDays($day - 1);

                $result = \Calendar::eng_to_nep(
                    (int) $date->format('Y'),
                    (int) $date->format('m'),
                    (int) $date->format('d')
                );

                abort_unless(
                    ($result['type'] ?? false) &&
                    ($result['nepali_year'] ?? '') === $targetYear &&
                    ($result['nepali_month'] ?? '') === $targetMonth,
                    404
                );

                $days[] = [
                    'day' => $day,
                    'weekday' => $date->dayOfWeek,
                    'date' => $date->toDateString(),
                    'isToday' => $date->isSameDay($today),
                    'bsDate' => $result['nepali_date'],
                    'bsWeekday' => $result['nepali_day'],
                ];
            }

            $calendarTitle = $languageId === 1
                ? 'विक्रम संवत् पात्रो'
                : 'Bikram Sambat Calendar';
        }

        return view('home.calendar', [
            'subdomain' => $subdomain,
                'abs_site_url' => ConfigurationController::abs_site_url(),
                'rel_site_url' => ConfigurationController::rel_site_url(),
                'site_name' => ConfigurationController::site_name(),
            'type' => $type,
            'languageId' => $languageId,
            'calendarUnavailable' => false,
            'calendarTitle' => $calendarTitle,
            'calendarData' => [
                'year' => $year,
                'month' => $month,
                'daysInMonth' => $daysInMonth,
                'firstWeekday' => $days[0]['weekday'],
                'today' => $today->toDateString(),
                'days' => $days,
            ],
        ]);
    }
}
