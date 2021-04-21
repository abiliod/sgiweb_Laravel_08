@extends('layouts._sgiweb.app')
@section('content')
    <div class="container">
        <h2 class="center">Planejamento Inspeção</h2>
        <div class="row">
            <nav>
                <div class="nav-wrapper green">
                    <div class="col s12">
                        <a href="{{ route('home')}}" class="breadcrumb">Início</a>
                        <a href="{{route('compliance.planejamento')}}" class="breadcrumb">Lista de Inspeções Monitoradas</a>
                        <a class="breadcrumb">Inspeção :   {{$registro->descricao}}  </a>
                    </div>
                </div>
            </nav>
        </div>
        <div class="row">
            <form action="{{route('compliance.planejamento.update',$registro->id)}}" method="post">
                @CSRF
                <div class="input-field col s6">
                    <select  disabled name="ciclo" id="ciclo" readonly="">
                        <option value="{{$registro->ciclo}}" selected>{{$registro->ciclo}}</option>
                    </select>
                    <label for="ciclo" >Ciclo de Verificação</label>
                </div>

                <div class="input-field col s6">
                    <input  disabled type="date" name="datainiPreInspeção"  id="datainiPreInspeção" value="{{$registro->datainiPreInspeção}}">
                    <label  class="active" for="datainiPreInspeção" >Data Início</label>
                </div>

                <div class="input-field col s6">
                    <input disabled type="text" name="codigo"  id="codigo" value="{{$registro->codigo}}" placeholder="Codigo Gerado Pelo Sistema" readonly>
                    <label for="codigo" >Código Inspeção</label>
                </div>


                <div class="input-field col s6">
                    <select disabled name="tipoUnidade_id">
                        @foreach($tiposDeUnidade as $tipoDeUnidade)
                            <option value="{{ $tipoDeUnidade->id }}">{{ $tipoDeUnidade->sigla }} - {{ $tipoDeUnidade->tipodescricao }}</option>
                        @endforeach
                    </select>
                    <label for="tipoUnidade_id" >Tipo de Unidade</label>
                </div>
                <div class="input-field col s6">
                    <select disabled name="tipoVerificacao" id="tipoVerificacao">
                        @foreach($tiposDeUnidade as $tipoDeUnidade)
                            @if($tipoDeUnidade->tipoInspecao == "Ambos")
                                <option value="Presencial">Presencial</option>
                                <option value="Remoto">Remoto</option>
                            @else
                                <option value="Presencial">Presencial</option>
                                <option value="Remoto">Remoto</option>
                                <option value="{{ $tipoDeUnidade->tipoInspecao }}" {{(isset($registro->tipoVerificacao) && $registro->tipoVerificacao == $tipoDeUnidade->tipoInspecao  ? 'selected' : '')}}>{{ $tipoDeUnidade->tipoInspecao }}</option>
{{--                                <option value="{{ $tipoDeUnidade->tipoInspecao }}" se>{{ $tipoDeUnidade->tipoInspecao }}</option>--}}
                            @endif
                        @endforeach
                    </select>
                    <label for="tipoVerificacao" >Tipo de Inspeção</label>
                </div>
                <div class="input-field col s6">
                    <select  disabled name="status" id="status">
                        <option value="Em Inspeção" selected>Em Inspeção</option>
                    </select>
                    <label for="status" >Status</label>
                </div>
                <div class="input-field col s6">
                    <select name="inspetorcoordenador" id="inspetorcoordenador">
                        <option value="">Inspetor Coordenador</option>
                        @foreach($inspetores as $inspetor)
                            <option value="{{$inspetor->document}}">SE - {{$inspetor->se}} - {{$inspetor->name }}</option>
                        @endforeach
                    </select>
                    <label for="inspetorcoordenador" >Inspetor Coordenador</label>
                </div>

                <div class="input-field col s6">
                    <select name="inspetorcolaborador" id="inspetorcolaborador">
                        <option value="">Inspetor Colaborador</option>
                        @foreach($inspetores as $inspetor)
                            <option value="{{$inspetor->document}}">SE - {{$inspetor->se}} - {{$inspetor->name }}</option>
                        @endforeach
                    </select>
                    <label for="inspetorcolaborador" >Inspetor Colaborador</label>
                </div>

                <div class="input-field col s4">
                    <input type="text" name="numHrsPreInsp"  id="numHrsPreInsp" value="{{ !empty($registro->categoria>=5) ? '4' : '6' }}">
                    <label for="numHrsPreInsp" >Horas Pré Inspeção</label>

                </div>
                <div class="input-field col s4">
                    <input type="text" name="numHrsDesloc"  id="numHrsDesloc" value="0">
                    <label for="numHrsDesloc" >Horas Deslocamento</label>
                </div>
                <div class="input-field col s4">
                    <input type="text" name="numHrsInsp"  id="numHrsInsp" value="{{ !empty($registro->categoria>=5) ? '4' : '10' }}">
                    <label for="numHrsInsp" >Horas Inspeção</label>
                </div>
{{--                @can('inspecao_adicionar')    @endcan--}}
                    <div class="row">
                        <button class="btn blue">Confirmar</button>
                    </div>


            </form>
        </div>
    </div>
@endsection
