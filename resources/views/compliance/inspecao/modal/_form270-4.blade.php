@if($total == 0.00)
    <div id="aprimoramento">
        <span class="lever rigth">
            Em análise ao Relatório "Saldo de Numerário em relação ao Limite de Saldo",
            do sistema BDF,
            referente ao período de {{\Carbon\Carbon::parse($dtnow)->format('d/m/Y')}}
            a  {{\Carbon\Carbon::parse($dtmenos90dias)->format('d/m/Y')}}, constataram que não houve
            descumprimento do limite de saldo estabelecido para a unidade.
        </span>
    </div>
    <div id="historico"></div>
    <div id="historico1"></div>
@else
    <div id="aprimoramento">
        <span class="lever rigth">
            Em análise ao Relatório “Saldo de Numerário em relação ao Limite de Saldo", do sistema BDF,
            referente ao período de
            {{\Carbon\Carbon::parse($dtmenos90dias)->format('d/m/Y')}}, a
            {{\Carbon\Carbon::parse($dtnow)->format('d/m/Y')}}
            constatou-se que o limite do
            saldo estabelecido para a unidade foi descumprido em {{$ocorrencias}} dias,
            o que corresponde a uma média de {{ $mediaocorrencias }} ocorrências por mês, considerando o
            período, conforme detalhado a seguir:
        </span>
    </div>
    <div id="historico">

        <p></p>
        @if(! $sl02bdfs30->isEmpty())
            <span class="lever rigth">
               No período de {{\Carbon\Carbon::parse($dtmenos30dias)->format('d/m/Y')}} a
                {{\Carbon\Carbon::parse($dtnow)->format('d/m/Y')}}, constataram que o limite do
               saldo estabelecido para a unidade foi descumprido em {{$ocorrencias30}} dias.  Média de {{ (number_format(($ocorrencias30/22)*100, 2, ',', '.')) }} por cento dos dias no período de 30 dias, e saldo médio ultrapassado de
               {{ 'R$'.number_format($acumulados30/$ocorrencias30, 2, ',', '.') }}.
            </span>
            <table class="highlight">
                <thead>
                <tr>
                    <th>Data</th>
                    <th>Saldo de Numerário</th>
                    <th>Limite de Saldo</th>
                    <th>Diferença</th>
                </tr>
                </thead>
                <tbody>
                @foreach($sl02bdfs30 as $tabela)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($tabela->dt_movimento)->format('d/m/Y')}}</td>
                        <td>{{ 'R$'.number_format($tabela->saldo_atual, 2, ',', '.') }}</td>
                        <td>{{ 'R$'.number_format($tabela->limite, 2, ',', '.') }}</td>
                        <td>{{ 'R$'.number_format($tabela->diferenca, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
                    <tr>
                        <td>Total</td>
                        <td>{{  'R$ '.number_format($acumulados30, 2, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        @endif
        <p></p>
        @if(! $sl02bdfs60->isEmpty())
                <span class="lever rigth">
               No período de {{\Carbon\Carbon::parse($dtmenos60dias)->format('d/m/Y')}} a {{\Carbon\Carbon::parse($dtmenos30dias)->format('d/m/Y')}}, constataram que o limite do
               saldo estabelecido para a unidade foi descumprido em {{$ocorrencias60}} dias.  Média de {{ (number_format(($ocorrencias60/22)*100, 2, ',', '.')) }} por cento dos dias no período de 30 dias, e saldo médio ultrapassado de
               {{ 'R$'.number_format($acumulados60/$ocorrencias60, 2, ',', '.') }}.
            </span>
                <table class="highlight">
                    <thead>
                    <tr>
                        <th>Data</th>
                        <th>Saldo de Numerário</th>
                        <th>Limite de Saldo</th>
                        <th>Diferença</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($sl02bdfs60 as $tabela)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($tabela->dt_movimento)->format('d/m/Y')}}</td>
                            <td>{{ 'R$'.number_format($tabela->saldo_atual, 2, ',', '.') }}</td>
                            <td>{{ 'R$'.number_format($tabela->limite, 2, ',', '.') }}</td>
                            <td>{{ 'R$'.number_format($tabela->diferenca, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td>Total</td>
                        <td>{{  'R$ '.number_format($acumulados60, 2, ',', '.') }}</td>
                    </tr>
                    </tbody>
                </table>
            @endif
        <p></p>
        @if(! $sl02bdfs90->isEmpty())
            <span class="lever rigth">
               No período de {{\Carbon\Carbon::parse($dtmenos90dias)->format('d/m/Y')}} a {{\Carbon\Carbon::parse($dtmenos60dias)->format('d/m/Y')}}, constataram que o limite do
               saldo estabelecido para a unidade foi descumprido em {{$ocorrencias90}} dias.  Média de {{ (number_format(($ocorrencias90/22)*100, 2, ',', '.')) }} por cento dos dias no período de 30 dias, e saldo médio ultrapassado de
               {{ 'R$'.number_format($acumulados90/$ocorrencias90, 2, ',', '.') }}.
            </span>
            <table class="highlight">
                <thead>
                <tr>
                    <th>Data</th>
                    <th>Saldo de Numerário</th>
                    <th>Limite de Saldo</th>
                    <th>Diferença</th>
                </tr>
                </thead>
                <tbody>
                @foreach($sl02bdfs90 as $tabela)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($tabela->dt_movimento)->format('d/m/Y')}}</td>
                        <td>{{ 'R$'.number_format($tabela->saldo_atual, 2, ',', '.') }}</td>
                        <td>{{ 'R$'.number_format($tabela->limite, 2, ',', '.') }}</td>
                        <td>{{ 'R$'.number_format($tabela->diferenca, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td>Total</td>
                    <td>{{  'R$ '.number_format($acumulados90, 2, ',', '.') }}</td>
                </tr>
                </tbody>
            </table>
        @endif
        <p></p>

    </div>
    <div id="historico1"></div>

@endif
<input type="hidden"  id="totalrisco" value="{{ isset($total) ?  $total : '' }}">
<input type="hidden"  id="totalfalta" value="0.00">
<input type="hidden"  id="totalsobra" value="0.00">
