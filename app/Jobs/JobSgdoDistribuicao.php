<?php

namespace App\Jobs;

use App\Models\Correios\ModelsAuxiliares\SgdoDistribuicao;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class JobSgdoDistribuicao implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var
     */
    protected $sgdoDistribuicao, $dt_job, $dtmenos180dias;

    /**
     * JobSgdoDistribuicao constructor.
     * @param $sgdoDistribuicao
     * @param $dt_job
     * @param $dtmenos180dias
     */
    public function __construct($sgdoDistribuicao, $dt_job, $dtmenos180dias)
    {
        $this->sgdoDistribuicao  = $sgdoDistribuicao;
        $this->dt_job  = $dt_job;
        $this->dtmenos180dias  = $dtmenos180dias;
    }

    public function handle()
    {
        $sgdoDistribuicao =  $this->sgdoDistribuicao;
        $dtmenos180dias = $this->dtmenos180dias;

        foreach($sgdoDistribuicao as $dados) {
            foreach($dados as $registro) {
//trata data inicio atividade
                if(! Empty($registro['data_inio_atividade'])) {
                    try {
                        $dt_number = intVal($registro['data_inio_atividade']);
                        if (is_numeric($dt_number)) {
                            $data_incio_atividade = new Carbon('1899-12-30');
                            $data_incio_atividade = $data_incio_atividade->addDays($dt_number);
                        }
                    }
                    catch (\Exception $e) {
                        $data_incio_atividade       =  null;
                    }
                }
                else {
                    $data_incio_atividade    = null;
                }

//trata hora inicio atividade

                if(! Empty($registro['hora_inio_atividade'])) {
                    try {
                        $dmt_number = floatVal($registro['hora_inio_atividade']);
                        $hora_inio_atividade = 1440 * $dmt_number / 60 ;  //15.33;
                        $hora_inio_atividade = sprintf('%02d:%02d', (int) $hora_inio_atividade, fmod($hora_inio_atividade, 1) * 60).':00'; // 15:19
                    }
                    catch (\Exception $e) {
                        $hora_inio_atividade    = null;
                    }
                }
                else {
                    $hora_inio_atividade    = null;
                }

//trata   inicio data saida
                if(! Empty($registro['data_saa'])) {
                    try {
                        $dt_number = intVal($registro['data_saa']);
                        if (is_numeric($dt_number)) {
                            $data_saida = new Carbon('1899-12-30');
                            $data_saida = $data_saida->addDays($dt_number);
                        }
                    }
                    catch (\Exception $e) {
                        $data_saida       = null;
                    }
                }
                else {
                    $data_saida    = null;
                }

//trata   inicio hora saida
                if(! Empty($registro['hora_saa'])) {
                    try {
                        $dmt_number = floatVal($registro['hora_saa']);
                        $hora_saa = 1440 * $dmt_number / 60 ;  //15.33;
                        $hora_saa = sprintf('%02d:%02d', (int) $hora_saa, fmod($hora_saa, 1) * 60).':00'; // 15:19
                    }
                    catch (\Exception $e) {
                        $hora_saa    = null;
                    }
                }
                else {
                    $hora_saa    = null;
                }

//trata   inicio data retorno
                if(! Empty($registro['data_retorno'])) {
                    try {
                        $dt_number = intVal($registro['data_retorno']);
                        if (is_numeric($dt_number)) {
                            $data_retorno = new Carbon('1899-12-30');
                            $data_retorno = $data_retorno->addDays($dt_number);
                        }
                    }
                    catch (\Exception $e) {
                        $data_retorno       = null;
                    }
                }
                else {
                    $data_retorno    = null;
                }

 //trata   hora_retorno
                if(! Empty($registro['hora_retorno'])) {
                    try {
                        $dmt_number = floatVal($registro['hora_retorno']);
                        $hora_retorno = 1440 * $dmt_number / 60 ;  //15.33;
                        $hora_retorno = sprintf('%02d:%02d', (int) $hora_retorno, fmod($hora_retorno, 1) * 60).':00'; // 15:19
                    }
                    catch (\Exception $e) {
                        $hora_retorno    = null;
                    }
                }
                else {
                    $hora_retorno    = null;
                }

 //trata   data_tpc
                if(! Empty($registro['data_tpc'])) {
                    try {
                        $dt_number = intVal($registro['data_tpc']);
                        if (is_numeric($dt_number)) {
                            $data_tpc = new Carbon('1899-12-30');
                            $data_tpc = $data_tpc->addDays($dt_number);
                        }
                    }
                    catch (\Exception $e) {
                        $data_tpc       = null;
                    }
                }
                else {
                    $data_tpc    = null;
                }

//trata   hora_do_tpc
                if(! Empty($registro['hora_do_tpc'])) {
                    try {
                            $dmt_number = floatVal($registro['hora_do_tpc']);
                            $hora_do_tpc = 1440 * $dmt_number / 60 ;  //15.33;
                            $hora_do_tpc = sprintf('%02d:%02d', (int) $hora_do_tpc, fmod($hora_do_tpc, 1) * 60).':00'; // 15:19
                    }
                    catch (\Exception $e) {
                        $hora_do_tpc    = null;
                    }
                }
                else {
                    $hora_do_tpc    = null;
                }

//trata   data_tmino_atividade
                if(! Empty($registro['data_tmino_atividade'])) {
                    try {
                        $dt_number = intVal($registro['data_tmino_atividade']);
                        if (is_numeric($dt_number)) {
                            $data_termino_atividade = new Carbon('1899-12-30');
                            $data_termino_atividade = $data_termino_atividade->addDays($dt_number);
                        }
                    }
                    catch (\Exception $e) {
                        $data_termino_atividade       = null;
                    }
                }
                else {
                    $data_termino_atividade    = null;
                }
//trata   hora_tmino_atividade
                if(! Empty($registro['data_tmino_atividade'])) {
                    try {
                        $dmt_number = floatVal($registro['hora_tmino_atividade']);
                        $hora_tmino_atividade = 1440 * $dmt_number / 60 ;  //15.33;
                        $hora_tmino_atividade = sprintf('%02d:%02d', (int) $hora_tmino_atividade, fmod($hora_tmino_atividade, 1) * 60).':00'; // 15:19
                    }
                    catch (\Exception $e) {
                        $hora_tmino_atividade    = null;
                    }
                }
                else {
                    $hora_tmino_atividade    = null;
                }

                if( $data_incio_atividade !== Null) {
                    SgdoDistribuicao::UpdateOrCreate(
                        [
                            'mcu' =>  $registro['mcu']
                            , 'matricula' =>  $registro['matrula']
                            , 'data_incio_atividade' =>  $data_incio_atividade
                        ],[
                        'dr' => $registro['dr']
                        , 'unidade' =>  $registro['unidade']
                        , 'mcu' =>  $registro['mcu']
                        , 'centralizadora' =>  $registro['centralizadora']
                        , 'mcu_centralizadora' => $registro['mcu_centralizadora']
                        , 'distrito' =>  $registro['distrito']
                        , 'area' =>  $registro['rea']
                        , 'locomocao' =>  $registro['locomoo']
                        , 'funcionario' =>  $registro['funcionio']
                        , 'matricula' =>  $registro['matrula']
                        , 'data_incio_atividade' =>  $data_incio_atividade
                        , 'hora_incio_atividade' => $hora_inio_atividade
                        , 'data_saida' =>  $data_saida
                        , 'hora_saida' =>  $hora_saa
                        , 'data_retorno' =>  $data_retorno
                        , 'hora_retorno' =>  $hora_retorno
                        , 'data_tpc' =>  $data_tpc
                        , 'hora_do_tpc' =>  $hora_do_tpc
                        , 'data_termino_atividade' =>  $data_termino_atividade
                        , 'hora_termino_atividade' =>   $hora_tmino_atividade
                        , 'justificado' =>  $registro['justificado']
                        , 'peso_da_bolsa_kg' =>  $registro['peso_da_bolsa_kg']
                        , 'peso_do_da_kg' =>  $registro['peso_do_da_kg']
                        , 'quantidade_de_da' =>  $registro['quantidade_de_da']
                        , 'quantidade_de_gu' =>  $registro['quantidade_de_gu']
                        , 'quantidade_de_objetos_qualificados' =>  $registro['quantidade_de_objetos_qualificados']
                        , 'quantidade_de_objetos_coletados' =>  $registro['quantidade_de_objetos_coletados']
                        , 'quantidade_de_pontos_de_entregacoleta' =>  $registro['quantidade_de_pontos_de_entregacoleta']
                        , 'quilometragem_percorrida' =>  $registro['quilometragem_percorrida']
                        , 'residuo_simples' =>  $registro['resuo_simples']
                        , 'residuo_qualificado' =>  $registro['resuo_qualificado']
                        , 'almoca_na_unidade' =>  $registro['almo_na_unidade']
                        , 'compartilhado' =>  $registro['compartilhado']
                        , 'tipo_de_distrito' =>  $registro['tipo_de_distrito']
                    ]);
                }
            }
        }
        DB::table('sgdo_distribuicao')
            ->where('data_incio_atividade', '<',   $dtmenos180dias)
        ->delete();
    }
}
