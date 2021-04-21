@extends('layouts._sgiweb.app')
@section('content')
    <div class="container">
        <h2 class="center">Adicionar Tipo de Unidades</h2>
        <div class="row">
            <nav>
                <div class="nav-wrapper green">
                    <div class="col s12">
                        <a href="{{ route('home')}}" class="breadcrumb">Início</a>
                        <a href="#" class="breadcrumb">Lista de Unidades</a>
                        <a class="breadcrumb">Adicionar Unidades</a>
                    </div>
                </div>
            </nav>
        </div>
        <div class="row">
            <form action="{{ route('compliance.tipounidades.salvar') }}" method="post">
                {{csrf_field()}}
                @include('compliance.tipounidades._form')
                <button class="btn blue">Adicionar</button>
            </form>
        </div>
    </div>
@endsection
