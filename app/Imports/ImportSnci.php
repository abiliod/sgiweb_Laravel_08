<?php


namespace App\Imports;

use App\Models\Correios\ModelsAuxiliares\Snci;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportSnci implements
    ToModel
    , WithHeadingRow
{
    public function headingRow(): int
    {
        return 1;
    }

    public function model(array $row){
        return new Snci([
            'sto'  => $row['sto'],
            'modalidade'  => $row['modalidade'],
            'diretoria'  => $row['diretoria'],
         //   'codigo_unidade'  => $row['codigo_unidade'],
            'descricao_da_unidade'  => $row['descricao_da_unidade'],
            'no_inspecao'  => $row['no_inspecao'],
            'no_grupo'  => $row['no_grupo'],
            'descricao_do_grupo'  => $row['descricao_do_grupo'],
            'no_item'  => $row['no_item'],
            'descricao_item'  => $row['descricao_item'],
            'codigo_reate'  => $row['codigo_reate'],
            'ano'  => $row['ano'],
            'resposta'  => $row['resposta'],
            'comentario'  => $row['comentario'],
            'valor'  => $row['valor'],
            'caracteresvlr'  => $row['caracteresvlr'],
            'falta'  => $row['falta'],
            'sobra'  => $row['sobra'],
            'emrisco'  => $row['emrisco'],
            'dtultatu'  => $row['dtultatu'],
            'nome_do_usuario'  => $row['nome_do_usuario'],
            'recomendacao'  => $row['recomendacao'],
            'hora_pre_inspecao'  => $row['hora_pre_inspecao'],
            'dt_inic_desloc'  => $row['dt_inic_desloc'],
            'hora_desloc'  => $row['hora_desloc'],
            'dt_fim_desloc'  => $row['dt_fim_desloc'],
            'dt_inic_inspecao'  => $row['dt_inic_inspecao'],
            'dt_fim_inspecao'  => $row['dt_fim_inspecao'],
            'hora_inspecao'  => $row['hora_inspecao'],
            'situacao'  => $row['situacao'],
            'dt_encerram'  => $row['dt_encerram'],
            'coordenador'  => $row['coordenador'],
            'responsavel'  => $row['responsavel'],
            'motivo'  => $row['motivo'],
            'status'  => $row['status'],
            'sigla_do_status'  => $row['sigla_do_status'],
            'descricao_do_status'  => $row['descricao_do_status'],
            'dt_posicao'  => $row['dt_posicao'],
            'data_previsao_solucao'  => $row['data_previsao_solucao'],
            'area'  => $row['area'],
            'nome_da_area'  => $row['nome_da_area'],
            'parecer'  => $row['parecer'],
            'valor_recuperado'  => $row['valor_recuperado'],
            'processo'  => $row['processo'],
            'tipo_processo'  => $row['tipo_processo'],
            'sei'  => $row['sei'],
            'ncisei'  => $row['ncisei'],
            'reinc_relat'  => $row['reinc_relat'],
            'reinc_grupo'  => $row['reinc_grupo'],
            'reinc_item'  => $row['reinc_item'],
        ]);
    }
}
