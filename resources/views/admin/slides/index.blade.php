@extends('layouts._sgiweb.app')
<!-- 26022020 inclusao da funcionalidade Slide-->
@section('content')
<div class="container">
	<h2 class="center">Lista de Slides</h2>
	<div class="row">
		<nav>
			<div class="nav-wrapper green">
				<div class="col s12">
					<a href="{{ route('home')}}" class="breadcrumb">Início</a>
					<a class="breadcrumb">Lista de Slides</a>
				</div>
			</div>
		</nav>
	</div>
	<div class="row">

		<table>
			<thead>
				<tr>
					<th width="50px">Ordem</th>
					<th width="150px">Título</th>
					<th width="200px">Descrição</th>
					<th width="50px">Publicado</th>
					<th width="100px">Imagem</th>
					<th width="200px" center>Ação</th>
				</tr>
			</thead>
			<tbody>
				@foreach($registros as $registro)
				<tr>
					<td width="50px">{{ $registro->ordem }}</td>
					<td width="150px">{{ $registro->titulo }}</td>
					<td width="200px">{{ $registro->descricao }}</td>
					<td width="50px">{{ $registro->publicado }}</td>
					<td width="100px"><img width="95px" src="{{asset($registro->imagem)}}"></td>

					<td>
						<a width=99px" class="waves-effect waves-light btn orange" href="{{ route('admin.slides.editar',$registro->id) }}">Editar</a>
						<a width="99px" class="waves-effect waves-light btn red" href="javascript: if(confirm('Deletar esse registro?')){ window.location.href = '{{ route('admin.slides.deletar',$registro->id) }}' }">Deletar</a>

					</td>
				</tr>
				@endforeach
			</tbody>
		</table>

	</div>
	<div class="row">
		<a class="btn blue" href="{{route('admin.slides.adicionar')}}">Adicionar</a>
	</div>
</div>
@endsection
