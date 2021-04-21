<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PapelPermissao extends Model {

    protected $table = "papel_permissao";

    protected $fillable= [
    	'permissao_id',
    	'papel_id'
    ];

    public $timestamps = false;

    public function papeis() {
    	return $this->belongsToMany(Papel::class);
    }
    public function papel_permissaos() {
    	return $this->belongsToMany(PapelPermissao::class);
    }

}
