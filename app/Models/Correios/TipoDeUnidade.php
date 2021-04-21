<?php

namespace App\Models\Correios;

use Illuminate\Database\Eloquent\Model;

class TipoDeUnidade extends Model
{
    protected $table = "tiposdeunidade";

    protected $fillable = ['codigo' , 'sigla', 'tipodescricao', 'inspecionar', 'tipoInspecao'];

    public function unidade() {
        return $this->hasMany('App\Models\Correios\Unidade',' tipoUnidade_id');
    }

    public function grupoDeVerificacao() {
        return $this->hasMany('App\Models\Correios\GrupoDeVerificacao',' tipoUnidade_id');
    }

    public function itemVerificado() {
        return $this->hasMany('App\Models\Correios\ItemVerificado',' tipoUnidade_id');
    }


}
