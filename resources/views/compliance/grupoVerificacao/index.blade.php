@extends('layouts._sgiweb.app')

@section('content')
<div class="container">
	<h2 class="center">Grupos de Verificação</h2>
	    <div class="row">
			<div class="nav-wrapper green">
               <form action="{{route('compliance.grupoVerificacao.search')}}" method="post">
					@csrf
                    <div class="input-field col s2">
                        <select name="ciclo" id="ciclo">
                            <option value="0" selected>Ciclo de Verificação</option>
                              <option value="2021" selected>2021</option>
                        </select>
                    </div>
                    <div class="input-field col s3">
                        <select name="tipoUnidade_id" id="tipoUnidade_id" onchange="ativaBtnFiltro();">
                        <option value="0" selected>Tipo de Unidade</option>
                            @foreach( $tiposDeUnidade as $tipoDeUnidade)
                            <option value="{{ $tipoDeUnidade->tipoUnidade_id }}">{{ $tipoDeUnidade->sigla }} - {{ $tipoDeUnidade->tipodescricao }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="input-field col s2">
                        <select name="tipoVerificacao" id="tipoVerificacao" onchange="ativaBtnFiltro();">
                            <option value="0" selected>Tipo de Verificação</option>
                            <option value="Monitorada">Monitorada</option>
                            <option value="Presencial">Presencial</option>
                            <option value="Remoto">Remoto</option>
                            Monitorada
                        </select>
                    </div>

                    <div class="input-field col s3">
                    <select name="nomegrupo" id="nomegrupo" onchange="ativaBtnFiltro();">
                         <option value="Selecione um Grupo de Unidade" selected>Grupo de Unidade</option>
                            @foreach($gruposdeverificacao as $grupodeverificacao)
                                 <option value="{{ $grupodeverificacao->nomegrupo }}">{{ $grupodeverificacao->nomegrupo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="input-field col s2">
                         <button id="btnFiltrar" class="btn blue" disabled = "false">Filtrar</button>
                    </div>
				</form>
		    </div>
		<div class="row">
		</div>
        <nav>
			<div class="nav-wrapper green">
				<div class="col s12">
					<a href="{{ route('home')}}" class="breadcrumb">Início</a>
					<a class="breadcrumb">Grupos de Verificação</a>
				</div>
			</div>
		</nav>
	</div>
	<div class="row">
		<table>
				<thead>
					<tr>
                        <th>Ciclo</th>
						<th>Tipo de Unidade</th>
                        <th>Tipo de Verificação</th>
                        <th>Grupo</th>
						<th>Ação</th>
					</tr>
				</thead>
				<tbody>
				@foreach($registros as $registro)
					<tr>
				    	<td>{{ $registro->ciclo }}</td>
                        <td>{{ $registro->sigla }}</td>
                        <td>{{ $registro->tipoVerificacao }}</td>
                        <td>{{ $registro->numeroGrupoVerificacao }}-{{ $registro->nomegrupo }}</td>
						<td>
                            @can('grupoverificacao_editar')
                                <a class="waves-effect waves-light btn orange"
                                   href="{{ route('compliance.grupoVerificacao.editar',$registro->id) }}">Editar</a>
                            @endcan
                            @can('grupoverificacao_deletar')
                                    <a class="btn red" href="javascript: if(confirm('Deletar esse registro?')){ window.location.href = '{{ route('compliance.grupoVerificacao.destroy',$registro->id) }}' }">Deletar</a>
                            @endcan
                        </td>
					</tr>
                @endforeach
				</tbody>
			</table>

            <div class="row">
			     {!! $registros->links() !!}
            </div>

		</div>

		<div class="row" >
            @can('grupoverificacao_adicionar')
                <a class="btn blue" href="{{route('compliance.grupoVerificacao.adicionar')}}">Adicionar</a>
            @endcan
		</div>
   	</div>


@endsection
