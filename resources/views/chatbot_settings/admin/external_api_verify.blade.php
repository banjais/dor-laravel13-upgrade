@extends('layout.admin.masters')

@section('content')
<div id="main-content" class="chatbot-admin-compact">
    <div class="container-fluid">
        @include('chatbot_settings.admin.partials.compact_styles')

        <h3>External API Settings – Security Verification</h3>
        <p>यो पृष्ठमा प्रवेश गर्न Super Admin को पहिचान पुनः पुष्टि गर्नुपर्छ।</p>

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        @if (empty($otpSent))
            <form method="POST" action="{{ route('admin.external-api-settings.confirm-password') }}">
                @csrf
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" required autocomplete="username">
                </div>
                <div class="form-group">
                    <label>Current Password</label>
                    <input type="password" name="password" class="form-control" required autocomplete="current-password">
                </div>
                <button type="submit" class="btn btn-primary">Verify and Send OTP</button>
            </form>
        @else
            <p>सर्भरमा सुरक्षित रूपमा तोकिएको इमेलमा OTP पठाइएको छ।</p>
            <form method="POST" action="{{ route('admin.external-api-settings.verify-otp') }}">
                @csrf
                <div class="form-group">
                    <label>6-digit OTP</label>
                    <input type="text" name="otp" class="form-control" inputmode="numeric"
                           pattern="[0-9]{6}" maxlength="6" required autocomplete="one-time-code">
                </div>
                <button type="submit" class="btn btn-primary">Verify OTP</button>
            </form>
        @endif
    </div>
</div>
@endsection
