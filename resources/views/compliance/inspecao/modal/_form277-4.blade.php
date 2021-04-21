@if($registro->tem_distribuicao == 'Não tem distribuição')
    <div id="aprimoramento">
                <span class="lever rigth">
                        A unidade não executa essa tarefa.
                </span>
    </div>
    <div id="historico"></div>
    <div id="historico1"></div>
@else
    <br>
    <p><span class="lever" text-align="rigth"> Link.</span></p>
    <p>
        <a href="http://app.correiosnet.int/portalsro/faces/pendenciaBaixa.jsp" target="_blank"> Portal SRO </a>
    </p>
    <div id="aprimoramento">
        <span class="lever" text-align="rigth">
            <p>
                Em análise aos dados do sistema PORTAL DO SRO, na data da inspeção constatou-se a inexistência de pendências de Baixa atribuídas à unidade.
            </p>
            <p>
              Em análise aos dados do sistema PORTAL DO SRO, do período de xx/xx a xx/xx/xxxx, constatou-se a existência de xx pendências de Baixa atribuídas à unidade, conforme relação a seguir:
            </p>
        </span>
    </div>
    <div id="historico">
        <p>
            Objeto                         Data (Lançado)
            <br> Xxxxxxxxxxxxxxx                xx/xx/xx
        </p>
        <p>
            Da análise, por amostragem, dos dados de rastreamento dos objetos pendentes, constatou-se a ocorrência de lançamentos invertidos conforme exemplificamos a seguir:
        </p>
        <p>
            Objeto: Xxxxxxxxxxxxxxxxx
            <br> Lançamento em xxxxxxx, às 17h – SAIU PARA ENTREGA
            <br>Lançamento em xxxxxx, às 12h - Entregue
        </p>
    </div>
    <div id="historico1"></div>
@endif
<input type="hidden"  id="totalfalta" value="{{ isset($total) ?  str_replace(',', '', $total) : '' }}" readonly>
<input type="hidden"  id="totalrisco" value="0.00">
<input type="hidden"  id="totalsobra" value="0.00">
