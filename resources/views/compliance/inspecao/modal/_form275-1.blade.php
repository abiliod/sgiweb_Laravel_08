<div id="aprimoramento">
    <span class="lever" text-align="rigth">

        Em conferência aos dados disponíveis no Portal do SRO, opção Desempenho de Qualidade > Pendência de Baixa Unidades > LDI, de xx/xx/xxxx, verificaram que não havia pendências de baixa.

        OU

        Em conferência aos dados disponíveis no Portal do SRO, opção Desempenho de Qualidade > Pendência de Baixa Unidades > LDI, de xx/xx/xxxx, constataram-se as seguintes irregularidades relacionadas aos procedimentos referente a entrega interna no que diz respeito aos prazos de guarda e à baixa no Sistema SRO

    </span>
</div>
<div id="historico">
    a)	Xx Objetos com prazo de guarda vencido:
    Entrega Normal:

    Objeto	           Data de Entrada	Prazo	   Dias acima do prazo de guarda
    xxxxxxxxxxxxxxxxx	xx/xx/20xx	xxxxxxxxxxxxxxxxx	xxxxxxxxxxxxxxxxx
    xxxxxxxxxxxxxxxx	xx/xx/20xx	xxxxxxxxxxxxxxxxx	xxxxxxxxxxxxxxxxx
    xxxxxxxxxxxxxxxxx	xx/xx/20xx	xxxxxxxxxxxxxxxxx	xxxxxxxxxxxxxxxxx

    b)	Pendências de Baixa:
    Objeto                Data de Entrega
    Xxxxxxxxxxxxx         xxxxxxxxxxxxxxxx
    Xxxxxxxxxxxxx         xxxxxxxxxxxxxxxx

</div>
<div id="historico1"></div>

<div class="row">
<p><span class="lever" text-align="rigth"> Link.</span></p>
<p>

<a href="http://app.correiosnet.int/portalsro/faces/pendenciaBaixa.jsp" target="_blank"> Portal SRO </a>
</p>
</div>
<input type="hidden"  id="totalfalta" value="{{ isset($total) ?  str_replace(',', '', $total) : '' }}" readonly>
<input type="hidden"  id="totalrisco" value="0.00">
<input type="hidden"  id="totalsobra" value="0.00">
