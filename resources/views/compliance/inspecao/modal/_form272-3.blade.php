@if ( !empty($naoMonitorado) )

    <div id="aprimoramento">
        <span>
            Em análise aos dados do Relatório de “Arme e Desarme” do Sistema de Alarme do período {{date("d/m/Y", strtotime($dtmenos12meses))}}  a {{date("d/m/Y", strtotime($dtnow))}} , constatou-se que:
        </span>
    </div>
    <div id="historico">{{ $naoMonitorado }}</div>
    <div id="historico1"></div>
@else

    @if ($count >= 1)
       <!--- nem nulo nem vazio-->
    <div id="aprimoramento">
        <span>
           Em análise aos dados do Relatório de Arme/Desarme do Sistema de Alarme, do Controle de Férias CEGEP e do Sistema PGP. Período de {{  \Carbon\Carbon::parse($dtnow)->format('d/m/Y') }}  a {{  \Carbon\Carbon::parse($dtfim)->format('d/m/Y') }}, constataram a utilização da senha de empregado em que no período mencionado não se encontrava na unidade. O que indicava a prática de compartilhamento de senha de alarme para acesso à unidade. Encontraram {{$count}} - ocorrência(s) em períodos oficiais de ausência do trabalho conforme a seguir:
        </span>
    </div>
    <div id="historico">
        <table class="highlight">
            <thead>
                <tr>
                    <th>Evento</th>
                    <th>Matricula</th>
                    <th>Data</th>
                    <th>Tipo Afastamento</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($compartilhaSenhas  as $compartilhaSenha)

                        <tr>
                            <td>{{$compartilhaSenha->evento}}</td>
                            <td>{{$compartilhaSenha->matricula}}</td>
                            <td>{{ date( 'd/m/Y' , strtotime($compartilhaSenha->data))}}</td>
                            <td>{{$compartilhaSenha->tipoafastamento}}</td>
                        </tr>

                @endforeach

            </tbody>
        </table>
    </div>
       <div id="historico1"></div>
    @else
        <div id="aprimoramento">
            <span>
                Em análise aos dados do Relatório de Arme/Desarme do Sistema de Alarme, do Controle de Férias CEGEP e do Sistema PGP. Período de {{ date( 'd/m/Y' , strtotime($dtini))}} a {{ date( 'd/m/Y' , strtotime($dtfim))}}, constataram que não havia indícios de prática de compartilhamento de senha de alarme para acesso à unidade.
            </span>
        </div>
        <div id="historico">  </div>
        <div id="historico1"></div>
    @endif
@endif
<input type="hidden"  id="totalfalta" value="{{ isset($total) ?  str_replace(',', '', $total) : '' }}" readonly>
<input type="hidden"  id="totalrisco" value="0.00">
<input type="hidden"  id="totalsobra" value="0.00">



