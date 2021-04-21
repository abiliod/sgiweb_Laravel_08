<!doctype html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <meta property="og:title" content="Sgi Web" />
    <meta property="og:type" content="Compliance" />
    <meta property="og:url" content="http://www.correios.com.br" />

    <meta property="og:site_name" content="Sgi Web" />
     <meta name="author" content="Abilio Dias Ferreira"/>
    <meta http-equiv="content-language" content="pt-br">
    <meta name="reply-to" content="abiliobonito@gmail.com">
    <meta name="robots" content="index,nofollow">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
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
    <title>{{ config('app.name', 'SgiWeb') }}</title>
</head>
<body onload="mostrarDiv()" >
<header >
    @auth
       @include('layouts._Admin._nav')
    @else
    <nav>
        <div class="nav-wrapper #1b5e20 green darken-4">
            <div class="container">
                <a href="#" class="brand-logo center"><img class="logo_institucional" src="#"></a>
                <ul id="nav-mobile" class="right hide-on-med-and-down">
                    @if (Route::has('login'))
                        @auth
                          <li><a href="{{ url('/home') }}">Home</a></li>
                        @else
                            @if (Route::has('register'))
                                <li><a href="{{ url('/') }}">Welcome</a></li>
                                <li><a href="{{ route('register') }}">Register</a></li>
                                <li><a href="{{ route('site.sobre') }}">Sobre</a></li>
                                <li><a href="{{ route('site.contato') }}">Contato</a>
                            @endif
                        @endauth
                    @endif
                </ul>
            </div>
        </div>
    </nav>
    @endif


</header>

        <main>
            @if(Session::has('mensagem'))
            <div class="container">
                <div class="row">
                    <div class="card {{ Session::get('mensagem')['class'] }}">
                        <div align="center" class="card-content">
                            {{ Session::get('mensagem')['msg'] }}
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @yield('content')
        </main>


    @include('layouts._Admin._footer')
    <!--Import jQuery.js-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>
    <!-- Compiled and minified JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/js/materialize.min.js"></script>



    <script src="{{asset('js/init.js')}}"></script>
    <script src="{{asset('js/script.js')}}"></script>
    </body>
</html>


