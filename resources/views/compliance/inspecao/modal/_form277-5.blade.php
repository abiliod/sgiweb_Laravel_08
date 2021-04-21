@if ($countSupervisor == 0)
<span class="lever" text-align="rigth">
    <span class="lever" text-align="rigth">Marque NÃO VERIFICADO.</span>
    <div id="aprimoramento">
        <span class="lever" text-align="rigth">
            Avaliado no Item 277.6 a Unidade Não está provida de  Supervisor.
        </span>
    </div>
    <div id="historico"></div>
    <div id="historico1"></div>
@else
    @if ($count >= 1)
        @if( (isset($painel_extravios)) && (!empty($painel_extravios)) )
            <div id="aprimoramento">
                <span class="lever" text-align="rigth">
                    Em análise aos relatórios disponíveis no Sistema Painel de Extravios referente ao período de período de {{\Carbon\Carbon::parse($dtini)->format('d/m/Y')}} até {{\Carbon\Carbon::parse($dtfim)->format('d/m/Y')}}, identificaram por meio dos dados contidos na coluna "gesto pré-alerta" a ocorrência de "Gestão Automática" para {{$count}} objetos, indicando que não era realizada a "gestão diária" do Pré-Alerta na unidade, conforme relatado a seguir:
                    <br>
                </span>
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
            <span class="lever" text-align="rigth">
                Foram verificados o período de dia {{\Carbon\Carbon::parse($dtini)->format('d/m/Y')}} até {{\Carbon\Carbon::parse($dtfim)->format('d/m/Y')}}, identificaram por meio dos dados contidos na coluna Gestão Pré-alerta que não havia ocorrência alusiva à Gestão Automática para objetos da unidade, que sugerisse falha na Gestão do diária da Conferência Eletrônica da unidade.
            </span>
        </div>
        <div id="historico"></div>
        <div id="historico1"></div>
    @endif
@endif
<input type="hidden"  id="totalfalta" value="{{ isset($total) ? $total : '' }}">
<input type="hidden"  id="totalrisco" value="0.00">
<input type="hidden"  id="totalsobra" value="0.00">

