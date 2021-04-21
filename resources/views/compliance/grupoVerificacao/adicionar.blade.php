@extends('layouts._sgiweb.app')

@section('content')
<div class="container">
	<h2 class="center">Adição de Grupo de Verificação</h2>
	<div class="row">
	 	<nav>
         <div class="nav-wrapper green">
		      	<div class="col s12">
			        <a href="{{ route('home')}}" class="breadcrumb">Início</a>
			        <a href="{{route('compliance.grupoVerificacao')}}" class="breadcrumb">Grupos de Verificação</a>
			        <a class="breadcrumb">Adição de Grupo de Verificação</a>
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
		<form action="{{ route('compliance.grupoVerificacao.salvar') }}" method="post">
		    @CSRF
            @include('compliance.grupoVerificacao._form')

            @can('grupoverificacao_adicionar')
                <div class="input-field col s12">
                    <button class="btn blue">Adicionar</button>
                </div>
            @endcan

		</form>
	</div>
</div>
@endsection
