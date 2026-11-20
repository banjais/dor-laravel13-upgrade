<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Reset Password | Department of Roads</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="/assets/css_2021/bootstrap.min.css" rel="stylesheet" />
    <link href="/assets/css_2021/fontawesome-all.min.css" rel="stylesheet" />
    <link href="/assets/css_2021/style.css" rel="stylesheet" />
    <link href="/assets/css_2021/responsive.css" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/style.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/style_responsive.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/style_default.css') }}" rel="stylesheet" id="style_color" />
</head>
<body id="login-body">
    <header class="main-header-wrap">
        <div class="top-header">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-xl-4 col-lg-4">
                        <div class="logo-wrap d-flex">
                            <div class="logo-img">
                                <a href="/"><img class="no-bandwidth" src="/assets/img/logo.png" alt=""></a>
                            </div>
                            <div class="logo-title">
                                <a href="/">
                                    <strong>नेपाल सरकार</strong>
                                    <small>पूर्वाधार विकास मन्त्रालय</small>
                                    <span>सडक विभाग</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-7 col-lg-7">
                        <h4><strong>Department Of Roads ( DOR )</strong></h4>
                        <p>Verify OTP &amp; Reset Password</p>
                    </div>
                    <div class="col-lg-1 d-none d-lg-block">
                        <div class="flag-wrap"><img class="no-bandwidth" src="/assets/img/nepal-flag.gif" alt=""></div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="login-content-body">
        <div id="login">
            <form class="form-vertical no-padding no-margin" action="{{ route('admin.password.verify') }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-3">
                        <div class="lock"><i class="fa fa-key"></i></div>
                    </div>
                    <div class="col-9">
                        <div class="control-wrap">
                            <h4>Reset Password</h4>
                            <p>Enter the 6-digit OTP sent to your registered email and choose a new password.</p>

                            @if (session('status'))
                                <div class="alert alert-success">{{ session('status') }}</div>
                            @endif
                            @if ($errors->any())
                                <div class="alert alert-danger">{{ $errors->first() }}</div>
                            @endif

                            <div class="control-group">
                                <div class="controls">
                                    <div class="input-prepend">
                                        <span class="add-on"><i class="fa fa-shield-alt"></i></span>
                                        <input class="form-control" name="otp" type="text"
                                            placeholder="6-digit OTP" inputmode="numeric"
                                            pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" required />
                                    </div>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="controls">
                                    <div class="input-prepend">
                                        <span class="add-on"><i class="fa fa-lock"></i></span>
                                        <input class="form-control" name="password" type="password"
                                            placeholder="New Password" autocomplete="new-password"
                                            minlength="8" required />
                                    </div>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="controls">
                                    <div class="input-prepend">
                                        <span class="add-on"><i class="fa fa-lock"></i></span>
                                        <input class="form-control" name="password_confirmation" type="password"
                                            placeholder="Confirm New Password" autocomplete="new-password"
                                            minlength="8" required />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="submit" class="btn btn-block login-btn w-100" value="Reset Password" />
                <div class="text-center mtop10">
                    <a href="{{ route('admin.login') }}">Back to Login</a>
                </div>
            </form>
        </div>
    </div>

    <footer class="main-footer">
        <div class="bottom-footer">
            <div class="container text-center">
                <p>© 2021 सर्वाधिकार सुरक्षित सडक बिभाग</p>
            </div>
        </div>
    </footer>
</body>
</html>
