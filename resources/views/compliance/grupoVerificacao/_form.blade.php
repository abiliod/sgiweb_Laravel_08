<div class="input-field col s6">
    <select name="ciclo" id="ciclo" class="validate">
        <option value="" {{(!empty($registro->ciclo) ? 'selected' : '')}}>Selecione um Ciclo</option>
        <option value="2019" {{(isset($registro->ciclo) && $registro->ciclo == '2019'  ? 'selected' : '')}}>2019</option>
        <option value="2020" {{(isset($registro->ciclo) && $registro->ciclo == '2020'  ? 'selected' : '')}}>2020</option>
        <option value="2021" {{(isset($registro->ciclo) && $registro->ciclo == '2021'  ? 'selected' : '')}}>2021</option>
        <option value="2022" {{(isset($registro->ciclo) && $registro->ciclo == '2022'  ? 'selected' : '')}}>2022</option>
    </select>
    <label for="ciclo" >Ciclo de Verificação</label>
</div>


<div class="input-field col s6">
    <select name="tipoVerificacao" id="tipoVerificacao" class="validate">
        <option value="" {{(!empty($registro->tipoVerificacao) ? 'selected' : '')}}>Selecione um Tipo de Verificação</option>
        <option value="Presencial" {{(isset($registro->tipoVerificacao) && $registro->tipoVerificacao == 'Presencial'  ? 'selected' : '')}}>Presencial</option>
        <option value="Remoto" {{(isset($registro->tipoVerificacao) && $registro->tipoVerificacao == 'Remoto'  ? 'selected' : '')}}>Remoto</option>
    </select>
    <label for="tipoVerificacao" >Tipo de Verificação</label>
</div>

<div class="input-field col s12">
    <select name="tipoUnidade_id">
        @foreach($tiposDeUnidade as $tipo)
            @isset($registro)
                <option value="{{ $tipo->id }}" {{(isset($registro->tipoUnidade_id)&& $registro->tipoUnidade_id == $tipo->id  ? 'selected' : '')}}>{{ $tipo->tipodescricao }}</option>
            @else
                <option value="0" >Selecione Um Tipo de Unidade</option>
                <option value="{{ $tipo->id }}">{{ $tipo->tipodescricao }}</option>
            @endisset
        @endforeach
    </select>
    <label for="tipoUnidade_id">Tipo Unidade</label>
</div>

<div class="input-field col s12">
    <input type="text" name="numeroGrupoVerificacao" class="validate" value="{{ isset($registro->numeroGrupoVerificacao) ? $registro->numeroGrupoVerificacao : '' }}">
    <label for="numeroGrupoVerificacao">Número do Grupo Verificacao</label>
</div>

<div class="input-field col s12">
    <input type="text" id="nomegrupo" name="nomegrupo" class="validate upper"  value="{{ isset($registro->nomegrupo) ? $registro->nomegrupo : '' }}" placerolder="nome do grupo">
    <label for="nomegrupo">Descrição</label>
</div>
