<?php

namespace App\Models\Correios\ModelsAuxiliares;

use Illuminate\Database\Eloquent\Model;

class BDF_FAT_02 extends Model
{

    protected $table = 'bdf_fat_02';

    protected $fillable=
    [
          'dr',
          'cd_orgao',
          'orgao',
          'ag_postagem',
          'dt_postagem',
          'etiqueta',
          'servico',
          'vlr_medida',
          'cd_grupo_pais_destino',
          'cep_destino',
          'vlr_cobrado_destinatario',
          'cd_grupo_pais_destino',
          'cod_adm',
          'produto',
          'qtde_prestada',
          'vlr_declarado',
          'vlr_servico',
          'vlr_desconto',
          'acrescimo',
          'vlr_final',
          'cartao',
          'documento',
          'servico_adicional',
          'nome_servico',
          'contrato',
          'atendimento',
          'dt_mov',

    ];

    protected $casts =
    [
        'dt_postagem' => 'date:Y-m-d',
        'dt_mov' => 'date:Y-m-d',




    ];

}
