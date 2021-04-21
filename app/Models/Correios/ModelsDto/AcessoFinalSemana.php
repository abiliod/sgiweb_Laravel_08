<?php

namespace App\Models\Correios\ModelsDto;

use Illuminate\Database\Eloquent\Model;

class AcessoFinalSemana extends Model
{


    protected $table = "acessos_final_semana";
    
    protected $fillable=
    [
          'mcu',
          'evAbertura',
          'evDataAbertura',
          'evHoraAbertura',
          'evFechamento',
          'evHoraFechamento',
          'diaSemana',
          'tempoPermanencia',
    ];

}
