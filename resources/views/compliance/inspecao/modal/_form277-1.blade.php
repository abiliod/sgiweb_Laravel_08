@if ($count >= 1)
    @if( (isset($sgdo_distribuicao)) && (!empty($sgdo_distribuicao)) )
        <div id="aprimoramento">
            <span class="lever" text-align="rigth">
                Em análise aos registros do sistema SGDO (Relatório Detalhado das Distribuições),
                período de {{\Carbon\Carbon::parse($dtini)->format('d/m/Y')}} a {{\Carbon\Carbon::parse($dtfim)->format('d/m/Y')}},
                constataram-se as seguintes inconsistências relacionadas aos lançamentos obrigatórios:
            </span>
        </div>
        <div id="historico">
            <table class="highlight">
                <thead>
                    <tr>
                        <th>Matricula</th>
                        <th>Data Início Atividade</th>
                        <th>Data Saída</th>
                        <th>Data Retorno</th>
                        <th>Data TPC</th>
                        <th>Data Término Atividade</th>
                    </tr>
                </thead>
                <tbody>

                @foreach($sgdo_distribuicao as $dados)
                    @if (( empty($dados->data_saida)) || (empty($dados->data_retorno))
                        || (empty($dados->data_tpc)) || (empty($dados->data_termino_atividade)))
                        <tr>
                            <td>{{ $dados->matricula }}</td>
                            <td>{{ ($dados->data_incio_atividade == ''  ? 'Falta Lançamento' : \Carbon\Carbon::parse($dados->data_incio_atividade)->format('d/m/Y')) }}</td>
                            @if ((!empty($dados->data_saida)) && ($dados->data_saida <> $dados->data_incio_atividade))
                                <td>Data Saída Diferente  {{\Carbon\Carbon::parse($dados->data_saida)->format('d/m/Y')}}</td>
                            @else
                                <td>{{ ($dados->data_saida == ''  ? 'Falta Lançamento' : \Carbon\Carbon::parse($dados->data_saida)->format('d/m/Y')) }}</td>
                            @endif
                            @if ((!empty($dados->data_retorno)) && ($dados->data_retorno <> $dados->data_incio_atividade))
                                <td>Data Retorno Diferente  {{\Carbon\Carbon::parse($dados->data_retorno)->format('d/m/Y')}}</td>
                            @else
                                <td>{{ ($dados->data_retorno == ''  ? 'Falta Lançamento' : \Carbon\Carbon::parse($dados->data_retorno)->format('d/m/Y')) }}</td>
                            @endif

                            @if ((!empty($dados->data_tpc)) && ($dados->data_tpc <> $dados->data_incio_atividade))
                                <td>Data TPC Diferente {{\Carbon\Carbon::parse($dados->data_tpc)->format('d/m/Y')}}</td>
                            @else
                                <td>{{ ($dados->data_tpc == ''  ? 'Falta Lançamento' : \Carbon\Carbon::parse($dados->data_tpc)->format('d/m/Y')) }}</td>
                            @endif
                            @if ((!empty($dados->data_termino_atividade)) && ($dados->data_termino_atividade <> $dados->data_incio_atividade))
                                <td>Data Término Atividade Diferente {{\Carbon\Carbon::parse($dados->data_termino_atividade)->format('d/m/Y')}} </td>
                            @else
                                <td>{{ ($dados->data_termino_atividade == ''  ? 'Falta Lançamento' : \Carbon\Carbon::parse($dados->data_termino_atividade)->format('d/m/Y')) }}</td>
                            @endif
                        </tr>

                    @endif

                @endforeach



                </tbody>
            </table>
        </div>
        <div id="historico1"></div>
    @endif
@else
    @if (( !empty($dados->data_saida)) || (!empty($dados->data_retorno))
                        || (!empty($dados->data_tpc)) || (!empty($dados->data_termino_atividade)))
        <div id="aprimoramento">
        <span class="lever" text-align="rigth">
            Em análise aos registros do sistema SGDO (Relatório Detalhado das Distribuições), período de {{\Carbon\Carbon::parse($dtini)->format('d/m/Y')}} a {{\Carbon\Carbon::parse($dtfim)->format('d/m/Y')}}, verificaram que não havia inconsistências relacionadas aos lançamentos obrigatórios na unidade.
        </span>
        </div>
        <div id="historico">  </div>
        <div id="historico1"></div>

    @else
        @if($registro->tem_distribuicao == 'Não tem distribuição')
            <div id="aprimoramento">
                <span class="lever rigth">
                        A unidade não executa essa tarefa.
                </span>
            </div>
            <div id="historico"></div>
            <div id="historico1"></div>
        @else
            <div id="aprimoramento">
                <span class="lever" text-align="rigth">
                   Não foi possível avaliar informações referente a unidade no Sistema  SGDO, dado que não há lançamentos sobre as rotinas da Distribuição.
                  <br>Verificaram o período a partir do dia {{\Carbon\Carbon::parse($dtini)->format('d/m/Y')}} até {{\Carbon\Carbon::parse($dtfim)->format('d/m/Y')}} .
                </span>
            </div>
            <div id="historico">  </div>
            <div id="historico1"></div>
        @endif
    @endif
@endif

<input type="hidden"  id="totalfalta" value="{{ isset($total) ? $total : '' }}">
<input type="hidden"  id="totalrisco" value="0.00">
<input type="hidden"  id="totalsobra" value="0.00">
