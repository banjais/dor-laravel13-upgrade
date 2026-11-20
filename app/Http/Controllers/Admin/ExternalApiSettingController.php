<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatbotIntegration;
use App\Models\ChatbotSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class ExternalApiSettingController extends Controller
{
    private function ensureSuperAdmin(): void
    {
        abort_unless(Auth::check() && (int) Auth::user()->user_type_id === 2, 403);
    }

    private function ensureOtpVerified(): void
    {
        $this->ensureSuperAdmin();
        abort_unless(
            (int) session('chatbot_api_settings_verified_until', 0) > now()->timestamp,
            403,
            'Please verify your password and OTP before accessing External API Settings.'
        );
    }

    public function index()
    {
        $this->ensureSuperAdmin();

        if ((int) session('chatbot_api_settings_verified_until', 0) <= now()->timestamp) {
            return view('chatbot_settings.admin.external_api_verify', [
                'otpSent' => (bool) session('chatbot_api_settings_otp_sent', false),
            ]);
        }

        $settings = ChatbotSetting::current();
        $integrations = ChatbotIntegration::all()->keyBy('integration_code');

        return view('chatbot_settings.admin.external_api', compact('settings', 'integrations'));
    }

    public function confirmPassword(Request $request)
    {
        $this->ensureSuperAdmin();

        $request->validate([
            'username' => ['required', 'string', 'max:150'],
            'password' => ['required', 'string', 'max:500'],
        ]);

        $user = Auth::user();

        abort_unless(
            hash_equals((string) $user->username, (string) $request->input('username')) &&
            Hash::check($request->input('password'), $user->password),
            422,
            'Username or password is incorrect.'
        );

        $key = 'chatbot-api-otp:' . $user->id;
        abort_if(RateLimiter::tooManyAttempts($key, 3), 429, 'Please wait before requesting another OTP.');
        RateLimiter::hit($key, 300);

        $recipient = config('chatbot.api_settings_otp_recipient');
        abort_unless(
            is_string($recipient) && filter_var($recipient, FILTER_VALIDATE_EMAIL),
            503,
            'The private OTP recipient is not configured on the server.'
        );

        $otp = (string) random_int(100000, 999999);

        DB::table('password_reset_otps')
            ->where('user_id', $user->id)
            ->whereNull('used_at')
            ->update(['used_at' => now(), 'updated_at' => now()]);

        DB::table('password_reset_otps')->insert([
            'user_id' => $user->id,
            'otp_hash' => Hash::make($otp),
            'expires_at' => now()->addMinutes(5),
            'attempts' => 0,
            'used_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            Mail::mailer('gmail')->raw(
                'Your DoR External API Settings verification code is: ' . $otp . '\nThis code expires in 5 minutes.',
                function ($message) use ($recipient) {
                    $message->to($recipient)->subject('DoR External API Settings OTP');
                }
            );
        } catch (\Throwable $e) {
            DB::table('password_reset_otps')
                ->where('user_id', $user->id)
                ->whereNull('used_at')
                ->update(['used_at' => now(), 'updated_at' => now()]);
            throw $e;
        }

        session([
            'chatbot_api_settings_otp_sent' => true,
            'chatbot_api_settings_verified_until' => 0,
        ]);

        return redirect()->route('admin.external-api-settings.index')
            ->with('success', 'OTP sent to the server-configured private email address.');
    }

    public function verifyOtp(Request $request)
    {
        $this->ensureSuperAdmin();

        $request->validate(['otp' => ['required', 'digits:6']]);

        $userId = Auth::id();
        $result = DB::transaction(function () use ($request, $userId) {
            $record = DB::table('password_reset_otps')
                ->where('user_id', $userId)
                ->whereNull('used_at')
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();

            if (!$record || now()->greaterThan($record->expires_at) || (int) $record->attempts >= 5) {
                return false;
            }

            if (!Hash::check($request->input('otp'), $record->otp_hash)) {
                DB::table('password_reset_otps')->where('id', $record->id)
                    ->increment('attempts', 1, ['updated_at' => now()]);
                return false;
            }

            DB::table('password_reset_otps')->where('id', $record->id)
                ->update(['used_at' => now(), 'updated_at' => now()]);

            return true;
        });

        if (!$result) {
            return back()->withErrors(['otp' => 'Invalid, expired, or already-used OTP.']);
        }

        session([
            'chatbot_api_settings_verified_until' => now()->addMinutes(15)->timestamp,
            'chatbot_api_settings_otp_sent' => false,
        ]);

        return redirect()->route('admin.external-api-settings.index')
            ->with('success', 'External API Settings access verified for 15 minutes.');
    }

    public function update(Request $request)
    {
        $this->ensureOtpVerified();

        $validated = $request->validate([
            'external_api_enabled' => ['nullable', 'boolean'],
            'primary_enabled' => ['nullable', 'boolean'],
            'primary_provider' => ['nullable', 'in:gemini,groq,cerebras,mistral,openai,azure,custom'],
            'primary_model' => ['nullable', 'string', 'max:150'],
            'primary_base_url' => ['nullable', 'url', 'max:500'],
            'primary_api_key' => ['nullable', 'string', 'max:5000'],
            'clear_primary_api_key' => ['nullable', 'boolean'],
            'secondary_enabled' => ['nullable', 'boolean'],
            'secondary_provider' => ['nullable', 'in:gemini,groq,cerebras,mistral,openai,azure,custom'],
            'secondary_model' => ['nullable', 'string', 'max:150'],
            'secondary_base_url' => ['nullable', 'url', 'max:500'],
            'secondary_api_key' => ['nullable', 'string', 'max:5000'],
            'clear_secondary_api_key' => ['nullable', 'boolean'],
            'fallback_enabled' => ['nullable', 'boolean'],
            'fallback_provider' => ['nullable', 'in:gemini,groq,cerebras,mistral,openai,azure,custom'],
            'fallback_model' => ['nullable', 'string', 'max:150'],
            'fallback_base_url' => ['nullable', 'url', 'max:500'],
            'fallback_api_key' => ['nullable', 'string', 'max:5000'],
            'clear_fallback_api_key' => ['nullable', 'boolean'],
            'appsheet_url' => ['nullable', 'url', 'max:2000'],
            'google_sheet_url' => ['nullable', 'url', 'max:2000'],
            'integrations' => ['nullable', 'array'],
            'integrations.*' => ['array'],
            'integrations.*.enabled' => ['nullable', 'boolean'],
            'integrations.*.credential' => ['nullable', 'string', 'max:5000'],
            'integrations.*.clear_credential' => ['nullable', 'boolean'],
            'integrations.*.url' => ['nullable', 'url', 'max:2000'],
            'integrations.appsheet.app_id' => ['nullable', 'string', 'max:255'],
            'integrations.appsheet.table_name' => ['nullable', 'string', 'max:100'],
        ]);

        $settings = ChatbotSetting::current();

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $validated, $settings) {
            foreach (['external_api_enabled', 'primary_enabled', 'secondary_enabled', 'fallback_enabled'] as $field) {
                $settings->{$field} = $request->boolean($field);
            }

            foreach (['primary', 'secondary', 'fallback'] as $slot) {
                foreach (['provider', 'model', 'base_url'] as $field) {
                    $key = $slot . '_' . $field;
                    if (array_key_exists($key, $validated)) {
                        $settings->{$key} = $validated[$key];
                    }
                }

                $keyField = $slot . '_api_key';
                if ($request->boolean('clear_' . $keyField)) {
                    $settings->{$keyField} = null;
                } elseif (filled($validated[$keyField] ?? null)) {
                    $settings->{$keyField} = $validated[$keyField];
                }
            }

            $settings->updated_by = Auth::id();
            $settings->save();

            $definitions = [
                'open_meteo' => ['Open-Meteo', 'weather'],
                'overpass' => ['OpenStreetMap Overpass', 'mapping'],
                'google_maps' => ['Google Maps', 'mapping'],
                'openweathermap' => ['OpenWeatherMap', 'weather'],
                'tomtom' => ['TomTom', 'mapping'],
                'waze_partner' => ['Waze Partner', 'traffic'],
                'google_sheets' => ['Google Sheets', 'data'],
                'appsheet' => ['AppSheet Contact Directory', 'directory'],
            ];

            foreach ($definitions as $code => [$displayName, $type]) {
                $input = $validated['integrations'][$code] ?? null;
                if (! is_array($input)) {
                    continue;
                }

                $integration = ChatbotIntegration::firstOrNew(['integration_code' => $code]);
                if (! $integration->exists) {
                    $integration->created_by = Auth::id();
                }

                $integration->display_name = $displayName;
                $integration->integration_type = $type;
                $integration->is_enabled = (bool) ($input['enabled'] ?? false);

                $credentials = $integration->credentials ?? [];
                $credentialName = $code === 'appsheet' ? 'ApplicationAccessKey' : 'secret';

                if (! empty($input['clear_credential'])) {
                    unset($credentials[$credentialName]);
                } elseif (filled($input['credential'] ?? null)) {
                    $credentials[$credentialName] = $input['credential'];
                }

                $integration->credentials = $credentials ?: null;
                $integrationSettings = $integration->settings ?? [];

                if (in_array($code, ['waze_partner', 'google_sheets', 'appsheet'], true)
                    && array_key_exists('url', $input)) {
                    $integrationSettings['url'] = $input['url'];
                }

                if ($code === 'appsheet') {
                    if (array_key_exists('app_id', $input)) {
                        $integrationSettings['app_id'] = $input['app_id'];
                    }
                    $integrationSettings['table_name'] = 'Contact Directory';
                }

                $integration->settings = $integrationSettings;
                $integration->updated_by = Auth::id();
                $integration->save();
            }

            if (array_key_exists('appsheet_url', $validated)) {
                $settings->appsheet_url = $validated['appsheet_url'];
                $settings->save();
            }
            if (array_key_exists('google_sheet_url', $validated)) {
                $settings->google_sheet_url = $validated['google_sheet_url'];
                $settings->save();
            }
        });

        return redirect()->route('admin.external-api-settings.index')
            ->with('success', 'External API and integration settings saved securely.');
    }

    public function testIntegration(Request $request)
    {
        $this->ensureOtpVerified();

        $validated = $request->validate([
            'integration_code' => ['required', 'in:open_meteo,overpass,google_maps,openweathermap,tomtom,waze_partner,google_sheets,appsheet'],
        ]);

        $integration = ChatbotIntegration::where(
            'integration_code',
            $validated['integration_code']
        )->firstOrFail();

        if (! $integration->is_enabled) {
            return redirect()->route('admin.external-api-settings.index')
                ->with('error', 'Enable and save this integration before testing.');
        }

        $failureReason = null;

        try {
            $client = Http::withUserAgent('DoR-Chatbot/1.0 (+https://dor.gov.np)')->connectTimeout(4)
                ->timeout(8)
                ->withoutRedirecting();

            if ($validated['integration_code'] === 'open_meteo') {
                $response = $client->acceptJson()->get(
                    'https://api.open-meteo.com/v1/forecast',
                    [
                        'latitude' => 27.7172,
                        'longitude' => 85.3240,
                        'current' => 'temperature_2m',
                    ]
                );

                if (! $response->successful() || ! is_array($response->json('current'))) {
                    throw new \RuntimeException('Connection test failed.');
                }
            } elseif ($validated['integration_code'] === 'overpass') {
                $response = $client->get('https://overpass-api.de/api/status');

                if (! $response->successful()) {
                    throw new \RuntimeException('Connection test failed.');
                }
            } elseif ($validated['integration_code'] === 'google_maps') {
                $apiKey = $integration->credentials['secret'] ?? null;
                if (! filled($apiKey)) {
                    throw new \RuntimeException('Google Maps API key is not configured.');
                }
                $response = $client->acceptJson()->get('https://maps.googleapis.com/maps/api/geocode/json', [
                    'address' => 'Kathmandu, Nepal', 'key' => $apiKey,
                ]);
                $status = $response->json('status', 'HTTP_' . $response->status());
                if (! $response->successful() || $status !== 'OK') {
                    $failureReason = 'Google Maps: ' . $status;
                    $message = strip_tags((string) $response->json('error_message', ''));
                    if ($message !== '') $failureReason .= ' — ' . mb_substr($message, 0, 220);
                    throw new \RuntimeException('Google Maps API test failed.');
                }
            } elseif ($validated['integration_code'] === 'openweathermap') {
                $apiKey = $integration->credentials['secret'] ?? null;
                if (! filled($apiKey)) throw new \RuntimeException('OpenWeatherMap API key is not configured.');
                $response = $client->acceptJson()->get('https://api.openweathermap.org/data/2.5/weather', [
                    'q' => 'Kathmandu,NP', 'appid' => $apiKey,
                ]);
                if (! $response->successful() || ! is_array($response->json('main'))) {
                    $failureReason = 'OpenWeatherMap: HTTP ' . $response->status() . ' — ' .
                        mb_substr((string) $response->json('message', 'API request failed'), 0, 180);
                    throw new \RuntimeException('OpenWeatherMap test failed.');
                }
            } elseif ($validated['integration_code'] === 'tomtom') {
                $apiKey = $integration->credentials['secret'] ?? null;
                if (! filled($apiKey)) throw new \RuntimeException('TomTom API key is not configured.');
                $response = $client->acceptJson()->get('https://api.tomtom.com/search/2/geocode/Kathmandu.json', [
                    'key' => $apiKey, 'limit' => 1,
                ]);
                if (! $response->successful() || ! is_array($response->json('results'))) {
                    $failureReason = 'TomTom: HTTP ' . $response->status();
                    throw new \RuntimeException('TomTom test failed.');
                }
            } elseif ($validated['integration_code'] === 'waze_partner') {
                $url = $integration->settings['url'] ?? null;
                if (! is_string($url) || ! str_starts_with($url, 'https://')) {
                    throw new \RuntimeException('Waze Partner HTTPS feed URL is not configured.');
                }
                $host = strtolower((string) parse_url($url, PHP_URL_HOST));
                $ip = filter_var($host, FILTER_VALIDATE_IP);
                if (! $host || $ip || in_array($host, ['localhost'], true) ||
                    str_ends_with($host, '.local') || str_ends_with($host, '.internal')) {
                    throw new \RuntimeException('Waze feed URL host is not allowed.');
                }
                $response = $client->get($url);
                $body = trim($response->body());
                if (! $response->successful() || $body === '' ||
                    (! str_contains(strtolower($response->header('Content-Type', '')), 'json') &&
                     ! str_contains(strtolower($response->header('Content-Type', '')), 'xml'))) {
                    $failureReason = 'Waze Partner feed: HTTP ' . $response->status() . ' or unsupported feed format.';
                    throw new \RuntimeException('Waze feed test failed.');
                }
            } elseif ($validated['integration_code'] === 'google_sheets') {
                $url = $integration->settings['url'] ?? null;
                if (! is_string($url) || ! str_starts_with($url, 'https://') ||
                    ! preg_match('~docs\.google\.com/spreadsheets/d/.+/(export\?format=csv|pub\?output=csv)~i', $url)) {
                    throw new \RuntimeException('Google Sheets must be a publicly accessible CSV export URL.');
                }
                $response = $client->get($url);
                $body = trim($response->body());
                if (! $response->successful() || $body === '' ||
                    ! str_contains($body, ',') || str_contains(strtolower(substr($body, 0, 200)), '<html')) {
                    $failureReason = 'Google Sheets CSV: HTTP ' . $response->status() . ' or CSV data unavailable.';
                    throw new \RuntimeException('Google Sheets test failed.');
                }
            } else {
                $apiKey = $integration->credentials['secret'] ?? null;
                $appId = $integration->settings['app_id'] ?? null;
                $tableName = $integration->settings['table_name'] ?? null;
                if (! filled($apiKey) || ! filled($appId) || ! filled($tableName)) {
                    throw new \RuntimeException('AppSheet requires its Access Key, App ID and Table Name.');
                }
                $response = $client->acceptJson()->withHeaders([
                    'ApplicationAccessKey' => $apiKey,
                ])->post('https://www.appsheet.com/api/v2/apps/' . rawurlencode($appId) .
                    '/tables/' . rawurlencode($tableName) . '/Action', [
                        'Action' => 'Find',
                        'Properties' => ['Locale' => 'en-US', 'Timezone' => 'Asia/Kathmandu'],
                        'Rows' => [],
                    ]);
                if (! $response->successful()) {
                    $failureReason = 'AppSheet API: HTTP ' . $response->status();
                    throw new \RuntimeException('AppSheet API test failed.');
                }
            }

            $integration->forceFill([
                'health_status' => 'healthy',
                'last_tested_at' => now(),
                'last_success_at' => now(),
                'last_error' => null,
                'updated_by' => Auth::id(),
            ])->save();

            return redirect()->to(
                route('admin.external-api-settings.index') . '#integration-' . $integration->integration_code
            );
        } catch (\Throwable) {
            $integration->forceFill([
                'health_status' => 'unavailable',
                'last_tested_at' => now(),
                'last_error' => $failureReason ?? 'Connection test failed. Check service availability and try again.',
                'updated_by' => Auth::id(),
            ])->save();

            return redirect()->to(
                route('admin.external-api-settings.index') . '#integration-' . $integration->integration_code
            );
        }
    }


}
