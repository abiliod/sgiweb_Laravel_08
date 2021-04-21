@if($count== 0)
    <div id="aprimoramento">
        <span class="lever" text-align="rigth">
            Em análise dos registros dos empregados contemplados com Adicionais de Distribuição e Coleta e de Atendimento em Guichê, período de {{substr($dtini,4,2)}}/{{substr($dtini,0,4)}} até {{substr($dtfim,4,2)}}/{{substr($dtfim,0,4)}}. Não identificaram empregado(s) com recebimento pela(s) Rubricas AADC-Adic.Ativ. Distrib/Coleta Ext. Bem como, Adic. de Atend. em Guichê na unidade.
        </span>
    </div>
    <div id="historico">  </div>
    <div id="historico1"></div>
@else

    @if( (isset($pgtoAdicionais)) && (!empty($pgtoAdicionais)) )
        <div id="aprimoramento">
            <span class="lever" text-align="rigth">
                Em análise dos registros dos empregados contemplados com Adicionais de Distribuição e Coleta e de Atendimento em Guichê, do período de {{substr($dtini,4,2)}}/{{substr($dtini,0,4)}} até {{substr($dtfim,4,2)}}/{{substr($dtfim,0,4)}}, disponibilizado pela área de recursos humanos da empresa, constatou-se a existência de empregados que recebiam tais adicionais/funções sem desempenhar as atividades que lhes davam o direito ao recebimento.
            </span>
        </div>
        <div id="historico">
            <span>
                Houve {{$count}} - ocorrência(s) de pagamentos conforme a Seguir:
            </span>
            <table class="highlight">
                <thead>
                    <tr>
                        <th>Matricula</th>
                        <th>Cargo</th>
                        <th>Adicional</th>
                        <th>Período de Rec. Adicional</th>
                        <th>Valor ATT Recebido (R$)</th>
                        <th>Situação Encontrada</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pgtoAdicionais as $dados)
                        <tr>
                            <td>{{ $dados->matricula }}</td>
                            <td>{{ $dados->cargo }}</td>
                            <td>{{ $dados->rubrica }}</td>
                            <td>{{ $dados->ref }}</td>
                            <td>{{ $dados->valor }}</td>
                            <td>{{ $dados->situacao }}</td>
                        </tr>
                    @endforeach
                    @if($total >= 1)
                    <tr>
                        <td colspan = "4">Total Geral
                        </td>
                        <td colspan = "2">
                            {{  'R$ '.number_format($total, 2, ',', '.') }}
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div id="historico1"></div>
    @endif
@endif
<input type="hidden"  id="totalfalta" value="{{ isset($total) ? $total : '' }}">
<input type="hidden"  id="totalrisco" value="0.00">
<input type="hidden"  id="totalsobra" value="0.00">



