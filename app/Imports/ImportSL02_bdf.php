<?php
namespace App\Imports;
use App\Models\Correios\ModelsAuxiliares\SL02_bdf;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;//linha de cabeççalho
use Maatwebsite\Excel\Excel;

class ImportSL02_bdf implements
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
            return new SL02_bdf([
                'dr'      => $row['dr'],
                'cod_orgao'      => $row['cod_orgao'],
                'reop'      => $row['reop'],
                'orgao'      => $row['orgao'],
                'dt_movimento'      => $row['dt_movimento'],
                'saldo_atual'      => $row['saldo_atual'],
                'limite'      => $row['limite'],
               
            ]);
        }
}



