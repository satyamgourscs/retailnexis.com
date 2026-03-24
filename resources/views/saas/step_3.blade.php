<!DOCTYPE html>
<html lang="en">
<head>
    <title>NEXA TECH Installer | Step-3</title>
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
	            <h1 class="text-center">NEXA TECH Auto Installer</h1>
                @include('includes.session_message')
	        </header>
	        <hr>
		    <div class="content">
		        <?php
		        if (isset($_GET['_error'])) {
		        	if ($_GET['_error'] != '') {
		        		echo '<h4 class="text-danger">'.$_GET['_error'].'</h4>';
		        	}
		        }
		        ?>
		        <form action="{{ route('saas-install-process', [], false) }}" method="post">
                    @csrf
		            <fieldset>
                        <label>Server Type :</label>
                        <div class="form-check-inline">
                            <label class="form-check-label">
                              <input type="radio" class="form-check-input" value="cpanel" name="server_type">cPanel
                            </label>
                        </div>
                        <div class="form-check-inline">
                            <label class="form-check-label">
                                <input type="radio" class="form-check-input" value="plesk" name="server_type">Plesk
                            </label>
                        </div>
                        <div class="form-check-inline">
                            <label class="form-check-label">
                                <input type="radio" class="form-check-input" value="localhost" name="server_type">localhost/VPS/Dedicated
                            </label>
                        </div>

                        <br>

						<label>Purchase Code <a href="#purchasecodeModal" role="button" data-toggle="modal">?</a></label>
		                <input type='text' value="123456789XXXXXXXX" required class="form-control" name="purchasecode" readonly>

                        <div id="cpanel_fields" style="display:none;">
                            <label>cPanel API Key</label>
                            <input type='text' placeholder="Ex: 5F5S5OF81XXXXXXXXXX" class="form-control" name="cpanel_api_key">

                            <label>cPanel User Name</label>
                            <input type='text' placeholder="Ex: saleprosaas" class="form-control" name="cpanel_username">
                        </div>

                        <div id="plesk_fields" style="display:none;">
                            <label>Plesk User Name</label>
                            <input type='text' placeholder="Ex: saleprosaas" class="form-control" name="plesk_username">

                            <label>Plesk Password</label>
                            <input type='text' placeholder="Ex: 5F5S5OF81XXXXXXXXXX" class="form-control" name="plesk_password">

                            <label>Plesk Database Server Id</label>
                            <input type='number' placeholder="Ex: 2" class="form-control" name="plesk_database_server_id">

                        </div>

                        <label>Database Host</label>
		                <input type='text' required placeholder="Ex: localhost" class="form-control" name="db_host">

                        <label>Database Port</label>
		                <input type='number' required placeholder="Ex: 3306" class="form-control" name="db_port">

                        <label>Database Username</label>
		                <input type='text' required placeholder="Ex: salepro2023" class="form-control" name="db_username">

                        <label>Database Password (leave blank if none)</label>
		                <input type='password' placeholder="Ex: PXsfdf1542" class="form-control" name="db_password">

                        <label>Database Name</label>
		                <input type='text' placeholder="Ex: saleprosaas_db" required class="form-control" name="db_name">

                        <button type='submit' class='btn btn-primary btn-block'>Submit</button>
		            </fieldset>
		        </form>
		    </div>
		    <hr>
		    <footer>Copyright &copy; {{ config('app.name') }}. All rights reserved.</footer>
		</div>
	</div>


	<script src="{{ asset('saas-install-assets/js/jquery.min.js')}}"></script>
	<script src="{{ asset('saas-install-assets/js/bootstrap.min.js')}}"></script>
    <script>
        $(document).ready(function() {
            $('input[name="server_type"]').change(function() {
                if ($(this).val() == 'cpanel') {
                    $('#cpanel_fields').show();
                    $('#plesk_fields').hide();
                } else if ($(this).val() == 'plesk') {
                    $('#cpanel_fields').hide();
                    $('#plesk_fields').show();
                } else if ($(this).val() == 'localhost') {
                    $('#cpanel_fields').hide();
                    $('#plesk_fields').hide();
                }
            });
        });
    </script>

</body>
</html>
