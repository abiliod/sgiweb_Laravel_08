<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
/**
   * Abilio 20-02-2020 Inclusão de Funcionalidade
   * App\Papel_user;
   * Utilizar a classes para atribuir papel ao usuário
   **/
class Papel_user extends Model {
    protected $table = "papel_user";

    protected $fillable = ['user_id','papel_id'];
    public $timestamps = false;
}
