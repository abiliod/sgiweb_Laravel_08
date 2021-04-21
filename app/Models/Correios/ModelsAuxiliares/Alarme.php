<?php

namespace App\Models\Correios\ModelsAuxiliares;

use Illuminate\Database\Eloquent\Model;
/**
 * modelo de dados para importação do relatório de Arme e Desarme
 */
class Alarme extends Model {

      protected $fillable= [
         'Cliente',
         'Armedesarme',
         'data',
         'hora',
         'diaSemana',
         'usuario',
         'mcu',
         'matricula',

      ];

      public $timestamps = false;

      protected $casts = [
              'data' => 'date:Y-m-d',
              'hora' => 'time:H:i:s',
        ];

}
