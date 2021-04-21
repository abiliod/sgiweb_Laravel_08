@extends('layouts._sgiweb.app')
@section('content')
<div class="container">
	<h3 class="center">Verificação: {{$registro->descricao}}</h3>
        <nav>
			<div class="nav-wrapper green">
				<div class="col s12">
					<a href="{{ route('home')}}" class="breadcrumb">Início</a>
					<a class="breadcrumb">Controle de Verificação</a>
				</div>
			</div>
		</nav>

        <div class="row col-sm-auto">
		<table class="float-right">
				<thead>
					<tr>
                        <th>Inspeção</th>
						<th>Grupo</th>
                        <th>Nº Teste</th>
                        <th>Objeto</th>
                        @if (($registro->numeroGrupoVerificacao==277) && ($registro->numeroDoTeste==2))
                            <th>Desc. Evento Baixa</th>
                        @elseif (($registro->numeroGrupoVerificacao==277) && ($registro->numeroDoTeste==3))
                            <th>
                                <p>
                                    Link:  <a href="https://ruas-brasil.openalfa.com/goias" target="_blank">Ruas do Brasil </a>
                                </p>
                                <p>
                                    Link:  <a href="https://www.google.com.br/maps" target="_blank">Google Maps </a>
                                </p>
                                <p>


                                <p>
                                <a class="btn waves-effect waves-teal orange"
                                   href="{{ url('/compliance/inspecao/exportsro',$registro->codigo) }}">
                                    Export File
                                    <i class="material-icons right">file_download</i></a>

                                </p>


                            </th>

                        @endif
					</tr>
				</thead>
				<tbody>
				@foreach($res as $dado)
                    @if (($dado->numeroGrupoVerificacao==277) && ($dado->numeroDoTeste==2))
					<tr>
				    	<td class="col-sm-1" >{{ $dado->codigo }}</td>
                        <td class="col-sm-1" >{{ $dado->numeroGrupoVerificacao }}</td>
                        <td class="col-sm-1" >{{ $dado->numeroDoTeste }}</td>
                        <td class="col-sm-1" >
                            <a href="http://wsmobile.correiosnet.int/ComprovanteSRO/entrega?ITEMCODE={{ $dado->objeto }}" target="_blank">{{ $dado->objeto }}</a>
                            <a href="http://websro2.correiosnet.int/rastreamento/sro?opcao=PESQUISA&objetos={{ $dado->objeto }} " target="_blank"> &nbsp;&nbsp;&nbsp; [ Agrupado ?]</a>
                        </td>

                        <td class="col-sm-1" >{{ $dado->localBaixa1tentativa }}</td>

                        <td>
                            <form action="{{route('compliance.inspecao.editar.sro', $dado->id )}}" method="post">
                                @CSRF
                                <input type="hidden" name="_method" value="put">

                                <input type="hidden"  name="item"
                                       value="{{ $id }}">
                                <input type="hidden"  name="codigo"
                                       value="{{ isset($dado->codigo ) ? $dado->codigo  : '' }}">
                                <input type="hidden"  name="numeroGrupoVerificacao"
                                       value="{{ isset($dado->numeroGrupoVerificacao ) ? $dado->numeroGrupoVerificacao  : '' }}">
                                <input type="hidden"  name="numeroDoTeste"
                                       value="{{ isset($dado->numeroDoTeste ) ? $dado->numeroDoTeste  : '' }}">
                                <input type="hidden"  name="estado"
                                       value="Avaliado">

                                <select id="falhaDetectada"  name="falhaDetectada">
                                    <option value="Ok" selected>Conforme</option>
                                    <option value="Assinatura ilegível">Assinatura ilegível</option>
                                    <option value="Falta assinatura do recebedor" >Falta Assinatura do recebedor</option>
                                    <option value="Dados da assinatura não identifica o recebedor" >Dados da Assinatura não identifica o Recebedor</option>
                                    <option value="Documento utilizado na imagem incorreto" >Documento utilizado na imagem incorreto</option>
                                    <option value="Imagem não registrada">Imagem não registrada</option>
                                    <option value="Imagem não consta o nº do objeto" >Imagem não consta o nº do objeto</option>
                                    <option value="Imagem não corresponde ao objeto baixa não agrupada" >Imagem não corresponde ao objeto baixa não agrupada</option>
                                    <option value="Imagem não corresponde ao Objeto">Imagem Não Corresponde ao Objeto</option>
                                    <option value="Imagem desfocada">Imagem desfocada</option>
                                    <option value="Imagem do AR não identifica o objeto/AR">Imagem do AR não Identifica o Objeto/AR</option>
                                    <option value="Objeto consta como não entregue no SRO">Objeto consta como não entregue no SRO</option>
                                </select>

                                <button class="btn blue float-left" >Confirmar</button>

                        </form>
                            </td>
					</tr>
                    @endif

                    @if (($dado->numeroGrupoVerificacao==277) && ($dado->numeroDoTeste==3))
                        <tr >
                            <td class="col-sm-1" >{{ $dado->codigo }}</td>
                            <td class="col-sm-1">{{ $dado->numeroGrupoVerificacao }}</td>
                            <td class="col-sm-1">{{ $dado->numeroDoTeste }}</td>
                            <td class="col-sm-1">
                                <a href="http://websro2.correiosnet.int/rastreamento/sro?opcao=PESQUISA&objetos={{ $dado->objeto }}" target="_blank">{{ $dado->objeto }}</a>
                            </td >
<td>
                                <form action="{{route('compliance.inspecao.editar.sro', $dado->id )}}" method="post">

                                    @CSRF
                                    <input type="hidden" name="_method" value="put">

                                    <input type="hidden"  name="item"
                                           value="{{ $id }}">
                                    <input type="hidden"  name="codigo"
                                           value="{{ isset($dado->codigo ) ? $dado->codigo  : '' }}">
                                    <input type="hidden"  name="numeroGrupoVerificacao"
                                           value="{{ isset($dado->numeroGrupoVerificacao ) ? $dado->numeroGrupoVerificacao  : '' }}">
                                    <input type="hidden"  name="numeroDoTeste"
                                           value="{{ isset($dado->numeroDoTeste ) ? $dado->numeroDoTeste  : '' }}">
                                    <input type="hidden"  name="estado"
                                           value="Avaliado">
                                    <input class="col-sm-2 " type="text"  name="enderecoPostagem"
                                           placeholder="Endereço LOEC"   value="">
                                    <input  class="col-sm-2" type="text"  name="localBaixa1tentativa"
                                          placeholder="Endereço LOEC Endereço Baixa 1ª Tentativa" value="">
                                    <select class="col-sm-2" id="falhaDetectada"  name="falhaDetectada" >
                                        <option value="Ok" selected>Conforme</option>
                                        <option value="Divergência de local previsto e baixado">Divergência de local previsto e baixado</option>
                                        <option value="Endereço Loec indisponível para comparação">Endereço Loec indisponivel para comparação</option>
                                        <option value="Logradouro lançado manualmente na LOEC não foi localizado em pesquisa">Logradouro lançado manualmente na LOEC não foi localizado em pesquisa</option>
                                        <option value="Não há dados da coordenada">Não há dados da coordenada</option>
                                        <option value="A Entrega não utilizou o SmartPhone">A Entrega não utilizou o SmartPhone</option>
                                    </select>

                                    <button class="col-sm-2 btn blue float-left" >Confirmar</button>


                                </form>
</td>
                        </tr>
                    @endif
                @endforeach
				</tbody>
			</table>
		</div>
   	</div>
@endsection
