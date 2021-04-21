<?php
namespace App\Imports;
use App\Models\Correios\ModelsAuxiliares\RespDefinida;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;//linha de cabeççalho
use Maatwebsite\Excel\Excel;

class ImportRespDefinida implements
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
            return new RespDefinida([
                'unidade'      => $row['unidade'],
                'data_pagamento'      => $row['data_pagamento'],
                'objeto'      => $row['objeto'],
                'datapostagem'      => $row['datapostagem'],
                'dt_movimento'      => $row['dt_movimento'],
                'servico_produto'      => $row['servico_produto'],
                'valor_da_indenizacao'      => $row['valor_da_indenizacao'],

                'sto'      => $row['sto'],
                'mcu'      => $row['mcu'],
                'subordinacao'      => $row['subordinacao'],
                'nu_pedidoinformacao'      => $row['nu_pedidoinformacao'],
                'se_pagadora'      => $row['se_pagadora'],
                'data'      => $row['data'],
                'nu_sei'      => $row['nu_sei'],
                'situacao'      => $row['situacao'],
                'empregadoresponsavel'      => $row['empregadoresponsavel'],
                'observacoes'      => $row['observacoes'],
                'conclusao'      => $row['conclusao'],
                'providenciaadotada'      => $row['providenciaadotada'],


            ]);
        }
}



