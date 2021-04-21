@extends('layouts._sgiweb.app')

@section('content')
<div class="container">
	<h2 class="center">Adição de  Teste de Inspeções</h2>
	<div class="row">
	 	<nav>
         <div class="nav-wrapper green">
		      	<div class="col s12">
			        <a href="{{ route('home')}}" class="breadcrumb">Início</a>
			        <a href="{{route('compliance.relatos')}}" class="breadcrumb"> Teste de Inspeções</a>
			        <a class="breadcrumb">Adição de  Teste de Inspeções</a>
		      	</div>
            </div>

          </nav>
    </div>
    @if($errors->any())
        <div class="row red">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
        </div>
    @endif
	<div class="row">
		<form action="{{ route('compliance.relatos.salvar') }}" method="post">
		    @CSRF
            @include('compliance.relatos._form')
            <div class="input-field col s6">
                <button class="btn blue">Adicionar</button>
            </div>
		</form>
	</div>
</div>
@endsection
