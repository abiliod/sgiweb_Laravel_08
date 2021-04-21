<?php
namespace App\Imports;

use App\Models\Correios\ModelsAuxiliares\Cadastral;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;//linha de cabeççalho
use Maatwebsite\Excel\Excel;

class ImportCadastral implements
      ToModel
    , WithHeadingRow
    ,WithBatchInserts

    {


        public function batchSize(): int
        {
            return 1000;
        }

        public function headingRow(): int
        {
            return 1;
        }

    public function model(array $row){
        return new Cadastral
        ([
            'matricula'      => $row['matricula'],
            'nome_do_empregado'      => $row['nome_do_empregado'],
            'lotacao'      => $row['lotacao'],
            'mcu'      => $row['mcu'],
            'cargo'      => $row['cargo'],
            'especializ'      => $row['especializ'],
            'funcao'      => $row['funcao'],
            'sexo'      => $row['sexo'],
            'situacao'      => $row['situacao'],
//            'data_nascto'      => $row['data_nascto'],
//            'data_admissao'      => $row['data_admissao'],
            'se'      => $row['se'],
        ]);
    }
}



