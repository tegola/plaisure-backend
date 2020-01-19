<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="{{ asset('css/mail.css') }}">
</head>
<body>
	<div class="body">
		<div class="container">
			<div class="my-4 my-md-5">
				<img class="logo" src="{{ asset('images/email/logo.png') }}" alt="{{ config('app.name') }}">
				<hr>
			</div>

			@yield('content')
		</div>
	</div>
</body>
</html>