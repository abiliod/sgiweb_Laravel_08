<?php

namespace App\Http\Requests\Compliance;
use Illuminate\Foundation\Http\FormRequest;

class SalvarGruposDeVerificacao extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return
        [
            'ciclo' => 'required|date_format:Y',
            'tipoVerificacao' => 'required',
            'tipoUnidade_id' => 'required|numeric',
            'numeroGrupoVerificacao' => 'required|integer',
            'nomegrupo' => 'required|string|min:5|max:190',
        ];
    }
    public function messages()
    {
        return
        [
            'ciclo.required' => 'O campo :attribute é requerido.',
            'tipoVerificacao.required' => 'O campo :attribute é requerido.',
            'tipoUnidade_id.required' => 'O campo :attribute é requerido.',
            'numeroGrupoVerificacao.required' => 'O campo :attribute é requerido.',
            'nomegrupo.required' => 'O campo :attribute é requerido.',
            'nomegrupo.string.min' => 'O campo :attribute deve ter no mínimo 5 caracteres.',
            'nomegrupo.string.max' => 'O campo :attribute deve ter no máximo 190 caracteres.',
            'numeroGrupoVerificacao.integer' => 'O campo :attribute deve ser um numero inteiro',
            'tipoUnidade_id.exists' => 'O campo :attribute Não se refere a um Tipo de Unidade'
       ];
    }
}


