<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="description" content="Omaya, Data Analytic">
    <meta name="keywords" content="Omaya">
    <meta name="author" content="PIXINVENT">
    <title>Omaya Admin | Portal</title>
    <link rel="apple-touch-icon" href="{{ url('images/icon.ico') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ url('images/icon.ico') }}">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/vendors/css/vendors.min.css') }}">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/css/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/css/bootstrap-extended.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/css/colors.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/css/components.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/css/themes/dark-layout.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/css/themes/bordered-layout.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/css/themes/semi-dark-layout.css') }}">

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/css/core/menu/menu-types/vertical-menu.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/css/plugins/forms/form-validation.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/app-assets/css/pages/authentication.css') }}">
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{ url('templates/vuexy/assets/css/style.css') }}">
    <!-- END: Custom CSS-->

</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern blank-page navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="blank-page">
    <!-- BEGIN: Content-->
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <div class="auth-wrapper auth-cover">
                    <div class="auth-inner row m-0">
                        <!-- Brand logo--><a class="brand-logo" href="{{ route('admin.login') }}">
                            <!-- <img src="{{ url('images/logo.png') }}"> -->
                            <h2 class="brand-text text-primary ms-1">Omaya | Data Analytic</h2>
                        </a>
                        <!-- /Brand logo-->
                        <!-- Left Text-->
                        <div class="d-none d-lg-flex col-lg-8 align-items-center p-5"  style="background: #fafafa;">
                            <div class="w-100 d-lg-flex align-items-center justify-content-center px-5"><img class="img-fluid" src="{{ url('images/login2.jpg') }}" alt="Login V2" /></div>
                        </div>
                        <!-- /Left Text-->
                        <!-- Login-->
                        <div class="d-flex col-lg-4 align-items-center auth-bg px-2 p-lg-5">
                            <div class="col-12 col-sm-8 col-md-6 col-lg-12 px-xl-2 mx-auto">
                                <h2 class="card-title fw-bold mb-1">Sign In To Your Account!  👋</h2>
                                <p class="card-text mb-2">Please sign-in to your account and start the adventure</p>

                                <form class="login auth-login-form mt-2" action="{{ route('admin.login.verify') }}" method="post">
                                    @csrf


                                    <?php

                                        try {
                                            \DB::connection()->getPdo();
                                        } catch (\Exception $e) {
                                            echo '<div class="alert alert-danger" role="alert"><div class="alert-body"><strong>ERROR: </strong>Please check your database connection.</div></div>';
                                        }

                                    ?>

                                    @if(!Request::secure())
                                        <div class="alert alert-warning" role="alert">
                                            <div class="alert-body"><strong>WARNING: </strong> You are using non-secure connection.</div>
                                        </div>
                                    @endif


                                    @if (Session::has('success'))
                                        <div class="alert alert-success" role="alert">
                                            <div class="alert-body">{{ session('success') }}</div>
                                        </div>
                                    @endif



                                    @if (count($errors) > 0)
                                        @foreach ($errors->all() as $error)
                                            <div class="alert alert-danger" role="alert">
                                                <div class="alert-body">{{ $error }}</div>
                                            </div>
                                        @endforeach
                                    @endif

                                    @if(config('general.multi_tenant'))
                                    <div class="mb-1">
                                        <label class="form-label" for="login-tenant">Tenant ID</label>
                                        <input class="form-control @error('tenant_id') is-invalid @enderror" type="text" name="tenant_id" value="{{ old('tenant_id') }}" placeholder="default" aria-describedby="login-tenant" autofocus="" tabindex="1" required="" autocomplete="off"  />
                                        @error('tenant_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    @endif

                                    <div class="mb-1">
                                        <label class="form-label" for="login-username">Username</label>
                                        <input class="form-control @error('username') is-invalid @enderror" type="text" name="username" value="{{ old('username') }}" placeholder="admin" aria-describedby="login-username" autofocus="" tabindex="2" required="" autocomplete="off" />
                                        @error('username')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>


                                    <div class="mb-1">
                                        <div class="d-flex justify-content-between">
                                            <label class="form-label" for="login-password">Password</label>
                                            <!-- <a href="#"><small>Forgot Password?</small></a> -->
                                        </div>
                                        <div class="input-group input-group-merge form-password-toggle">
                                            <input class="form-control form-control-merge  @error('password') is-invalid @enderror" id="login-password" type="password" name="password" placeholder="············" aria-describedby="login-password" tabindex="3" /><span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                        </div>
                                         @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <!-- <div class="mb-1">
                                        <div class="form-check">
                                            <input class="form-check-input" id="remember-me" name="remember" type="checkbox" tabindex="4" value="1" />
                                            <label class="form-check-label" for="remember-me"> Remember Me</label>
                                        </div>
                                    </div> -->
                                    <button class="btn btn-primary btn-block mt-2" tabindex="5" type="submit">Sign in</button>
                                </form>

                            </div>
                        </div>
                        <!-- /Login-->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END: Content-->


    <!-- BEGIN: Vendor JS-->
    <script src="{{ url('templates/vuexy/app-assets/vendors/js/vendors.min.js') }}"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <script src="{{ url('templates/vuexy/app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="{{ url('templates/vuexy/app-assets/js/core/app-menu.js') }}"></script>
    <script src="{{ url('templates/vuexy/app-assets/js/core/app.js') }}"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <script src="{{ url('templates/vuexy/app-assets/js/scripts/pages/auth-login.js') }}"></script>
    <!-- END: Page JS-->

    <script>
        $(window).on('load', function() {
            if (feather) {
                feather.replace({
                    width: 14,
                    height: 14
                });
            }
        })
    </script>
</body>
<!-- END: Body-->

</html>