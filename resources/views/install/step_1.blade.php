<!DOCTYPE html>
<html lang="en">
<head>
    <title>Retail Nexis Installer | Step-1</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('install-assets/images/favicon.ico') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ asset('install-assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('install-assets/css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('install-assets/css/style.css') }}" rel="stylesheet">
</head>
<body>
	<div class="col-md-6 offset-md-3">
		<div class="wrapper">
	        <header>
	            <img src="{{ asset('install-assets/images/logo.png') }}" alt="Logo" style="max-width: 120px;"/>
	            <h1 class="text-center">Retail Nexis Auto Installer</h1>
	        </header>
            <hr>
            <div class="content text-center">
                <a href="{{ route('install-step-2') }}" class="btn btn-primary">Let's Start</a>
                <hr class="mt-lg-5">
                <h6>If you need any help with installation, <br>
                    Please contact through support <a target="_blank" href="https://tryonedigital.com/support">Contact Support</a></h6>
            </div>
            <hr>
            <footer>Copyright &copy; TryOneDigital. All Rights Reserved.</footer>
		</div>
	</div>
</body>
</html>
