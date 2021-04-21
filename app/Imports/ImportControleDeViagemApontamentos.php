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

class ImportControleDeViagemApontamentos implements
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
              'origem_destino' =>  $row['origem_destino'],
              'tipo_linha' =>  $row['tipo_linha'],
              'numero_da_linha' => $row['numero_da_linha'],
              'controle_viagem' =>  $row['controle_viagem'],
              'numero_ficha_tec' =>  $row['numero_ficha_tec'],
              'inicio_viagem' =>  $row['inicio_viagem'],
              'sentido' =>  $row['sentido'],
              'status' =>  $row['status'],
              'sequencia_do_cv' =>  $row['sequencia_do_cv'],
              'ponto_parada' =>  $row['ponto_parada'],
              'data_chegada' =>  $row['data_chegada'],
              'descricao_ponto_parada' =>  $row['descricao_ponto_parada'],
              'drac_ponto_de_parada' =>  $row['drac_ponto_de_parada'],
              'hora_chegada' =>  $row['hora_chegada'],
              'data_chegada_prevista' =>  $row['data_chegada_prevista'],
              'horario_chegada_previsto' =>  $row['horario_chegada_previsto'],
              'hodometro_chegada' =>  $row['hodometro_chegada'],
              'hodometro_previsto_do_trecho' =>  $row['hodometro_previsto_do_trecho'],
              'hodometro_partida' =>  $row['hodometro_partida'],
              'data_partida' =>  $row['data_partida'],
              'hora_partida' =>  $row['hora_partida'],
              'data_partida_prevista' =>  $row['data_partida_prevista'],
              'horario_partida_previsto' =>  $row['horario_partida_previsto'],
              'data_conclusao' =>  $row['data_conclusao'],
              'codigo_ocorrencia' =>  $row['codigo_ocorrencia'],
              'descricao_ocorrencia' => $row['descricao_ocorrencia'],
              'codigo_transportador' =>  $row['codigo_transportador'],
              'descricao_transportador' =>  $row['descricao_transportador'],
              'no_seq_contrato' =>  $row['no_seq_contrato'],
              'item_do_contrato' =>  $row['item_do_contrato'],
              'placa_veiculo' =>  $row['placa_veiculo'],
              'placa_carreta' =>  $row['placa_carreta'],

        ]);
    }
}



