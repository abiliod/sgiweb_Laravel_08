<?php

namespace App\Http\Requests\Compliance;

use Illuminate\Foundation\Http\FormRequest;



class SalvarTesteDeVerificacao extends FormRequest
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
            'grupoVerificacao_id' => 'required|integer',
            'numeroDoTeste' => 'required|integer',
            'inspecaoObrigatoria' => 'required|integer',
            'teste' => 'required|string|min:20',
            'ajuda'=> 'nullable|string|min:30',
            'amostra'=> 'nullable|string|min:30',
            'norma' => 'required|string|min:8',
            'sappp'=> 'nullable|string|min:20',
            'tabela_CFP'=> 'nullable|string|min:20',
            'impactoFinanceiro' => 'required|integer',
            'riscoFinanceiro' => 'required|integer',
            'descumprimentoLeisContratos' => 'required|integer',
            'descumprimentoNormaInterna' => 'required|integer',
            'riscoSegurancaIntegridade' => 'required|integer',
            'riscoImgInstitucional' => 'required|integer',
            'totalPontos' => 'required|integer',
            'consequencias'=> 'nullable|string|min:20',
            'roteiroConforme'=> 'nullable|string|min:20',
            'roteiroNaoConforme'=> 'nullable|string|min:20',
            'roteiroNaoVerificado'=> 'nullable|string|min:20',
            'itemanosanteriores'=> 'nullable|string|min:5',
            'orientacao'=> 'nullable|string|min:20',

        ];
    }

    public function messages()
    {
        return
        [
            'grupoVerificacao_id.required' => 'O campo :attribute é requerido.',
            'numeroDoTeste.required' => 'O campo :attribute é requerido.',
            'teste.required' => 'O campo :attribute é requerido.',
            'norma.required' => 'O campo :attribute é requerido.',
            'inspecaoObrigatoria.required' => 'O campo :attribute é requerido.',
            'impactoFinanceiro.required' => 'O campo :attribute é requerido.',
            'riscoFinanceiro.required' => 'O campo :attribute é requerido.',
            'descumprimentoLeisContratos.required' => 'O campo :attribute é requerido.',
            'descumprimentoNormaInterna.required' => 'O campo :attribute é requerido.',
            'riscoSegurancaIntegridade.required' => 'O campo :attribute é requerido.',
            'riscoImgInstitucional.required' => 'O campo :attribute é requerido.',
            'totalPontos.required' => 'O campo :attribute é requerido.',
            'teste.string.min' => 'O campo :attribute deve ter no mínimo 5 caracteres.',
            'norma.string.min' => 'O campo :attribute deve ter no mínimo 5 caracteres.',
            'ajuda.string.min' => 'O campo :attribute deve ter no mínimo 5 caracteres.',
            'amostra.string.min' => 'O campo :attribute deve ter no mínimo 5 caracteres.',
            'sappp.string.min' => 'O campo :attribute deve ter no mínimo 5 caracteres.',
            'tabela_CFP.string.min' => 'O campo :attribute deve ter no mínimo 5 caracteres.',
            'consequencias.string.min' => 'O campo :attribute deve ter no mínimo 20 caracteres.',
            'roteiroConforme.string.min' => 'O campo :attribute deve ter no mínimo 20 caracteres.',
            'roteiroNaoConforme.string.min' => 'O campo :attribute deve ter no mínimo 20 caracteres.',
            'roteiroNaoVerificado.string.min' => 'O campo :attribute deve ter no mínimo 20 caracteres.',
            'itemanosanteriores.string.min' => 'O campo :attribute deve ter no mínimo 5 caracteres.',
            'orientacao.string.min' => 'O campo :attribute deve ter no mínimo 20 caracteres.',
       ];
    }
}

