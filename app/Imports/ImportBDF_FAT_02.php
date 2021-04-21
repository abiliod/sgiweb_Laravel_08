<?php
namespace App\Imports;
use App\Models\Correios\ModelsAuxiliares\BDF_FAT_02;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow; //linha de cabeççalho
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Excel;

class ImportBDF_FAT_02 implements
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
        return new BDF_FAT_02([
            'dr'      => $row['dr'],

            'cd_orgao'      => $row['cd_orgao'],
            'orgao'      => $row['orgao'],
            'ag_postagem'      => $row['ag_postagem'],
            'dt_postagem'      => $row['dt_postagem'],
            'etiqueta'      => $row['etiqueta'],
            'servico'      => $row['servico'],
            'vlr_medida'      => $row['vlr_medida'],
            'cd_grupo_pais_destino'      => $row['cd_grupo_pais_destino'],
            'cep_destino'      => $row['cep_destino'],
            'vlr_cobrado_destinatario'      => $row['vlr_cobrado_destinatario'],
            'cd_grupo_pais_destino'      => $row['cd_grupo_pais_destino'],
            'cep_destino'      => $row['cep_destino'],
            'vlr_cobrado_destinatario'      => $row['vlr_cobrado_destinatario'],
            'intdeclarado'      => $row['intdeclarado'],
            'vlr_declarado'      => $row['vlr_declarado'],
            'cod_adm'      => $row['cod_adm'],
            'produto'      => $row['produto'],
            'qtde_prestada'      => $row['qtde_prestada'],
            'vlr_servico'      => $row['vlr_servico'],
            'vlr_desconto'      => $row['vlr_desconto'],
            'acrescimo'      => $row['acrescimo'],
            'vlr_final'      => $row['vlr_final'],
            'cartao'      => $row['cartao'],
            'documento'      => $row['documento'],
            'servico_adicional'      => $row['servico_adicional'],
            'nome_servico'      => $row['nome_servico'],
            'contrato'      => $row['contrato'],
            'atendimento'      => $row['atendimento'],
            'dt_mov'      => $row['dt_mov'],
        ]);
    }
}



