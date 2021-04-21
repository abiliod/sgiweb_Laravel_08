<?php

namespace App\Jobs;

use App\Models\Correios\ModelsAuxiliares\ControleDeViagem;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class JobControleViagem implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
protected $controle_de_viagens, $dt_job, $dtmenos180dias ;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($controle_de_viagens, $dt_job, $dtmenos180dias )
    {
        $this->controle_de_viagens  = $controle_de_viagens;
        $this->dt_job  = $dt_job;
        $this->dt_job  = $dtmenos180dias;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $controle_de_viagens = $this->controle_de_viagens;
        $dtmenos180dias = $this->dt_job;

        foreach($controle_de_viagens as $dados) {
            foreach($dados as $registro) {

                $inicio_viagem = null;
                if(!empty($registro['inicio_viagem'])) {
                    try {
                        $dateTimeString = $registro['inicio_viagem'] . '00:00:00';
                        $format = 'Y-m-d';
                        $inicio_viagem = Carbon::createFromFormat($format, $dateTimeString, 'America/São Paulo');
                    }
                    catch (\Exception $e) {
                        $inicio_viagem       = null;
                    }
                }
                $data_chegada_prevista     = null;
                if(!empty($registro['data_chegada_prevista'])) {
                    try {
                        $dateTimeString = $registro['data_chegada_prevista'] . '00:00:00';
                        $format = 'Y-m-d';
                        $data_chegada_prevista = Carbon::createFromFormat($format, $dateTimeString, 'America/São Paulo');
                    }
                    catch (\Exception $e) {
                        $data_chegada_prevista     = null;
                    }
                }
                $data_partida_prevista     = null;

                if(!empty($registro['data_partida_prevista'])) {
                    try {
                        $dateTimeString = $registro['data_partida_prevista'] . '00:00:00';
                        $format = 'Y-m-d';
                        $data_partida_prevista = Carbon::createFromFormat($format, $dateTimeString, 'America/São Paulo');
                    }
                    catch (\Exception $e) {
                        $data_partida_prevista     = null;
                    }
                }

                ControleDeViagem::updateOrCreate(
                    [
                        'controle_viagem' =>  $registro['controle_viagem']
                        , 'sequencia_do_cv' =>  $registro['sequencia_do_cv']
                    ]
                    ,[
                    'dr_detentora' => $registro['dr_detentora']
                    , 'unidade_detentora' =>  $registro['unidade_detentora']
                    , 'origem_destino' =>  $registro['origem_destino']
                    , 'tipo_linha' =>  $registro['tipo_linha']
                    , 'numero_da_linha' =>  $registro['numero_da_linha']
                    , 'controle_viagem' =>  $registro['controle_viagem']
                    , 'numero_ficha_tec' =>  $registro['numero_ficha_tec']
                    , 'sentido' =>  $registro['sentido']
                    , 'status' =>  $registro['status']
                    , 'sequencia_do_cv' =>  $registro['sequencia_do_cv']
                    , 'ponto_parada' =>  $registro['ponto_parada']
                    , 'descricao_ponto_parada' =>  $registro['descricao_ponto_parada']
                    , 'drac_ponto_de_parada' =>  $registro['drac_ponto_de_parada']
                    , 'tipo_de_operacao' =>  $registro['tipo_de_operacao']
                    , 'quantidade' =>  $registro['quantidade']
                    , 'peso' =>  $registro['peso']
                    , 'unitizador' =>  $registro['unitizador']
                    , 'tipo_de_servico' =>  $registro['tipo_de_servico']
                    , 'descricao_do_servico' =>  $registro['descricao_do_servico']
                    , 'codigo_de_destino' =>  $registro['codigo_de_destino']
                    , 'local_de_destino' =>  $registro['local_de_destino']
                    , 'inicio_viagem' =>  $inicio_viagem
                    , 'data_chegada_prevista' =>  $data_chegada_prevista
                    , 'data_partida_prevista' =>  $data_partida_prevista
                    , 'horario_chegada_prevista' =>  $registro['horario_chegada_prevista']
                    , 'horario_partida_prevista' =>  $registro['horario_partida_prevista']
                ]);
            }
        }
        DB::table('controle_de_viagens')
            ->where('inicio_viagem', '<',   $dtmenos180dias)
        ->delete();
    }
}
