<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permissao extends Model
{

    protected $table = "permissaos";

    protected $fillable= [
    	'nome',
    	'descricao'
    ];

    public function papeis()
    {
    	return $this->belongsToMany(Papel::class);
    }

    public function create()
    {
        //
    }
    public function where()
    {
        //
    }
    public function count()
    {
        //
    }
    public function first()
    {
        //
    }

}
