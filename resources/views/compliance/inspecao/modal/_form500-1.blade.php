<div class="row">
    <span class="lever" text-align="rigth">Caso esse item não seja avaliado. Marque no Formulario a Opção NÃO VERIFICADO.</span>
    <div id="aprimoramento">
        <span class="lever" text-align="rigth"> Não existem neste momento, situações que ensejam aprimoramento. Além das que constam na presente Inspeção. </span>
    </div>
    <div id="historico">  </div>
    <div id="historico1"></div>
</div>
<input type="hidden"  id="totalfalta" value="{{ isset($total) ?  str_replace(',', '', $total) : '' }}" readonly>
<input type="hidden"  id="totalrisco" value="0.00">
<input type="hidden"  id="totalsobra" value="0.00">
