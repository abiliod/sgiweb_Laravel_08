@extends('layouts._sgiweb.app')
@section('content')
<div class="container">
	<h2 class="center">Controle de Inspeções</h2>
	<div class="row">
		<div class="nav-wrapper green">
            <form action="{{route('compliance.inspecionados.search')}}" method="post">
			@csrf
                <div class="input-field col s4">
                    <select name="ciclo" id="ciclo">
                        <option value="2019">2019</option>
                        <option value="2020 " selected >2020</option>
                        <option value="2021">2021</option>
                    </select>
                    <label for="ciclo">Ciclo de Verificação</label>
                </div>
                <div class="input-field col s4">
                    <select name="tipoUnidade_id" id="tipoUnidade_id">
                        <option value="" selected >Tipo de Unidade</option>
                        @foreach($tiposDeUnidade as $tipoDeUnidade)
                            <option value="{{$tipoDeUnidade->tipoUnidade_id}}">{{$tipoDeUnidade->sigla }} - {{ $tipoDeUnidade->tipodescricao }}</option>
                        @endforeach
                    </select>
                    <label for="tipoUnidade_id">Tipo de Unidade</label>
                </div>
                <div class="input-field col s4">
                    <select name="tipoVerificacao" id="tipoVerificacao">
                        <option value="" selected>Tipo de Verificação</option>
                        <option value="Presencial">Presencial</option>
                        <option value="Remoto">Remoto</option>
                    </select>
                    <label for="tipoVerificacao">Tipo de Verificação</label>
                </div>

                <div class="input-field col s6">
                        <select name="inspetor" id="inspetor">
                            <option value="">Inspetor Envolvido</option>
                            @foreach($inspetores as $inspetor)
                                <option value="{{$inspetor->document}}">{{$inspetor->name }}</option>
                            @endforeach
                        </select>
                        <label for="inspetor" >Inspetor Envolvido</label>
                </div>

                <div class="input-field col s6">
                        <input type="text" name="codigo" id="codigo" value="">
                        <label for="codigo" >Código Inspeção</label>
                </div>

                <div class="input-field col s8">
                    <input id="search" type="search"  name="search"  value="">
                    <label class="label-icon" for="search">
                    <i class="material-icons">search</i>Parte do Nome da Unidade</label>
                    <i class="material-icons">close</i>
                </div>

                <div class="input-field col s2">
                        <button class="btn blue">Filtrar</button>
                </div>
			</form>
		</div>
    </div>
	<div class="row">
        <nav>
			<div class="nav-wrapper green">
				<div class="col s12">
					<a href="{{ route('home')}}" class="breadcrumb">Início</a>
					<a class="breadcrumb">Controle de Inspeções</a>
				</div>
			</div>
		</nav>
    </div>

	<div class="row">
		<table class="row">
            <thead>
                <tr>
                    <th>Ciclo</th>
                    <th>Tipo Verificação</th>
                    <th>Codigo</th>
                    <th>Unidade</th>
                    <th>Status</th>
                    <th>Data</th>
                    <th colspan="5">Ação</th>
                </tr>
            </thead>
            <tbody>

                @forelse($registros as $registro)
                <tr>
                    <td>{{ $registro->ciclo }}</td>
                    <td>{{ $registro->tipoVerificacao }}</td>
                    <td>{{ $registro->codigo }}</td>
                    <td>{{ $registro->descricao }}</td>
                    <td>{{ $registro->status }}</td>
                    <td> {{ \Carbon\Carbon::parse($registro->datainiPreInspeção)->format( 'd/m/Y' ) }} </td>
                    <td>
                        <a class="waves-effect waves-light btn blue"
                         href="{{ route('compliance.inspecionados.papelTrabalho',$registro->id) }}">Papel_Rel</a>

                    </td>
                    @can('inspecao_gerar_xml')   @endcan
                        <td>
                            <a class="waves-effect waves-light btn orange"
                               href="{{ route('compliance.inspecionados.xml',$registro->id) }}">XML</a>

                            <a class="waves-effect waves-light btn #00897b teal darken-1"
                               href="">NCI</a>
                        </td>

                    <td>
                         <a class="btn btn-primary"
                         href="{{ route('compliance.inspecionados.pdfPapelTrabalho',$registro->id) }}">Papel_PDF</a>
                    </td>
                    @can('inspecao_recusar')
                    <td>
                         <a class="btn red" href="javascript: if(confirm('Devolver a Inspeção {{ $registro->codigo }} para Ajuste?')){ window.location.href = '{{ route('compliance.inspecionados.recusar',$registro->id) }}' }">Devolver</a>
                    </td>
                    @endcan

                </tr>
                @empty
                    <tr>
                    <td>
                    <p class="red" >Não Há inspeções para o Status.</p>
                    </td>
                    </tr>
                @endforelse
			</tbody>
		</table>
    </div>
    <div class="row">
	     {!! $registros->links() !!}
    </div>


</div>
@endsection
