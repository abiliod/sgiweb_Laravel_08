<?php
namespace App\Imports;
use App\Models\Correios\ModelsAuxiliares\Cftv;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow; //linha de cabeççalho
use Maatwebsite\Excel\Excel;

class ImportCftv implements
      ToModel
    , WithHeadingRow

    {
        public function headingRow(): int
        {
            return 1;
        }

    public function model(array $row){
        return new Cftv([
            'unidade'      => $row['unidade'],
            'mcu'      => $row['mcu'],
            'cameras_fixa_cf'      => $row['cameras_fixa_cf'],
            'cameras_infra_vermelho_cir'      => $row['cameras_infra_vermelho_cir'],
            'dome'      => $row['dome'],
            'modulo_dvr'      => $row['modulo_dvr'],
            'no_break'      => $row['no_break'],
            'hack'      => $row['hack'],
            'pc_auxiliar'      => $row['pc_auxiliar'],
            'portaweb'      => $row['portaweb'],
            'end_ip'      => $row['end_ip'],
            'link'      => $row['link'],
            'user'      => $row['user'],
            'password'      => $row['password'],
            'port'      => $row['port'],
            'marcamodelo'      => $row['marcamodelo'],
            'statusconexao'      => $row['statusconexao'],
            'data_ultima_conexao'      => $row['data_ultima_conexao'],
            'observacao'      => $row['observacao'],
            'data_no_equipamento'      => $row['data_no_equipamento'],
            'hora_no_equipamento'      => $row['hora_no_equipamento'],

        ]);
    }
}



