@extends('layout.admin.masters')

@section('content')
<div id="main-content" class="chatbot-admin-compact">
    <div class="container-fluid">
        @include('chatbot_settings.admin.partials.compact_styles')

        <div class="row-fluid">
            <div class="span12">
                <h3 class="page-title">
                    Chatbot
                    <small>Configure DoR website chatbot and AI settings.</small>
                </h3>
                <ul class="breadcrumb">
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="icon-home"></i></a>
                        <span class="divider">&nbsp;</span>
                    </li>
                    <li>Chatbot</li>
                </ul>
            </div>
        </div>

        <div class="row-fluid">
            <div class="span12">
                <div class="widget">
                <div class="widget-title">
                    <h4><i class="icon-comments"></i> Chatbot</h4>
                </div>

                <div class="widget-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <strong>Please correct the following errors:</strong>
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST"
                          action="{{ route('admin.chatbot-settings.update') }}"
                          class="form-horizontal chatbot-general-form">
                        @csrf

                        <h4>1. General Settings</h4>
                        <hr>

                        <div class="control-group">
                            <label class="control-label">Enable Chatbot</label>
                            <div class="controls">
                                <input type="hidden" name="enabled" value="0">
                                <label>
                                    <input type="checkbox" name="enabled" value="1"
                                        {{ old('enabled', $settings->enabled) ? 'checked' : '' }}>
                                    Show chatbot on website
                                </label>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">Bot Name <span style="color:#b94a48; font-weight:bold;">*</span></label>
                            <div class="controls">
                                <input type="text" name="bot_name" class="span6"
                                    value="{{ old('bot_name', $settings->bot_name) }}" required>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">Welcome Message</label>
                            <div class="controls">
                                <textarea name="welcome_message" class="span8" rows="3">{{ old('welcome_message', $settings->welcome_message) }}</textarea>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">Offline Message</label>
                            <div class="controls">
                                <textarea name="offline_message" class="span8" rows="3">{{ old('offline_message', $settings->offline_message) }}</textarea>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">Response Language <span style="color:#b94a48; font-weight:bold;">*</span></label>
                            <div class="controls">
                                <select name="response_language">
                                    <option value="both" {{ old('response_language', $settings->response_language) == 'both' ? 'selected' : '' }}>Nepali and English</option>
                                    <option value="ne" {{ old('response_language', $settings->response_language) == 'ne' ? 'selected' : '' }}>Nepali only</option>
                                    <option value="en" {{ old('response_language', $settings->response_language) == 'en' ? 'selected' : '' }}>English only</option>
                                </select>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">Chat Window Position <span style="color:#b94a48; font-weight:bold;">*</span></label>
                            <div class="controls">
                                <select name="position">
                                    <option value="bottom_right" {{ old('position', $settings->position) == 'bottom_right' ? 'selected' : '' }}>Bottom Right</option>
                                    <option value="bottom_left" {{ old('position', $settings->position) == 'bottom_left' ? 'selected' : '' }}>Bottom Left</option>
                                </select>
                            </div>
                        </div>

                        <h4>2. Chatbot Colours</h4>
                        <hr>

                        @foreach([
                            'panel_color' => 'Chat Window Background',
                            'header_color' => 'Header Background',
                            'user_message_color' => 'User Message',
                            'bot_message_color' => 'Bot Message',
                            'text_color' => 'Text Colour',
                        ] as $field => $label)
                            <div class="control-group">
                                <label class="control-label">{{ $label }} <span style="color:#b94a48; font-weight:bold;">*</span></label>
                                <div class="controls">
                                    <input type="color" name="{{ $field }}"
                                        value="{{ old($field, $settings->$field) }}">
                                </div>
                            </div>
                        @endforeach

                        <h4>3. Internal DoR AI Settings</h4>
                        <hr>
                        <p class="help-block">
                            External provider selection and API credentials are managed below.
                            Internal DoR AI and hardware settings remain separate.
                        </p>

                        <div class="control-group">
                            <label class="control-label">Enable Internal DoR AI</label>
                            <div class="controls">
                                <input type="hidden" name="internal_ai_enabled" value="0">
                                <label>
                                    <input type="checkbox" name="internal_ai_enabled" value="1"
                                        {{ old('internal_ai_enabled', $settings->internal_ai_enabled) ? 'checked' : '' }}>
                                    Allow the DoR-hosted AI service to be selected
                                </label>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">AI Hardware Mode <span style="color:#b94a48; font-weight:bold;">*</span></label>
                            <div class="controls">
                                <select name="hardware_mode">
                                    <option value="cpu" {{ old('hardware_mode', $settings->hardware_mode ?: 'cpu') == 'cpu' ? 'selected' : '' }}>CPU</option>
                                    <option value="gpu" {{ old('hardware_mode', $settings->hardware_mode) == 'gpu' ? 'selected' : '' }}>NVIDIA GPU</option>
                                    <option value="auto" {{ old('hardware_mode', $settings->hardware_mode) == 'auto' ? 'selected' : '' }}>Automatic</option>
                                </select>
                                <p class="help-block">Configuration preference only. Actual hardware support must be available on the AI server.</p>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">Enable GPU Server</label>
                            <div class="controls">
                                <input type="hidden" name="gpu_enabled" value="0">
                                <label>
                                    <input type="checkbox" name="gpu_enabled" value="1"
                                        {{ old('gpu_enabled', $settings->gpu_enabled) ? 'checked' : '' }}>
                                    Enable GPU server connection when available
                                </label>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">GPU Server URL</label>
                            <div class="controls">
                                <input type="url" name="gpu_server_url" class="span8"
                                    value="{{ old('gpu_server_url', $settings->gpu_server_url) }}"
                                    placeholder="https://gpu-server.example/v1">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">GPU Model Name</label>
                            <div class="controls">
                                <input type="text" name="gpu_model" class="span6"
                                    value="{{ old('gpu_model', $settings->gpu_model) }}"
                                    placeholder="e.g. NVIDIA GPU model">
                            </div>
                        </div>

                        <h4>4. Provider Information</h4>
                        <hr>
                        <div class="control-group">
                            <label class="control-label">Show AI Provider Information</label>
                            <div class="controls">
                                <input type="hidden" name="provider_info_visible" value="0">
                                <label>
                                    <input type="checkbox" name="provider_info_visible" value="1"
                                        {{ old('provider_info_visible', $settings->provider_info_visible ?? false) ? 'checked' : '' }}>
                                    Show a small provider label (for example, Powered by Gemini) in the chatbot
                                </label>
                                <p class="help-block">When enabled, the chatbot may show the active provider name and a details link. API keys and credentials are never displayed.</p>
                            </div>
                        </div>

                        <h4>5. Shared Answer Cache</h4>
                        <hr>

                        <div class="control-group">
                            <label class="control-label">Enable Shared Cache</label>
                            <div class="controls">
                                <input type="hidden" name="cache_enabled" value="0">
                                <label>
                                    <input type="checkbox" name="cache_enabled" value="1"
                                        {{ old('cache_enabled', $settings->cache_enabled) ? 'checked' : '' }}>
                                    Reuse eligible public answers to reduce API requests
                                </label>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">Exact Question Matching</label>
                            <div class="controls">
                                <input type="hidden" name="cache_exact_match_enabled" value="0">
                                <label>
                                    <input type="checkbox" name="cache_exact_match_enabled" value="1"
                                        {{ old('cache_exact_match_enabled', $settings->cache_exact_match_enabled) ? 'checked' : '' }}>
                                    Reuse an answer only when the normalized question and language match
                                </label>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">Cache Duration (minutes) <span style="color:#b94a48; font-weight:bold;">*</span></label>
                            <div class="controls">
                                <input type="number" name="cache_ttl_minutes" min="1" max="10080"
                                    value="{{ old('cache_ttl_minutes', $settings->cache_ttl_minutes ?? 1440) }}">
                                <p class="help-block">1 to 10080 minutes (maximum 7 days). Private or user-specific answers must never be shared.</p>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">DoR Website Base URL <span style="color:#b94a48; font-weight:bold;">*</span></label>
                            <div class="controls">
                                <input type="url" name="website_base_url" class="span8"
                                    value="{{ old('website_base_url', $settings->website_base_url ?: 'https://dor.gov.np') }}"
                                    required>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">Website Indexing</label>
                            <div class="controls">
                                <input type="hidden" name="website_index_enabled" value="0">
                                <label>
                                    <input type="checkbox" name="website_index_enabled" value="1"
                                        {{ old('website_index_enabled', $settings->website_index_enabled ?? true) ? 'checked' : '' }}>
                                    Enable DoR website content indexing
                                </label>
                            </div>
                        </div>

                        <h4>6. Created / Supported By</h4>
                        <hr>

                        <div class="control-group">
                            <label class="control-label">Name</label>
                            <div class="controls">
                                <input type="text" name="supported_by_name" class="span6"
                                    value="{{ old('supported_by_name', $settings->supported_by_name) }}"
                                    maxlength="150" placeholder="Name of creator / support person">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">Contact Email</label>
                            <div class="controls">
                                <input type="email" name="supported_by_email" class="span6"
                                    value="{{ old('supported_by_email', $settings->supported_by_email) }}"
                                    maxlength="254" placeholder="support@example.com">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">Show in AI Chatbot</label>
                            <div class="controls">
                                <input type="hidden" name="supported_by_visible" value="0">
                                <label>
                                    <input type="checkbox" name="supported_by_visible" value="1"
                                        {{ old('supported_by_visible', $settings->supported_by_visible) ? 'checked' : '' }}>
                                    Display name and contact email in the public chatbot
                                </label>
                            </div>
                        </div>

                        <h4>7. Voice Settings</h4>
                        <hr>

                        @foreach([
                            'voice_enabled' => 'Enable Voice Features',
                            'speech_input_enabled' => 'Enable Speech Input',
                            'voice_output_enabled' => 'Enable Spoken Responses',
                        ] as $field => $label)
                            <div class="control-group">
                                <label class="control-label">{{ $label }}</label>
                                <div class="controls">
                                    <input type="hidden" name="{{ $field }}" value="0">
                                    <label>
                                        <input type="checkbox" name="{{ $field }}" value="1"
                                            {{ old($field, $settings->$field) ? 'checked' : '' }}>
                                        Enabled
                                    </label>
                                </div>
                            </div>
                        @endforeach

                        <div class="control-group">
                            <label class="control-label">Voice Type <span style="color:#b94a48; font-weight:bold;">*</span></label>
                            <div class="controls">
                                <select name="voice_type">
                                    <option value="female" {{ old('voice_type', $settings->voice_type) == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="male" {{ old('voice_type', $settings->voice_type) == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="custom" {{ old('voice_type', $settings->voice_type) == 'custom' ? 'selected' : '' }}>Custom</option>
                                </select>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">Custom Voice Name</label>
                            <div class="controls">
                                <input type="text" name="custom_voice" class="span6"
                                    value="{{ old('custom_voice', $settings->custom_voice) }}"
                                    placeholder="Optional provider voice name">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">Voice Speed <span style="color:#b94a48; font-weight:bold;">*</span></label>
                            <div class="controls">
                                <input type="number" name="voice_speed" min="0.50" max="2.00"
                                    step="0.05" value="{{ old('voice_speed', $settings->voice_speed) }}">
                                <span class="help-inline">0.50 to 2.00 (1.00 = normal)</span>
                            </div>
                        </div>

                        <hr>
                        <div class="form-actions">
                            <button type="button" class="btn" id="restoreChatbotDefaults">
                                Restore Defaults
                            </button>
                            <span style="margin-left:8px; color:#777;">
                                API keys are preserved. Save changes to apply.
                            </span>
                            <button type="submit" class="btn btn-primary">
                                <i class="icon-save"></i> Save Chatbot
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const geminiProfile = {
        model: 'gemini-3.8-flash',
        url: 'https://generativelanguage.googleapis.com/v1beta/openai/'
    };

    document.querySelectorAll('select[name$="_provider"]').forEach(function (select) {
        const prefix = select.name.replace(/_provider$/, '');
        const model = document.querySelector('[name="' + prefix + '_model"]');
        const url = document.querySelector('[name="' + prefix + '_base_url"]');

        function applyProviderDefaults() {
            if (select.value === 'gemini') {
                if (model) model.value = geminiProfile.model;
                if (url) url.value = geminiProfile.url;
            }
        }

        select.addEventListener('change', applyProviderDefaults);
        applyProviderDefaults();
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const button = document.getElementById('restoreChatbotDefaults');
    if (!button) return;

    button.addEventListener('click', function () {
        if (!window.confirm('Restore the default Chatbot settings? API keys will be preserved. Changes will apply only after you click Save.')) {
            return;
        }

        const form = button.closest('form');
        const defaults = {
            enabled: false,
            bot_name: 'DoR Assistant',
            welcome_message: '',
            offline_message: '',
            response_language: 'both',
            position: 'bottom_right',
            panel_color: '#FFFFFF',
            header_color: '#142D4E',
            user_message_color: '#1769AA',
            bot_message_color: '#F1F4F7',
            text_color: '#263238',
            ai_provider: 'openai',
            ai_model: 'gpt-4o-mini',
            api_base_url: '',
            api_format: 'openai_compatible',
            internal_ai_enabled: false,
            hardware_mode: 'cpu',
            gpu_enabled: false,
            gpu_server_url: '',
            gpu_model: '',
            cache_enabled: true,
            cache_exact_match_enabled: true,
            cache_ttl_minutes: 1440,
            website_base_url: 'https://dor.gov.np',
            website_index_enabled: true,
            supported_by_name: '',
            supported_by_email: '',
            supported_by_visible: false,
            provider_info_visible: false,
            voice_enabled: false,
            speech_input_enabled: false,
            voice_output_enabled: false,
            voice_type: 'female',
            custom_voice: '',
            voice_speed: '1.00',
            clear_api_key: false,
        };

        Object.entries(defaults).forEach(([name, value]) => {
            const checkbox = form.querySelector('input[type="checkbox"][name="' + name + '"]');

            if (checkbox) {
                checkbox.checked = Boolean(value);
                return;
            }

            const field = form.querySelector('[name="' + name + '"]');
            if (field) field.value = value;
        });

        window.alert('Default values restored in the form. Review them and click Save to apply.');
    });
});
</script>

@endsection
