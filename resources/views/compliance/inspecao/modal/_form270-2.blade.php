@if(($countproters_con ==0) && ($countproters_peso ==0) && ($countproters_cep ==0))
    <div id="aprimoramento">
        <span class="lever rigth">
            Em análise aos dados do Sistema Proter, do período de Janeiro/2017
            a {{ date( 'd/m/Y' , strtotime($dtmenos90dias))}}, constataram que não
            havia pendências com mais de 90 dias.
        </span>
    </div>
@endif

@if(($countproters_con >=1) || ($countproters_peso >=1) || ($countproters_cep >=1))
    <div id="aprimoramento">
        <span class="lever rigth">
            Em análise aos dados do Sistema Proter, do período de  Janeiro/2017 a  {{ date( 'd/m/Y' , strtotime($dtmenos90dias))}}
            constataram-se as seguintes pendências com mais de 90 dias:
        </span>
    </div>
    <div id="historico">
            @if($countproters_con >=1)
                <span>Pendencia(s) de Contabilização:  {{$countproters_con }} Pendência(s):</span>
                <table class="highlight">
                    <thead>
                    <tr>
                        <th>Data da Pendência</th>
                        <th>Número do Objeto</th>
                        <th>CEP Entrega</th>
                    </tr>
                    </thead>
                <tbody>
                @foreach($proters_con as $proter_con)
                    <tr>
                        <td>{{ date( 'd/m/Y' , strtotime($proter_con->data_da_pendencia))}}</td>
                        <td>{{ $proter_con->no_do_objeto }}</td>
                        <td>{{ $proter_con->cep_entrega_sro }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @endif

            @if($countproters_cep >=1)
                <p><span>CEP Divergente :  {{$countproters_cep }} Pendência(s):</span></p>
                <table class="highlight">
                    <thead>
                    <tr>
                        <th>Data da Pendência</th>
                        <th>Número do Objeto</th>
                        <th>Diferença na Tarifação (R$)</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($proters_cep as $data)
                        <tr>
                            <td>    {{ date( 'd/m/Y' , strtotime($data->data_da_pendencia))}}    </td>
                            <td>    {{ $data->no_do_objeto }}    </td>
                            <td>    {{  'R$ '.number_format($data->diferenca_a_recolher, 2, ',', '.') }}    </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td>Total</td>
                        <td>{{  'R$ '.number_format($total_proters_cep, 2, ',', '.') }}</td>
                    </tr>
                    </tbody>
                </table>
            @endif

            @if($countproters_peso >=1)
                <p> <span>Peso Divergente :  {{$countproters_peso }} Pendência(s):</span> </p>
                <table class="highlight">
                    <thead>
                    <tr>
                        <th>Data da Pendência</th>
                        <th>Número do Objeto</th>
                        <th>Diferença na Tarifação (R$)</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($proters_peso as $proter_peso)
                        <tr>
                            <td>{{ date( 'd/m/Y' , strtotime($proter_peso->data_da_pendencia))}}</td>
                            <td>{{ $proter_peso->no_do_objeto }}</td>
                            <td>{{  'R$ '.number_format($proter_peso->diferenca_a_recolher, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td>Total</td>
                        <td>{{  'R$ '.number_format($total_proters_peso, 2, ',', '.') }}</td>
                    </tr>
                    </tbody>
                </table>
            @endif
    </div>
    <div id="historico1">
        @if(($countproters_peso >=1) || ($countproters_cep >=1))
            <ul>
                <li>Total Geral</li>
                <li>{{  'R$ '.number_format($total, 2, ',', '.') }}</li>
            </ul>
        @endif
    </div>
@endif

<input type="hidden"  id="totalfalta" value="{{ isset($total) ? $total : '' }}">
<input type="hidden"  id="totalrisco" value="0.00">
<input type="hidden"  id="totalsobra" value="0.00">
