@extends('layouts._sgiweb.app')
@section('content')
<div class="container">
	<h2 class="center">Inspeção: {{$registro->descricao}}</h2>
	<div class="row">
	 	<nav>
		    <div class="nav-wrapper green">
		      	<div class="col s12">
			        <a href="{{ route('home')}}" class="breadcrumb">Início</a>
			        <a href="{{route('compliance.verificacoes.search')}}" class="breadcrumb">Verificação</a>
			        <a class="breadcrumb">Inspeção </a>
		      	</div>
		    </div>
		</nav>
	</div>

	<div  class="row col s12">
		<ul class="collapsible" data-collapsible="accordion">
			<li>
				<div class="collapsible-header col s12">
				<i class="material-icons">filter_drama</i>
                Item Inspecionado Identificação: <h5 class="center">Grupo: {{$registro->numeroGrupoVerificacao}} -{{$registro->nomegrupo}} Teste Nº : {{ $registro->numeroDoTeste }} </h5>
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
				    	     <td>Inspeção é Obrigatória:</td>
                             <td>{{ $registro->inspecaoObrigatoria }}</td>
                          </tr>
                          <tr>
                             <td>Grau de Risco -Total de Pontos do Item:</td>
                             <td>{{ $registro->totalPontos }}</td>
                          </tr>
                          <tr>
                             <td>Descrição do Teste:</td>
                             <td>{{ $registro->teste }}</td>
                          </tr>

 				      </tbody>
			        </table>

                </div>
			</li>
			<li>
				<div class="collapsible-header">
				<i class="material-icons">place</i>
				Dados do Item SITUAÇÃO :<h5 class="center">{{ $registro->situacao }}</h5>
				<span class="badge">1</span></div>
				<div class="collapsible-body"><p>
                    <table>
				       <thead>
					      <tr>
                             <th class=" col s2">Campo</th>
						     <th class=" col s10">Descrição</th>
				          </tr>
				       </thead>
				       <tbody>

                          <tr>
                             <td class=" col s2"> Eventos de Gravação:</td>
                             <td class=" col s10">
                                <div class="input-field">
                                    <textarea readonly name="eventosSistema" class="materialize-textarea">
                                    {{ isset($registro->eventosSistema) ? $registro->eventosSistema : '' }}
                                    </textarea>
                                </div>
                            </td>
                          </tr>

 				      </tbody>
			        </table>
              </div>
			</li>
		</ul>
	</div>
		<form action="{{route('compliance.inspecao.atualizar', $id)}}" method="post" enctype="multipart/form-data">
            {{csrf_field()}}
            <input type="hidden" name="_method" value="put">
            <div class="row">
                <!-- Modal Trigger -->

                <button data-target="modal1" class="btn modal-trigger waves-effect waves-light"
                style="{{(isset($registro->preVerificar) && $registro->preVerificar !== 'Não'  ? 'display:block;' : 'display:none;')}};"
                >Checar Evidencias<i class="material-icons right">send</i></button>

                <!-- Modal Structure -->
                <div id="modal1" class="modal modal-fixed-footer">
                    <div class="modal-content" id="modalteste">

                    <div class="row"><!------    teste de opções  ----->
                        <p>
                            <input onChange="avalia('avaliacao')" class="with-gap"
                            name="avaliacao" type="radio" id="avaliacao11"
                            value="Conforme"{{(isset($registro->avaliacao)
                            && $registro->avaliacao == 'Conforme'  ? 'checked' : '')}}/>
                            <label for="avaliacao11">Conforme</label>
                        </p>
                        <p>
                            <input onChange="avalia('avaliacao')" class="with-gap"
                             name="avaliacao"
                             type="radio" id="avaliacao12" value="Não Conforme"{{(isset($registro->avaliacao)
                              && $registro->avaliacao == 'Não Conforme'  ? 'checked' : '')}}/>
                            <label for="avaliacao12">Não Conforme</label>
                        </p>
                        <p>
                            <input onChange="avalia('avaliacao')" class="with-gap"
                             name="avaliacao"
                            type="radio" id="avaliacao13" value="Não Executa Tarefa"{{(isset($registro->avaliacao)
                             && $registro->avaliacao == 'Não Executa Tarefa'  ? 'checked' : '')}}/>
                            <label for="avaliacao13">Não Executa Tarefa</label>
                        </p>
                        <p>
                            <input onChange="avalia('avaliacao')" class="with-gap"
                             name="avaliacao"
                             type="radio" id="avaliacao14" value="Não Verificado"{{(isset($registro->avaliacao)
                              && $registro->avaliacao == 'Não Verificado'  ? 'checked' : '')}}/>
                            <label for="avaliacao14">Não Verificado</label>
                        </p>
                    </div>
                        <div>

                            @if( (($registro->numeroGrupoVerificacao == 230)&&($registro->numeroDoTeste == 4))
	                            || ( ($registro->numeroGrupoVerificacao==270)&&($registro->numeroDoTeste==1) ))

                                    @include('compliance.inspecao.modal._form270-1')
                            @endif

                            @if((($registro->numeroGrupoVerificacao == 202)&&($registro->numeroDoTeste == 1))
                                || (($registro->numeroGrupoVerificacao == 332)&&($registro->numeroDoTeste ==1))
                                || (($registro->numeroGrupoVerificacao == 213)&&($registro->numeroDoTeste ==1))
                                || (($registro->numeroGrupoVerificacao == 230)&&($registro->numeroDoTeste == 5))
                                || (($registro->numeroGrupoVerificacao == 270)&&($registro->numeroDoTeste == 2)))

                                    @include('compliance.inspecao.modal._form270-2')
                            @endif

                            @if( (($registro->numeroGrupoVerificacao == 230)&&($registro->numeroDoTeste == 6))
                                || (($registro->numeroGrupoVerificacao==270)&&($registro->numeroDoTeste==3)) )

                                    @include('compliance.inspecao.modal._form270-3')
                            @endif

                            @if( (($registro->numeroGrupoVerificacao == 230)&&($registro->numeroDoTeste == 7))
                                || (($registro->numeroGrupoVerificacao==270)&&($registro->numeroDoTeste==4)) )
                                    @include('compliance.inspecao.modal._form270-4')
                            @endif

                            @if( (($registro->numeroGrupoVerificacao == 205)&&($registro->numeroDoTeste == 2))
                                || (($registro->numeroGrupoVerificacao==334)&&($registro->numeroDoTeste==1))
                                || (($registro->numeroGrupoVerificacao==372)&&($registro->numeroDoTeste==1))
                                || (($registro->numeroGrupoVerificacao==286)&&($registro->numeroDoTeste==2))
                                || (($registro->numeroGrupoVerificacao==221)&&($registro->numeroDoTeste==2))
                                || (($registro->numeroGrupoVerificacao==354)&&($registro->numeroDoTeste==1))
                                || (($registro->numeroGrupoVerificacao == 231)&&($registro->numeroDoTeste == 1))
                                || (($registro->numeroGrupoVerificacao==271)&&($registro->numeroDoTeste==1)) )

                                    @include('compliance.inspecao.modal._form271-1')
                            @endif

                            @if( (($registro->numeroGrupoVerificacao==232)&&($registro->numeroDoTeste==2))
	                            || ( ($registro->numeroGrupoVerificacao==272)&&($registro->numeroDoTeste==1) ))

                                    @include('compliance.inspecao.modal._form272-1')
                            @endif


                            @if((($registro->numeroGrupoVerificacao == 206 )&&($registro->numeroDoTeste == 1 ))
                                || (( $registro->numeroGrupoVerificacao == 335 ) && ( $registro->numeroDoTeste == 1 ))
                                || (($registro->numeroGrupoVerificacao == 232)&&($registro->numeroDoTeste == 3 ))
                                || (( $registro->numeroGrupoVerificacao == 272 ) && ( $registro->numeroDoTeste == 2 )))

                                    @include('compliance.inspecao.modal._form272-2')
                            @endif

                            @if((($registro->numeroGrupoVerificacao==206) && ($registro->numeroDoTeste==2))
                                || (($registro->numeroGrupoVerificacao==335) && ($registro->numeroDoTeste==2))
                                || (($registro->numeroGrupoVerificacao==232) && ($registro->numeroDoTeste==4))
                                || (($registro->numeroGrupoVerificacao==272) && ($registro->numeroDoTeste==3)))

                                    @include('compliance.inspecao.modal._form272-3')
                            @endif

                            @if((($registro->numeroGrupoVerificacao==206) && ($registro->numeroDoTeste==3))
                                || (($registro->numeroGrupoVerificacao==335) && ($registro->numeroDoTeste==3))
                                || (($registro->numeroGrupoVerificacao==232) && ($registro->numeroDoTeste==6))
                                || (($registro->numeroGrupoVerificacao==272) && ($registro->numeroDoTeste==4)))

                                    @include('compliance.inspecao.modal._form272-4')
                            @endif

                            @if((($registro->numeroGrupoVerificacao==207) && ($registro->numeroDoTeste==3))
                               || (($registro->numeroGrupoVerificacao==336) && ($registro->numeroDoTeste==1))
                               || (($registro->numeroGrupoVerificacao==231) && ($registro->numeroDoTeste==1))
                               || (($registro->numeroGrupoVerificacao==273) && ($registro->numeroDoTeste==1)))

                                    @include('compliance.inspecao.modal._form273-1')
                            @endif

                            @if((($registro->numeroGrupoVerificacao==212) && ($registro->numeroDoTeste==2))
                                || (($registro->numeroGrupoVerificacao==350) && ($registro->numeroDoTeste==1))
                                || (($registro->numeroGrupoVerificacao==235) && ($registro->numeroDoTeste==4))
                                || (($registro->numeroGrupoVerificacao==274) && ($registro->numeroDoTeste==1))))

                                   @include('compliance.inspecao.modal._form274-1')
                            @endif

                            @if( (($registro->numeroGrupoVerificacao==204)&&($registro->numeroDoTeste==1))
                                || (($registro->numeroGrupoVerificacao==233)&&($registro->numeroDoTeste==1))  //cee remoto nao está mostrando o formulario
                                || (($registro->numeroGrupoVerificacao==228)&&($registro->numeroDoTeste==2))  //cee remoto nao está mostrando o formulario
                                || (($registro->numeroGrupoVerificacao==237)&&($registro->numeroDoTeste==1))
                                || ( ($registro->numeroGrupoVerificacao==275)&&($registro->numeroDoTeste==1 )))

                                    @include('compliance.inspecao.modal._form275-1')
                            @endif

                            @if((($registro->numeroGrupoVerificacao==200) && ($registro->numeroDoTeste==1))
                                || (($registro->numeroGrupoVerificacao==330) && ($registro->numeroDoTeste==1))
                                || (($registro->numeroGrupoVerificacao==287) && ($registro->numeroDoTeste==2))
                                || (($registro->numeroGrupoVerificacao==222) && ($registro->numeroDoTeste==4))
                                || (($registro->numeroGrupoVerificacao==239) && ($registro->numeroDoTeste==1))
                                || (($registro->numeroGrupoVerificacao==276) && ($registro->numeroDoTeste==1)))

                                    @include('compliance.inspecao.modal._form276-1')
                            @endif

                            @if((($registro->numeroGrupoVerificacao==201) && ($registro->numeroDoTeste==1))
                                || (($registro->numeroGrupoVerificacao==331) && ($registro->numeroDoTeste==1))
                                || (($registro->numeroGrupoVerificacao==240) && ($registro->numeroDoTeste==1))
                                || (($registro->numeroGrupoVerificacao==277) && ($registro->numeroDoTeste==1)))

                                    @include('compliance.inspecao.modal._form277-1')
                            @endif

                            @if((($registro->numeroGrupoVerificacao==201) && ($registro->numeroDoTeste==5))
                                || (($registro->numeroGrupoVerificacao==331) && ($registro->numeroDoTeste==4))
                                || (($registro->numeroGrupoVerificacao==240) && ($registro->numeroDoTeste==3))
                                || (($registro->numeroGrupoVerificacao==277) && ($registro->numeroDoTeste==2)))

                                    @include('compliance.inspecao.modal._form277-2')
                            @endif

                            @if((($registro->numeroGrupoVerificacao==201) && ($registro->numeroDoTeste==6))
                                || (($registro->numeroGrupoVerificacao==331) && ($registro->numeroDoTeste==5))
                                || (($registro->numeroGrupoVerificacao==240) && ($registro->numeroDoTeste==4))
                                || (($registro->numeroGrupoVerificacao==277) && ($registro->numeroDoTeste==3)))

                                    @include('compliance.inspecao.modal._form277-3')
                            @endif

                            @if( (($registro->numeroGrupoVerificacao==201)&&($registro->numeroDoTeste==7))
	                            || ( ($registro->numeroGrupoVerificacao==331)&&($registro->numeroDoTeste==6) )
	                            || (($registro->numeroGrupoVerificacao==240)&&($registro->numeroDoTeste==5))
	                            || ( ($registro->numeroGrupoVerificacao==277)&&($registro->numeroDoTeste==4) ))

                                    @include('compliance.inspecao.modal._form277-4')
                            @endif

                            @if((($registro->numeroGrupoVerificacao==201) && ($registro->numeroDoTeste==15))
                                || (($registro->numeroGrupoVerificacao==331) && ($registro->numeroDoTeste==11))
                                || (($registro->numeroGrupoVerificacao==240) && ($registro->numeroDoTeste==8))
                                || (($registro->numeroGrupoVerificacao==277) && ($registro->numeroDoTeste==5)))

                                    @include('compliance.inspecao.modal._form277-5')
                            @endif

                            @if((($registro->numeroGrupoVerificacao==240) && ($registro->numeroDoTeste==7))
                                || (($registro->numeroGrupoVerificacao==277) && ($registro->numeroDoTeste==6)))

                                    @include('compliance.inspecao.modal._form277-6')
                            @endif

                            @if((($registro->numeroGrupoVerificacao==201) && ($registro->numeroDoTeste==9))
                                || (($registro->numeroGrupoVerificacao==331) && ($registro->numeroDoTeste==8))
                                || (($registro->numeroGrupoVerificacao==240) && ($registro->numeroDoTeste==9))
                                || (($registro->numeroGrupoVerificacao==277) && ($registro->numeroDoTeste==7)))

                                    @include('compliance.inspecao.modal._form277-7')
                            @endif

                            @if((($registro->numeroGrupoVerificacao==209) && ($registro->numeroDoTeste==2))
                                || (($registro->numeroGrupoVerificacao==337) && ($registro->numeroDoTeste==1))
                                || (($registro->numeroGrupoVerificacao==241) && ($registro->numeroDoTeste==2))
                                || (($registro->numeroGrupoVerificacao==278) && ($registro->numeroDoTeste==1)))

                                    @include('compliance.inspecao.modal._form278-1')
                            @endif

                            @if((($registro->numeroGrupoVerificacao==209)&&($registro->numeroDoTeste==3))
                                || (($registro->numeroGrupoVerificacao==337)&&($registro->numeroDoTeste==2))
                                || (($registro->numeroGrupoVerificacao==241)&&($registro->numeroDoTeste==3))
                                || (($registro->numeroGrupoVerificacao==278)&&($registro->numeroDoTeste==2)))

                                    @include('compliance.inspecao.modal._form278-2')
                            @endif

                            @if(($registro->numeroGrupoVerificacao==500)&&($registro->numeroDoTeste==1))

                                    @include('compliance.inspecao.modal._form500-1')
                            @endif
                        </div>
                    </div>

                    <div class="modal-footer">
                    <div class="row">

                    </div>
                    <a href="#" class="modal-action modal-close waves-effect waves-green btn-flat " id="fecharModal">Fechar</a>
                    </div>
                </div>
            </div>
			<div class="row">
        		@include('compliance.inspecao._form')
			</div>
			<div class="row">
            @if(($registro->situacao=='Em Inspeção'))
                <button class="btn orange" id="salvar" name="situacao" value="cancel">Avaliar Depois</button>
                <button class="btn blue" id="salvar" name="situacao" value="Em Inspeção">Atualizar</button>
                <button class="btn blue" id="salvar" name="situacao"  value="Inspecionado">Salvar e Concluir</button>
                <button disabled class="btn blue" id="salvar" name="situacao" value="Corroborado">Corroborar</button>
                @elseif(($registro->situacao=='Inspecionado'))
                    <button class="btn orange" id="salvar" name="situacao" value="cancel">Corroborar Depois</button>
                    <button disabled class="btn blue" id="salvar" name="situacao" value="Em Inspeção">Atualizar</button>
                    <button disabled class="btn blue" id="salvar" name="situacao"  value="Inspecionado">Salvar e Concluir</button>
                    <button class="btn blue" id="salvar" name="situacao" value="Corroborado">Corroborar</button>
            @endif
           </div>
		</form>

</div>
@endsection

