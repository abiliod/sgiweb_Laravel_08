<?php
namespace App\Models\Correios\ModelsAuxiliares;

use Illuminate\Database\Eloquent\Model;

class Absenteismo extends Model
{
    protected $table = 'absenteismos';
    protected $fillable=
    [
          'matricula',
          'nome',
          'cargo',
          'lotacao',
          'data_evento',
          'motivo',
          'dias',
    ];

    protected $casts =
    [
        'data_evento' => 'date:Y-m-d',

    ];
}




