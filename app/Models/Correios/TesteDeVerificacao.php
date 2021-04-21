<?php

namespace App\Models\Correios;

use Illuminate\Database\Eloquent\Model;

class TesteDeVerificacao extends Model
{
    protected $table = "testesdeverificacao";

    protected $fillable=
    [
       'grupoVerificacao_id',
       'numeroDoTeste',
       'ajuda',
       'teste',
       'amostra',
       'sappp',
       'norma',
       'tabela_CFP',
       'impactoFinanceiro',
       'riscoFinanceiro',
       'descumprimentoLeisContratos',
       'descumprimentoNormaInterna',
       'riscoSegurancaIntegridade',
       'riscoImgInstitucional',
       'inspecaoObrigatoria',
       'totalPontos',
       'roteiroConforme',
       'roteiroNaoConforme',
       'roteiroNaoVerificado',
       'itemanosanteriores',
       'orientacao',
       'consequencias',
       'preVerificar',

    ];

    public function grupoDeVerificacao() {
        return $this->belongsTo('App\Models\Correios\GrupoDeVerificacao','grupoVerificacao_id');
    }


    public function itemVerificado() {
        return $this->hasMany('App\Models\Correios\ItemVerificado',' testeVerificacao_id');
    }



}
