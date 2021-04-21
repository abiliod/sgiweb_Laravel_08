<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\PapelPermissao;
use App\Permissao;
use App\Papel;

class PapelPermissaoTableSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $permissaos = Permissao::all();

        $papel = Papel::where('nome','=','admin')->first();
        foreach($permissaos as $permissao){
            PapelPermissao::firstOrCreate(
                ['permissao_id' => $permissao->id],
                ['papel_id' => $papel->id]
             );
        }

    }
}
