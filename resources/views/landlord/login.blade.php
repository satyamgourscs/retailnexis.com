<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $general_setting?->site_title ?? $general_setting?->meta_title ?? config('app.name') }} - Login</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="all,follow">
    <link rel="manifest" href="{{ url('manifest.json') }}">
    @if ($general_setting && $general_setting->site_logo)
        <link rel="icon" type="image/png" href="{{ url('landlord/images/logo', $general_setting->site_logo) }}" />
    @endif
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('vendor/font-awesome/css/font-awesome.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}" id="theme-stylesheet" type="text/css">
    <link rel="preload" href="https://fonts.googleapis.com/css?family=Nunito:400,500,700" as="style"
        onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link href="https://fonts.googleapis.com/css?family=Nunito:400,500,700" rel="stylesheet">
    </noscript>
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
                        @if ($general_setting && $general_setting->site_logo)
                            <img src="{{ url('landlord/images/logo', $general_setting->site_logo) }}" width="110" alt="">
                        @endif
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
                                value="" autocomplete="username">
                            <label for="login-username" class="label-material">{{ __('db.UserName') }} / email</label>
                            @if (session()->has('error'))
                                <p>
                                    <strong>{{ session()->get('error') }}</strong>
                                </p>
                            @endif
                        </div>

                        <div class="form-group-material">
                            <input id="login-password" type="password" name="password" required class="input-material"
                                value="" autocomplete="current-password">
                            <label for="login-password" class="label-material">{{ __('db.Password') }}</label>
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
                    <p>&copy; {{ $general_setting?->site_title ?? config('app.name') }} | {{ __('db.Developed By') }}
                        <span class="external">{{ $general_setting?->developed_by ?? '' }}</span>
                        @if(env('VERSION'))
                            | V {{ env('VERSION') }}
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script>
    $("div.alert").delay(4000).slideUp(800);
    var theme = @json($theme);
    if (theme == 'dark') {
        $('body').addClass('dark-mode');
    } else {
        $('body').removeClass('dark-mode');
    }

    $('#togglePassword').click(function() {
        var passwordField = $("#login-password");
        var icon = $(this).find("i");

        if (passwordField.attr("type") === "password") {
            passwordField.attr("type", "text");
            icon.removeClass("fa-eye-slash").addClass("fa-eye");
        } else {
            passwordField.attr("type", "password");
            icon.removeClass("fa-eye").addClass("fa-eye-slash");
        }
    });

    var materialInputs = $('input.input-material');

    materialInputs.filter(function() {
        return $(this).val() !== "";
    }).siblings('.label-material').addClass('active');

    materialInputs.on('focus', function() {
        $(this).siblings('.label-material').addClass('active');
    });

    materialInputs.on('blur', function() {
        $(this).siblings('.label-material').removeClass('active');

        if ($(this).val() !== '') {
            $(this).siblings('.label-material').addClass('active');
        } else {
            $(this).siblings('.label-material').removeClass('active');
        }
    });
    </script>
</body>

</html>
