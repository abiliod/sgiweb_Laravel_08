@if ($count >= 1)
    @if( (isset($cie_eletronicas)) && (!empty($cie_eletronicas)) )
    <div id="aprimoramento">
        <span class="lever">
            Em consulta realizada ao sistema de CIE Eletrônica do período de {{\Carbon\Carbon::parse($dtini)->format('d/m/Y')}} até {{\Carbon\Carbon::parse($dtfim)->format('d/m/Y')}} constatou-se as seguintes situações:
        </span>
   </div>
    <div id="historico">

        <span>{{$count}} - ocorrência(s) de Mensagens não respondida(s) com prazo superior a 3 dias, conforme a Seguir:</span>
        <table class="highlight">
            <thead>
                <tr>
                    <th>Nº CIE</th>
                    <th>Data</th>
                    <th>Origem</th>
                    <th>Irregularidade</th>
                    <th>Categoria</th>
                </tr>
            </thead>
            <tbody>
            @foreach($cie_eletronicas as $dados)
                <tr>
                    <td>{{ ($dados->numero == ''  ? 'Não Consta' : $dados->numero) }}</td>
                    <td> {{\Carbon\Carbon::parse($dados->emissao)->format('d/m/Y H:m:s')}}</td>
                    <td>{{ $dados->origem }}</td>
                    <td>{{ $dados->irregularidade }}</td>
                    <td>{{ $dados->categoria }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div id="historico1"></div>
    @endif
    @else
    <div id="aprimoramento">
        <span class="lever">
            Em consulta realizada no Sistema de CIE Eletrônica do período
            de {{\Carbon\Carbon::parse($dtini)->format('d/m/Y')}}
            a {{\Carbon\Carbon::parse($dtfim)->format('d/m/Y')}} não foram constatadas inconformidades.
        </span>
    </div>
    <div id="historico">  </div>
    <div id="historico1"></div>
@endif
<input type="hidden"  id="totalfalta" value="{{ isset($total) ? $total : '' }}">
<input type="hidden"  id="totalrisco" value="0.00">
<input type="hidden"  id="totalsobra" value="0.00">
