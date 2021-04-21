<?php
namespace App\Imports;
use App\Models\Correios\ModelsAuxiliares\PainelExtravio;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow; //linha de cabeççalho
use Maatwebsite\Excel\Excel;

class ImportPainelExtravio implements
      ToModel
    , WithHeadingRow

    {
        public function headingRow(): int
        {
            return 1;
        }

    public function model(array $row){
        return new ImportPainelExtravio ([
            'objeto'      => $row['objeto'],
            'data_evento'      => $row['data_evento'],
            'trecho'      => $row['trecho'],
            'evento_trecho'      => $row['evento_trecho'],
            'unid_origem'      => $row['unid_origem'],
            'unid_destino'      => $row['unid_destino'],
            'dr_origem'      => $row['dr_origem'],
            'dr_destino'      => $row['dr_destino'],
            'gestao_prealerta'      => $row['gestao_prealerta'],
            'automatico'      => $row['automatico'],
            'manual'      => $row['manual'],
            'total'      => $row['total'],

            'macroprocesso'      => $row['macroprocesso'],
            'postado'      => $row['postado'],
            'ultimo_evento_extraviado'      => $row['ultimo_evento_extraviado'],
            'ultimo_evento_em_transito'      => $row['ultimo_evento_em_transito'],
            'ultimo_evento'      => $row['ultimo_evento'],
            'ultimo_evento_data'      => $row['ultimo_evento_data'],
            'evento_finalizador'      => $row['evento_finalizador'],
            'analise_sro'      => $row['analise_sro'],
            'unid_origem_apelido'      => $row['unid_origem_apelido'],
            'unid_destino_apelido'      => $row['unid_destino_apelido'],

            'trecho_real'      => $row['trecho_real'],
            'se_postagem'      => $row['se_postagem'],
            'unidade_postagem'      => $row['unidade_postagem'],
            'data_postagem'      => $row['data_postagem'],
            'familia'      => $row['familia'],
            'ultimo_evento_sinistro'      => $row['ultimo_evento_sinistro'],
            
        ]);
    }
}



