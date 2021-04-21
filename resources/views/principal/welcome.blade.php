<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="utf-8">
	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<!--Let browser know website is optimized for mobile-->
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<!--Import Google Icon Font-->
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

	<!-- Compiled and minified CSS -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/css/materialize.min.css">

	<link rel="stylesheet" type="text/css" href="{{asset('css/style.css')}}">
	<title>{{ config('app.name', 'GynPromo') }}</title>
</head>
<body>

	<header>
    @include('layouts._Admin._nav')
	</header>
	<div class="container" >
		<div class="row">
			<nav>
				<div class="nav-wrapper green">
					<div class="col s12">
						<a class="breadcrumb">Bem - Vindo</a>
					</div>
				</div>
			</nav>
		</div>


            @include('principal.cartaoDepartamento')


		<!--Import jQuery.js-->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

		<!-- Compiled and minified JavaScript -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/js/materialize.min.js"></script>

		<script src="{{asset('js/init.js')}}"></script>
	</body>
	</html>
