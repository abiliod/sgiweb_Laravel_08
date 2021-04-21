<?php
namespace App\Imports;
use App\Models\Correios\ModelsAuxiliares\ControleDeViagem;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;//linha de cabeççalho
use Maatwebsite\Excel\Excel;

class ImportControleDeViagem implements
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
        return new ControleDeViagem
        ([
            'dr_detentora' => $row['dr_detentora'],
            'unidade_detentora' => $row['unidade_detentora'],
            'origem_destino' => $row['origem_destino'],
            'tipo_linha' => $row['tipo_linha'],
            'dr_detentora' => $row['dr_detentora'],
            'numero_da_linha' => $row['numero_da_linha'],
            'controle_viagem' => $row['controle_viagem'],
            'numero_ficha_tec' => $row['numero_ficha_tec'],
            'sentido' => $row['sentido'],
            'status' => $row['status'],
            'sequencia_do_cv' => $row['sequencia_do_cv'],
            'ponto_parada' => $row['ponto_parada'],
            'descricao_ponto_parada' => $row['descricao_ponto_parada'],
            'drac_ponto_de_parada' => $row['drac_ponto_de_parada'],
            'tipo_de_operacao' => $row['tipo_de_operacao'],
            'quantidade' => $row['quantidade'],
            'peso' => $row['peso'],
            'unitizador' => $row['unitizador'],
            'tipo_de_servico' => $row['tipo_de_servico'],
            'descricao_do_servico' => $row['descricao_do_servico'],
            'codigo_de_destino' => $row['codigo_de_destino'],
            'local_de_destino' => $row['local_de_destino'],
            'inicio_viagem' => $row['inicio_viagem'],
            'data_chegada_prevista' => $row['data_chegada_prevista'],
            'data_partida_prevista' => $row['data_partida_prevista'],
            'horario_chegada_prevista' => $row['horario_chegada_prevista'],
            'horario_partida_prevista' => $row['horario_partida_prevista'],


        ]);
    }
}



