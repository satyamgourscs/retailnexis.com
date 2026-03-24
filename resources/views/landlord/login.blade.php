<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $general_setting->site_title ?? $general_setting->meta_title }} - Login</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="all,follow">
    <link rel="manifest" href="{{ url('manifest.json') }}">
    @if (!config('database.connections.saleprosaas_landlord'))
        <link rel="icon" type="image/png" href="{{ url('public/logo', $general_setting->site_logo) }}" />
        <!-- Bootstrap CSS-->
        <link rel="stylesheet" href="<?php echo asset('vendor/bootstrap/css/bootstrap.min.css'); ?>" type="text/css">
        <!-- login stylesheet-->
        <link rel="stylesheet" href="<?php echo asset('css/auth.css'); ?>" id="theme-stylesheet" type="text/css">
        <!-- Google fonts - Roboto -->
        <link rel="preload" href="https://fonts.googleapis.com/css?family=Nunito:400,500,700" as="style"
            onload="this.onload=null;this.rel='stylesheet'">
        <noscript>
            <link href="https://fonts.googleapis.com/css?family=Nunito:400,500,700" rel="stylesheet">
        </noscript>
    @else
        <link rel="icon" type="image/png"
            href="{{ url('../../landlord/images/logo', $general_setting->site_logo) }}" />
        <!-- Bootstrap CSS-->
        <link rel="stylesheet" href="<?php echo asset('../../vendor/bootstrap/css/bootstrap.min.css'); ?>" type="text/css">
        <!-- Font Awesome CSS-->
        <link rel="preload" href="<?php echo asset('../../vendor/font-awesome/css/font-awesome.min.css'); ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
        <noscript>
            <link href="<?php echo asset('../../vendor/font-awesome/css/font-awesome.min.css'); ?>" rel="stylesheet">
        </noscript>
        <!-- login stylesheet-->
        <link rel="stylesheet" href="<?php echo asset('../../css/auth.css'); ?>" id="theme-stylesheet" type="text/css">
        <!-- Google fonts - Roboto -->
        <link rel="preload" href="https://fonts.googleapis.com/css?family=Nunito:400,500,700" as="style"
            onload="this.onload=null;this.rel='stylesheet'">
        <noscript>
            <link href="https://fonts.googleapis.com/css?family=Nunito:400,500,700" rel="stylesheet">
        </noscript>
    @endif
</head>

<body>
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close"
                data-dismiss="alert" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>{!! session()->get('message') !!}</div>
    @endif
    @if (session()->has('not_permitted'))
        <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close"
                data-dismiss="alert" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>{!! session()->get('not_permitted') !!}</div>
    @endif
    <div class="page login-page">
        <div class="container">
            <div class="form-outer text-center d-flex align-items-center">
                <div class="form-inner">
                    <div class="logo">
                        <img src="{{ url('landlord/images/logo', $general_setting->site_logo) }}" width="110">
                    </div>
                    @if (session()->has('delete_message'))
                        <div class="alert alert-danger alert-dismissible text-center"><button type="button"
                                class="close" data-dismiss="alert" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>{{ session()->get('delete_message') }}
                        </div>
                    @endif
                    <form method="POST" action="{{ url('superadmin-login/store') }}" id="login-form">
                        @csrf
                        <div class="form-group-material">
                            <input id="login-username" type="text" name="name" required class="input-material"
                                value="">
                            <label for="login-username" class="label-material">{{ __('db.UserName') }} / email</label>
                            @if (session()->has('error'))
                                <p>
                                    <strong>{{ session()->get('error') }}</strong>
                                </p>
                            @endif
                        </div>

                        <div class="form-group-material">
                            <input id="login-password" type="password" name="password" required class="input-material"
                                value="">
                            <label for="login-password" class="label-material">{{ __('db.Password') }}</label>
                            <!-- Eye Icon -->
                            <span id="togglePassword" class="position-absolute"
                                style="right: 0; top: 50%; transform: translateY(-50%); cursor: pointer;">
                                <i class="fa fa-eye-slash"></i>
                            </span>
                            @if (session()->has('error'))
                                <p>
                                    <strong>{{ session()->get('error') }}</strong>
                                </p>
                            @endif
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">{{ __('db.LogIn') }}</button>
                    </form>
                    <!-- <br> -->
                    <p>&copy; {{ $general_setting->site_title }} | {{ __('db.Developed By') }}
                        <span class="external">{{ $general_setting->developed_by }}</span>
                        @if(env('VERSION'))
                            | V {{ env('VERSION') }}
                        @endif
                    </p>
                </div>
            </div>
        </div>
        <!-- This section for demo only-->
        @if (!env('USER_VERIFIED'))
            <div class="switch-theme" id="switch-theme"
                style="background-color:rgba(255,255,255,0.9);border:1px solid #999;padding:15px;position:fixed;bottom:0px;left:0px;right:0px;z-index:99">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <div class="" style="font-size:11px;color:#666;margin-bottom:15px">Login as</div>
                        <button type="submit" class="btn btn-sm btn-success admin-btn">Super Admin</button>
                    </div>
                </div>
            </div>
        @endif
        <!-- This section for demo only-->
    </div>
</body>

</html>

<script type="text/javascript" src="<?php echo asset('../../vendor/jquery/jquery.min.js'); ?>"></script>

<script>
    $("div.alert").delay(4000).slideUp(800);
    //switch theme code
    var theme = <?php echo json_encode($theme); ?>;
    if (theme == 'dark') {
        $('body').addClass('dark-mode');
        $('#switch-theme i').addClass('dripicons-brightness-low');
    } else {
        $('body').removeClass('dark-mode');
        $('#switch-theme i').addClass('dripicons-brightness-max');
    }

    $('#togglePassword').click(function() {
        var passwordField = $("#login-password"); // Select password input
        var icon = $(this).find("i"); // Select eye icon inside #togglePassword

        if (passwordField.attr("type") === "password") {
            passwordField.attr("type", "text"); // Show password
            icon.removeClass("fa-eye-slash").addClass("fa-eye"); // Change icon
        } else {
            passwordField.attr("type", "password"); // Hide password
            icon.removeClass("fa-eye").addClass("fa-eye-slash"); // Change back icon
        }
    });

    $('.admin-btn').on('click', function(){
        $("input[name='name']").focus().val('superadmin');
        $("input[name='password']").focus().val('superadmin');
        $('#login-form').submit();
    });
    // ------------------------------------------------------- //
    // Material Inputs
    // ------------------------------------------------------ //

    var materialInputs = $('input.input-material');

    // activate labels for prefilled values
    materialInputs.filter(function() {
        return $(this).val() !== "";
    }).siblings('.label-material').addClass('active');

    // move label on focus
    materialInputs.on('focus', function() {
        $(this).siblings('.label-material').addClass('active');
    });

    // remove/keep label on blur
    materialInputs.on('blur', function() {
        $(this).siblings('.label-material').removeClass('active');

        if ($(this).val() !== '') {
            $(this).siblings('.label-material').addClass('active');
        } else {
            $(this).siblings('.label-material').removeClass('active');
        }
    });
</script>
