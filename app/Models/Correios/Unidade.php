<?php

namespace App\Models\Correios;

use Illuminate\Database\Eloquent\Model;

class Unidade extends Model
{
    protected $table = "unidades";

    protected $fillable=
    [
        'tipoUnidade_id',
        'se',
        'seDescricao',
        'mcu',
        'an8',
        'sto',
        'status_unidadeDesc',
        'status_unidade',
        'descricao',
        'tipoOrgaoCod',
        'tipoOrgaoDesc',
        'cnpj',
        'categoria',
        'mecanizacao',
        'faixaCepIni',
        'faixaCepFim',
        'tem_distribuicao',
        'tipoEstrutura',
        'quantidade_guiches',
        'guiches_ocupados',
        'ddd',
        'telefone',
        'mcu_subordinacaoAdm',
        'desc_subordinacaoAdm',
        'nomeResponsavelUnidade',
        'documentRespUnidade',
        'email',
        'tipo_de_estrutura',
        'subordinacao_tecnica',
        'inicio_expediente',
        'final_expediente',
        'inicio_intervalo_refeicao',
        'final_intervalo_refeicao',
        'trabalha_sabado',
        'inicio_expediente_sabado',
        'final_expediente_sabado',
        'trabalha_domingo',
        'inicio_expediente_domingo',
        'final_expediente_domingo',
        'tem_plantao',
        'inicio_plantao_sabado',
        'final_plantao_sabado',
        'inicio_plantao_domingo',
        'final_plantao_domingo',
        'inicio_distribuicao',
        'final_distribuicao',
        'horario_lim_post_na_semana',
        'horario_lim_post_final_semana',

    ];

    protected $casts =
    [
        //verificar nomes
        'inicio_expediente' => 'time:H:i:s',
        'final_expediente' => 'time:H:i:s',
        'inicio_intervalo_refeicao' => 'time:H:i:s',
        'final_intervalo_refeicao' => 'time:H:i:s',
        'inicio_expediente_sabado' => 'time:H:i:s',
        'final_expediente_sabado' => 'time:H:i:s',
        'inicio_expediente_domingo' => 'time:H:i:s',
        'final_expediente_domingo' => 'time:H:i:s',
        'inicio_plantao_sabaado' => 'time:H:i:s',
        'final_plantao_sabado' => 'time:H:i:s',
        'inicio_plantao_domingo' => 'time:H:i:s',
        'final_plantao_domingo' => 'time:H:i:s',
        'inicio_distribuicao' => 'time:H:i:s',
        'final_distribuicao' => 'time:H:i:s',
        'horario_lim_post_na_semana' => 'time:H:i:s',
        'horario_lim_post_final_semana' => 'time:H:i:s'

    ];

    public function tipoDeUnidade()
    {
        return $this->belongsTo('App\Models\Correios\TipoDeUnidade','tipoUnidade_id');
    }

    public function verificacao()
    {
        return $this->hasMany('App\Models\Correios\Inspecao','id');
    }

    public function clientesMonitoramento() {
      //  return $this->hasMany('App\Models\Correios\ClienteMonitoramento',' mcu_cliente');
    }

    public function itemVerificado() {
        return $this->hasMany('App\Models\Correios\ItemVerificado',' unidade_id');
    }


}
