@extends('layouts._sgiweb.app')

@section('content')
<div class="container">
	<h2 class="center">Adicionar Usuário</h2>
	<div class="row">
	 	<nav>
		    <div class="nav-wrapper green">
		      	<div class="col s12">
			        <a href="{{ route('home')}}" class="breadcrumb">Início</a>
			        <a href="{{route('admin.usuarios')}}" class="breadcrumb">Lista de Usuários</a>
			        <a class="breadcrumb">Adicionar Usuário</a>
		      	</div>
		    </div>
	  	</nav>
	</div>
	<div class="row">
		<form action="{{ route('admin.usuarios.salvar') }}" method="post">

		{{csrf_field()}}
		@include('admin.usuarios._form')

            @can('usuario_adicionar')
                <button class="btn blue">Adicionar</button>
            @endcan
		</form>
	</div>
</div>
@endsection
