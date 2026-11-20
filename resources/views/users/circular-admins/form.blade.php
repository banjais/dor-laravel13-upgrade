@extends('layout.admin.masters')
@section('content')
<div id="main-content">
    <div class="container-fluid">
        <h3 class="page-title">Circular Admin
            <small>{{ $account ? 'Edit account' : 'Create account' }}</small>
        </h3>
        <ul class="breadcrumb">
            <li><a href="{{ route('dashboard') }}"><i class="icon-home"></i></a></li>
            <li><a href="{{ route('admin.circular-admin.index') }}">Circular Admin</a></li>
            <li>{{ $account ? 'Edit' : 'Add' }}</li>
        </ul>

        <div class="widget">
            <div class="widget-title">
                <h4><i class="icon-reorder"></i>{{ $account ? 'Edit Circular Admin' : 'Add Circular Admin' }}</h4>
            </div>
            <div class="widget-body form">
                @include('layout.admin.alert')

                @if ($errors->any())
                    <div class="alert alert-error">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST"
                      action="{{ $account ? route('admin.circular-admin.update', $account->id) : route('admin.circular-admin.store') }}"
                      class="form-horizontal">
                    @csrf

                    <div class="control-group">
                        <label class="control-label">Account Email (OTP / Password Recovery) *</label>
                        <div class="controls">
                            <input type="email" name="email_address" class="span8" required maxlength="100"
                                   value="{{ old('email_address', $account->email_address ?? '') }}">
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Office *</label>
                        <div class="controls">
                            @if ((int) Auth::user()->user_type_id === 2)
                                <select name="office_id" class="span8" required>
                                    <option value="">Select Office</option>
                                    @foreach ($offices as $office)
                                        <option value="{{ $office->id }}"
                                            {{ (string) old('office_id', $selectedOfficeId) === (string) $office->id ? 'selected' : '' }}>
                                            {{ $office->office_name }} ({{ $office->office_address }})
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <p class="text-info">{{ $selectedOfficeName ?? 'Your assigned office' }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Password {{ $account ? '(leave blank to keep current)' : '*' }}</label>
                        <div class="controls">
                            <input type="password" name="password" class="span8"
                                   {{ $account ? '' : 'required' }} minlength="8" autocomplete="new-password">
                            <span class="help-block">At least 8 characters, 1 capital letter, 1 number and 1 special character.</span>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Confirm Password {{ $account ? '' : '*' }}</label>
                        <div class="controls">
                            <input type="password" name="confirm_password" class="span8"
                                   {{ $account ? '' : 'required' }} minlength="8" autocomplete="new-password">
                            <span class="help-block">Enter the same password again.</span>
                        </div>
                    </div>

                    @if ($account)
                        <div class="control-group">
                            <label class="control-label">Status</label>
                            <div class="controls">
                                <select name="is_active" class="span8" required>
                                    <option value="1" {{ (string) old('is_active', $account->is_active) === '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ (string) old('is_active', $account->is_active) === '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>
                    @endif

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="icon-ok-sign"></i> {{ $account ? 'Update' : 'Create' }}
                        </button>
                        <a href="{{ route('admin.circular-admin.index') }}" class="btn btn-danger">
                            <i class="icon-remove-sign"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
