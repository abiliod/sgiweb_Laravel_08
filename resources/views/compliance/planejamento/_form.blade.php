<div class="input-field col s9">
    <select class="slMultiple" multiple="multiple" name="codigo[]" id="codigo" size="50">
        <option value="" disabled selected>Você pode selecionar várias opções:</option>
        @foreach($registros as $registro)
            <option value="{{ $registro->codigo}}">{{ $registro->descricao }}</option>
        @endforeach
    </select>
    <label for="codigo">Agendamento Para:</label>
</div>


{{--<div class="input-field col s6">--}}
{{--    <select name="tipodeunidade" id="tipodeunidade">--}}
{{--        <option value="" selected>Tipo de Unidade</option>--}}
{{--        @foreach($tiposDeUnidade as $tipoDeUnidade)--}}
{{--            <option value="{{$tipoDeUnidade->id}}">{{ $tipoDeUnidade->tipodescricao }}</option>--}}
{{--        @endforeach--}}
{{--    </select>--}}
{{--    <label for="tipodeunidade">Selecione um tipo de Unidade:</label>--}}
{{--</div>--}}


{{--            @can('inspeçãomonitorada_adicionar')      @endcan--}}

<div class="input-field col s6">
    <button class="btn blue">Selecionar</button>
</div>

