<?php
namespace App\Imports;
use App\Models\Correios\ModelsAuxiliares\Proter;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;//linha de cabeççalho
use Maatwebsite\Excel\Excel;

class ImportProter implements
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
        return new Proter([
            'tipo_de_pendencia'      => $row['tipo_de_pendencia'],
            'divergencia_peso'      => $row['divergencia_peso'],
            'divergencia_cep'      => $row['divergencia_cep'],
            'se' => $row['se'],
            'tipo_de_unidade' => $row['tipo_de_unidade'],
            'mcu' => $row['nome_da_unidade'],
            'nome_da_unidade' => $row['data_inicial_e_data_final_para_o_periodo_consultado'],
            'tipo_de_atendimento' => $row['tipo_de_atendimento'],
            'data_da_pendencia' => $row['data_da_pendencia'],
            'status_da_pendencia' => $row['status_da_pendencia'],
            'status_da_unidade' => $row['status_da_unidade'],
            'matricula_atendente' => $row['matricula_atendente'],
            'no_do_objeto' => $row['no_do_objeto'],
            'data_da_postagem' => $row['data_da_postagem'],
            'data_da_entrega' => $row['data_da_entrega'],
            'codigo_do_servico' => $row['codigo_do_servico'],
            'cep_contabilizado_sara' => $row['cep_contabilizado_sara'],
            'cep_entrega_sro' => $row['cep_entrega_sro'],
            'peso_tarifado_financeiro' => $row['peso_tarifado_financeiro'],
            'comprimento_financeiro' => $row['comprimento_financeiro'],
            'largura_financeiro' => $row['largura_financeiro'],
            'altura_financeiro' => $row['altura_financeiro'],
            'peso_cubico_financeiro' => $row['peso_cubico_financeiro'],
            'peso_real_mectri' => $row['peso_real_mectri'],
            'comprimento_mectri' => $row['comprimento_mectri'],
            'largura_mectri' => $row['largura_mectri'],
            'altura_mectri' => $row['altura_mectri'],
            'peso_cubico_mectri' => $row['peso_cubico_mectri'],
            'peso_tarifado_mectri' => $row['peso_tarifado_mectri'],
            'valor_tarifado_financeiro' => $row['valor_tarifado_financeiro'],
            'valor_tarifado_mectri' => $row['valor_tarifado_mectri'],
            'diferenca_a_recolher' => $row['diferenca_a_recolher'],
            'cnpj_do_cliente' => $row['cnpj_do_cliente'],
            'contrato' => $row['contrato'],
            'nome_do_cliente' => $row['nome_do_cliente'],
            'qtd_duplicidades' => $row['qtd_duplicidades'],
            'cartao_postagem' => $row['cartao_postagem'],
            'data_ultima_manifestacao' => $row['data_ultima_manifestacao'],
            'mcu_triagem' => $row['mcu_triagem'],
            'centro' => $row['centro'],
            'peso' => $row['peso'],
            'volume' => $row['volume'],
            'altura' => $row['altura'],
            'largura' => $row['largura'],
            'comprimento' => $row['comprimento'],
            'data_de_leitura' => $row['data_de_leitura'],
            'tipo_do_objeto' => $row['tipo_do_objeto'],
            'cep_destino' => $row['cep_destino'],
            'tipo_de_inducao' => $row['tipo_de_inducao'],
            'numero_da_maquina' => $row['numero_da_maquina'],
            'codigo_da_estacao' => $row['codigo_da_estacao'],
            'origem_pendencia' => $row['origem_pendencia'],
        ]);
    }
}



