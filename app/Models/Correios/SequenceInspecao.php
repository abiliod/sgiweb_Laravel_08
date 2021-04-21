<?php

namespace App\Models\Correios;

use Illuminate\Database\Eloquent\Model;

class SequenceInspecao extends Model
{
    protected $table = "sequence_inspcaos";

    protected $fillable= [
        'se',
        'sequence',
        'ciclo',
    ];

    public static function find($id)
    {
        return SequenceInspecao::class;
    }

}
