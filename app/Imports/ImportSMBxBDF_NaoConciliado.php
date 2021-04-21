<?php
namespace App\Imports;
use App\Models\Correios\ModelsAuxiliares\SMBxBDF_NaoConciliado;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;//linha de cabeççalho
use Maatwebsite\Excel\Excel;




class ImportSMBxBDF_NaoConciliado implements
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
            return new SMBxBDF_NaoConciliado([
                'mcu'      => $row['mcu'],
                'Agencia'      => $row['Agencia'],
                'CNPJ'      => $row['CNPJ'],
                'Data'      => $row['Data'],
                'SMBDinheiro'      => $row['SMBDinheiro'],
                'SMBCheque'      => $row['SMBCheque'],
                'SMBBoleto'      => $row['SMBBoleto'],
                'SMBEstorno'      => $row['SMBEstorno'],
                'BDFDinheiro'      => $row['BDFDinheiro'],
                'BDFCheque'      => $row['BDFCheque'],
                'BDFBoleto'      => $row['BDFBoleto'],
                'Divergencia'      => $row['Divergencia'],
                'Status'      => $row['Status'],

            ]);
        }
}



