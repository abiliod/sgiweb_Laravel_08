<div class="row">
    <div class="row col s2">
        <p>
            <input onChange="mudarApontamento('avaliacao');" class="with-gap" name="avaliacao" type="radio" id="avaliacao1" value="Conforme"{{(isset($registro->avaliacao) && $registro->avaliacao == 'Conforme'  ? 'checked' : '')}}/>
            <label for="avaliacao1">Conforme</label>
        </p>
        <p>
            <input onChange="mudarApontamento('avaliacao');" class="with-gap" name="avaliacao" type="radio" id="avaliacao2" value="Não Conforme"{{(isset($registro->avaliacao) && $registro->avaliacao == 'Não Conforme'  ? 'checked' : '')}}/>
            <label for="avaliacao2">Não Conforme</label>
        </p>
        <p>
            <input onChange="mudarApontamento('avaliacao');" class="with-gap" name="avaliacao" type="radio" id="avaliacao3" value="Não Executa Tarefa"{{(isset($registro->avaliacao) && $registro->avaliacao == 'Não Executa Tarefa'  ? 'checked' : '')}}/>
            <label for="avaliacao3">Não Executa Tarefa</label>
        </p>
        <p>
            <input onChange="mudarApontamento('avaliacao');" class="with-gap" name="avaliacao" type="radio" id="avaliacao4" value="Não Verificado"{{(isset($registro->avaliacao) && $registro->avaliacao == 'Não Verificado'  ? 'checked' : '')}}/>
            <label for="avaliacao4">Não Verificado</label>
        </p>
    </div>

    <div class="row">
        <p>
            <input id ="exibe" type="checkbox" class="filled-in col s1" onclick="Mudarestado('ajuda')"/>
		    <label for="exibe">Exibir Ajuda do Item?</label>
	    </p>
	    <div class="input-field col s9"  id ="ajuda" style="display:none;">
			<i class="material-icons prefix">mode_edit</i>
			<textarea  id="ajuda" name="ajuda" class="materialize-textarea">
			{{ isset($registro->ajuda) ? $registro->ajuda : '' }}
			</textarea>
			<label for="ajuda">Ajuda do Item</label>
	    </div>
    </div>

    <div class="input-field col s12" id="teste">
        <i class="material-icons prefix">mode_edit</i>
        <textarea  id="teste" name="teste" class="materialize-textarea">
			{{ isset($registro->teste) ? $registro->teste : '' }}
		</textarea>
        <label for="teste">Teste de Inspeção: </label>
    </div>

    <div class="input-field col s12">
            <i class="material-icons prefix ">mode_edit</i>
        <textarea id="oportunidadeAprimoramento"
            name="oportunidadeAprimoramento" class="materialize-textarea">
            {{ isset($registro->oportunidadeAprimoramento) ? $registro->oportunidadeAprimoramento : '' }}
        </textarea>
        <label for="oportunidadeAprimoramento">Oportunidade de Aprimomamento</label>

        <textarea id="roteiroConforme"  style="display:none;">
                {{ isset($registro->roteiroConforme) ? $registro->roteiroConforme : '' }}
        </textarea>

        <textarea id="roteiroNaoConforme"  style="display:none;">
            {{ isset($registro->roteiroNaoConforme) ? $registro->roteiroNaoConforme : '' }}
        </textarea>

        <textarea id="roteiroNaoVerificado"  style="display:none;">
            {{ isset($registro->roteiroNaoVerificado) ? $registro->roteiroNaoVerificado : '' }}
        </textarea>
    </div>


    <div class="input-field col s12" id="evidencias"
        {{($registro->avaliacao == 'Não Conforme'  ? 'style=display:block;' : 'style=display:none;')}}>
        <i class="material-icons prefix">mode_edit</i>
		<textarea  id="evidencia" name="evidencia" class="materialize-textarea">
			{{ isset($registro->evidencia) ? $registro->evidencia : '' }}
		</textarea>
        <label for="evidencia">Evidencias: </label>
    </div>


    <div class="row" id="imagens"  {{($registro->avaliacao == 'Não Conforme'  ? 'style=display:block;' : 'style=display:none;')}}">
        <div class="file-field input-field col  s12 m4">
            <div class="btn">
                <span>Imagem</span>
                <input type="file" id="imagem" name="imagem[]" multiple/>
            </div>
            <div class="file-path-wrapper">
                    <input type="text" class="file-path validade">
            </div>
        </div>
        <span class="text-danger">{{ $errors->first('imagem') }}</span>
        <div id="thumb-output"></div>
       <br>
        <div class="row col s12">
            @if(!empty($registro->diretorio))
                @foreach(File::glob(($registro->diretorio.'*.*')) as $imagem)

                    <a href="javascript: if(confirm('Deletar essa Imagem?')){ window.location.href = '{{ route('compliance.inspecao.destroyfiles',str_replace('/', '-', $imagem).'&'.$id) }}' }">  <img display:inline; width="120"  src="{{asset( $imagem )}}"></a>
                 @endforeach
            @endif
        </div>
    </div>

    <div class="input-field col s12" id="norma">
        <input type="text"  name="norma" class="validate"
        value="{{ isset($registro->norma) ? $registro->norma : '' }}">
        <label for="norma">Ref. Normativa: </label>
    </div>

    <div class="input-field col s12" id="consequencias" {{($registro->avaliacao == 'Não Conforme'  ? 'style=display:block;' : 'style=display:none;')}}>
        <input type="text"  name="consequencias" class="validate"
        value="{{ isset($registro->consequencias) ? $registro->consequencias : '' }}" placerolder= "Possíveis Consequências da Situação Encontrada: Prejuízo Financeiro à Empresa" >
        <label for="consequencias">Possíveis Consequências da Situação Encontrada:</label>
    </div>

<div class="row col s12" id="itemsQuantificados"
    {{($registro->avaliacao == 'Não Conforme'  ? 'style=display:block;' : 'style=display:none;')}}>
    <div class="input-field  col s4">
        <select onChange="ItemQuantificado('itemQuantificado')"   id="itemQuantificado" name="itemQuantificado">
            <option value="Sim" {{(isset($registro->itemQuantificado) && $registro->itemQuantificado == 'Sim'  ? 'selected' : '')}}>Sim</option>
            <option value="Não" {{(isset($registro->itemQuantificado) && $registro->itemQuantificado == 'Não'  ? 'selected' : '')}}>Não</option>
        </select>
        <label for="itemQuantificado">É Item Quantificado?</label>
    </div>
</div>

<div  class="row col s12" class="row" name="quantificacao" id="quantificacao"
    {{($registro->itemQuantificado == 'Não'  ? 'style=display:none;' : 'style=display:block;')}}
>
            <div class="input-field col s4">
                <input type="text" id="valorFalta" name="valorFalta" class="validate"
                value="{{ isset($registro->valorFalta) ? $registro->valorFalta : '0,00'}}">
                <label for="valorFalta">Valor em Falta</label>

            </div>
           <div class="input-field col s4">
                <input type="text" id="valorSobra" name="valorSobra" class="validate"
                value="{{ isset($registro->valorSobra) ? $registro->valorSobra : '0.00'}}">
                <label for="valorSobra">Valor da Sobra</label>
            </div>
            <div class="input-field col s4">
                <input type="text" id="valorRisco" name="valorRisco" class="validate"
                value="{{ isset($registro->valorRisco) ? $registro->valorRisco : '0.00'}}">
                <label for="valorRisco">Valor em Risco</label>
            </div>
        </div>




        <div class="row col s12" id="ereincidencia"
        {{($registro->avaliacao == 'Não Conforme'  ? 'style=display:block;' : 'style=display:none;')}}>
            <div class="input-field  col s4">
                <select onChange="Reincidencia('reincidencia')"  id="reincidencia" name="reincidencia">
                    <option value="Sim" {{(isset($registro->reincidencia) && $registro->reincidencia == 'Sim'  ? 'selected' : '')}}>Sim</option>
                    <option value="Não" {{(isset($registro->reincidencia) && $registro->reincidencia == 'Não'  ? 'selected' : '')}}>Não</option>
                </select>
                <label for="reincidencia">É Reincidência?</label>
            </div>
        </div>

        <div  class="row col s12" class="row" name="reincidencias" id="reincidencias"
         {{($registro->reincidencia == 'Não'  ? 'style=display:none;' : 'style=display:block;')}}
          >
            <div class="input-field col s4">
                <input type="text" id="codVerificacaoAnterior" name="codVerificacaoAnterior" class="validate"
                value="{{ isset($registro->codVerificacaoAnterior) ? $registro->codVerificacaoAnterior : '' }}"
                onkeypress="return onlynumber();">
                <label for="codVerificacaoAnterior">Número da Inspeção Anterior</label>
            </div>
            <div class="input-field col s4">
                <input type="text" id="numeroGrupoReincidente" name="numeroGrupoReincidente" class="validate"
                value="{{ isset($registro->numeroGrupoReincidente) ? $registro->numeroGrupoReincidente : '' }}"
                onkeypress="return onlynumber();">
                <label for="numeroGrupoReincidente">Número do Grupo</label>
            </div>
            <div class="input-field col s4">
                <input type="text" id="numeroItemReincidente" name="numeroItemReincidente" class="validate"
                value="{{ isset($registro->numeroItemReincidente) ? $registro->numeroItemReincidente : '' }}"
                onkeypress="return onlynumber();">
                <label for="numeroItemReincidente">Item Reincidente</label>
            </div>
        </div>


    <div class="input-field col s12" id="orientacao"  {{($registro->avaliacao == 'Não Conforme'  ? 'style=display:block;' : 'style=display:none;')}}">
        <i class="material-icons prefix">mode_edit</i>
                <textarea  id="orientacao" name="orientacao" class="materialize-textarea">
                {{ isset($registro->orientacao) ? $registro->orientacao : '' }}
                </textarea>
            <label for="orientacao">Orientações: </label>
    </div>

</div>
