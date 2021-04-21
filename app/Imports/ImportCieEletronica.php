<?php
namespace App\Imports;
use App\Models\Correios\ModelsAuxiliares\CieEletronica;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow; //linha de cabeççalho
use Maatwebsite\Excel\Excel;

class ImportCieEletronica implements
      ToModel
    , WithHeadingRow

    {
        public function headingRow(): int
        {
            return 1;
        }

    public function model(array $row){
        return new ImportCieEletronica ([

            'numero'=> $row['numero'],
            'emissao'=> $row['emissao'],
            'origem'=> $row['origem'],
            'se_origem'=> $row['se_origem'],
            'destino'=> $row['destino'],
            'se_destino'=> $row['se_destino'],
            'irregularidade'=> $row['irregularidade'],
            'categoria'=> $row['categoria'],
            'numero_objeto'=> $row['numero_objeto'],
            'lida'=> $row['lida'],
            'respondida'=> $row['respondida'],
            'fora_do_prazo'=> $row['fora_do_prazo'],
            'data_de_resposta'=> $row['data_de_resposta'],
            'resposta'=> $row['resposta'],


        ]);
    }
}



