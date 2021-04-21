@extends('layouts._sgiweb.app')
@section('content')
<div class="container">
	<h2 class="center">Lista de Unidades</h2>
	<div class="row">
		<nav>
			<div class="nav-wrapper orange">
                <form action="{{route('compliance.unidades.search')}}" method="post">
					@csrf
					<div class="input-field">
						<input id="search" type="search"  name="search" required autofocus>
						<label class="label-icon" for="search">Pesquisas - Digite: Parte do Nome da Unidade, do Telefone, MCU ou Telefone<i class="material-icons">search</i></label>
						<i class="material-icons">close</i>
					</div>
				</form>
			</div>
			<div class="row">
			</div>
			<div class="nav-wrapper green">
				<div class="col s12">
					<a href="{{ route('home')}}" class="breadcrumb">Início</a>
					<a class="breadcrumb">Lista de Unidades</a>
				</div>
			</div>
		</nav>
	</div>
	<div class="row">
		<table>
				<thead>
					<tr>
						<th>Se</th>
                        <th>Unidade</th>
						<th>Telefone</th>
                        <th>E-Mail</th>
                        <th>Hora Abertura</th>
						<th>Ação</th>
					</tr>
				</thead>
				<tbody>
                    @forelse($registros as $registro)
                    @can('unidade_editar')

                        <tr>

                            <td>{{ $registro->seDescricao }}</td>
                            <td>{{ $registro->descricao }}</td>
                            <td>{{ $registro->telefone }}</td>
                            <td>{{ $registro->email }}</td>
                            <td>{{ $registro->inicio_expediente }}     </td>

                            <td>

                            <!---------
                                <ul id="slide-out" class="side-nav">
                                    <li><div class="user-view">
                                            <div class="background">
                                                <img src="images/office.jpg">
                                            </div>
                                            <a href="#!user"><img class="circle" src="images/yuna.jpg"></a>
                                            <a href="#!name"><span class="white-text name">John Doe</span></a>
                                            <a href="#!email"><span class="white-text email">jdandturk@gmail.com</span></a>
                                        </div></li>
                                    <li><a href="#!"><i class="material-icons">cloud</i>First Link With Icon</a></li>
                                    <li><a href="#!">Second Link</a></li>
                                    <li><div class="divider"></div></li>
                                    <li><a class="subheader">Subheader</a></li>
                                    <li><a class="waves-effect" href="#!">Third Link With Waves</a></li>
                                </ul>
                                <a href="#" data-activates="slide-out" class="button-collapse"><i class="material-icons">menu</i></a>
----->

                            <td>


                            <a class="waves-effect waves-light btn orange" href="{{ route('compliance.unidades.editar',$registro->id) }}">Editar</a>
                                <!---------
                                  <a class="waves-effect waves-light btn #00897b teal darken-1"
                                    href="">Alarmes</a>
                                 <a class="waves-effect waves-light btn #00897b teal darken-1"
                                    href="">Cofres</a>
                                 <a class="waves-effect waves-light btn #00897b teal darken-1"
                                    href="">CFTVs</a>
                                 <a class="waves-effect waves-light btn #00897b teal darken-1"
                                    href="">Extravios</a>
                                 <a class="waves-effect waves-light btn #00897b teal darken-1"
                                    href="">Feriados</a>
                                 <a class="waves-effect waves-light btn #00897b teal darken-1"
                                    href="">Cadastral</a>
                                     ------>
                    @endcan

                    @can('inspecao_adicionar')
                        <a class="waves-effect waves-light btn blue
                            {{(isset($registro->tipoInspecao) && $registro->tipoInspecao == 'Monitorada'  ? ' disabled' : '')}}"
                            href="{{ route('compliance.unidades.gerarInspecao',$registro->id) }}">Gerar Inspeção</a>
                    @endcan

                    @can('unidade_deletar')
                        <a class="waves-effect waves-light btn red " href="" >Deletar</a>
                    @endcan

                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td>
                                <span class="row red" >
                                    Unidade não disponível para inspeção ou não cadastrada
                                </span>
                            </td>
                        </tr>
                    @endforelse
				</tbody>
			</table>
        <div class="row">
            {!! $registros->links() !!}
        </div>
		</div>
    @can('unidade_adicionar')
		<div class="row">
			<a class="btn blue" href="#">Adicionar</a>
		</div>
    @endcan

   	</div>
@endsection
