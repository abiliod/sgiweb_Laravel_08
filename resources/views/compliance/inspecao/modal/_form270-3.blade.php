@if($total != 0.00)
    <div id="aprimoramento">
        <span class="lever rigth">
            Em análise ao sistema SDE – Sistema de Depósito, na opção "Contabilização",
            Conciliação SMB x BDF – dados “Não Conciliados”, em {{ date( 'd/m/Y' , strtotime($dtnow))}},
            referente ao período de {{ date( 'd/m/Y' , strtotime($dtini))}}
            a {{ date( 'd/m/Y' , strtotime($dtfim))}},
            constatou-se a existência de divergências entre o valor depositado na conta bancária dos
            Correios pela Agência e o valor do bloqueto gerado no sistema SARA, no total de
            {{  'R$ '.number_format($total, 2, ',', '.') }}, {{($total > 0 ? ' Valor em Falta' : ' Valor em Sobra')}},
            conforme relacionado a seguir:
        </span>
    </div>
    <div id="historico">
        @if(isset($smb_bdf_naoconciliados)&&(!empty($smb_bdf_naoconciliados)))
        <table class="highlight">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Divergência</th>
                    <th>Tipo</th>
                </tr>
            </thead>
            <tbody>
            @foreach($smb_bdf_naoconciliados as $smb_bdf_naoconciliado)
                <tr>
                    <td>{{ date( 'd/m/Y' , strtotime($smb_bdf_naoconciliado->Data))}}</td>
                    <td>{{  'R$ '.number_format($smb_bdf_naoconciliado->Divergencia, 2, ',', '.') }}</td>
                    @if(($smb_bdf_naoconciliado->BDFDinheiro<>0) && ($smb_bdf_naoconciliado->BDFCheque<>0) && ($smb_bdf_naoconciliado->BDFBoleto<>0))
                            <td>Dinheiro/Cheque/Boleto</td>
                        @elseif (($smb_bdf_naoconciliado->BDFDinheiro<>0) && ($smb_bdf_naoconciliado->BDFBoleto<>0))
                            <td>Dinheiro/Boleto</td>
                        @elseif (($smb_bdf_naoconciliado->BDFDinheiro<>0) && ($smb_bdf_naoconciliado->BDFCheque<>0))
                            <td>Dinheiro/Cheque</td>
                        @elseif (($smb_bdf_naoconciliado->BDFBoleto<>0) && ($smb_bdf_naoconciliado->BDFCheque<>0))
                            <td>Boleto/Cheque</td>
                        @elseif ($smb_bdf_naoconciliado->BDFDinheiro<>0)
                            <td>Dinheiro</td>
                        @elseif ($smb_bdf_naoconciliado->BDFBoleto<>0)
                            <td>Boleto</td>
                        @elseif ($smb_bdf_naoconciliado->BDFCheque<>0)
                            <td>Cheque</td>
                    @else
                        <td>Não identificado</td>
                    @endif
                </tr>
            @endforeach
            <tr>
                <td>{{($total > 0 ? ' Valor em Falta' : ' Valor em Sobra')}}</td>
                <td>{{  'R$ '.number_format($total, 2, ',', '.') }}</td>
            </tr>
            </tbody>
        </table>
    @endif
    </div>
    <div id="historico1"></div>
@endif
@if($total == 0.00)
<div id="aprimoramento">
    <span class="lever rigth">
        Em análise ao sistema SDE – Sistema de Depósito, na opção
        "Contabilização", Conciliação SMB x BDF – dados “Não Conciliados”, em
         {{ date( 'd/m/Y' , strtotime($dtnow))}}, referente ao período de
        {{ date( 'd/m/Y' , strtotime($dtini))}} a {{ date( 'd/m/Y' , strtotime($dtfim))}},
        verificou-se a inexistência de divergências.
    </span>
</div>
<div id="historico"></div>
<div id="historico1"></div>
@endif

<input type="hidden"  id="totalfalta" value="{{ $total > 0 ? $total : '' }}">
<input type="hidden"  id="totalsobra" value="{{ $total < 0 ? ($total*-1) : '' }}">
<input type="hidden"  id="totalrisco" value="0.00">
