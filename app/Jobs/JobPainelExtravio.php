<?php

namespace App\Jobs;

use App\Models\Correios\ModelsAuxiliares\PainelExtravio;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class JobPainelExtravio implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected  $painel_extravios, $dt_job, $dtmenos360dias;

    public function __construct($painel_extravios, $dt_job, $dtmenos360dias)
    {
        $this->painel_extravios =  $painel_extravios;
        $this->dt_job = $dt_job;
        $this->dtmenos360dias = $dtmenos360dias;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $painel_extravios = $this->painel_extravios;
        $dtmenos360dias = $this->dtmenos360dias;

        foreach($painel_extravios as $dados) {
            foreach($dados as $registro) {

                //trata data   $data_evento
                if(! Empty($registro['data_evento'])) {
                    try {
                        $dt_number = intVal($registro['data_evento']);
                        if (is_numeric($dt_number)) {
                            $data_evento = new Carbon('1899-12-30');
                            $data_evento = $data_evento->addDays($dt_number);
                        }
                    }
                    catch (\Exception $e) {
                        $data_evento       =  null;
                    }
                }
                else {
                    $data_evento    = null;
                }

                //trata data   $ultimo_evento_data
                if(! Empty($registro['ultimo_evento_data'])) {
                    try {
                        $dt_number = intVal($registro['ultimo_evento_data']);
                        if (is_numeric($dt_number)) {
                            $ultimo_evento_data = new Carbon('1899-12-30');
                            $ultimo_evento_data = $ultimo_evento_data->addDays($dt_number);
                        }
                    }
                    catch (\Exception $e) {
                        $ultimo_evento_data       =  null;
                    }
                }
                else {
                    $ultimo_evento_data    = null;
                }

                //trata data   $data_postagem
                if(! Empty($registro['data_postagem'])) {
                    try {
                        $dt_number = intVal($registro['data_postagem']);
                        if (is_numeric($dt_number)) {
                            $data_postagem = new Carbon('1899-12-30');
                            $data_postagem = $data_postagem->addDays($dt_number);
                        }
                    }
                    catch (\Exception $e) {
                        $data_postagem       =  null;
                    }
                }
                else {
                    $data_postagem    = null;
                }

                if(! Empty($registro['objeto'])) {

                    PainelExtravio::updateOrCreate([
                        'objeto' => $registro['objeto']
                    ],[
                            'objeto' => $registro['objeto']
                            , 'data_evento' => $data_evento
                            , 'evento' => $registro['evento']
                            , 'cliente' => $registro['cliente']
                            , 'trecho' => $registro['trecho']
                            , 'evento_trecho' => $registro['evento_trecho']
                            , 'unid_origem' => $registro['unid_origem']
                            , 'unid_destino' => $registro['unid_destino']
                            , 'dr_origem' => $registro['dr_origem']
                            , 'dr_destino' => $registro['dr_destino']
                            , 'gestao_prealerta' => $registro['gestao_prealerta']
                            , 'automatico' => $registro['automatico']
                            , 'manual' => $registro['manual']
                            , 'total' => $registro['total']
                            , 'macroprocesso' => $registro['macroprocesso']
                            , 'ultimo_evento_extraviado' => $registro['ultimo_evento_extraviado']
                            , 'ultimo_evento_em_transito' => $registro['ultimo_evento_em_transito']
                            , 'ultimo_evento' => $registro['ultimo_evento']
                            , 'ultimo_evento_data' => $ultimo_evento_data
                            , 'evento_finalizador' => $registro['evento_finalizador']
                            , 'analise_sro' => $registro['analise_sro']
                            , 'unid_origem_apelido' => $registro['unid_origem_apelido']
                            , 'unid_destino_apelido' => $registro['unid_destino_apelido']
                            , 'trecho_real' => $registro['trecho_real']
                            , 'se_postagem' => $registro['se_postagem']
                            , 'unidade_postagem' => $registro['unidade_postagem']
                            , 'data_postagem' => $data_postagem
                            , 'familia' => $registro['familia']
                            , 'ultimo_evento_sinistro' => $registro['ultimo_evento_sinistro']
                        ]
                    );

                }
            }
        }
        DB::table('painel_extravios')
            ->where('data_evento', '<=', $dtmenos360dias)
        ->delete();
    }
}
