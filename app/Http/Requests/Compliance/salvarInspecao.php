<?php

namespace App\Http\Requests\Compliance;

use Illuminate\Foundation\Http\FormRequest;

class salvarInspecao extends FormRequest
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
          //  'codigo' => 'required|unique:verificacoes|string|min:10|max:10',
            'unidade_id' => 'required|numeric',
            'ciclo' => 'required|date_format:Y',
            'tipoUnidade_id' => 'required|numeric',
            'tipoVerificacao' => 'required|string|min:5',
            'status' => 'required|string|min:5',
            'inspetorcoordenador' =>  'required',
            'inspetorcolaborador' =>  'required',
            'datainiPreInspeção' => 'required',
        ];
    }

    public function messages()
    {
        return
        [
           // 'codigo.required' => 'O campo :attribute é requerido. Ex: 1600162020',
          //  'codigo.string.min' => 'O campo :attribute deve ter 10 caracteres. Ex: 1600162020',
          //  'codigo.string.max' => 'O campo :attribute deve ter 10 caracteres. Ex: 1600162020',
         //   'codigo.unique' => 'Já existe uma Inspeção com esse Código',
            'unidade_id.required' => 'O campo :attribute é requerido.',
            'ciclo.required' => 'O campo :attribute é requerido.',
            'tipoUnidade_id.required' => 'O campo :attribute é requerido.',
            'tipoVerificacao.required' => 'O campo :attribute é requerido.',
            'status.required' => 'O campo :attribute é requerido.',

            'inspetorcoordenador.required' => 'O campo :attribute é requerido.',
            'inspetorcolaborador.required' => 'O campo :attribute é requerido.',

            'datainiPreInspeção.required' => 'O campo :attribute é requerido.',
            'status.string.min' => 'O campo :attribute deve ter no mínimo 5 caracteres.',
            'tipoVerificacao.string.min' => 'O campo :attribute deve ser um numero inteiro',
            'unidade_id.numeric' => 'O campo :attribute deve ser um numero inteiro',
            'tipoUnidade_id.numeric' => 'O campo :attribute deve ser um numero inteiro',
       ];
    }
}
