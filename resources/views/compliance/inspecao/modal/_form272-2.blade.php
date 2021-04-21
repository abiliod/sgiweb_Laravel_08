@if ( !empty($naoMonitorado) )

    <div id="aprimoramento">
        <span>
            Em análise aos dados do Relatório de “Arme e Desarme” do Sistema de Alarme do período de
            {{ date( 'd/m/Y' , strtotime($dtmenos12meses ))}}
            a {{ date( 'd/m/Y' , strtotime($now))}}, constatou-se que:
        </span>
    </div>
    <div id="historico">{{ $naoMonitorado }}</div>
    <div id="historico1"></div>
@else

    @if( ($rowAberturaFinalSemana >= 1) ||  (isset($tempoAbertura)&&(!empty($tempoAbertura))) || (isset($tempoAberturaPosExpediente)&&(!empty($tempoAberturaPosExpediente))) || (isset($acessosEmFeriados)&&(!empty($acessosEmFeriados))) || (isset($tempoAberturaAntecipada)&&(!empty($tempoAberturaAntecipada)))  )
        <div id="aprimoramento">
            <span>
                Em análise aos dados do Relatório de “Arme e Desarme” do Sistema de Alarme em
                {{ date( 'd/m/Y' , strtotime($now ))}}
                referente ao período de
                {{ date( 'd/m/Y' , strtotime($dtmenos12meses))}} a {{ date( 'd/m/Y' , strtotime($now))}},
                constataram-se que o sistema permaneceu desativado e fora de funcionamento nos períodos relacionados a seguir:
            </span>
        </div>
    @else
        <div id="aprimoramento">
            <span>
                Em análise aos dados do Relatório de “Arme e Desarme” do Sistema de Alarme do
                período de  {{ date( 'd/m/Y' , strtotime($now ))}}
                a {{ date( 'd/m/Y' , strtotime($dtmenos12meses))}}, constataram-se que o sistema de alarme
                permaneceu ativado quando dos horários fora de atendimento inclusive finais de semana.
            </span>
        </div>
    @endif


    <div id="historico">
    @if($rowAberturaFinalSemana >= 1)
        <p><span>{{$rowAberturaFinalSemana }} - Ocorrências de desativação do alarme em períodos de finais de semana conforme a seguir:</span> </p>
        <table class="highlight">
            <thead>
                <tr>
                    <th>Evento Abertura</th>
                    <th>Data Abertura</th>
                    <th>Hora Abertura</th>
                    <th>Evento Fechamento</th>
                    <th>Hora Fechamento</th>
                    <th>Dia Semana</th>
                    <th>Tempo Permanência</th>
                </tr>
            </thead>
            <tbody>

            @foreach ($acessos_final_semana as $tabela)

                <tr>
                <td>{{ $tabela->evAbertura }}</td>
                <td>{{ $tabela->evDataAbertura }}</td>
                <td>{{ $tabela->evHoraAbertura }}</td>
                <td>{{ $tabela->evFechamento }}</td>
                <td>{{ $tabela->evHoraFechamento }}</td>
                <td>{{ $tabela->diaSemana }}</td>
                <td>{{ $tabela->tempoPermanencia }}</td>
                </tr>


            @endforeach
            </tbody>
        </table>
    @endif


    @if(isset($tempoAbertura)&&(!empty($tempoAbertura)))
    <p><span> Tempo de abertura em Relação ao Horário de Atendimento conforme a seguir:</span> </p>
        <table class="highlight">
        <thead>
            <tr>
                <th>Data</th>
                <th>Horário Atendimento</th>
                <th>Horário da Abertura</th>
                <th>Tempo Abertura</th>

            </tr>
        </thead>
        <tbody>
        @foreach ($tempoAbertura  as $tempo => $mdaData)
            <tr>
                <td>{{ \Carbon\Carbon::parse($mdaData["dataInicioExpediente"])->format('d/m/Y')}}</td>
                <td>{{$mdaData["InicioExpediente"]}}</td>
                <td>{{$mdaData["HorárioDeAbertura"]}}</td>
                <td>{{$mdaData["DiferencaTempoDeAbertura"]}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @endif

    @if(isset($tempoAberturaAntecipada)&&(!empty($tempoAberturaAntecipada)))
    <p><span>Unidade em Risco. Abertura da Unidade em horário fora do padrão
     em relação ao horário de abertura da unidade conforme a seguir:</span> </p>
        <table class="highlight">
        <thead>
            <tr>
                <th>Data</th>
                <th>Horário de Atendimento</th>
                <th>Hora da Abertura</th>
                <th>Tempo Abertura</th>

            </tr>
        </thead>
        <tbody>
        @foreach ($tempoAberturaAntecipada  as $tempo => $mdaData)
            <tr>
                <td>{{ \Carbon\Carbon::parse($mdaData["dataInicioExpediente"])->format('d/m/Y')}}</td>
                <td>{{$mdaData["InicioExpediente"]}}</td>
                <td>{{$mdaData["HorárioDeAbertura"]}}</td>
                <td>{{$mdaData["DiferencaTempoDeAbertura"]}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @endif

        
    @if(isset($tempoAberturaPosExpediente)&&(!empty($tempoAberturaPosExpediente)))
    <p><span>Unidade em Risco. Abertura da unidade em horário fora do padrão
     em relação ao horário de fechamento da unidade conforme a seguir:</span> </p>
        <table class="highlight">
        <thead>
            <tr>
                <th>Data</th>
                <th>Horário Fechamento</th>
                <th>Horário Abertura</th>
                <th>Tempo Abertura</th>

            </tr>
        </thead>
        <tbody>
        @foreach ($tempoAberturaPosExpediente  as $tempo => $mdaData)
            <tr>
                <td>{{ \Carbon\Carbon::parse($mdaData["dataFinalExpediente"])->format('d/m/Y')}}</td>
                <td>{{$mdaData["FinalExpediente"]}}</td>
                <td>{{$mdaData["HorárioDeAbertura"]}}</td>
                <td>{{$mdaData["DiferencaTempoDeAbertura"]}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @endif

    @if(isset($acessosEmFeriados)&&(!empty($acessosEmFeriados)))
    <p><span>Unidade em Risco. Abertura da unidade em dia de feriado conforme a seguir:</span> </p>
        <table class="highlight">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Hora</th>

                </tr>
            </thead>
            <tbody>
            @foreach ($acessosEmFeriados  as $acessosEmFeriado => $mdaData)
                <tr>
                    <td>{{$mdaData["Acesso"]}}</td>
                    <td>{{$mdaData["hora"]}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif

        @if(isset($aviso)&&(!empty($aviso)))
        <p><span> {{$aviso}}</span> </p>
        @endif
    </div>
    <div id="historico1"></div>
@endif

<input type="hidden"  id="totalfalta" value="{{ isset($total) ?  str_replace(',', '', $total) : '' }}" readonly>
<input type="hidden"  id="totalrisco" value="0.00">
<input type="hidden"  id="totalsobra" value="0.00">
