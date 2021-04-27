@extends('layouts._sgiweb.app')

@section('content')
    <div class="container">
        <h2 class="center">Conclusão  Inspeção Monitorada</h2>
        <div class="row">
            <nav>
                <div class="nav-wrapper green">
                    <div class="col s12">
                        <a href="{{ route('home')}}" class="breadcrumb">Início</a>
                        <a class="breadcrumb">Conclusão de Inspeções Monitorada</a>
                    </div>
                </div>
            </nav>
        </div>
        @if($errors->any())
            <div class="row red">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </div>
        @endif
        <div class="row">
            <form action="{{ route('compliance.monitoramento.concluir_insp') }}" method="post">
                @CSRF
                @include('compliance.monitoramento._formAvaliar')

            </form>
        </div>
    </div>
@endsection
