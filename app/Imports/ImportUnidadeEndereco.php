<?php
namespace App\Imports;
use App\Models\Correios\ModelsAuxiliares\UnidadeEndereco;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow; //linha de cabeççalho
use Maatwebsite\Excel\Excel;

class ImportUnidadeEndereco implements
      ToModel
    , WithHeadingRow

    {
        public function headingRow(): int
        {
            return 1;
        }

    public function model(array $row){

        return new UnidadeEndereco([
            'mcu'      => $row['mcu'],
            'codIbge'      => $row['codIbge'],
            'cidade_id' => $row['cidade_id'],
            'complemento' => $row['complemento'],
            'bairro' => $row['bairro'],
            'cidade' => $row['cidade'],
            'uf' => $row['uf'],
            'cep' => $row['cep'],
            'mapa' => $row['mapa'],
            'latitude' => $row['latitude'],
            'longitude' => $row['longitude'],
        ]);
    }
}



