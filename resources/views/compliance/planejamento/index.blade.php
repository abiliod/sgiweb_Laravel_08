@extends('layouts._sgiweb.app')
@section('content')
    <div class="container">
        <h2 class="center">Planejamento de Inspeção</h2>
        <div class="row">
            <div class="nav-wrapper green">
                <form action="{{route('compliance.planejamento.search')}}" method="post">
                    @csrf
                    <div class="input-field col s4">
                        <select name="ciclo" id="ciclo">
                           <option value="2021" selected>2021</option>
                            <option value="2022">2022</option>
                        </select>
                        <label for="ciclo">Ciclo de Inspeção</label>
                    </div>
                    <div class="input-field col s4">
                        <select name="tipoUnidade_id" id="tipoUnidade_id">
                            <option value="" selected>Tipo de Unidade</option>
                            @foreach($tiposDeUnidade as $tipoDeUnidade)
                                <option value="{{$tipoDeUnidade->id}}">{{ $tipoDeUnidade->tipodescricao }}</option>
                            @endforeach
                        </select>
                        <label for="tipoUnidade_id">Tipo de Unidade</label>
                    </div>
                    <div class="input-field col s4">
                        <select name="tipoVerificacao" id="tipoVerificacao">
                            <option value="" selected>Tipo de Inspeção</option>
                            <option value="Monitorada">Monitorada</option>
                            <option value="Presencial">Presencial</option>
                            <option value="Remoto">Remoto</option>
                        </select>
                        <label for="tipoVerificacao">Tipo de Inspeção</label>
                    </div>
                    <div class="input-field col s6">
                        <select name="inspetor" id="inspetor">
                            <option value="">Inspetor Envolvido</option>
                            @foreach($inspetores as $inspetor)
                                <option value="{{$inspetor->document}}">{{ $inspetor->se .' - '. $inspetor->name }}</option>
                            @endforeach
                        </select>
                        <label for="inspetor" >SE - Inspetor Envolvido</label>
                    </div>
                    <div class="input-field col s6">
                        <select name="status" id="status">
                            <!--  <option value="" selected>Status</option>  -->
                            <option value="Em Inspeção">Em Inspeção</option>
                        </select>
                        <label for="status" >Status</label>
                    </div>
                    <div class="input-field col s8">
                        <input id="search" type="search"  name="search"  value="">
                        <label class="label-icon" for="search">
                            <i class="material-icons">search</i>Parte do nome da unidade inspecionada.</label>
                        <i class="material-icons">close</i>
                    </div>
                    <div class="input-field col s4">
                        <input type="text" name="codigo" id="codigo"  value="">
                        <label for="codigo" >Código Inspeção</label>
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
                        <a class="breadcrumb">Controle de Inspeção</a>
                    </div>
                </div>
            </nav>
        </div>

        <div class="row">
            <table>
                <thead>
                <tr>
                    <th>Ciclo</th>
                    <th>Tipo Verificação</th>
                    <th>Codigo</th>
                    <th>Unidade</th>
                    <th>Status</th>
                    <th>Coordenador</th>
                    <th>Colaborador</th>
                    <th>Início</th>
                    <th>Ação</th>
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
                        <td>{{ $registro->inspetorcoordenador }}</td>
                        <td>{{ $registro->inspetorcolaborador }}</td>
                        <td>{{ $registro->datainiPreInspeção }}</td>
                        <td>

                            @can('inspecao_editar')
                                <a class="waves-effect waves-light btn orange"
                                   href="{{ route('compliance.planejamento.edit', $registro->id) }}">Editar</a>


                                <a class="waves-effect waves-light btn blue"
                                   href="{{ route('compliance.inspecionados.papelTrabalho',$registro->id) }}">Previa_Rel</a>
                            @endcan

{{--                            @can('inspecao_deletar')--}}
{{--                                <a class="btn red" href="javascript: if(confirm('Deletar esse registro?'))--}}
{{--                                { window.location.href = '{{ route('compliance.verificacoes.destroy',$registro->id) }}' }">Deletar</a>--}}
{{--                            @endcan--}}

                        </td>
                    </tr>

                @empty
                    <tr>
                        <td>
                            <p class="red" >Não Há inspeções disponíveis.</p>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            <div class="row">
                {!! $registros->links() !!}
            </div>
        </div>
        @can('inspecao_adicionar')
            <div class="row">
                <a class="btn blue" href="{{ route('compliance.unidades') }}">Adicionar</a>
            </div>
        @endcan
    </div>

@endsection
