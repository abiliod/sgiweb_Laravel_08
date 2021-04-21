<?php

namespace App\Models\Correios\ModelsAuxiliares;

use Illuminate\Database\Eloquent\Model;

class Relevancia extends Model
{
    protected $fillable= [
        'valor_inicio',
        'valor_final',
        'fator_multiplicador',
    ];
}
