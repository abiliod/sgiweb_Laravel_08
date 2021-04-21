<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TiposDeUnidadesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('tiposDeUnidade')->insert([


            ['codigo'=>'09','sigla'=>'AC','descricao'=>'AGENCIA CORREIO'],
            ['codigo' => '25','sigla' => 'ACCI','descricao' => 'AG CORREIOS COMER PROPRIA'],
            ['codigo' => '27','sigla' => 'ACCI','descricao' => 'AG CORREIOS COMER TERCEIRIZ'],
            ['codigo' => '26','sigla' => 'ACCII','descricao' => 'AG CORREIOS COMER PROPRIA'],
            ['codigo' => '28','sigla' => 'ACCII','descricao' => 'AG CORREIOS COMER TERCEIRIZ'],
            ['codigo' => '42','sigla' => 'ACD','descricao' => 'AG CORREIOS DIGITAL'],
            ['codigo' => '16','sigla' => 'ACF','descricao' => 'AG CORREIOS FRANQUEADA (ACF)'],
            ['codigo' => '41','sigla' => 'ACM','descricao' => 'AG CORREIOS MODULAR'],
            ['codigo' => '15','sigla' => 'ACS','descricao' => 'AGENCIA DE CORREIO SATELITE'],
            ['codigo' => '20','sigla' => 'AF','descricao' => 'AGENCIA FILATELICA'],
            ['codigo' => '21','sigla' => 'AGC','descricao' => 'AG CORREIOS COMUNITARIA'],
            ['codigo' => '12','sigla' => 'AGF','descricao' => 'AG CORREIOS FRANQUEADA (AGF)'],
            ['codigo' => '17','sigla' => 'CAT','descricao' => 'CENTRO ATEND TELEMARKETING'],
            ['codigo' => '04','sigla' => 'CDD','descricao' => 'CENTRO DE DISTRIB DOMICILIARIA'],
            ['codigo' => '32','sigla' => 'CDIP','descricao' => 'CENTRO DE DIGITALIZACAO'],
            ['codigo' => '29','sigla' => 'CEE','descricao' => 'CENTRO ENTREGA ENCOMENDAS'],
            ['codigo' => '31','sigla' => 'CGLI','descricao' => 'CENTRO GESTAO LOGIST INTEG'],
            ['codigo' => '19','sigla' => 'CLI','descricao' => 'CENTRO LOGISTICA INTEGRADA'],
            ['codigo' => '07','sigla' => 'CST','descricao' => 'CENTRO DE SERV TELEMATICOS'],
            ['codigo' => '08','sigla' => 'CTC','descricao' => 'CENTRO TRATAMENTO CARTAS'],
            ['codigo' => '06','sigla' => 'CTCE','descricao' => 'CENTRO TRAT CARTAS E ENCOMEN'],
            ['codigo' => '30','sigla' => 'CTCI','descricao' => 'CENTRO TRATAM CORR INTERNACI'],
            ['codigo' => '05','sigla' => 'CTE','descricao' => 'CENTRO TRATAMENTO ENCOMENDAS'],
            ['codigo' => '22','sigla' => 'CTO','descricao' => 'CENTRO TRANSPORTE OPERACIONAL'],
            ['codigo' => '02','sigla' => 'DIR','descricao' => 'CORREIOS SEDE - DIRECAO'],
            ['codigo' => '03','sigla' => 'SE','descricao' => 'SUPERINTEND ESTADUAL -SETORIAL'],
            ['codigo' => '18','sigla' => 'PC','descricao' => 'POSTO DE CORREIO'],
            ['codigo' => '24','sigla' => 'PVP','descricao' => 'POSTO DE VENDA DE PRODUTOS'],
            ['codigo' => '11','sigla' => 'REVEN/REATE','descricao' => 'REVEN/REATE'],
            ['codigo' => '13','sigla' => 'TECA','descricao' => 'TERMINAL DE CARGA'],
            ['codigo' => '100','sigla' => 'GCCAP','descricao' => 'GCCAP']

        ]);
        $this->command->info('Tipos De Unidades importados com sucesso!');
    }
}
