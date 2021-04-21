@extends('layouts._sgiweb.app')

@section('content')
<div class="container">
	<h2 class="center">Testes de Inspeção</h2>
	    <div class="row">
			<div class="nav-wrapper green">
                <form action="{{route('compliance.relatos.search')}}" method="post">
					@csrf
                    <div class="input-field col s3">
                        <select name="tipoUnidade_id">
                        <option value="0">Tipo de Unidade</option>
                            @foreach($tiposDeUnidade as $tipoDeUnidade)
                            <option value="{{ $tipoDeUnidade->tipoUnidade_id }}">{{ $tipoDeUnidade->tipodescricao }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="input-field col s3">
                        <select name="tipoVerificacao" id="tipoVerificacao">
                            <option value="">Tipo de Inspeção</option>
                            <option value="Monitorada">Monitorada</option>
                            <option value="Presencial">Presencial</option>
                            <option value="Remoto">Remoto</option>
                        </select>
                    </div>
                    <div class="input-field col s4">
                    <select name="nomegrupo">
                    <option value="">Grupo de Inspeção</option>
                            @foreach($gruposdeverificacao as $grupodeverificacao)
                                 <option value="{{ $grupodeverificacao->nomegrupo }}">{{ $grupodeverificacao->nomegrupo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="input-field col s2">
                         <button class="btn blue">Filtrar</button>
                    </div>
				</form>
		    </div>
		<div class="row">
		</div>
        <nav>
			<div class="nav-wrapper green">
				<div class="col s12">
					<a href="{{ route('home')}}" class="breadcrumb">Início</a>
					<a class="breadcrumb">Testes de Inspeção</a>
				</div>
			</div>
		</nav>
	</div>
	<div class="row">
		<table>
				<thead>
					<tr>
                        <th>Ciclo</th>
						<th>Tipo de Inspeção</th>
                        <th>Tipo de Unidade</th>
                        <th>Grupo</th>
                        <th>Nº do Teste</th>
                        <th>Teste</th>
						<th>Ação</th>
					</tr>
				</thead>
				<tbody>
				@foreach($registros as $registro)
					<tr>
				    	<td>{{ $registro->ciclo }}</td>
                        <td>{{ $registro->tipoVerificacao }}</td>
                        <td>{{ $registro->sigla }}</td>
                        <td>{{ $registro->numeroGrupoVerificacao }}-{{ $registro->nomegrupo }}</td>
                        <td>{{ $registro->numeroDoTeste }}</td>
                        <td>{{ substr(strip_tags($registro->teste), 0, 150) .'    ....'}}</td>
						<td>


                            <a class="waves-effect waves-light btn orange"
                             href="{{ route('compliance.relatos.editar',$registro->id) }}">Editar</a>
                             <a class="btn red" href="javascript: if(confirm('Deletar esse registro?')){ window.location.href = '{{ route('compliance.relatos.destroy',$registro->id) }}' }">Deletar</a>



                        </td>
					</tr>
                @endforeach
				</tbody>
			</table>

            <div class="row">
			     {!! $registros->links() !!}
            </div>

		</div>

		<div class="row">
			<a class="btn blue" href="{{ route('compliance.relatos.adicionar') }}">Adicionar</a>
		</div>
   	</div>

@endsection
