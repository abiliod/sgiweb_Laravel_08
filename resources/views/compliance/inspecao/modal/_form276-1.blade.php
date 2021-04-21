@if ($count >= 1)
        @if ($reg >= 1)
            <div id="aprimoramento">
                <span>
                    Em análise aos dados do sistema ERP - GESTÃO DE LINHAS DE TRANSPORTE,
                    constataram o descumprimento dos procedimentos de embarque e desembarque da carga,
                    conforme relatado a seguir:
                 <br>{{$viagens}} - viagens prevista(s).
               </span>
            </div>
            <div id="historico">
                <table class="highlight">
                    <thead>
                        <tr>
                            <th>Data Viagem</th>
                            <th>Número da Viagem</th>
                            <th>Tipo Operação</th>
                            <th>Quantidade Unitizadores</th>
                            <th>Peso Unitizadores</th>
                            <th>Tipo Unitizador</th>
                            <th>Tipo Serviço</th>
                            <th>Destino</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($controle_de_viagens as $controle_de_viagen)
                        <tr>
                            <td>{{ ($controle_de_viagen->inicio_viagem == ''  ? '   ----------  ' : \Carbon\Carbon::parse($controle_de_viagen->inicio_viagem)->format('d/m/Y'))}}</td>
                            <td>{{ ($controle_de_viagen->controle_viagem == ''  ? '   ----------  ' : $controle_de_viagen->controle_viagem ) }}</td>
                            <td>{{ ($controle_de_viagen->tipo_de_operacao == ''  ? '   ----------  ' : $controle_de_viagen->tipo_de_operacao ) }}</td>
                            <td>{{ ($controle_de_viagen->quantidade == ''  ? '   ----------  ' : $controle_de_viagen->quantidade ) }}</td>
                            <td>{{ ($controle_de_viagen->peso == ''  ? '   ----------  ' : $controle_de_viagen->peso ) }}</td>
                            <td>{{ ($controle_de_viagen->unitizador == ''  ? '   ----------  ' : $controle_de_viagen->unitizador ) }}</td>
                            <td>{{ ($controle_de_viagen->descricao_do_servico == ''  ? '   ----------  ' : $controle_de_viagen->descricao_do_servico ) }}</td>
                            <td>{{ ($controle_de_viagen->local_de_destino == ''  ? '   ----------  ' : $controle_de_viagen->local_de_destino ) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <span>
                    <br>a) Foram verificada(s) todas programações de viagens no período do dia
                    {{\Carbon\Carbon::parse($dtini)->format('d/m/Y')}} até {{\Carbon\Carbon::parse($dtfim)->format('d/m/Y')}}
                    verificou-se que houve {{$count}} viagen(s) realizadas e com possíveis operações de Embarque/Desembarque a serem realizadas.
                    <br>b) Verificou a necessidade de aprimoramento na qualidade do apontamento colunas com falhas ou informações genéricas/incompletas.
                    <br>c) Verificaram que no período havia {{ $viagens}} viagen(s) prevista(s) sendo que não houve apontamento de EMBARQUE/DESEMBARQUE para {{$viagemNaorealizada}} viagens.
                </span>
            </div>
            <div id="historico1"></div>
        @else
            <div id="aprimoramento">
                <span class="lever">
                    Em análise aos dados do sistema ERP - GESTÃO DE LINHAS DE TRANSPORTE, constatou-se que não havia operações de Embarque/Desembarque em falta ou incompletas. Foram verificada(s) todas programações de viagens no período do dia {{\Carbon\Carbon::parse($dtini)->format('d/m/Y')}} até {{\Carbon\Carbon::parse($dtfim)->format('d/m/Y')}} .
                </span>
            </div>
            <div id="historico"></div>
            <div id="historico1"></div>

        @endif
@else
    <div id="aprimoramento">
        <span class="lever">
            Em análise aos dados do sistema ERP - GESTÃO DE LINHAS DE TRANSPORTE, constataram o descumprimento
            dos procedimentos de embarque e desembarque da carga, conforme relatado a seguir:
        </span>
    </div>
    <div id="historico">
        <span class="lever">
            - Verificou-se que a unidade não está executando os lançamentos das informações de embarque e desembarque
            da carga no Sistema ERP, pois não há histórico de registro de embarque/desembarque para troca de expedições.
            <br>- Foram verificadas as programações de viagens no período de
            {{\Carbon\Carbon::parse($dtini)->format('d/m/Y')}} até {{\Carbon\Carbon::parse($dtfim)->format('d/m/Y')}},
            sendo que em 100% das viagens não houve apontamentos.
        </span>
    </div>
    <div id="historico1"></div>
@endif
<input type="hidden"  id="totalfalta" value="{{ isset($total) ? $total : '' }}">
<input type="hidden"  id="totalrisco" value="0.00">
<input type="hidden"  id="totalsobra" value="0.00">
