<?php

namespace App\Models\Correios\ModelsAuxiliares;

use Illuminate\Database\Eloquent\Model;

class PLPListaPendente extends Model
{

    protected $table = 'plpListaPendentes';

    protected $fillable=
    [
          'dr',
          'stomcu',
          'nome_agencia',
          'lista',
          'plp',
          'objeto',
          'cliente',
          'dh_lista_postagem',
    ];

    protected $casts =
    [
        'dh_lista_postagem' => 'date:Y-m-d',
    ];
}
