<div class="row"> </div>

<div class="row">
    <div class="input-field col s6">
        <select name="activeUser" id="activeUser" {{($usuario->papel_id <= '4'  ? '' : 'disabled')}}>
            <option value="1" {{(isset($usuario->activeUser) && $usuario->activeUser == '1'  ? 'selected' : '')}}>Usuário Ativo</option>
            <option value="0" {{(isset($usuario->activeUser) && $usuario->activeUser == '0'  ? 'selected' : '')}}>Usuário Inativo</option>
        </select>
        <label for="activeUser" >Situação do Usuário</label>
    </div>
    <div class="input-field  col s6">
        <input type="text"  id="name" name="name" class="validade" value="{{ isset($usuario->name) ? $usuario->name : '' }}">
        <label for="name">Nome</label>
    </div>
</div>

<div class="row col s12">
    <div class="input-field col s6">
        <input type="text" name="document" class="validade" value="{{ isset($usuario->document) ? $usuario->document : '' }}">
        <label>Matrícula</label>
    </div>
    <div class="input-field  col s6">
        <input type="text"  id="email"  name="email" class="validade" value="{{ isset($usuario->email) ? $usuario->email : '' }}">
        <label for="email">E-mail</label>
    </div>
</div>

<div class="row col s12">
    <div class="input-field col s6">
        <input type="text" name="businessUnit" class="validade" value="{{ isset($usuario->businessUnit) ? $usuario->businessUnit : '' }}">

        <label for="businessUnit">Lotação</label>
    </div>
    <div class="input-field col s6">
        <label>{{ isset($usuario->businessUnit) ? $usuario->descricao : 'Unidade não localizada' }}</label>
    </div>
</div>

<div class="row col s12">
    <div class="input-field col s6">
        <select name="coordenacao" id="coordenacao"  {{($usuario->papel_id <= '4'  ? '' : 'disabled')}} >
            <option value="BSB" {{(isset($registro->coordenacao) && $registro->coordenacao == 'BSB'  ? 'selected' : '')}}>BSB</option>
            <option value="CS/DIGOV/DCINT/GCOP" {{(isset($registro->coordenacao) && $registro->coordenacao == 'CS/DIGOV/DCINT/GCOP'  ? 'selected' : '')}}>CS/DIGOV/DCINT/GCOP</option>
            <option value="MG" {{(isset($registro->coordenacao) && $registro->coordenacao == 'MG'  ? 'selected' : '')}}>MG</option>
            <option value="PE" {{(isset($registro->coordenacao) && $registro->coordenacao == 'PE'  ? 'selected' : '')}}>PE</option>
            <option value="PR" {{(isset($registro->coordenacao) && $registro->coordenacao == 'PR'  ? 'selected' : '')}}>PR</option>
            <option value="SPI" {{(isset($registro->coordenacao) && $registro->coordenacao == 'SPI'  ? 'selected' : '')}}>SPI</option>
            <option value="SPM" {{(isset($registro->coordenacao) && $registro->coordenacao == 'SPM'  ? 'selected' : '')}}>SPM</option>
        </select>
        <label for="coordenacao">Coordenação</label>
    </div>
    <div class="input-field col s6">
        <input type="text" name="localizacao" class="validade" value="{{ isset($usuario->localizacao) ? $usuario->localizacao : '' }}">
        <label for="localizacao">Localização</label>
    </div>
</div>

<div class="row col s12">
    <div class="input-field col s4">
        <input type="text" name="funcao" class="validade" value="{{ isset($usuario->funcao) ? $usuario->funcao : '' }}">
        <label for="funcao">Função</label>
    </div>
    <div class="input-field col s4">
        <input type="text" name="telefone_ect" class="validade" value="{{ isset($usuario->telefone_ect) ? $usuario->telefone_ect : '' }}">
        <label for="telefone_ect">Telefone ECT</label>
    </div>
    <div class="input-field col s4">
        <input type="text" name="telefone_pessoal" class="validade" value="{{ isset($usuario->telefone_pessoal) ? $usuario->telefone_pessoal : '' }}">
        <label for="telefone_pessoal">Telefone Pessoal</label>
    </div>

</div>

