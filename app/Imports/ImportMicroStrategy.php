<?php
namespace App\Imports;
use App\Models\Correios\ModelsAuxiliares\MicroEstrategy;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow; //linha de cabeççalho
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Excel;

class ImportMicroStrategy implements
      ToModel
    , WithHeadingRow
    , WithBatchInserts

    {

    public function batchSize(): int
    {
        return 100;
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function model(array $row){
        return new MicroStrategy([

            'dr_de_destino'      => $row['dr_de_destino'],
            'nome_da_unidade'      => $row['nome_da_unidade'],
            'codigo_do_objeto'      => $row['codigo_do_objeto'],
            'descricao_do_evento'      => $row['descricao_do_evento'],
            'codigo_do_evento'      => $row['codigo_do_evento'],
            'data_do_evento'      => $row['data_do_evento'],
        ]);
    }
}



