@extends('layouts._sgiweb.app')
@section('content')

    <div class="container">
         <div class="row">
            <table class="row">
                <thead>
                <tr>
                    <th>
                        <h2 class="center">Papel de Trabalho</h2>
                        <h4>Inspeção Regional</h4>
                        <b><br>Unidade: {{ $inspecao->descricao }}.
                            <br> Ciclo: {{ $inspecao->ciclo }}.
                            <br> Tipo Verificação : {{ $inspecao->tipoVerificacao }}.
                            <br> Período do Relatório : {{ \Carbon\Carbon::parse($inspecao->datainiPreInspeção)->format( 'd/m/Y' ) }}.
                            <br> Número do Relatório : {{ $inspecao->codigo }}.
                            <br> Status da Inspeção : {{ $inspecao->status }}.</b>
                    </th>

                </tr>
                </thead>
                <tbody>
                @foreach($registros as $registro)
                    <tr>
                        <td>
                            <b>Grupo:  {{ $registro->numeroGrupoVerificacao }} {{ $registro->nomegrupo }}</b>
                            <br><b>Item -{{ $registro->numeroDoTeste }} :</b> {{ $registro->teste }}
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <b>Avaliação: </b> {{ $registro->avaliacao }}
                            <p class="h5"><b>Oportunidades de aprimoramento/Metodo de Avaliação:</b></p>
                            {{ $registro->oportunidadeAprimoramento }}
                        </td>
                    </tr>

                    <tr>
                        <td>
                            @if ($registro->avaliacao == 'Não Conforme')
                                @if (!empty($registro->evidencia ))
                                    <textarea wrap="hard" style="resize: both; height: 100px; overflow-y: auto;">
                                        {{ $registro->evidencia }}
                                    </textarea>
                                @endif
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <b>Norma: </b>   {{ $registro->norma }}
                        </td>
                    </tr>

                    <tr>
                        <td>
                            @if ($registro->avaliacao == 'Não Conforme')
                                <b>Possiveis consequências: </b>{{ $registro->consequencias }}
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <td>
                            @if (($registro->avaliacao == 'Não Conforme') && ( $registro->itemQuantificado == 'Sim'))

                                <b>Item Quantificado.</b>
                                <br><b>Valores:</b> Falta : {{ isset($registro->valorFalta) ?  'R$'.number_format($registro->valorFalta, 2, ',', '.') : 'R$ 0,00' }} Sobra: {{ isset($registro->valorSobra) ?  'R$'.number_format($registro->valorSobra, 2, ',', '.') : 'R$ 0,00' }} Em Risco: {{ isset($registro->valorRisco) ?  'R$'.number_format($registro->valorRisco, 2, ',', '.') : 'R$ 0,00' }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>
                            @if (($registro->avaliacao == 'Não Conforme') && ( $registro->reincidencia == 'Sim'))
                                <b>Item Reincidente.</b>
                                <br><b>Nº Relatório:</b>
                                {{ isset($registro->codVerificacaoAnterior) ?  $registro->codVerificacaoAnterior : '' }} - Nº Grupo: {{ isset($registro->numeroGrupoReincidente) ?  $registro->numeroGrupoReincidente : '' }} - Nº Item:  {{ isset($registro->numeroItemReincidente) ?  $registro->numeroItemReincidente : '' }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>
                            @if ($registro->avaliacao == 'Não Conforme')
                                <b>Orientações:</b>
                                <br>{{ $registro->orientacao }}
                                @if(!empty($registro->diretorio))
                                    <br><b>Anexo:</b>
                                    @foreach(File::glob(($registro->diretorio.'*.*')) as $imagem)
                                        <div class="row col s12">
                                            <img display:inline; width="120"  src="{{asset( $imagem )}}">
                                        </div>
                                    @endforeach
                                @endif
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>

            </table>
        </div>

    </div>
@endsection
