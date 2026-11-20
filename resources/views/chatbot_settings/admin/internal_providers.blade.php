@extends('layout.admin.masters')

@section('content')
<div id="main-content" class="chatbot-admin-compact">
    <div class="container-fluid">
        @include('chatbot_settings.admin.partials.compact_styles')

        <h3>Internal AI Provider Management</h3>
        <p>Configure AI services hosted within DoR or on an approved internal server.</p>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <div class="alert alert-info">
            Internal providers are used only when Internal AI is enabled in Chatbot Settings.
            This page does not configure External AI providers or change Gemini settings.
        </div>

        <section class="well">
            <h4>Add Internal AI Provider</h4>
            <form method="POST" action="{{ route('admin.chatbot-internal-providers.store') }}">
                @csrf

                <div class="row-fluid">
                    <div class="span4">
                        <label>Provider Code</label>
                        <input class="span12" name="provider_code" required maxlength="80"
                               pattern="[A-Za-z0-9_-]+" value="{{ old('provider_code') }}"
                               placeholder="dor-internal-ai">
                    </div>
                    <div class="span4">
                        <label>Provider Name</label>
                        <input class="span12" name="provider_name" required maxlength="150"
                               value="{{ old('provider_name') }}" placeholder="DoR Internal AI">
                    </div>
                    <div class="span4">
                        <label>Model</label>
                        <input class="span12" name="model" required maxlength="150"
                               value="{{ old('model') }}" placeholder="Model name">
                    </div>
                </div>

                <label>Base URL</label>
                <input class="span12" type="url" name="base_url" required
                       value="{{ old('base_url') }}" placeholder="http://internal-ai-server:8000/v1">

                <label>API Key (optional for internal services)</label>
                <input class="span12" type="password" name="api_key"
                       autocomplete="new-password" placeholder="Enter key if required">

                <div class="row-fluid">
                    <div class="span3">
                        <label>Priority (lower runs first)</label>
                        <input class="span12" type="number" name="priority" min="1" max="65535" value="100" required>
                    </div>
                    <div class="span3">
                        <label>Timeout (seconds)</label>
                        <input class="span12" type="number" name="timeout_seconds" min="5" max="120" value="30" required>
                    </div>
                    <div class="span3">
                        <label>Max Retries</label>
                        <input class="span12" type="number" name="max_retries" min="0" max="3" value="1" required>
                    </div>
                </div>

                <input type="hidden" name="is_enabled" value="0">
                <label>
                    <input type="checkbox" name="is_enabled" value="1">
                    Enable this provider
                </label>

                <button type="submit" class="btn btn-primary">Add Internal Provider</button>
            </form>
        </section>

        <hr>
        <h4>Configured Internal Providers</h4>

        @forelse($providers as $provider)
            <section class="well" style="margin-bottom:18px;">
                <form method="POST"
                      action="{{ route('admin.chatbot-internal-providers.update', $provider->id) }}">
                    @csrf

                    <h4>{{ $provider->provider_name }}</h4>
                    <p>
                        <strong>Status:</strong>
                        {{ $provider->is_enabled ? 'Enabled' : 'Disabled' }}
                        &nbsp; | &nbsp;
                        <strong>Health:</strong> {{ $provider->health_status }}
                        &nbsp; | &nbsp;
                        <strong>API Key:</strong>
                        {{ filled($provider->api_key) ? 'Saved (encrypted)' : 'Not configured' }}
                    </p>

                    <div class="row-fluid">
                        <div class="span4">
                            <label>Provider Code</label>
                            <input class="span12" name="provider_code" required maxlength="80"
                                   pattern="[A-Za-z0-9_-]+" value="{{ $provider->provider_code }}">
                        </div>
                        <div class="span4">
                            <label>Provider Name</label>
                            <input class="span12" name="provider_name" required maxlength="150"
                                   value="{{ $provider->provider_name }}">
                        </div>
                        <div class="span4">
                            <label>Model</label>
                            <input class="span12" name="model" required maxlength="150"
                                   value="{{ $provider->model }}">
                        </div>
                    </div>

                    <label>Base URL</label>
                    <input class="span12" type="url" name="base_url" required
                           value="{{ $provider->base_url }}">

                    <label>Replace API Key</label>
                    <input class="span12" type="password" name="api_key"
                           autocomplete="new-password" value=""
                           placeholder="{{ filled($provider->api_key) ? 'Saved key retained; enter only to replace' : 'Enter key if required' }}">

                    @if(filled($provider->api_key))
                        <label>
                            <input type="checkbox" name="clear_api_key" value="1">
                            Clear saved API key
                        </label>
                    @endif

                    <div class="row-fluid">
                        <div class="span3">
                            <label>Priority</label>
                            <input class="span12" type="number" name="priority" min="1" max="65535"
                                   value="{{ $provider->priority }}" required>
                        </div>
                        <div class="span3">
                            <label>Timeout (seconds)</label>
                            <input class="span12" type="number" name="timeout_seconds" min="5" max="120"
                                   value="{{ $provider->timeout_seconds }}" required>
                        </div>
                        <div class="span3">
                            <label>Max Retries</label>
                            <input class="span12" type="number" name="max_retries" min="0" max="3"
                                   value="{{ $provider->max_retries }}" required>
                        </div>
                    </div>

                    <input type="hidden" name="is_enabled" value="0">
                    <label>
                        <input type="checkbox" name="is_enabled" value="1"
                               {{ $provider->is_enabled ? 'checked' : '' }}>
                        Enable this provider
                    </label>

                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>

                <form method="POST"
                      action="{{ route('admin.chatbot-internal-providers.destroy', $provider->id) }}"
                      onsubmit="return confirm('Delete this internal provider? Its usage logs will be retained.');"
                      style="margin-top:8px;">
                    @csrf
                    <button type="submit" class="btn btn-danger">Delete Provider</button>
                </form>
            </section>
        @empty
            <p>No Internal AI providers have been configured yet.</p>
        @endforelse

        <p><a href="{{ route('admin.chatbot-settings.index') }}">Back to Chatbot Settings</a></p>
    </div>
</div>
@endsection
