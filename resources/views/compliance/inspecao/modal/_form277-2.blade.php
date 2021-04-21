@if($count== 0)
    @if($registro->tem_distribuicao == 'Não tem distribuição')
        <div id="aprimoramento">
        <span class="lever rigth">
                A unidade não executa essa tarefa.
        </span>
        </div>
        <div id="historico"> </div>
        <div id="historico1"></div>
        @else
        <div id="aprimoramento">
        <span class="lever rigth">
                Não há registro na base de dados para verificação. verifique a existência de SmartPhone na unidade.
        </span>
        </div>
        <div id="historico">  </div>
        <div id="historico1"></div>
    @endif
@endif

@if (($qtd_falhas == 0) && ($registro->tem_distribuicao <> 'Não tem distribuição'))
    <div id="aprimoramento">
        <span class="lever rigth">
                Foram avaliados {{$amostra}} Imagens de Objetos, relativas ao período de {{substr($dtini,8,2)}}/{{substr($dtini,5,2)}}/{{substr($dtini,0,4)}} até {{substr($dtfim,8,2)}}/{{substr($dtfim,5,2)}}/{{substr($dtfim,0,4)}}, que representava 100 por cento, do total da amostra para o período mencionado, sendo que em 100 por cento, não constataram inconsistências.
        </span>
    </div>
    <div id="historico">  </div>
    <div id="historico1"></div>
@elseif( !empty( $res ) )
    <div id="aprimoramento">
        <span class="lever rigth">
            Em análise, por amostragem, às anotações registradas nas Imagens dos comprovantes
             de entrega e anotação disponíveis no sistema SRO,  foram constatadas as seguintes
              inconsistências relacionadas aos procedimentos de entrega:
        </span>
    </div>

    <div id="historico">
        <table class="highlight">
            <thead>
            <tr>
                <th>Objeto</th>
                <th>Data</th>
                <th>Desc. Evento Baixa</th>
                <th>Falha detectada     </th>

            </tr>
            </thead>
            <tbody>
            @foreach($res as $dado)
                <tr>
                    <td><a href="http://wsmobile.correiosnet.int/ComprovanteSRO/entrega?ITEMCODE={{ $dado->objeto }}" target="_blank">{{ $dado->objeto }}</a></td>

                    <td>{{ date( 'd/m/Y' , strtotime($dado->data))}}</td>
                    <td>{{$dado->localBaixa1tentativa}}</td>
                    <td>{{$dado->falhaDetectada}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <span class="lever rigth">
                Foram avaliados {{$amostra}} Imagens de objetos relativas ao período de {{substr($dtini,8,2)}}/{{substr($dtini,5,2)}}/{{substr($dtini,0,4)}} até {{substr($dtfim,8,2)}}/{{substr($dtfim,5,2)}}/{{substr($dtfim,0,4)}}, que representava 100 por cento, do total da amostra para o período mencionado, sendo que em {{$percentagem_falhas}} por cento, constam inconsistências.
        </span>
    </div>
    <div id="historico1"></div>
@endif


<input type="hidden"  id="totalfalta" value="{{ isset($total) ? $total : '' }}">
<input type="hidden"  id="totalrisco" value="0.00">
<input type="hidden"  id="totalsobra" value="0.00">

