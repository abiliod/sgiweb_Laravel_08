<?php


namespace App\Models\Correios\ModelsAuxiliares;


use Illuminate\Database\Eloquent\Model;

class Snci  extends Model
{
    protected $table = 'snci';

    protected $fillable=
        [
          'sto',
          'modalidade',
          'diretoria',
          'codigo_unidade',
          'descricao_da_unidade',
          'no_inspecao',
          'no_grupo',
          'descricao_do_grupo',
          'no_item',
          'descricao_item',
          'codigo_reate',
          'ano',
          'resposta',
          'comentario',
          'valor',
          'caracteresvlr',
          'falta',
          'sobra',
          'emrisco',
          'dtultatu',
          'nome_do_usuario',
          'recomendacao',
          'hora_pre_inspecao',
          'dt_inic_desloc',
          'hora_desloc',
          'dt_fim_desloc',
          'dt_inic_inspecao',
          'dt_fim_inspecao',
          'hora_inspecao',
          'situacao',
          'dt_encerram',
          'coordenador',
          'responsavel',
          'motivo',
          'status',
          'sigla_do_status',
          'descricao_do_status',
          'dt_posicao',
          'data_previsao_solucao',
          'area',
          'nome_da_area',
          'parecer',
          'valor_recuperado',
          'processo',
          'tipo_processo',
          'sei',
          'ncisei',
          'reinc_relat',
          'reinc_grupo',
          'reinc_item',
        ];

    protected $casts =
        [
            'dtultatu','date:Y-m-d',
            'dt_inic_desloc','date:Y-m-d',
            'dt_fim_desloc','date:Y-m-d',
            'dt_inic_inspecao','date:Y-m-d',
            'dt_fim_inspecao','date:Y-m-d',
            'dt_encerram','date:Y-m-d',
            'dt_posicao', 'date:Y-m-d',
            'data_previsao_solucao','date:Y-m-d',

      ];

//    public static function updateOrCreate(array $array, array $array1)
//    {
//
//    }
}
