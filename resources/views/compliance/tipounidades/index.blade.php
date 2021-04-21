@extends('layouts._sgiweb.app')
@section('content')
<div class="container">
	<h2 class="center">Tipo de Unidades</h2>
	    <div class="row">
		<div class="row">
		</div>
        <nav>
			<div class="nav-wrapper green">
				<div class="col s12">
					<a href="{{ route('home')}}" class="breadcrumb">Início</a>
					<a class="breadcrumb">Tipo de Unidades</a>
				</div>
			</div>
		</nav>
	</div>
    <div class="row">
        <div class="nav-wrapper green">
            <form action="{{route('compliance.tipounidades.search')}}" method="post">
                @csrf

                <div class="input-field col s5">
                    <input id="search" type="search"  name="search"  value="">
                    <label class="label-icon" for="search">
                        <i class="material-icons">search</i>
                        Sigla,	Descricao ou Liberado p/Inspecionar = (SIM/NÃO)</label>
                    <i class="material-icons">close</i>
                </div>
                <div class="input-field col s5">
                          <select name="tipoInspecao" >
                        <option value="Ambos" >Ambos</option>
                        <option value="Presencial">Presencial</option>
                        <option value="Remoto" selected>Remoto</option>
                        <option value="Monitorada">Monitorada</option>
                        <option value="Suspenso">Suspenso</option>
                    </select>
                    <label for="tipoInspecao" >Tipo de Inspeção</label>
                </div>
                <div class="input-field col s2">
                    <button class="btn blue">Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
		<table>
				<thead>
					<tr>
                       <th>Sigla</th>
                        <th>Descricao</th>
                        <th>Liberado p/Inspecionar</th>
                        <th>Tipo de Inspeção</th>
                    	<th>Ação</th>
					</tr>
				</thead>
				<tbody>
				@foreach($registros as $registro)
					<tr>
				    	<td>{{ $registro->sigla }}</td>
                        <td>{{ $registro->tipodescricao }}</td>
                        <td>{{ $registro->inspecionar }}</td>
                        <td>{{ $registro->tipoInspecao }}</td>
						<td>
                        @can('tipoUnidade_editar')
                                <a class="waves-effect waves-light btn orange"
                                   href="{{ route('compliance.tipounidades.editar',$registro->id) }}">Editar</a>
                        @endcan
                        @can('tipoUnidade_deletar')
                                <a class="btn red" href="javascript: (alert('Não se deve Deletar esse registro! \r\n Não será deletado.'))" >Deletar</a>
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
    @can('tipoUnidade_adicionar')
        <div class="row">
            <a class="btn blue" href="{{route('compliance.tipounidades.adicionar')}}">Adicionar</a>
        </div>
    @endcan
   	</div>
@endsection
