@extends('layout.admin.masters')

@section('content')
<div id="main-content" class="chatbot-admin-compact">
    <div class="container-fluid">
        @include('chatbot_settings.admin.partials.compact_styles')

        <h3>External API Settings</h3>
        <p>Provider configuration is restricted to verified Super Admin sessions.</p>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('admin.external-api-settings.update') }}">
            @csrf

            <div class="form-group">
                <label>
                    <input type="checkbox" name="external_api_enabled" value="1"
                        {{ old('external_api_enabled', $settings->external_api_enabled) ? 'checked' : '' }}>
                    Enable External AI Providers
                </label>
            </div>

            @foreach (['primary' => 'Primary Provider', 'secondary' => 'Secondary Provider', 'fallback' => 'Fallback Provider'] as $slot => $label)
                <section class="card mb-3">
                    <div class="card-body">
                        <h4>{{ $label }}</h4>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="{{ $slot }}_enabled" value="1"
                                    {{ old($slot . '_enabled', $settings->{$slot . '_enabled'}) ? 'checked' : '' }}>
                                Enable this provider
                            </label>
                        </div>
                        <div class="form-group">
                            <label>Provider</label>
                            <select class="form-control" name="{{ $slot }}_provider">
                                <option value="">Select provider</option>
                                @foreach (['gemini' => 'Google Gemini', 'groq' => 'Groq', 'cerebras' => 'Cerebras', 'mistral' => 'Mistral', 'openai' => 'OpenAI', 'azure' => 'Azure OpenAI', 'custom' => 'Custom OpenAI-compatible'] as $value => $name)
                                    <option value="{{ $value }}" {{ old($slot . '_provider', $settings->{$slot . '_provider'}) === $value ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Model</label>
                            <input class="form-control" name="{{ $slot }}_model" maxlength="150"
                                value="{{ old($slot . '_model', $settings->{$slot . '_model'}) }}">
                        </div>
                        <div class="form-group">
                            <label>Base URL (optional)</label>
                            <input type="url" class="form-control" name="{{ $slot }}_base_url"
                                value="{{ old($slot . '_base_url', $settings->{$slot . '_base_url'}) }}">
                        </div>
                        <div class="form-group">
                            <label>API Key</label>
                            <input type="password" class="form-control" name="{{ $slot }}_api_key"
                                value="" autocomplete="new-password"
                                placeholder="{{ $settings->{$slot . '_api_key'} ? 'Saved key retained; enter only to replace' : 'Enter API key' }}">
                            <small>Saved keys are encrypted and never displayed.</small>
                        </div>
                        @if ($settings->{$slot . '_api_key'})
                            <label>
                                <input type="checkbox" name="clear_{{ $slot }}_api_key" value="1">
                                Clear saved API key
                            </label>
                        @endif
                    </div>
                </section>
            @endforeach

            @include('chatbot_settings.admin.sections.knowledge_sources')

            <button type="submit" class="btn btn-primary">Save External API Settings</button>
        </form>
    </div>
</div>
@endsection
