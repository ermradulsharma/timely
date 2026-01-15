<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

<html>

<body>
	<div align="center">
		<div style="max-width: 680px; min-width: 500px; border: 2px solid #e3e3e3; border-radius:5px; margin-top: 20px">
			<div>
				<img src="{{ asset('images/logo.png') }}" width="250" alt="CREATIVE TALENT MANAGEMENT" border="0" />
			</div>
			<div style="background-color: #fbfcfd; border-top: thick double #cccccc; text-align: left;">
				<div style="margin: 30px;">
					<p>
						Dear {{ $user_data['name'] }},<br> <br>
						Welcome to {{ config('app.name') }} App!<br> <br>
					</p>

					<div style="text-align: Right;">
						With warm regards,<br>
						{{ config('app.name') }} App Team
					</div>
				</div>
			</div>
		</div>
	</div>
	<center>{{ date('Y') }} © CTM. ALL Rights Reserved.</center>
</body>

</html>