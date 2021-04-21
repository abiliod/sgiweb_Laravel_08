@if ($countSupervisor >= 1)
    <span class="lever">Marque NÃO VERIFICADO.</span>
    <div id="aprimoramento">
            Avaliado no Item 277.5 a Unidade está provida de {{$countSupervisor}} - Supervisor.
    </div>
    <div id="historico"></div>
    <div id="historico1"></div>
@else
    @if ($count >= 1)
        @if( (isset($painel_extravios)) && (!empty($painel_extravios)) )
            <div id="aprimoramento">
                    Em análise aos relatórios disponíveis no Sistema Painel de Extravios referente
                    ao período de {{\Carbon\Carbon::parse($dtini)->format('d/m/Y')}}
                    até {{\Carbon\Carbon::parse($dtfim)->format('d/m/Y')}},
                    identificaram por meio dos dados contidos na coluna "gesto pré-alerta"
                    a ocorrência de "Gestão Automática" para {{$count}} objeto(s),
                    indicando que não era realizada a "gestão diária" do Pré-Alerta na unidade,
                    conforme relatado a seguir:
                    <br>

            </div>
            <div id="historico">
                <table class="highlight">
                    <thead>
                        <tr>
                            <th>Objeto</th>
                            <th>Data Último Evento </th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($painel_extravios as $dados)
                        <tr>
                            <td>{{ $dados->objeto }}</td>
                            <td> {{\Carbon\Carbon::parse($dados->data_evento)->format('d/m/Y')}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div id="historico1"></div>
        @endif
    @else
        <div id="aprimoramento">
               Em análise aos relatórios disponíveis no Sistema Painel de Extravios referente
                ao período de {{\Carbon\Carbon::parse($dtini)->format('d/m/Y')}}
                até {{\Carbon\Carbon::parse($dtfim)->format('d/m/Y')}}, identificaram por
                meio dos dados contidos na coluna Gestão Pré-alerta que não havia ocorrência
                alusiva à Gestão Automática que sugerisse falha na Gestão do diária da
                Conferência Eletrônica da unidade inspecionada.
        </div>
        <div id="historico"> </div>
        <div id="historico1"></div>
    @endif
@endif
<input type="hidden"  id="totalfalta" value="{{ isset($total) ? $total : '' }}">
<input type="hidden"  id="totalrisco" value="0.00">
<input type="hidden"  id="totalsobra" value="0.00">
