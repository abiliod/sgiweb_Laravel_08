@extends('layouts._sgiweb.app')
@section('content')
<div class="container">
	<h2 class="center">Editar Grupos de Verificação</h2>
	<div class="row">
	 	<nav>
		    <div class="nav-wrapper green">
		      	<div class="col s12">
			        <a href="{{ route('home')}}" class="breadcrumb">Início</a>
			        <a href="{{route('compliance.grupoVerificacao')}}" class="breadcrumb">Grupos de Verificação</a>
			        <a class="breadcrumb">Editar Grupos de Verificação</a>
		      	</div>
		    </div>
	  	</nav>
	</div>
	<div class="row">
		<form action="{{route('compliance.grupoVerificacao.atualizar', $registro->id)}}" method="post">
		@CSRF
		<input type="hidden" name="_method" value="put">
		    @include('compliance.grupoVerificacao._form')

            @can('grupoverificacao_editar')
                <button class="btn blue">Atualizar</button>
            @endcan
		</form>
	</div>
</div>
@endsection
