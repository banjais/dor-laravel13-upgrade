<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatbotProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ChatbotInternalProviderController extends Controller
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

        $providers = ChatbotProvider::query()
            ->where('provider_type', 'internal')
            ->orderBy('priority')
            ->orderBy('id')
            ->get();

        return view('chatbot_settings.admin.internal_providers', compact('providers'));
    }

    private function validated(Request $request, ?ChatbotProvider $provider = null): array
    {
        $data = $request->validate([
            'provider_code' => [
                'required', 'string', 'max:80',
                'regex:/^[a-zA-Z0-9_-]+$/',
                Rule::unique('chatbot_providers', 'provider_code')->ignore($provider?->id),
            ],
            'provider_name' => ['required', 'string', 'max:150'],
            'model' => ['required', 'string', 'max:150'],
            'base_url' => ['required', 'url', 'max:2000'],
            'api_key' => ['nullable', 'string', 'max:5000'],
            'clear_api_key' => ['nullable', 'boolean'],
            'priority' => ['required', 'integer', 'between:1,65535'],
            'timeout_seconds' => ['required', 'integer', 'between:5,120'],
            'max_retries' => ['required', 'integer', 'between:0,3'],
            'is_enabled' => ['nullable', 'boolean'],
        ]);

        $parts = parse_url($data['base_url']);

        if (
            $parts === false ||
            empty($parts['host']) ||
            ! in_array(strtolower($parts['scheme'] ?? ''), ['http', 'https'], true) ||
            isset($parts['user']) ||
            isset($parts['pass']) ||
            isset($parts['query']) ||
            isset($parts['fragment'])
        ) {
            abort(422, 'Internal provider URL is invalid. Use an HTTP(S) base URL without credentials, query strings or fragments.');
        }

        return $data;
    }

    public function store(Request $request)
    {
        $this->ensureSuperAdmin();

        $data = $this->validated($request);

        $provider = new ChatbotProvider();
        $provider->provider_code = $data['provider_code'];
        $provider->provider_name = $data['provider_name'];
        $provider->provider_type = 'internal';
        $provider->api_format = 'openai_compatible';
        $provider->model = $data['model'];
        $provider->base_url = rtrim($data['base_url'], '/');
        $provider->api_key = filled($data['api_key'] ?? null) ? $data['api_key'] : null;
        $provider->priority = $data['priority'];
        $provider->timeout_seconds = $data['timeout_seconds'];
        $provider->max_retries = $data['max_retries'];
        $provider->is_enabled = $request->boolean('is_enabled');
        $provider->health_status = 'unknown';
        $provider->created_by = Auth::id();
        $provider->updated_by = Auth::id();
        $provider->save();

        return redirect()->route('admin.chatbot-internal-providers.index')
            ->with('success', 'Internal AI provider added successfully.');
    }

    public function update(Request $request, ChatbotProvider $provider)
    {
        $this->ensureSuperAdmin();
        abort_unless($provider->provider_type === 'internal', 404);

        $data = $this->validated($request, $provider);

        $provider->provider_code = $data['provider_code'];
        $provider->provider_name = $data['provider_name'];
        $provider->model = $data['model'];
        $provider->base_url = rtrim($data['base_url'], '/');
        $provider->priority = $data['priority'];
        $provider->timeout_seconds = $data['timeout_seconds'];
        $provider->max_retries = $data['max_retries'];
        $provider->is_enabled = $request->boolean('is_enabled');

        if ($request->boolean('clear_api_key')) {
            $provider->api_key = null;
        } elseif (filled($data['api_key'] ?? null)) {
            $provider->api_key = $data['api_key'];
        }

        $provider->health_status = 'unknown';
        $provider->last_error = null;
        $provider->last_health_check_at = null;
        $provider->updated_by = Auth::id();
        $provider->save();

        return redirect()->route('admin.chatbot-internal-providers.index')
            ->with('success', 'Internal AI provider updated successfully.');
    }

    public function destroy(ChatbotProvider $provider)
    {
        $this->ensureSuperAdmin();
        abort_unless($provider->provider_type === 'internal', 404);

        $provider->delete();

        return redirect()->route('admin.chatbot-internal-providers.index')
            ->with('success', 'Internal AI provider deleted.');
    }
}
