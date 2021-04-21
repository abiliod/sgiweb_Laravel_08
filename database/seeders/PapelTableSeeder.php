<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Papel;

class PapelTableSeeder extends Seeder{
    /**
    * Run the database seeds.
    *
    * @return void
    */
    public function run(){
        DB::table('papels')->truncate(); //excluir e zerar a tabela
        //        1- admin
        if(!Papel::where('nome','=','admin')->count()){
            Papel::create([
                'nome'=>'admin',
                'descricao'=>'Administrador do Sistema'
            ]);
        }
        //        2- gestor_ac
        if(!Papel::where('nome','=','gestor_ac')->count()){
            Papel::create([
                'nome'=>'gestor_ac',
                'descricao'=>'Gestor AC'
            ]);
        }
        //3- gestor_cvco
        if(!Papel::where('nome','=','gestor_cvco')->count()){
            $admin = Papel::create([
                'nome'=>'gestor_cvco',
                'descricao'=>'Gestor CVCO'
            ]);
        }
//4- gestor-scoi
        if(!Papel::where('nome','=','gestor-scoi')->count()){
            $admin = Papel::create([
                'nome'=>'gestor-scoi',
                'descricao'=>'Gestor SCOI'
            ]);
        }
//5- gestor_unidade_operacional
        if(!Papel::where('nome','=','Cliente/Fornecedor')->count()){
            $admin = Papel::create([
                'nome'=>'Cliente/Fornecedor',
                'descricao'=>'Resp. Unidades Inspecionadas'
            ]);
        }
//6- Inspetor
        if(!Papel::where('nome','=','Inspetor')->count()){
            $admin = Papel::create([
                'nome'=>'Inspetor',
                'descricao'=>'Inspetor'
            ]);
        }
//7- gestor reate/geope e areas pontuadas
        if(!Papel::where('nome','=','Gestor_Subordinação_Adm')->count()){
            $admin = Papel::create([
                'nome'=>'Gestor_Subordinação_Adm',
                'descricao'=>'Gestor_Subordinação_Administrativa e Técnica'
            ]);
        }
        echo "Papeis gerados com sucesso!\n";
    }
}

