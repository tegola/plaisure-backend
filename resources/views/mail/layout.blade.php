<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="{{ asset('css/mail.css') }}">
</head>
<body>
	<div class="pg-body">
		<div class="pg-header">
			<div class="pg-container">
				<img class="pg-header__logo" src="{{ asset('images/email/logo.png') }}" alt="{{ config('app.name') }}">
			</div>
		</div>
		<div class="pg-container">
			@yield('content')
		</div>
	</div>
</body>
</html>