<?php

namespace App\Models\Correios;

use Illuminate\Database\Eloquent\Model;

class GrupoDeVerificacao extends Model
{
    protected $table = 'gruposdeverificacao';

    protected $fillable=
    [
        'ciclo',
        'tipoUnidade_id',
        'tipoVerificacao',
        'numeroGrupoVerificacao',
        'nomegrupo'
    ];

    public function tipoDeUnidade()
    {
        return $this->belongsTo('App\Models\Correios\TipoDeUnidade','tipoUnidade_id');
    }

    public function testeDeVerificacao() {
        return $this->hasMany('App\Models\Correios\TesteDeVerificacao',' grupoVerificacao_id');
    }

    public function itemVerificado() {
        return $this->hasMany('App\Models\Correios\ItemVerificado',' grupoVerificacao_id');
    }

}
