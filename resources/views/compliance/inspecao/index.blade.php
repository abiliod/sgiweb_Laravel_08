@extends('layouts._sgiweb.app')
@section('content')
<div class="container">
	<h3 class="center">INSPEÇÃO : {{$inspecao->descricao}}</h3>
	    <div class="row">
			<div class="nav-wrapper green">
                <form action="{{route('compliance.inspecao.search')}}" method="post">
					@csrf
                    <input type="hidden" name="id" value="{{$inspecao->id}}">
                    <div class="input-field col s6">
                        <select name="gruposdeverificacao" id="gruposdeverificacao">
                           <option value="" selected>Grupo de Inspeção</option>
                            @foreach($gruposdeverificacao as $grupodeverificacao)
                               <option value="{{$grupodeverificacao->id}}">{{ $grupodeverificacao->nomegrupo }}</option>
                            @endforeach
                        </select>
                        <label for="gruposdeverificacao">Grupo de Inspeção</label>
                    </div>
                    <div class="input-field col s6">
                        <select name="situacao" id="situacao">
                            <option value="" selected>Situação</option>
                            <option value="Em Inspeção">Em Inspeção</option>
                            <option value="Inspecionado">Inspecionado</option>
                            <option value="Corroborado">Corroborado</option>
                            <option value="Pendente na Unidade">Outros..nnn</option>

                         </select>
                        <label for="situacao" >Situação do Item Inspecionado</label>
                    </div>
                    <div class="input-field col s8">
	                    <input id="search" type="search"  name="search"  value="">
                        <label class="label-icon" for="search">
                        <i class="material-icons">search</i>Parte da Descrição do Teste</label>
						<i class="material-icons">close</i>
                    </div>
                    <div class="input-field col s4">
                         <button class="btn blue">Filtrar</button>
                    </div>
				</form>
            </div>
            </div>

<div  class="row">
		<ul class="collapsible" data-collapsible="accordion">
			<li>
				<div class="collapsible-header">
				<i class="material-icons">filter_drama</i>
				Unidade Inspecionada: <h5 class="center"> {{$dado->descricao}} </h5>
				<span class="new badge">4</span></div>
				<div class="collapsible-body"><p>
                    <table>
				       <thead>
					      <tr>
                             <th>Campo</th>
						     <th>Descrição</th>
				          </tr>
				       </thead>
				       <tbody>

                           <tr>
                               <td>Superintendência:</td>
                               <td>{{ $dado->se }}</td>
                           </tr>
                           <tr>
                               <td>MCU:</td>
                               <td>{{ $dado->mcu }}</td>
                           </tr>
                           <tr>
                               <td>STO:</td>
                               <td>{{ $dado->sto }}</td>
                           </tr>
                           <tr>
                               <td>Subordinação administrativa:</td>
                               <td>{{ $dado->desc_subordinacaoAdm }}</td>
                           </tr>
                           <tr>
                               <td>Responsável pela unidade:</td>
                               <td>Documento: {{ $dado->documentRespUnidade }} Nome: {{ $dado->nomeResponsavelUnidade }}</td>
                           </tr>
                           <tr>
                               <td>Tipo de Mecanização:</td>
                               <td>{{ $dado->mecanizacao }}</td>
                           </tr>
                           <tr>
                               <td>Categoria:</td>
                               <td>{{ $dado->categoria }}</td>
                           </tr>
                           <tr>
                               <td>Guiches Ativos:</td>
                               <td>{{ $dado->guiches_ocupados }}</td>
                           </tr>
                           <tr>
                               <td>Executa Distribuição Domiciliária?</td>
                               <td>{{ $dado->tem_distribuicao }}</td>
                           </tr>
                           <tr>
                               <td>Telefone:</td>
                               <td>{{ $dado->telefone }}</td>
                           </tr>
                           <tr>
                               <td>E-Mail:</td>
                               <td>{{ $dado->email }}</td>
                           </tr>
                           <tr>
                             <td>Início do Expediente:</td>
                             <td>{{ $dado->inicio_expediente }}</td>
                          </tr>
                           <tr>
                             <td>Final do Expediente:</td>
                             <td>{{ $dado->final_expediente }}</td>
                          </tr>
                           <tr>
                            <td>Expediente em Dias de Sábado? </td>
                            <td>{{ $dado->trabalha_sabado }}</td>
                          </tr>
                           <tr>
                            <td>Expediente em Dias de Domingo? </td>
                            <td>{{ $dado->trabalha_domingo }}</td>
                          </tr>
                           <tr>
                            <td>Tem Plantão? </td>
                            <td>{{ $dado->tem_plantao }}</td>
                          </tr>

 				      </tbody>
			        </table>

                </div>
			</li>
			<li>
				<div class="collapsible-header">
				<i class="material-icons">place</i>
				Dados da Inspeção CÓDIGO :<h5 class="center">{{ $dado->codigo }}</h5>
				<span class="badge">1</span></div>
				<div class="collapsible-body"><p>
                    <table>
				       <thead>
					      <tr>
                             <th>Campo</th>
						     <th>Descrição</th>
				          </tr>
				       </thead>
				       <tbody>
   					      <tr>
				    	     <td>Código da inspeção:</td>
                             <td>{{ $dado->codigo }}</td>
                          </tr>
                          <tr>
                             <td>Ciclo:</td>
                             <td>{{ $dado->ciclo }}</td>
                          </tr>
                          <tr>
                             <td>Tipo de inspeção:</td>
                             <td>{{ $dado->tipoVerificacao }}</td>
                          </tr>
                          <tr>
                             <td>Inspetor coordenador:</td>
                             <td>{{ $dado->inspetorcoordenador }}</td>
                          </tr>
                          <tr>
                             <td>Inspetor colaborador:</td>
                             <td>{{ $dado->inspetorcolaborador }}</td>
                          </tr>
                          <tr>
                              <td>Previsão de tempo para realizar a pré inspeção  em horas::</td>
                              <td>{{ $dado->NumHrsPreInsp }}</td>
                          </tr>
                          <tr>
                              <td>Previsão de Tempo previsto para deslocamento  em horas:</td>
                              <td>{{ $dado->NumHrsDesloc }}</td>
                          </tr>
                          <tr>
                              <td>Previsão de Tempo previsto para conclusão da inspeção em horas:</td>
                              <td>{{ $dado->NumHrsInsp }}</td>
                          </tr>
                          <tr>
                            <td>Data de Requisição: </td>

                              <td>{{ isset($dado->datainiPreInspeção) ? date( 'd/m/Y' , strtotime($dado->datainiPreInspeção)) : '' }}</td>

                          </tr>
                          <tr>
                            <td>Data Última Atualização: </td>
                            <td>{{ isset($dado->updated_at) ? date( 'd/m/Y' , strtotime($dado->updated_at)) : date( 'd/m/Y' , strtotime($dado->datainiPreInspeção)) }}</td>
                          </tr>
 				      </tbody>
			        </table>
                </div>
			</li>
		</ul>
	</div>
        <nav>
			<div class="nav-wrapper green">
				<div class="col s12">
					<a href="{{ route('home')}}" class="breadcrumb">Início</a>
					<a class="breadcrumb">Controle de Verificação</a>
				</div>
			</div>
		</nav>

        <div class="row">
		<table class="highlight">
				<thead>
					<tr>
                        <th>Grupo</th>
						<th>Grupo Descrição</th>
                        <th>Nº Teste</th>
                        <th>Teste</th>
                        <th>Situação</th>

                        @foreach($registros as $registro)

                        @endforeach

                        @if($registro->situacao == 'Inspecionado')
                            <th>
                                @if($registro->tipoVerificacao == 'Monitorada')
                                <th>Ação:</th>
                                @else
                                    @if($dado->inspetorcolaborador ==  auth()->user()->document)
                                        <a class="btn blue" href="javascript: if(confirm('Corroborar Todos Registros dessa Inspeção?'))
                                     { window.location.href = '{{ route('compliance.inspecao.corroborar',$registro->inspecao_id) }}' }">Corroborar_Tudo</a>
                                    @endif
                                @endif

                            </th>
                        @else
                            <th>Ação:</th>
                        @endif
					</tr>
				</thead>
				<tbody>
				@foreach($registros as $registro)
					<tr>
				    	<td>{{ $registro->numeroGrupoVerificacao }}</td>
                        <td>{{ $registro->nomegrupo }}</td>
                        <td>{{ $registro->numeroDoTeste }}</td>
                        <td>{{ $registro->teste }}</td>

                        @if($registro->situacao == 'Inspecionado')
                            <td class="card-panel teal lighten-2">
                                    {{ $registro->situacao }}
                            </td>
                            <td>
                                @if($dado->inspetorcolaborador ==  auth()->user()->document)
                                    <a class="waves-effect waves-light btn orange"
                                       href="{{ route('compliance.inspecao.editar',$registro->id) }}">Corroborar</a>
                                @endif
                            </td>
                        @elseif($registro->situacao == 'Corroborado')
                        <td class="card-panel teal lighten-2">
                            <button
                                data-target="modal1"
                                class="btn modal-trigger waves-effect waves-light" >
                                {{ $registro->situacao }}
                                <i class="material-icons right">event_note</i>
                            </button>
                        </td>

                        @elseif($registro->situacao == 'Em Inspeção')
                            <td>
                                    {{ $registro->situacao }}
                            </td>

                            <td>

                                @can('inspecao_editar')
                                    <a class="waves-effect waves-light btn orange"
                                       href="{{ route('compliance.inspecao.editar',$registro->id) }}">Avaliar</a>
                                @endcan

                            </td>
                        @endif

					</tr>
                @endforeach



				</tbody>
			</table>
            <div class="row">
                    {!! $registros->links() !!}
            </div>
		</div>
   	</div>
@endsection
