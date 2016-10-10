<!DOCTYPE html>
<html lang="it">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta http-equiv="x-ua-compatible" content="ie=edge">

		<title>{{ config('constants.name') }} - Amministrazione - @yield('title')</title>

		<!-- Bootstrap -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.4/css/bootstrap.min.css" integrity="sha384-2hfp1SzUoho7/TsGGGDaFdsuuDL0LX2hnUp6VkX3CUQ2K4K+xjboZdsXyp4oUHZj" crossorigin="anonymous">
	</head>
	<body>
		<nav class="navbar navbar-full navbar-light bg-faded m-b-3">
			<div class="container">
				<a class="navbar-brand" href="{{ route('admin.home') }}">{{ config('constants.name') }} - Amministrazione</a>
				<div class="collapse navbar-toggleable-sm" id="navbar-collapse">
					<ul class="nav navbar-nav">
						<li class="nav-item">
							<a class="nav-link" href="{{ route('admin.venues.upload') }}">Carica esercizi</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('admin.venues.maintain') }}">Manutenzione esercizi</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('admin.venues.clean') }}">Pulizia esercizi</a>
						</li>
					</ul>
				</div>
				<button class="navbar-toggler hidden-md-up pull-xs-right" type="button" data-toggle="collapse" data-target="#navbar-collapse" aria-controls="navbar-collapse" aria-expanded="false" aria-label="Mostra/nascondi navigazione">&#9776;</button>
			</div>
		</nav>

		@yield('content')

		<div class="container">
			<hr>
			<p>&copy; {{ date('Y') }} {{ config('constants.company') }}</p>
		</div>

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.0.0/jquery.min.js" integrity="sha384-THPy051/pYDQGanwU6poAc/hOdQxjnOEXzbT+OuUAFqNqFjL+4IGLBgCJC3ZOShY" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.2.0/js/tether.min.js" integrity="sha384-Plbmg8JY28KFelvJVai01l8WyZzrYWG825m+cZ0eDDS1f7d/js6ikvy1+X+guPIB" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.4/js/bootstrap.min.js" integrity="sha384-VjEeINv9OSwtWFLAtmc4JCtEJXXBub00gtSnszmspDLCtC0I4z4nqz7rEFbIZLLU" crossorigin="anonymous"></script>
	</body>
</html>