@extends('layouts.app')

@section('content')
<div class="container">
	<h2 class="center">Adicionar Categorias</h2>
	<div class="row">
	 	<nav>
		    <div class="nav-wrapper green">
		      	<div class="col s12">
			        <a href="{{ route('admin.home')}}" class="breadcrumb">Início</a>
			        <a href="{{route('admin.categorias')}}" class="breadcrumb">Lista de Categorias</a>
			        <a class="breadcrumb">Adicionar Categoria</a>
		      	</div>
		    </div>
	  	</nav>
	</div>
	<div class="row">
		<form action="{{ route('admin.tipos.salvar') }}" method="post">

		{{csrf_field()}}
		@include('admin.categorias._form')

		<button class="btn blue">Adicionar</button>

			
		</form>
			
	</div>
	
</div>
	

@endsection