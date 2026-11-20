@extends('layout.admin.masters')

@section('content')
<div id="main-content">
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="span12">
                <h3 class="page-title">
                    Configuration
                    <small>Global appearance settings for all DoR public website pages and sub-offices.</small>
                </h3>
                <ul class="breadcrumb">
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="icon-home"></i></a>
                        <span class="divider">&nbsp;</span>
                    </li>
                    <li>Configuration</li>
                </ul>
            </div>
        </div>

        @php
            $identityFields = [
                'government_name_np', 'government_name_en',
                'ministry_name_np', 'ministry_name_en',
                'department_name_np', 'department_name_en',
            ];
            $identityHasErrors = $errors->hasAny($identityFields);
        @endphp

        <ul class="nav nav-tabs" style="margin-bottom:20px;">
            <li class="{{ $identityHasErrors ? '' : 'active' }}">
                <a href="#" data-settings-tab="theme"><i class="icon-tint"></i> Website Appearance</a>
            </li>
            <li class="{{ $identityHasErrors ? 'active' : '' }}">
                <a href="#" data-settings-tab="identity"><i class="icon-globe"></i> Government Identity</a>
            </li>
        </ul>

        <div id="website-theme-panel" style="{{ $identityHasErrors ? 'display:none;' : '' }}">
        <div class="row-fluid">
            <div class="span12">
                <div class="widget">
                    <div class="widget-title">
                        <h4><i class="icon-tint"></i> Global Colour Palette</h4>
                    </div>

                    <div class="widget-body form">
                        @include('layout.admin.alert')

                        <div class="alert alert-info">
                            Changes apply globally to the public website, including all sub-offices.
                            The Admin Control Panel appearance is not affected.
                            Use Preview to inspect changes before saving.
                        </div>

                        @php
                            $colourFields = [
                                'page_background' => 'Page Background',
                                'card_background' => 'Card Background',
                                'header_background' => 'Header Background',
                                'nav_background' => 'Navigation Background',
                                'accent_color' => 'Accent Colour',
                                'footer_background' => 'Footer Background',
                                'text_color' => 'Text Colour',
                                'border_color' => 'Border Colour',
                            ];
                        @endphp

                        <form action="{{ route('admin.website-theme.update') }}"
                              method="POST" id="website-theme-form"
                              class="form-horizontal">
                            @csrf

                            @foreach ($colourFields as $field => $label)
                                <div class="control-group">
                                    <label class="control-label" for="{{ $field }}">
                                        {{ $label }}
                                    </label>
                                    <div class="controls">
                                        <input type="color"
                                               id="{{ $field }}_picker"
                                               class="theme-picker"
                                               data-target="{{ $field }}"
                                               value="{{ $theme->{$field} }}"
                                               style="width:55px;height:35px;padding:2px;vertical-align:middle;">
                                        <input type="text"
                                               id="{{ $field }}"
                                               name="{{ $field }}"
                                               class="span3 theme-hex"
                                               value="{{ $theme->{$field} }}"
                                               pattern="^#[0-9A-Fa-f]{6}$"
                                               maxlength="7"
                                               required
                                               autocomplete="off">
                                        @error($field)
                                            <span class="help-inline" style="color:#b94a48;">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            @endforeach

                            <div class="control-group">
                                <label class="control-label">Live Preview</label>
                                <div class="controls">
                                    <div id="theme-preview" style="border:1px solid #ddd;border-radius:5px;overflow:hidden;max-width:650px;">
                                        <div id="preview-header" style="padding:18px;font-weight:bold;">
                                            Department of Roads — Website Header
                                        </div>
                                        <div id="preview-nav" style="padding:10px 18px;color:white;">
                                            Home　 About Us　 Divisions　 Contact
                                        </div>
                                        <div id="preview-body" style="padding:22px;">
                                            <div id="preview-card" style="padding:18px;border:1px solid;border-radius:4px;">
                                                <strong>Website Content Preview</strong>
                                                <p style="margin:10px 0;">This is an example of a public website content card.</p>
                                                <span id="preview-accent" style="font-weight:bold;">Accent Colour</span>
                                            </div>
                                        </div>
                                        <div id="preview-footer" style="padding:16px;color:white;">
                                            Department of Roads — Website Footer
                                        </div>
                                    </div>
                                    <p class="help-block">Preview is temporary. Save Theme to apply the colours across the website.</p>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">
                                    <i class="icon-ok-sign"></i> Save Theme
                                </button>
                                <button type="button" id="reset-theme" class="btn btn-danger">
                                    <i class="icon-refresh"></i> Reset to Default
                                </button>
                                <a href="{{ route('dashboard') }}" class="btn">
                                    <i class="icon-remove-sign"></i> Cancel
                                </a>
                            </div>
                        </form>

                        <form action="{{ route('admin.website-theme.reset') }}"
                              method="POST" id="reset-theme-form" style="display:none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
        </div>

        <div id="website-identity-panel" style="{{ $identityHasErrors ? '' : 'display:none;' }}">
            <div class="row-fluid">
                <div class="span12">
                    <div class="widget">
                        <div class="widget-title">
                            <h4><i class="icon-globe"></i> Government Identity Settings</h4>
                        </div>
                        <div class="widget-body form">
                            <div class="alert alert-info">
                                Manage the official Government, Ministry and Department names displayed
                                in the public website header. Office names remain separate.
                            </div>

                            <form action="{{ route('admin.website-identity.update') }}"
                                  method="POST" class="form-horizontal">
                                @csrf

                                @php
                                    $identityLabels = [
                                        'government_name_np' => 'Government Name (नेपाली)',
                                        'government_name_en' => 'Government Name (English)',
                                        'ministry_name_np' => 'Ministry Name (नेपाली)',
                                        'ministry_name_en' => 'Ministry Name (English)',
                                        'department_name_np' => 'Department Name (नेपाली)',
                                        'department_name_en' => 'Department Name (English)',
                                    ];
                                @endphp

                                @foreach ($identityLabels as $field => $label)
                                    <div class="control-group">
                                        <label class="control-label" for="{{ $field }}">{{ $label }}</label>
                                        <div class="controls">
                                            <input type="text"
                                                   id="{{ $field }}"
                                                   name="{{ $field }}"
                                                   class="span6"
                                                   value="{{ old($field, $identity->{$field}) }}"
                                                   maxlength="255"
                                                   required>
                                            @error($field)
                                                <span class="help-inline" style="color:#b94a48;">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                @endforeach

                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="icon-ok-sign"></i> Save Government Identity
                                    </button>
                                    <a href="{{ route('dashboard') }}" class="btn">
                                        <i class="icon-remove-sign"></i> Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
@endsection

@section('footer_js')
<script>
(function () {
    'use strict';

    document.querySelectorAll('[data-settings-tab]').forEach(function (tab) {
        tab.addEventListener('click', function (event) {
            event.preventDefault();
            var selected = tab.getAttribute('data-settings-tab');

            document.getElementById('website-theme-panel').style.display =
                selected === 'theme' ? '' : 'none';
            document.getElementById('website-identity-panel').style.display =
                selected === 'identity' ? '' : 'none';

            document.querySelectorAll('[data-settings-tab]').forEach(function (item) {
                item.parentElement.classList.toggle(
                    'active',
                    item.getAttribute('data-settings-tab') === selected
                );
            });
        });
    });


    var fields = [
        'page_background',
        'card_background',
        'header_background',
        'nav_background',
        'accent_color',
        'footer_background',
        'text_color',
        'border_color'
    ];

    function validHex(value) {
        return /^#[0-9A-Fa-f]{6}$/.test(value);
    }

    function updatePreview() {
        var values = {};

        fields.forEach(function (field) {
            var input = document.getElementById(field);
            values[field] = input.value;
            input.setCustomValidity(
                validHex(input.value) ? '' : 'Enter a valid HEX colour, e.g. #142D4E.'
            );
        });

        if (!fields.every(function (field) { return validHex(values[field]); })) {
            return;
        }

        document.getElementById('preview-header').style.backgroundColor = values.header_background;
        document.getElementById('preview-header').style.color = '#FFFFFF';

        document.getElementById('preview-nav').style.backgroundColor = values.nav_background;

        document.getElementById('preview-body').style.backgroundColor = values.page_background;
        document.getElementById('preview-body').style.color = values.text_color;

        document.getElementById('preview-card').style.backgroundColor = values.card_background;
        document.getElementById('preview-card').style.borderColor = values.border_color;
        document.getElementById('preview-card').style.color = values.text_color;

        document.getElementById('preview-accent').style.color = values.accent_color;

        document.getElementById('preview-footer').style.backgroundColor = values.footer_background;
    }

    fields.forEach(function (field) {
        var textInput = document.getElementById(field);
        var picker = document.getElementById(field + '_picker');

        picker.addEventListener('input', function () {
            textInput.value = picker.value.toUpperCase();
            updatePreview();
        });

        textInput.addEventListener('input', function () {
            if (validHex(textInput.value)) {
                picker.value = textInput.value;
            }
            updatePreview();
        });
    });

    document.getElementById('reset-theme').addEventListener('click', function () {
        if (window.confirm('Reset all website colours to the default palette?')) {
            document.getElementById('reset-theme-form').submit();
        }
    });

    updatePreview();
})();
</script>
@endsection
