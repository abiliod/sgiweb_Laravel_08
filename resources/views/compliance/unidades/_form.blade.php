<div class="input-field col s3">
	<input type="text" name="se" class="validate" value="{{ isset($registro->se) ? $registro->se : '' }}">
	<label>Superintendência</label>
</div>
<div class="input-field col s2">
	<input type="text" name="an8" class="validate" value="{{ isset($registro->an8) ? $registro->an8 : '' }}">
	<label>AN8</label>
</div>
<div class="input-field col s3">
    <select name="tipoUnidade_id">
        @foreach($tiposDeUnidade as $tipo)
        <option value="{{ $tipo->id }}" {{(isset($registro->tipoUnidade_id) && $registro->tipo_id == $tipo->id  ? 'selected' : '')}}>{{ $tipo->descricao }}</option>
        @endforeach
    </select>
	<label>Tipo Unidade</label>
</div>
<div class="input-field col s4">
	<input type="text" name="descricao" class="validate" value="{{ isset($registro->descricao) ? $registro->descricao : '' }}">
	<label>Unidade</label>
</div>
<div class="input-field col s2">
	<input type="text" name="mcu" class="validate" value="{{ isset($registro->mcu) ? $registro->mcu : '' }}">
	<label>MCU</label>
</div>
<div class="input-field col s2">
	<input type="text" name="sto" class="validate" value="{{ isset($registro->sto) ? $registro->sto : '' }}">
	<label>STO</label>
</div>
<div class="input-field col s4">
	<input type="text" name="telefone" class="validate" value="{{ isset($registro->telefone) ? $registro->telefone : '' }}">
	<label>TELEFONE</label>
</div>
<div class="input-field col s4">
	<input type="text" name="email" class="validate" value="{{ isset($registro->email) ? $registro->email : '' }}">
	<label>E-MAIL</label>
</div>
<div class="input-field col s2">
	<input type="time"  name="inicio_expediente" class="validate" value="{{ isset($registro->inicio_expediente) ? $registro->inicio_expediente : '' }}">
	<label class="active">INICIO EXPEDIENTE</label>
</div>
<div class="input-field col s2">
    <input type="time"  name="inicio_atendimento" class="validate" value="{{ isset($registro->inicio_atendimento) ? $registro->inicio_atendimento : '' }}">
    <label class="active">INICIO ATENDIMENTO</label>
</div>

<div class="input-field col s2">
    <input type="time"   name="final_atendimento" class="validate" value="{{ isset($registro->final_atendimento) ? $registro->final_atendimento : '' }}">
    <label class="active">FINAL ATENDIMENTO</label>
</div>

<div class="input-field col s2">
    <input type="time"   name="final_expediente" class="validate" value="{{ isset($registro->final_expediente) ? $registro->final_expediente : '' }}">
    <label class="active">FINAL EXPEDIENTE</label>
</div>

<div class="input-field col s2">
	<input type="time"  name="inicio_intervalo_refeicao" class="validate" value="{{ isset($registro->inicio_intervalo_refeicao) ? $registro->inicio_intervalo_refeicao : '' }}">
	<label class="active">INICIO INTERVALO REFEIÇÃO</label>
</div>
<div class="input-field col s2">
	<input type="time"  name="final_intervalo_refeicao" class="validate" value="{{ isset($registro->final_intervalo_refeicao) ? $registro->final_intervalo_refeicao : '' }}">
	<label class="active">FINAL INTERVALO REFEIÇÃO</label>
</div>
<div class="input-field col s4">
  <select name="trabalha_sabado" id="trabalha_sabado" onChange="sabado();">
        <option value="Sim" {{(isset($registro->trabalha_sabado) && $registro->trabalha_sabado == 'Sim'  ? 'selected' : '')}}>Sim</option>
        <option value="Não" {{(isset($registro->trabalha_sabado) && $registro->trabalha_sabado == 'Não'  ? 'selected' : '')}}>Não</option>
   </select>
   <label>TRABALHA SABADO</label>
</div>
<div class="input-field col s4">
	<input    type="time"  id="inicio_expediente_sabado" name="inicio_expediente_sabado" class="validate" value="{{ isset($registro->inicio_expediente_sabado) ? $registro->inicio_expediente_sabado : '' }}">
	<label class="active">INICIO EXP. SÁBADO</label>
</div>
<div class="input-field col s4">
	<input type="time"  id="final_expediente_sabado"  name="final_expediente_sabado" class="validate" value="{{ isset($registro->final_expediente_sabado) ? $registro->final_expediente_sabado : '' }}">
	<label class="active">FINAL EXP. SÁBADO</label>
</div>
<div class="input-field col s4">
    <select name="trabalha_domingo" id="trabalha_domingo" onChange="domingo();">
        <option value="Sim" {{(isset($registro->trabalha_domingo) && $registro->trabalha_domingo == 'Sim'  ? 'selected' : '')}}>Sim</option>
        <option value="Não" {{(isset($registro->trabalha_domingo) && $registro->trabalha_domingo == 'Não'  ? 'selected' : '')}}>Não</option>
   </select>
	<label>TRAB. DOMINGO</label>
</div>
<div class="input-field col s4">
	<input type="time"  id="inicio_expediente_domingo" name="inicio_expediente_domingo" class="validate" value="{{ isset($registro->inicio_expediente_domingo) ? $registro->inicio_expediente_domingo : '' }}">
	<label class="active">INICIO EXP. DOMINGO</label>
</div>
<div class="input-field col s4">
	<input type="time"  id="final_expediente_domingo" name="final_expediente_domingo" class="validate" value="{{ isset($registro->final_expediente_domingo) ? $registro->final_expediente_domingo : '' }}">
	<label class="active">FINAL EXP. DOMINGO</label>
</div>
<div class="input-field col s4">
    <select name="tem_plantao" id="tem_plantao" onChange="plantao();">
        <option value="Sim" {{(isset($registro->tem_plantao) && $registro->tem_plantao == 'Sim'  ? 'selected' : '')}}>Sim</option>
        <option value="Não" {{(isset($registro->tem_plantao) && $registro->tem_plantao == 'Não'  ? 'selected' : '')}}>Não</option>
    </select>
	<label>TEM PLANTÃO</label>
</div>
<div class="input-field col s4">
	<input type="time"  id="inicio_plantao_sabado" name="inicio_plantao_sabado" class="validate" value="{{ isset($registro->inicio_plantao_sabado) ? $registro->inicio_plantao_sabado : '' }}">
	<label class="active">INÍCIO PLANTÃO SABADO</label>
</div>
<div class="input-field col s4">
	<input type="time"  id="final_plantao_sabado" name="final_plantao_sabado" class="validate" value="{{ isset($registro->final_plantao_sabado) ? $registro->final_plantao_sabado : '' }}">
	<label class="active">FINAL PLANTÃO SABADO</label>
</div>
<div class="input-field col s6">
	<input type="time"  id="inicio_plantao_domingo" name="inicio_plantao_domingo" class="validate" value="{{ isset($registro->inicio_plantao_domingo) ? $registro->inicio_plantao_domingo : '' }}">
	<label class="active">INÍCIO PLANTÃO DOMINGO</label>
</div>
<div class="input-field col s6">
	<input type="time"  id="final_plantao_domingo" name="final_plantao_domingo" class="validate" value="{{ isset($registro->final_plantao_domingo) ? $registro->final_plantao_domingo : '' }}">
	<label class="active">FINAL PLANTÃO DOMINGO</label>
</div>
<div class="input-field col s4">
	<select name="tem_distribuicao" id="tem_distribuicao" onChange="distribuicao();">
        <option value="Tem distribuição" {{(isset($registro->tem_distribuicao) && $registro->tem_distribuicao == 'Tem distribuição'  ? 'selected' : '')}}>Tem distribuição</option>
        <option value="Não tem distribuição" {{(isset($registro->tem_distribuicao) && $registro->tem_distribuicao == 'Não tem distribuição'  ? 'selected' : '')}}>Não tem distribuição</option>
    </select>
	<label>TEM DISTRIBUIÇÃO</label>
</div>
<div class="input-field col s4">
	<input type="time"  id="inicio_distribuicao" name="inicio_distribuicao" class="validate" value="{{ isset($registro->inicio_distribuicao) ? $registro->inicio_distribuicao : '' }}">
	<label class="active">INÍCIO DISTRIBUIÇÃO</label>
</div>
<div class="input-field col s4">
	<input type="time"  id="final_distribuicao" name="final_distribuicao" class="validate" value="{{ isset($registro->final_distribuicao) ? $registro->final_distribuicao : '' }}">
	<label class="active">FINAL DISTRIBUIÇÃO</label>
</div>
<div class="input-field col s4">
	<input type="time"  name="horario_lim_post_na_semana" class="validate" value="{{ isset($registro->horario_lim_post_na_semana) ? $registro->horario_lim_post_na_semana : '' }}">
	<label class="active">HORARIO LIM. POSTAGEM SEMANA</label>
</div>
<div class="input-field col s4">
	<input type="time"  name="horario_lim_post_final_semana" class="validate" value="{{ isset($registro->horario_lim_post_final_semana) ? $registro->horario_lim_post_final_semana : '' }}">
	<label class="active">HORARIO LIM. POSTAGEM FIM SEMANA</label>
</div>
<div class="input-field col s4">
    <select name="status_unidadeDesc">
        @foreach($status_unidadeDesc as $status)
        <option value="{{ $status->status_unidadeDesc }}"
        {{(isset($registro->status_unidadeDesc) && $registro->status_unidadeDesc == $status->status_unidadeDesc  ? 'selected' : '')}}>{{ $status->status_unidadeDesc }}</option>
        @endforeach
    </select>
	<label>STATUS</label>
</div>

<script type="text/javascript">
    function sabado()
    {
        var select = document.getElementById('trabalha_sabado');
        var option = select.options[select.selectedIndex];
        if(document.getElementById('trabalha_sabado').value == 'Sim')
        {
            document.getElementById('inicio_expediente_sabado').readonly  = false;
            document.getElementById('final_expediente_sabado').readonly  = false;
        }else{
            document.getElementById('inicio_expediente_sabado').readonly  = true;
            document.getElementById('final_expediente_sabado').readonly  = true;
        }
    }
    sabado();
</script>

<script type="text/javascript">
    function domingo()
    {
        var select = document.getElementById('trabalha_domingo');
        var option = select.options[select.selectedIndex];
        if(document.getElementById('trabalha_domingo').value == 'Sim')
        {
            document.getElementById('inicio_expediente_domingo').readonly  = false;
            document.getElementById('final_expediente_domingo').readonly  = false;
        }else{
            document.getElementById('inicio_expediente_domingo').readonly  = true;
            document.getElementById('final_expediente_domingo').readonly  = true;
        }
    }
    domingo();
</script>

<script type="text/javascript">
    function plantao()
    {
        var select = document.getElementById('tem_plantao');
        var option = select.options[select.selectedIndex];
        if(document.getElementById('tem_plantao').value == 'Sim')
        {
            document.getElementById('inicio_plantao_sabado').readonly  = false;
            document.getElementById('final_plantao_sabado').readonly  = false;
            document.getElementById('inicio_plantao_domingo').readonly  = false;
            document.getElementById('final_plantao_domingo').readonly  = false;
        }else{
            document.getElementById('inicio_plantao_sabado').readonly  = true;
            document.getElementById('final_plantao_sabado').readonly  = true;
            document.getElementById('inicio_plantao_domingo').readonly  = true;
            document.getElementById('final_plantao_domingo').readonly  = true;
        }
    }
    plantao();
</script>

<script type="text/javascript">
    function distribuicao()
    {
        var select = document.getElementById('tem_distribuicao');
        var option = select.options[select.selectedIndex];
        if(document.getElementById('tem_distribuicao').value == 'Sim')
        {
            document.getElementById('inicio_distribuicao').readonly  = false;
            document.getElementById('final_distribuicao').readonly  = false;
        }else{
            document.getElementById('inicio_distribuicao').readonly  = true;
            document.getElementById('final_distribuicao').readonly  = true;
        }
    }
    distribuicao();
</script>

