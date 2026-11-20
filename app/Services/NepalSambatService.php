<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NepalSambatService
{
    private const API_BASE = 'https://trisambat.com/api/v1';

    private static function fetchAdMonth(int $year, int $month): ?array
    {
        $cacheKey = sprintf('trisambat_ad_month_%04d_%02d', $year, $month);
        $failureKey = $cacheKey . '_failed';

        $cached = Cache::get($cacheKey);

        if (is_array($cached)) {
            return $cached;
        }

        if (Cache::get($failureKey)) {
            return null;
        }

        try {
            $host = parse_url(self::API_BASE, PHP_URL_HOST);
            $ip = gethostbyname($host);

            if (
                !$host ||
                $ip === $host ||
                filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false
            ) {
                throw new \RuntimeException('Trisambat hostname could not be resolved.');
            }

            $response = Http::acceptJson()
                ->withOptions([
                    'curl' => [
                        CURLOPT_RESOLVE => [$host . ':443:' . $ip],
                    ],
                ])
                ->connectTimeout(3)
                ->timeout(10)
                ->get(sprintf('%s/ad/%04d/%02d.json', self::API_BASE, $year, $month));

            if (!$response->successful()) {
                throw new \RuntimeException(
                    'Trisambat monthly API returned HTTP ' . $response->status()
                );
            }

            $payload = $response->json();

            if (
                !is_array($payload) ||
                !isset($payload['days']) ||
                !is_array($payload['days'])
            ) {
                throw new \RuntimeException('Trisambat monthly response is incomplete.');
            }

            Cache::put($cacheKey, $payload, now()->addHours(24));

            return $payload;
        } catch (\Throwable $e) {
            Log::warning('Nepal Sambat monthly data could not be retrieved.', [
                'year' => $year,
                'month' => $month,
                'error' => $e->getMessage(),
            ]);

            Cache::put($failureKey, true, now()->addMinutes(5));

            return null;
        }
    }

    public static function today($languageId = 1): ?array
    {
        $date = Carbon::now('Asia/Kathmandu')->toDateString();
        $cacheKey = 'nepal_sambat_v2_' . $date;

        $cached = Cache::get($cacheKey);

        if (is_array($cached)) {
            return $cached;
        }

        try {
            $today = Carbon::parse($date, 'Asia/Kathmandu');
            $payload = self::fetchAdMonth(
                (int) $today->format('Y'),
                (int) $today->format('m')
            );

            if (!$payload) {
                return null;
            }

            foreach ($payload['days'] as $day) {
                if (
                    ($day['ad'] ?? null) === $date &&
                    !empty($day['ns']['year']) &&
                    !empty($day['text']['ns']['ne']) &&
                    !empty($day['text']['ns']['en'])
                ) {
                    $result = [
                        'ne' => $day['text']['ns']['ne'],
                        'en' => $day['text']['ns']['en'],
                        'year' => (int) $day['ns']['year'],
                        'month' => (int) $day['ns']['month'],
                        'leapMonth' => (bool) ($day['ns']['leapMonth'] ?? false),
                        'paksha' => $day['ns']['paksha'] ?? null,
                        'tithi' => $day['ns']['tithi'] ?? null,
                        'date' => $day['ad'],
                    ];

                    Cache::put($cacheKey, $result, now()->addHours(26));

                    return $result;
                }
            }

            throw new \RuntimeException('Today was not found in the Trisambat monthly data.');
        } catch (\Throwable $e) {
            Log::warning('Nepal Sambat date could not be retrieved.', [
                'date' => $date,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    public static function month(int $nsYear, int $nsMonth, bool $leapMonth = false): ?array
    {
        if ($nsYear < 1060 || $nsYear > 1913 || $nsMonth < 1 || $nsMonth > 12) {
            return null;
        }

        $cacheKey = sprintf(
            'nepal_sambat_month_%d_%02d_%d',
            $nsYear,
            $nsMonth,
            $leapMonth ? 1 : 0
        );

        $cached = Cache::get($cacheKey);

        if (is_array($cached)) {
            return $cached;
        }

        $gregorianStartYear = $nsYear + 879;
        $days = [];

        // NS months can cross Gregorian month boundaries.
        // Scan October through November around the expected NS year.
        $start = Carbon::create(
            $gregorianStartYear,
            10,
            1,
            0,
            0,
            0,
            'Asia/Kathmandu'
        );

        for ($offset = 0; $offset < 14; $offset++) {
            $date = $start->copy()->addMonthsNoOverflow($offset);

            $payload = self::fetchAdMonth(
                (int) $date->format('Y'),
                (int) $date->format('m')
            );

            if (!$payload) {
                return null;
            }

            foreach ($payload['days'] as $day) {
                $ns = $day['ns'] ?? [];

                if (
                    (int) ($ns['year'] ?? 0) !== $nsYear ||
                    (int) ($ns['month'] ?? 0) !== $nsMonth ||
                    (bool) ($ns['leapMonth'] ?? false) !== $leapMonth
                ) {
                    continue;
                }

                if (
                    empty($day['ad']) ||
                    !isset($day['weekday']) ||
                    empty($day['text']['ns']['ne']) ||
                    empty($day['text']['ns']['en'])
                ) {
                    continue;
                }

                $days[] = [
                    'date' => $day['ad'],
                    'weekday' => (int) $day['weekday'],
                    'ne' => $day['text']['ns']['ne'],
                    'en' => $day['text']['ns']['en'],
                    'tithiNe' => $day['text']['tithi']['ne'] ?? null,
                    'tithiEn' => $day['text']['tithi']['en'] ?? null,
                    'paksha' => $ns['paksha'] ?? null,
                    'tithi' => $ns['tithi'] ?? null,
                    'holiday' => (bool) ($day['holiday'] ?? false),
                    'events' => $day['events'] ?? [],
                    'provisional' => (bool) ($day['provisional'] ?? false),
                ];
            }
        }

        if (empty($days)) {
            return null;
        }

        usort($days, fn ($a, $b) => strcmp($a['date'], $b['date']));

        $result = [
            'year' => $nsYear,
            'month' => $nsMonth,
            'leapMonth' => $leapMonth,
            'days' => $days,
        ];

        Cache::put($cacheKey, $result, now()->addHours(24));

        return $result;
    }
}
