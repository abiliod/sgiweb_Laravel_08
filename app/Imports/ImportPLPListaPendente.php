<?php
namespace App\Imports;
use App\Models\Correios\ModelsAuxiliares\PLPListaPendente;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;//linha de cabeççalho
use Maatwebsite\Excel\Excel;

class ImportPLPListaPendente implements
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
        return new PLPListaPendente
        ([
            'dr'      => $row['dr'],
            'stomcu'      => $row['stomcu'],
            'nome_agencia'      => $row['nome_agencia'],
            'lista'      => $row['lista'],
            'plp'      => $row['plp'],
            'objeto'      => $row['objeto'],
            'cliente'      => $row['cliente'],
            'dh_lista_postagem'      => $row['dh_lista_postagem'],


        ]);
    }
}



