<!DOCTYPE html>
<html lang="en">
<head>
    <title>SalePro SaaS Installer | Step-1</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('saas-install-assets/images/favicon.ico') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ asset('saas-install-assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('saas-install-assets/css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('saas-install-assets/css/style.css') }}" rel="stylesheet">
</head>
<body>
	<div class="col-md-6 offset-md-3">
		<div class="wrapper">
	        <header>
	            <img src="{{ asset('saas-install-assets/images/logo.png') }}" alt="Logo" style="max-width: 120px;"/>
	            <h1 class="text-center">SalePro SaaS  Auto Installer</h1>
	        </header>
            <hr>
            <div class="content text-center">
                <a id="letsstart" href="{{ route('saas-install-step-2') }}" class="btn btn-primary">Let's Start</a>
                <hr class="mt-lg-5">
                <h6>If you need any help with installation, <br>
                    Please contact through support <a target="_blank" href="https://lion-coders.com/support">Contact Support</a></h6>
            </div>
            <hr>
            <footer>Copyright &copy; LionCoders. All Rights Reserved.</footer>
		</div>
	</div>

	<script src="{{ asset('saas-install-assets/js/jquery.min.js')}}"></script>
	<script src="{{ asset('saas-install-assets/js/bootstrap.min.js')}}"></script>
    <script>
        $(document).ready(function() {
            var allowed = @json($allowed);
            $('#letsstart').on('click', function(e) {
                if (!allowed) {
                    e.preventDefault();
                    alert('Installation of the SaaS is only allowed on the root domain.');
                }
            });
        });
    </script>
</body>
</html>
