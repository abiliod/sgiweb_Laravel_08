<?php

namespace App\Models\Correios\ModelsAuxiliares;

use Illuminate\Database\Eloquent\Model;

class Proter extends Model
{
    protected $table = 'proters';

    protected $fillable=
    [

          'tipo_de_pendencia',
          'divergencia_peso',
          'divergencia_cep',
          'origem_pendencia',
          'se',
          'tipo_de_unidade',
          'mcu',
          'nome_da_unidade',
          'tipo_de_atendimento',
          'matricula_atendente',
          'data_da_pendencia',
          'status_da_pendencia',
          'status_da_unidade',
          'no_do_objeto',
          'data_da_postagem',
          'data_da_entrega',
          'codigo_do_servico',
          'cep_contabilizado_sara',
          'cep_entrega_sro',
          'peso_tarifado_financeiro',
          'comprimento_financeiro',
          'largura_financeiro',
          'altura_financeiro',
          'peso_cubico_financeiro',
          'peso_real_mectri',
          'comprimento_mectri',
          'largura_mectri',
          'altura_mectri',
          'peso_cubico_mectri',
          'peso_tarifado_mectri',
          'valor_tarifado_financeiro',
          'peso_cubico_financeiro',
          'diferenca_a_recolher',
          'cnpj_do_cliente',
          'contrato',
          'cartao_postagem',
          'nome_do_cliente',
          'qtd_duplicidades',
          'ultima_manifestacao',

    ];

    protected $casts =
    [
        'data_da_postagem' => 'date:Y-m-d',
        'data_da_entrega' => 'date:Y-m-d',
        'data_da_pendencia' => 'date:Y-m-d',
    ];
}
