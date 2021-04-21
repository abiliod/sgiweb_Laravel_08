@if($total >= 1)
    <div id="aprimoramento">
        <span class="lever rigth">
          Em análise à planilha de controle de processos de apuração de extravios de objetos
            indenizados com responsabilidade definida, disponibilizada pela área de Segurança da
            Superintendência Regional CSEP, que detem informações a partir de 2015 até
            {{\Carbon\Carbon::parse($dtmax)->format('d/m/Y')}}, constatou-se
            a existência de {{$count}} processos pendentes de conclusão há mais de 90 dias sob responsabilidade da unidade, conforme relacionado a seguir:
        </span>
    </div>
    <div id="historico">
        <table class="highlight">
            <thead>
                <tr>
                    <th>Número Objeto</th>
                    <th>Número Processo</th>
                    <th>Data Processo</th>
                    <th>Data Atualização</th>
                    <th>Última Atualização</th>
                    <th>Valor</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($resp_definidas  as $tabela)
            <tr>
                <td>{{ $tabela->objeto }}</td>
                <td>{{(isset($tabela->nu_sei) && $tabela->nu_sei == ''  ? '   ----------  ' : $tabela->nu_sei)}}</td>
                <td>{{ \Carbon\Carbon::parse($tabela->data_pagamento)->format('d/m/Y')}}</td>
                <td>{{(isset($tabela->data) && $tabela->data == ''  ? '   ----------  ' : \Carbon\Carbon::parse($tabela->data)->format('d/m/Y'))}}</td>
                <td>{{(isset($tabela->situacao) && $tabela->situacao == ''  ? '   ----------  ' : $tabela->situacao)}}</td>
                <td>{{ 'R$'.number_format($tabela->valor_da_indenizacao, 2, ',', '.') }}</td>

            </tr>
            @empty
            <tr>
                <th colspan='4'>Não há Registros</th>
            </tr>
            @endforelse
           </tbody>
        </table>
        <p><span>Total</span> {{ isset($total) ?  'R$  '.number_format($total, 2, ',', '.') : '' }}</p>
    </div>
    <div id="historico1"></div>
@else
    <div id="aprimoramento">
        <span class="lever rigth">
            Em análise à planilha de controle de processos de apuração de extravios de objetos
            indenizados com responsabilidade definida, disponibilizada pela área de Segurança da Superintendência
            Regional CSEP, que detem informações a partir de 2015 até
            {{\Carbon\Carbon::parse($dtmax)->format('d/m/Y')}},
            constatou-se a inexistência de processos pendentes de
            conclusão há mais de 90 dias sob responsabilidade da unidade.
        </span>
    </div>
@endif
<input type="hidden"  id="totalfalta" value="{{ isset($total) ?  str_replace(',', '', $total) : '' }}" readonly>
<input type="hidden"  id="totalrisco" value="0.00">
<input type="hidden"  id="totalsobra" value="0.00">







