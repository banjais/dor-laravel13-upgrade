<h4>6. Knowledge Sources</h4>
                        <hr>

                        <div class="control-group">
                            <label class="control-label">AppSheet Contact Directory URL</label>
                            <div class="controls">
                                <input type="url" name="appsheet_url" class="span8"
                                    value="{{ old('appsheet_url', $settings->appsheet_url) }}"
                                    placeholder="Paste your AppSheet link">
                                <p class="help-block">Directory link only. Data access and permissions will be connected separately.</p>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">Google Sheets URL</label>
                            <div class="controls">
                                <input type="url" name="google_sheet_url" class="span8"
                                    value="{{ old('google_sheet_url', $settings->google_sheet_url) }}"
                                    placeholder="Paste your Google Sheets link">
                                <p class="help-block">Private sheets will require authorised access.</p>
                            </div>
                        </div>

                        <h4>7. External Service Integrations</h4>
                        <hr>
                        <p class="help-block">
                            Enable only services you intend to use. New or changed settings require a connection test before runtime use.
                            Saved credentials are encrypted and are never displayed here.
                        </p>

                        @php
                            $integrationRows = [
                                'open_meteo' => ['Open-Meteo', 'No API key required'],
                                'overpass' => ['Overpass API', 'No API key required'],
                                'google_maps' => ['Google Maps', 'API key; live tests may incur billing'],
                                'openweathermap' => ['OpenWeatherMap', 'API key required'],
                                'tomtom' => ['TomTom', 'API key; live tests may incur billing'],
                                'waze_partner' => ['Waze for Cities Partner', 'Partner feed URL and access details'],
                                'google_sheets' => ['Google Sheets', 'Uses the Google Sheets URL above'],
                                'appsheet' => ['AppSheet Contact Directory', 'Uses the AppSheet URL above'],
                            ];
                        @endphp

                        @foreach($integrationRows as $code => [$label, $description])
                            @php $integration = $integrations[$code] ?? null; @endphp
                            <div id="integration-{{ $code }}" class="control-group" style="border-bottom:1px solid #e5e5e5; padding:10px 0;">
                                <label class="control-label">{{ $label }}</label>
                                <div class="controls">
                                    <input type="hidden" name="integrations[{{ $code }}][enabled]" value="0">
                                    <label>
                                        <input type="checkbox"
                                            name="integrations[{{ $code }}][enabled]"
                                            value="1"
                                            {{ old('integrations.' . $code . '.enabled', $integration?->is_enabled) ? 'checked' : '' }}>
                                        Enable this integration
                                    </label>
                                    <p class="help-block">{{ $description }}</p>

                                    @if(in_array($code, ['google_maps', 'openweathermap', 'tomtom', 'waze_partner', 'appsheet'], true))
                                        <input type="password"
                                            name="integrations[{{ $code }}][credential]"
                                            class="span6"
                                            autocomplete="new-password"
                                            placeholder="{{ !empty($integration?->credentials['secret']) ? 'Credential saved; leave blank to keep it' : 'Enter credential' }}">
                                        @if(!empty($integration?->credentials['secret']))
                                            <label>
                                                <input type="checkbox"
                                                    name="integrations[{{ $code }}][clear_credential]"
                                                    value="1">
                                                Remove saved credential
                                            </label>
                                        @endif
                                    @endif

                                    @if($code === 'appsheet')
                                        <input type="text" name="integrations[appsheet][app_id]"
                                            class="span6"
                                            value="{{ old('integrations.appsheet.app_id', $integration?->settings['app_id'] ?? '') }}"
                                            placeholder="AppSheet App ID">
                                        <input type="text" name="integrations[appsheet][table_name]"
                                            class="span6"
                                            value="{{ old('integrations.appsheet.table_name', $integration?->settings['table_name'] ?? 'Contact Directory') }}"
                                            placeholder="Contact Directory Table Name">
                                    @endif

                                    @if($code === 'waze_partner')
                                        <input type="url"
                                            name="integrations[{{ $code }}][url]"
                                            class="span8"
                                            value="{{ old('integrations.' . $code . '.url', $integration?->settings['url'] ?? '') }}"
                                            placeholder="Waze for Cities Partner feed URL">
                                    @endif

                                    <div style="margin-top:8px;">
                                        <small>
                                            Status:
                                            @if($integration?->health_status === 'healthy')
                                                <strong style="color:#218838;">SUCCESS</strong>
                                            @elseif($integration?->health_status === 'unavailable')
                                                <strong style="color:#c0392b;">UNSUCCESSFUL</strong>
                                            @else
                                                <strong>{{ strtoupper($integration?->health_status ?? 'NOT CONFIGURED') }}</strong>
                                            @endif
                                            @if($integration?->last_tested_at)
                                                | Last tested: {{ $integration->last_tested_at->format('Y-m-d H:i') }}
                                            @endif
                                            @if($integration?->last_error)
                                                | {{ $integration->last_error }}
                                            @endif
                                        </small>
                                        @if($integration?->exists && in_array($code, ['open_meteo', 'overpass', 'google_maps', 'openweathermap', 'tomtom', 'waze_partner', 'google_sheets', 'appsheet'], true))
                                            <button type="submit"
                                                class="btn btn-small"
                                                style="margin:5px 0 0 10px;"
                                                formaction="{{ route('admin.external-api-settings.integration-test') }}"
                                                formmethod="POST"
                                                name="integration_code"
                                                value="{{ $code }}">
                                                Test Connection
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
