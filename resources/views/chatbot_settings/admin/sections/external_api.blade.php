<h4>4. External API Settings</h4>
                        <hr>

                        <div class="control-group">
                            <label class="control-label">Enable External APIs</label>
                            <div class="controls">
                                <input type="hidden" name="external_api_enabled" value="0">
                                <label>
                                    <input type="checkbox" name="external_api_enabled" value="1"
                                        {{ old('external_api_enabled', $settings->external_api_enabled) ? 'checked' : '' }}>
                                    Allow external AI providers (Super Admin controlled)
                                </label>
                                <p class="help-block">When disabled, no external AI provider may receive chatbot requests.</p>
                            </div>
                        </div>

                        @foreach(['primary' => 'Primary API', 'secondary' => 'Secondary API', 'fallback' => 'Fallback API'] as $slot => $slotLabel)
                            <div class="well">
                                <h4>{{ $slotLabel }}</h4>

                                <div class="control-group">
                                    <label class="control-label">Provider</label>
                                    <div class="controls">
                                        <select name="{{ $slot }}_provider">
                                            <option value="">Select provider</option>
                                            @foreach(['gemini' => 'Google Gemini', 'groq' => 'Groq', 'cerebras' => 'Cerebras', 'mistral' => 'Mistral', 'openai' => 'OpenAI', 'azure' => 'Microsoft Azure', 'custom' => 'Custom OpenAI-compatible'] as $provider => $providerLabel)
                                                <option value="{{ $provider }}" {{ old($slot . '_provider', $settings->{$slot . '_provider'}) == $provider ? 'selected' : '' }}>{{ $providerLabel }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label">Model Name (advanced providers)</label>
                                    <div class="controls">
                                        <input type="text" name="{{ $slot }}_model" class="span6"
                                            value="{{ old($slot . '_model', $settings->{$slot . '_model'}) }}"
                                            placeholder="Auto-filled for Gemini; required for custom providers">
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label">API Endpoint URL (advanced providers)</label>
                                    <div class="controls">
                                        <input type="url" name="{{ $slot }}_base_url" class="span8"
                                            value="{{ old($slot . '_base_url', $settings->{$slot . '_base_url'}) }}"
                                            placeholder="Auto-filled for Gemini; required for custom providers">
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label">API Key</label>
                                    <div class="controls">
                                        <input type="password" name="{{ $slot }}_api_key" class="span6"
                                            value="" autocomplete="new-password"
                                            placeholder="{{ filled($settings->{$slot . '_api_key'}) ? 'Key saved securely; leave blank to keep it' : 'Enter API key' }}">
                                        @if(filled($settings->{$slot . '_api_key'}))
                                            <p class="help-block">A key is saved securely and is not displayed.</p>
                                            <label>
                                                <input type="checkbox" name="clear_{{ $slot }}_api_key" value="1">
                                                Remove this saved API key
                                            </label>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
