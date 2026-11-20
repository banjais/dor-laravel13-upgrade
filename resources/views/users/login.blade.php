<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->

<head>
    <meta charset="utf-8" />
    <title>Login page</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta content="" name="description" />
    <meta content="" name="author" />
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
                                <a href="/">
                                    <img class="no-bandwidth" src="/assets/img/logo.png" alt="">
                                </a>
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
                        <p>Admin Login</p>
                    </div>
                    <div class="col-lg-1 d-none d-lg-block">
                        <div class="flag-wrap"><img class="no-bandwidth" src="/assets/img/nepal-flag.gif" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="login-content-body">
        <div id="login">
            @if(!empty($error_message))
                <div class="alert alert-danger" role="alert">
                    {{ $error_message }}
                </div>
            @endif

            <form name="login" id="loginform" class="form-vertical no-padding no-margin" action="{{ route('admin.login') }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-3">
                        <div class="lock">
                            <i class="fa fa-lock"></i>
                        </div>
                    </div>
                    <div class="col-9">
                        <div class="control-wrap">
                            <h4>User Login</h4>
                            <div class="control-group">
                                <div class="controls">
                                    <div class="input-prepend">
                                        <span class="add-on"><i class="fa fa-user"></i></span><input
                                            class="form-control" id="input-username" name="username" type="text"
                                            placeholder="Email Address / Username" />
                                    </div>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="controls">
                                    <div class="input-prepend">
                                        <span class="add-on"><i class="fa fa-key"></i></span><input
                                            class="form-control" id="input-password" name="password" type="password"
                                            placeholder="Password" />
                                    </div>
                                    <div class="mtop10">
                                        <div class="block-hint pull-right">
                                            <a href="{{ route('admin.password.request') }}">Forgot Password?</a>
                                        </div>
                                    </div>

                                    <div class="clearfix space5"></div>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
                <input type="submit" id="login-btn" class="btn btn-block login-btn w-100" value="Login" />
            </form>
            <!-- END LOGIN FORM -->
        </div>
    </div>
    <footer class="main-footer">
        <div class="bottom-footer">
            <div class="container text-center">
                <p>© 2021 सर्वाधिकार सुरक्षित सडक बिभाग</p>
            </div>
        </div>
    </footer>
    <script src="{{ asset('assets/admin/js/jquery-1.8.3.min.js') }}"></script>
    <script src="{{ asset('assets/admin/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/jquery.blockui.js') }}"></script>
    <script src="{{ asset('assets/admin/js/scripts.js') }}"></script>
    <script>
        jQuery(document).ready(function() {
            App.initLogin();
            // initiate layout and plugins
            //App.init();
        });
    </script>
    <!-- END JAVASCRIPTS -->
</body>
</html>
