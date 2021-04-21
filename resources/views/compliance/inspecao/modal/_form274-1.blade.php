@if ($count >= 1)
    @if( (isset($plplistapendentes)) && (!empty($plplistapendentes)) )
        <div id="aprimoramento">
            <span>
                Em análise à Relação de Listas Pendentes do sistema SARA, consulta em
                {{ date( 'd/m/Y' , strtotime($dtfim))}}, e aos eventos registrados no sistema SRO,
                constataram-se as inconsistências relacionadas a seguir:
            </span>
        </div>
        <div id="historico">
             <table class="highlight">
                <thead>
                    <tr>
                        <th>Lista</th>
                        <th>PLP</th>
                        <th>Objeto</th>
                        <th>Cliente</th>
                        <th>Data da Postagem</th>
                        <th>Situação</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($plplistapendentes as $plplistapendente)
                    <tr>
                        <td>{{ $plplistapendente->lista }}</td>
                        <td>{{ $plplistapendente->plp }}</td>
                        <td>{{ $plplistapendente->objeto }}</td>
                        <td>{{ $plplistapendente->cliente }}</td>
                        <td>{{(isset($plplistapendente->dh_lista_postagem) && $plplistapendente->dh_lista_postagem == ''  ? '   ----------  ' : \Carbon\Carbon::parse($plplistapendente->dh_lista_postagem)->format('d/m/Y'))}}</td>
                        <th>Falta de Conferencia ou Sem Contabilização</th>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div id="historico1">
            1) Rastrear os objetos a fim de identificar Objetos com registro de POSTAGEM e SEM eventos
            posteriores que indiquem sua entrada na ECT. Se houver registros nestas condições, registrar
            como Falta de Conferência. (Fazer o ajuste na linha que consta os objeto)<br>
            2) Verificar se existem objetos que passaram no Fluxo Postal,
            mas não tem o evento de POSTAGEM. Se houver registros nestas condições,
            registrar que não houve a contabilização. (Fazer o ajuste na linha que consta os objeto)<br>
            3) Objetos com Registros Postado e Encaminhado ou sem eventos, considerar o item como Conforme. Apagar a linha que contém o objeto<br>
            4) O Inspetor deverá definir a situação para cada objeto.<br>
            5) Caso este o objeto esteja conforme apagar a Linha de Registro
            6) Apagar as instruções de 1 a 6. para permanecer apenas as suas observações.
        </div>
    @endif
@else
<div id="aprimoramento">
    <span class="lever">
        Em análise à Relação de Listas Pendentes do sistema SARA, Planilha disponibilizada
        em \sac3063\INSTITUCIONAL\DIOPE\DERAT\PUBLICO\GMAT_pub\LISTA_PENDENTE, planilha acessada em data anterior à {{ date( 'd/m/Y' , strtotime($dtfim))}},
        e aos eventos registrados no sistema SRO, constataram-se que não havia pendência para a unidade inspecionada.
    </span>
</div>
<div id="historico"></div>
<div id="historico1"></div>
@endif
<input type="hidden"  id="totalfalta" value="{{ isset($total) ? $total : '' }}">
<input type="hidden"  id="totalrisco" value="0.00">
<input type="hidden"  id="totalsobra" value="0.00">
