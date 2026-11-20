<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ConfigurationController;
use App\Models\ChatbotSetting;
use App\Models\ChatbotIntegration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ChatbotSettingController extends Controller
{
    private function ensureSuperAdmin(): void
    {
        abort_unless(
            Auth::check() && (int) Auth::user()->user_type_id === 2,
            403
        );
    }

    public function index()
    {
        $this->ensureSuperAdmin();

        $settings = ChatbotSetting::current();
        $integrations = ChatbotIntegration::query()
            ->get()
            ->keyBy('integration_code');
        $site_name = ConfigurationController::site_name();
        $abs_site_url = ConfigurationController::abs_site_url();

        return view('chatbot_settings.admin.index', [
            'settings' => $settings,
            'integrations' => $integrations,
            'hasApiKey' => filled($settings->api_key),
            'site_name' => $site_name,
            'abs_site_url' => $abs_site_url,
        ]);
    }

    public function update(Request $request)
    {
        $this->ensureSuperAdmin();

        $validated = $request->validate([
            'enabled' => ['nullable', 'boolean'],
            'bot_name' => ['required', 'string', 'max:100'],
            'welcome_message' => ['nullable', 'string', 'max:2000'],
            'offline_message' => ['nullable', 'string', 'max:2000'],
            'response_language' => ['required', 'in:both,en,ne'],
            'position' => ['required', 'in:bottom_right,bottom_left'],
            'panel_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'header_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'user_message_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'bot_message_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'text_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'website_base_url' => ['required', 'url', 'max:2048'],
            'website_index_enabled' => ['nullable', 'boolean'],
            'internal_ai_enabled' => ['nullable', 'boolean'],
            'hardware_mode' => ['required', 'in:cpu,gpu,auto'],
            'gpu_enabled' => ['nullable', 'boolean'],
            'gpu_server_url' => ['nullable', 'url', 'max:2048'],
            'gpu_model' => ['nullable', 'string', 'max:100'],
            'supported_by_name' => ['nullable', 'string', 'max:150'],
            'supported_by_email' => ['nullable', 'email', 'max:254'],
            'supported_by_visible' => ['nullable', 'boolean'],
            'provider_info_visible' => ['nullable', 'boolean'],

            'cache_enabled' => ['nullable', 'boolean'],
            'cache_exact_match_enabled' => ['nullable', 'boolean'],
            'cache_ttl_minutes' => ['required', 'integer', 'between:1,10080'],

            'api_key' => ['nullable', 'string', 'max:5000'],
            'clear_api_key' => ['nullable', 'boolean'],
            'voice_enabled' => ['nullable', 'boolean'],
            'speech_input_enabled' => ['nullable', 'boolean'],
            'voice_output_enabled' => ['nullable', 'boolean'],
            'voice_type' => ['required', 'in:male,female,custom'],
            'custom_voice' => ['nullable', 'string', 'max:100'],
            'voice_speed' => ['required', 'numeric', 'between:0.50,2.00'],
        ], [
            'required' => 'Please fill out this required field.',
            'response_language.required' => 'Please select a response language.',
            'response_language.in' => 'Please select a valid response language.',
            'position.required' => 'Please select a chat window position.',
            'position.in' => 'Please select a valid chat window position.',
            'ai_provider.required' => 'Please select an AI provider.',
            'ai_provider.in' => 'Please select a valid AI provider.',
            'api_format.required' => 'Please select an API format.',
            'hardware_mode.required' => 'Please select an AI hardware mode.',
            'hardware_mode.in' => 'Please select a valid hardware mode.',
            'voice_type.required' => 'Please select a voice type.',
            'voice_type.in' => 'Please select a valid voice type.',
            'panel_color.regex' => 'Please select a valid chat window background colour.',
            'header_color.regex' => 'Please select a valid header background colour.',
            'user_message_color.regex' => 'Please select a valid user message colour.',
            'bot_message_color.regex' => 'Please select a valid bot message colour.',
            'text_color.regex' => 'Please select a valid text colour.',
            'url' => 'Please enter a valid URL.',
            'supported_by_email.email' => 'Please enter a valid contact email address.',
            'cache_ttl_minutes.integer' => 'Please enter the cache duration as a whole number.',
            'cache_ttl_minutes.between' => 'Cache duration must be between 1 and 10080 minutes.',
            'voice_speed.numeric' => 'Please enter a valid voice speed.',
            'voice_speed.between' => 'Voice speed must be between 0.50 and 2.00.',
        ]);

        $settings = ChatbotSetting::current();

        unset(
            $validated['api_key'],
            $validated['clear_api_key'],
        );

        foreach ([
            'enabled',
            'voice_enabled',
            'speech_input_enabled',
            'voice_output_enabled',
            'website_index_enabled',
            'internal_ai_enabled',
            'gpu_enabled',
            'supported_by_visible',
            'provider_info_visible',
            'cache_enabled',
            'cache_exact_match_enabled',
        ] as $field) {
            $validated[$field] = $request->boolean($field);
        }

        $validated['updated_by'] = Auth::id();

        if ($request->boolean('clear_api_key')) {
            $settings->api_key = null;
        } elseif ($request->filled('api_key')) {
            $settings->api_key = $request->input('api_key');
        }

        $settings->fill($validated);
        $settings->save();

        return redirect()
            ->route('admin.chatbot-settings.index')
            ->with('success', 'Chatbot updated successfully.');
    }
}
