@extends('layouts._sgiweb.app')

@section('content')
    <div class="container">
        <h2 class="center">Inspeção Monitorada</h2>
        <div class="row">
            <nav>
                <div class="nav-wrapper green">
                    <div class="col s12">
                        <a href="{{ route('home')}}" class="breadcrumb">Início</a>
                        <a class="breadcrumb">Compliance Inspeções Monitorada</a>
                    </div>
                </div>
            </nav>
        </div>
        <div class="row">
            @can('gerar_inspecao_automatica') @endcan
                <div class="col s12 m6">
                    <div class="card blue darken-1">
                        <div class="card-content white-text">
                            <span class="card-title">Gerar Inspeções</span>
                            <p>Unidades<br>Assunto: Gera inspeções por tipo de unidade e por Superintendência.</p>
                        </div>
                        <div class="card-action">
                            <a class="white-text" href="{{route('compliance.monitoramento.criar')}}">Gerar Inspeção</a>
                        </div>
                    </div>
                </div>

            @can('avaliar_inspecao_automatica') @endcan
            <div class="col s12 m6">
                <div class="card orange darken-1">
                    <div class="card-content white-text">
                        <span class="card-title">Avaliar Inspeções</span>
                        <p>Unidades<br>Assunto: Avaliar inspeções por tipo de unidade e por Superintendência.</p>
                    </div>
                    <div class="card-action">
                        <a class="white-text" href="{{route('compliance.monitoramento.avaliar')}}">Avalia Inspeçao</a>
                    </div>
                </div>
            </div>

            @can('integrar_XML_inspecao_automatica') @endcan
            <div class="col s12 m6">
                <div class="card green darken-1">
                    <div class="card-content white-text">
                        <span class="card-title">Integrar Inspeções via XML</span>
                        <p>Unidades<br>Assunto: Integração das inspeções por tipo de unidade e por Superintendência via método XML.</p>
                    </div>
                    <div class="card-action">
                        <a class="white-text" href="{{route('compliance.monitoramento.xml')}}">Gerar XML</a>
                    </div>
                </div>
            </div>


        </div>
@endsection
