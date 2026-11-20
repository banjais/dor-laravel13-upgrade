@extends('layout.admin.masters')

@section('content')
<div id="main-content">
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="span12">
                <h3 class="page-title">
                    GRMS
                    <small>Manage the GRMS blind-copy recipient for the public Contact Form.</small>
                </h3>
                <ul class="breadcrumb">
                    <li>
                        <a href="{{ route('dashboard') }}"><i class="icon-home"></i></a>
                        <span class="divider">&nbsp;</span>
                    </li>
                    <li>GRMS</li>
                </ul>
            </div>
        </div>

        <div class="row-fluid">
            <div class="span12">
                <div class="widget">
                    <div class="widget-title">
                        <h4><i class="icon-envelope"></i> GRMS Email Settings</h4>
                    </div>

                    <div class="widget-body form">
                        @include('layout.admin.alert')

                        <div class="alert alert-info">
                            These settings control whether GRMS receives a blind copy (BCC)
                            of messages submitted through the public Contact Form.
                            The Head Office primary and secondary email recipients are
                            automatically obtained from the Head Office profile and are
                            managed separately.
                        </div>

                        <form action="{{ route('admin.contact-bcc-settings.update') }}"
                              method="POST"
                              class="form-horizontal">
                            @csrf

                            <div class="control-group">
                                <label class="control-label" for="grs_enabled">
                                    GRMS BCC
                                </label>
                                <div class="controls">
                                    <input type="hidden" name="grs_enabled" value="0">
                                    <label class="checkbox">
                                        <input type="checkbox"
                                               id="grs_enabled"
                                               name="grs_enabled"
                                               value="1"
                                               {{ old('grs_enabled', $grsEnabled ? '1' : '0') == '1' ? 'checked' : '' }}>
                                        Enable GRMS BCC
                                    </label>
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label" for="grs_email">
                                    GRMS Email Address
                                </label>
                                <div class="controls">
                                    <input type="email"
                                           id="grs_email"
                                           name="grs_email"
                                           class="span5"
                                           value="{{ old('grs_email', $grsEmail) }}"
                                           maxlength="255"
                                           required>
                                    @error('grs_email')
                                        <span class="help-block" style="color:#b94a48;">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">
                                    <i class="icon-ok-sign"></i> Save GRMS Settings
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
@endsection
