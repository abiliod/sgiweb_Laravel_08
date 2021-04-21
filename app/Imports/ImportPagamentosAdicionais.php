<?php
namespace App\Imports;
use App\Models\Correios\ModelsAuxiliares\PagamentosAdicionais;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow; //linha de cabeççalho
use Maatwebsite\Excel\Excel;

class ImportPagamentosAdicionais implements
      ToModel
    , WithHeadingRow

    {
        public function headingRow(): int
        {
            return 1;
        }

    public function model(array $row){
        return new ImportPagamentosAdicionais ([

            'se'=> $row['se'],
            'sigla_lotacao'=> $row['sigla_lotacao'],
            'matricula'=> $row['matricula'],
            'nome'=> $row['nome'],
            'cargo'=> $row['cargo'],
            'espec'=> $row['espec'],
            'titular_da_funcao'=> $row['titular_da_funcao'],
            'dif_mer'=> $row['dif_mer'],
            'rubrica'=> $row['rubrica'],
            'qtd'=> $row['qtd'],
            'valor'=> $row['valor'],
            'ref'=> $row['ref'],



        ]);
    }
}



