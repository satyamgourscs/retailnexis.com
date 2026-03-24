<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Nexa Technologies Installer | Step-4</title>
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('saas-install-assets/images/favicon.ico') }}">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="{{ asset('saas-install-assets/css/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('saas-install-assets/css/font-awesome.min.css') }}" rel="stylesheet">
        <link href="{{ asset('saas-install-assets/css/style.css') }}" rel="stylesheet">
    </head>
<body>
    <div class="col-md-6 offset-md-3">
        <div class='wrapper'>
            <header>
                <h1 class="text-center">Nexa Technologies Auto Installer</h1>
            </header>
            <hr>
            <div class="content pad-top-bot-50">
                <h3 class="text-center"><strong class="theme-color">Congratulations!</strong></h3><br>
                <h5 class="text-center">You have successfully installed Nexa Technologies.</h5><br> 
                <hr>   
                <br>             
                <p>Access SAAS landing page - <strong><a href="{{ rtrim(request()->getBaseUrl(), '/') }}/" target="_blank">Click here</a></strong></p>
                <p>Access superadmin login page - <strong><a href="{{ rtrim(request()->getBaseUrl(), '/') }}/superadmin-login" target="_blank">Click here</a></strong></p>
            </div>
            <hr>
            <footer>Copyright &copy; {{ config('app.name') }}. All rights reserved.</footer>
        </div>
    </div>
    <script type="text/javascript" src="{{ asset('saas-install-assets/js/jquery.min.js')}}"></script>
</body>
</html>
