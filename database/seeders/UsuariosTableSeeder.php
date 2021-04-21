<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\User;
use App\Papel_user;

class UsuariosTableSeeder extends Seeder{
// php artisan db:seed --class=UsuariosTableSeeder
    /**
    * @return void
    */
    public function run(){

        if(User::where('email','=','admin@sgiweb.com')->count()){
            $usuario = User::where('email','=','admin@sgiweb.com')->first();
            $usuario->id	 = '231';
            $usuario->se	 = '16';
            $usuario->name	 = 'ABILIO DIAS FERREIRA';
            $usuario->document	 = '83288082';
            $usuario->businessUnit	 = '434446';
            $usuario->coordenacao	 = 'BSB';
            $usuario->funcao	 = 'INSPETOR REGIONAL - G4';
            $usuario->localizacao	 = 'GOIÂNIA';
            $usuario->telefone_ect	 = '';
            $usuario->telefone_pessoal	 = '';
            $usuario->email	 = 'admin@sgiweb.com';
            $usuario->password = bcrypt('83288082');
            $usuario->save();
        }else{
            $usuario = new User();
            $usuario->id	 = '231';
            $usuario->se	 = '16';
            $usuario->name	 = 'ABILIO DIAS FERREIRA';
            $usuario->document	 = '83288082';
            $usuario->businessUnit	 = '434448';
            $usuario->coordenacao	 = 'BSB';
            $usuario->funcao	 = 'INSPETOR REGIONAL - G4';
            $usuario->localizacao	 = 'GOIÂNIA';
            $usuario->telefone_ect	 = '';
            $usuario->telefone_pessoal	 = '';
            $usuario->email	 = 'admin@sgiweb.com';
            $usuario->password = bcrypt('83288082');
            $usuario->save();
        }
        Papel_user::UpdateOrCreate([
            'user_id'=>$usuario->id ,
            'papel_id'=>'1'
        ]);

        echo '\n Usuario Master criado com sucesso!'
        ,'\n email = admin@sgiweb.com'
        ,'\n password = 12345678';

        echo '\r\n Demais Usuários criados com sucesso!'
        ,'\n email = >seuemail>@correios.com.br'
        ,'\n password = MATRICULA-> 99999999';

    }
}
