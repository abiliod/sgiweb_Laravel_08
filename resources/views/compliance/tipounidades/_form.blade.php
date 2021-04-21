<div class="input-field col s2">
    <input type="text" id="codigo" name="codigo" class="validate upper"  value="{{ isset($registro->codigo) ? $registro->codigo : '' }}" readonly>
    <label for="codigo">codigo</label>
</div>
<div class="input-field col s4">
    <input type="text" id="sigla" name="sigla" class="validate upper"  value="{{ isset($registro->sigla) ? $registro->sigla : '' }}" readonly>
    <label for="sigla">Sigla</label>
</div>
<div class="input-field col s6">
    <input type="text" id="descricao" name="descricao" class="validate upper"  value="{{ isset($registro->tipodescricao) ? $registro->tipodescricao : '' }}" readonly>
    <label for="descricao">Descrição</label>
</div>

<div class="input-field col s6">
    <select name="tipoInspecao" id="tipoInspecao" class="validate">
        <option value="Ambos" {{(isset($registro->tipoInspecao) && $registro->tipoInspecao == 'Ambos'  ? 'selected' : '')}}>Ambos</option>
        <option value="Monitorada" {{(isset($registro->Monitorada) && $registro->Monitorada == 'Monitorada'  ? 'selected' : '')}}>Monitorada</option>
        <option value="Presencial" {{(isset($registro->tipoInspecao) && $registro->tipoInspecao == 'Presencial'  ? 'selected' : '')}}>Presencial</option>
        <option value="Remoto" {{(isset($registro->tipoInspecao) && $registro->tipoInspecao == 'Remoto'  ? 'selected' : '')}}>Remoto</option>
        <option value="Suspenso" {{(isset($registro->tipoInspecao) && $registro->tipoInspecao == 'Suspenso'  ? 'selected' : '')}}>Suspenso</option>

    </select>
    <label for="tipoInspecao" >Tipo de Inspeção</label>
</div>


<div class="input-field col s6">
    <select name="inspecionar" id="inspecionar" class="validate">

        <option value="Sim" {{(isset($registro->inspecionar) && $registro->inspecionar == 'Sim'  ? 'selected' : '')}}>Sim</option>
        <option value="Não" {{(isset($registro->inspecionar) && $registro->inspecionar == 'Não'  ? 'selected' : '')}}>Não</option>

    </select>
    <label for="inspecionar" >Liberado Para Inspecionar</label>
</div>








