<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\Slide;

class SlidesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

        $registros = Slide::where('id', '>=', 1)->get();

        if($registros) {
            DB::table('slides')->delete();
        }

        DB::table('slides')->insert([
            [
                'titulo' => 'TESTE DE INCLUSAO DE SLIDE',
                'descricao'  => 'Qualquer descrição que atinja o objetivo do cliente',
                'imagem'  => 'img/slides//_img_95147.jpeg',
                'link' => 'https://www.google.com/',
                'ordem' => '1',
                'publicado' => 'sim'
            ],
            [
                'titulo' => 'TESTE DE INCLUSAO DE SLIDE',
                'descricao'  => 'Qualquer descrição que atinja o objetivo do cliente',
                'imagem'  => 'img/slides//_img_38031.jpeg',
                'link' => 'https://www.google.com/',
                'ordem' => '2',
                'publicado' => 'sim'
            ],
            [
                'titulo' => 'TESTE DE INCLUSAO DE SLIDE',
                'descricao'  => 'Qualquer descrição que atinja o objetivo do cliente',
                'imagem'  => 'img/slides//_img_96055.jpeg',
                'link' => 'https://www.google.com/',
                'ordem' => '3',
                'publicado' => 'sim'
            ]
        ]);

        $this->command->info('Slides importados com sucesso!');

    }
}




