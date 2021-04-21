@if ($count == 0)
    <div id="aprimoramento">
        <span class="lever" text-align="rigth">
            Em análise aos registros do Sistema PGP e aos dados do Relatório “Arme e Desarme” do Sistema Monitorado de Alarme, constatou-se que não há inconsistências quanto ao lançamento de serviços extras no período de {{ $dtini }} a {{ $reffinal }}.
            Adicionalmente verificaram que a partir de 01/09/2020, não constataram  pagamentos do Adicional de Fim de Semana.
        </span>
    </div>
    <div id="historico">  </div>
    <div id="historico1"></div>
@else
    @if( (isset($pgtoAdicionais)) && (!empty($pgtoAdicionais)) )
        <div id="aprimoramento">
            <span class="lever" text-align="rigth">
                Em análise aos registros do Sistema PGP e aos dados do Relatório “Arme e Desarme” do sistema de alarme, no período de {{ $dtini }}  a {{ $reffinal }}, constataram-se as seguintes inconsistências:
            </span>
        </div>
        <div id="historico">
            <span>
                Verificaram {{$count}} - ocorrência(s) de pagamentos conforme a Seguir:
            </span>
            <table class="highlight">
                <thead>
                    <tr>
                        <th>Matricula</th>
                        <th>Cargo</th>
                        <th>Tipo do Provento</th>
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

