<?php

namespace App\Jobs;

use App\Models\Correios\ModelsAuxiliares\ApontamentoCV;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class JobApontamentoCV implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $controle_de_viagens, $dt_job;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($controle_de_viagens, $dt_job)
    {
        $this->controle_de_viagens = $controle_de_viagens;
        $this->dt_job = $dt_job;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $controle_de_viagens = $this->controle_de_viagens;
        $dt_job = $this->dt_job;
        foreach ($controle_de_viagens as $dados) {
            foreach ($dados as $row) {
                //trata data   inicio_viagem
                if (!empty($row['inicio_viagem'])) {
                    try {
                        $dt_number = intVal($row['inicio_viagem']);
                        if (is_numeric($dt_number)) {
                            $inicio_viagem = new Carbon('1899-12-30');
                            $inicio_viagem = $inicio_viagem->addDays($dt_number);
                        }
                    } catch (\Exception $e) {
                        $inicio_viagem = null;
                    }
                } else {
                    $inicio_viagem = null;
                }
                //trata data   data_chegada
                if (!empty($row['data_chegada'])) {
                    try {
                        $dt_number = intVal($row['data_chegada']);
                        if (is_numeric($dt_number)) {
                            $data_chegada = new Carbon('1899-12-30');
                            $data_chegada = $data_chegada->addDays($dt_number);
                        }
                    } catch (\Exception $e) {
                        $data_chegada = null;
                    }
                } else {
                    $data_chegada = null;
                }
                //trata data   data_chegada_prevista
                if (!empty($row['data_chegada_prevista'])) {
                    try {
                        $dt_number = intVal($row['data_chegada_prevista']);
                        if (is_numeric($dt_number)) {
                            $data_chegada_prevista = new Carbon('1899-12-30');
                            $data_chegada_prevista = $data_chegada_prevista->addDays($dt_number);
                        }
                    } catch (\Exception $e) {
                        $data_chegada_prevista = null;
                    }
                } else {
                    $data_chegada_prevista = null;
                }
                //trata data   data_partida
                if (!empty($row['data_partida'])) {
                    try {
                        $dt_number = intVal($row['data_partida']);
                        if (is_numeric($dt_number)) {
                            $data_partida = new Carbon('1899-12-30');
                            $data_partida = $data_partida->addDays($dt_number);
                        }
                    } catch (\Exception $e) {
                        $data_partida = null;
                    }
                } else {
                    $data_partida = null;
                }
                //trata data   data_partida_prevista
                if (!empty($row['data_partida_prevista'])) {
                    try {
                        $dt_number = intVal($row['data_partida_prevista']);
                        if (is_numeric($dt_number)) {
                            $data_partida_prevista = new Carbon('1899-12-30');
                            $data_partida_prevista = $data_partida_prevista->addDays($dt_number);
                        }
                    } catch (\Exception $e) {
                        $data_partida_prevista = null;
                    }
                } else {
                    $data_partida_prevista = null;
                }
                //trata data   data_conclusao
                if (!empty($row['data_conclusao'])) {
                    try {
                        $dt_number = intVal($row['data_conclusao']);
                        if (is_numeric($dt_number)) {
                            $data_conclusao = new Carbon('1899-12-30');
                            $data_conclusao = $data_conclusao->addDays($dt_number);
                        }
                    } catch (\Exception $e) {
                        $data_conclusao = null;
                    }
                } else {
                    $data_conclusao = null;
                }
                ApontamentoCV:: firstOrCreate([
                    'controle_viagem' => $row['controle_viagem']
                ], [
                    'dr_detentora' => $row['dr_detentora'],
                    'unidade_detentora' => $row['unidade_detentora'],
                    'origem_destino' => $row['origem_destino'],
                    'tipo_linha' => $row['tipo_linha'],
                    'numero_da_linha' => $row['numero_da_linha'],
                    'controle_viagem' => $row['controle_viagem'],
                    'numero_ficha_tec' => $row['numero_ficha_tec'],
                    'inicio_viagem' => $inicio_viagem,
                    'sentido' => $row['sentido'],
                    'status' => $row['status'],
                    'sequencia_do_cv' => $row['sequencia_do_cv'],
                    'ponto_parada' => $row['ponto_parada'],
                    'data_chegada' => $data_chegada,
                    'descricao_ponto_parada' => $row['descricao_ponto_parada'],
                    'drac_ponto_de_parada' => $row['drac_ponto_de_parada'],
                    'hora_chegada' => $row['hora_chegada'],
                    'data_chegada_prevista' => $data_chegada_prevista,
                    'horario_chegada_previsto' => $row['horario_chegada_previsto'],
                    'hodometro_chegada' => $row['hodometro_chegada'],
                    'hodometro_previsto_do_trecho' => $row['hodometro_previsto_do_trecho'],
                    'hodometro_partida' => $row['hodometro_partida'],
                    'data_partida' => $data_partida,
                    'hora_partida' => $row['hora_partida'],
                    'data_partida_prevista' => $data_partida_prevista,
                    'horario_partida_previsto' => $row['horario_partida_previsto'],
                    'data_conclusao' => $data_conclusao,
                    'codigo_ocorrencia' => $row['codigo_ocorrencia'],
                    'descricao_ocorrencia' => $row['descricao_ocorrencia'],
                    'codigo_transportador' => $row['codigo_transportador'],
                    'descricao_transportador' => $row['descricao_transportador'],
                    'no_seq_contrato' => $row['no_seq_contrato'],
                    'item_do_contrato' => $row['item_do_contrato'],
                    'placa_veiculo' => $row['placa_veiculo'],
                    'placa_carreta' => $row['placa_carreta']
                ]);
                $dr_detentora = $row['dr_detentora'];
                $tipo_linha = $row['tipo_linha'];
            }
        }
        DB::table('apontamento_c_v_s')
            ->where('dr_detentora', '=', $dr_detentora)
            ->where('tipo_linha', '=', $tipo_linha)
            ->where('created_at', '<', $dt_job)
            ->delete();
    }


}
