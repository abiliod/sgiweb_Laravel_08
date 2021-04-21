@extends('layouts._sgiweb.app')
@section('content')
<div class="container">
	<h2 class="center">Editar Tipo de Unidades</h2>
	<div class="row">
	 	<nav>
		    <div class="nav-wrapper green">
		      	<div class="col s12">
			        <a href="{{ route('home')}}" class="breadcrumb">Início</a>
			        <a href="{{route('compliance.tipounidades')}}" class="breadcrumb">Tipo de Unidades</a>
			        <a class="breadcrumb">Editar Tipo de Unidades</a>
		      	</div>
		    </div>
	  	</nav>
	</div>
	<div class="row">
		<form action="{{route('compliance.tipounidades.atualizar', $registro->id)}}" method="post">
		@CSRF
		<input type="hidden" name="_method" value="put">
		    @include('compliance.tipounidades._form')

            @can('tipoUnidade_editar')
                <div class="input-field col s12">
                    <button class="btn blue">Atualizar</button>
                </div>
            @endcan

		</form>
	</div>
</div>
@endsection
