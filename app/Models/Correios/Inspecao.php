<?php

namespace App\Models\Correios;

use Illuminate\Database\Eloquent\Model;

class Inspecao extends Model
{
    protected $table = 'inspecoes';

    protected $fillable=
    [
        'codigo',
        'unidade_id',
        'ciclo',
        'tipoUnidade_id',
        'tipoVerificacao',
        'status',
        'inspetorcoordenador',
        'inspetorcolaborador',
        'datainiPreInspeção',
        'numHrsPreInsp',
        'numHrsDesloc',
        'numHrsInsp',
        'eventoInspecao',
        'totalPontos',
        'valor_ref_itens_inspecionados',
        'totalpontosnaoconforme',
        'tnc',
        'classificacao',
        'totalitensavaliados',
        'totalitensnaoconforme',
        'data_programacao',
        'job_programado',
        'xml'
    ];

    public function unidade()
    {
        return $this->belongsTo('App\Models\Correios\Unidade','unidade_id');
    }

    public function itemVerificado()
    {
        return $this->hasMany('App\Models\Correios\itensDeInspecao','verificacao_id');
    }

}
