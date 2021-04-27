<?php

namespace App\Jobs;

use App\Models\Correios\Unidade;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class JobHorariosDERAT implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected   $unidades, $dt_job;

    public function __construct($unidades, $dt_job)
    {
        $this->unidades = $unidades;
        $this->dt_job = $dt_job;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $unidades = $this->unidades;
//        Atenção: o arquivo desse job deve ser exclusivamente oriundo da Planilha de horários do DERAT
//        DERAT_ Analítico - Lista de Agências
//      subir   1000 linhas por job aproximadamente

        foreach($unidades as $dados) {
            foreach($dados as $registro) {

                //trata hora  horario_inicio_expediente
                if(! Empty($registro['horario_inicio_expediente'])) {
                    try {
                        $dmt_number = floatVal($registro['horario_inicio_expediente']);
                        $horario_inicio_expediente = 1440 * $dmt_number / 60 ;  //15.33;
                        $horario_inicio_expediente = sprintf('%02d:%02d', (int) $horario_inicio_expediente, fmod($horario_inicio_expediente, 1) * 60).':00'; // 15:19
                    }
                    catch (\Exception $e) {
                        $horario_inicio_expediente    = null;
                    }
                }
                else {
                    $horario_inicio_expediente    = null;
                }

                //trata hora  horario_fim_expediente
                if(! Empty($registro['horario_fim_expediente'])) {
                    try {
                        $dmt_number = floatVal($registro['horario_fim_expediente']);
                        $horario_fim_expediente = 1440 * $dmt_number / 60 ;  //15.33;
                        $horario_fim_expediente = sprintf('%02d:%02d', (int) $horario_fim_expediente, fmod($horario_fim_expediente, 1) * 60).':00'; // 15:19
                    }
                    catch (\Exception $e) {
                        $horario_fim_expediente    = null;
                    }
                }
                else {
                    $horario_fim_expediente    = null;
                }

                //trata hora  horario_inicio_almoco
                if(! Empty($registro['horario_inicio_almoco'])) {
                    try {
                        $dmt_number = floatVal($registro['horario_inicio_almoco']);
                        $horario_inicio_almoco = 1440 * $dmt_number / 60 ;  //15.33;
                        $horario_inicio_almoco = sprintf('%02d:%02d', (int) $horario_inicio_almoco, fmod($horario_inicio_almoco, 1) * 60).':00'; // 15:19
                    }
                    catch (\Exception $e) {
                        $horario_inicio_almoco    = null;
                    }
                }
                else {
                    $horario_inicio_almoco    = null;
                }

//trata hora  horario_fim_almoco
                if(! Empty($registro['horario_fim_almoco'])) {
                    try {
                        $dmt_number = floatVal($registro['horario_fim_almoco']);
                        $horario_fim_almoco = 1440 * $dmt_number / 60 ;  //15.33;
                        $horario_fim_almoco = sprintf('%02d:%02d', (int) $horario_fim_almoco, fmod($horario_fim_almoco, 1) * 60).':00'; // 15:19
                    }
                    catch (\Exception $e) {
                        $horario_fim_almoco    = null;
                    }
                }
                else {
                    $horario_fim_almoco    = null;
                }

//trata hora  inicio_funcionamento_sabado
                if(! Empty($registro['inicio_funcionamento_sabado'])) {
                    try {
                        $dmt_number = floatVal($registro['inicio_funcionamento_sabado']);
                        $inicio_funcionamento_sabado = 1440 * $dmt_number / 60 ;  //15.33;
                        $inicio_funcionamento_sabado = sprintf('%02d:%02d', (int) $inicio_funcionamento_sabado, fmod($inicio_funcionamento_sabado, 1) * 60).':00'; // 15:19
                    }
                    catch (\Exception $e) {
                        $inicio_funcionamento_sabado    = null;
                    }
                }
                else {
                    $inicio_funcionamento_sabado    = null;
                }

                //trata hora  fim_funcionamento_sabado
                if(! Empty($registro['fim_funcionamento_sabado'])) {
                    try {
                        $dmt_number = floatVal($registro['fim_funcionamento_sabado']);
                        $fim_funcionamento_sabado = 1440 * $dmt_number / 60 ;  //15.33;
                        $fim_funcionamento_sabado = sprintf('%02d:%02d', (int) $fim_funcionamento_sabado, fmod($fim_funcionamento_sabado, 1) * 60).':00'; // 15:19
                    }
                    catch (\Exception $e) {
                        $fim_funcionamento_sabado    = null;
                    }
                }
                else {
                    $fim_funcionamento_sabado    = null;
                }

//trata hora  inicio_funcionamento_domingo
                if(! Empty($registro['inicio_funcionamento_domingo'])) {
                    try {
                        $dmt_number = floatVal($registro['inicio_funcionamento_domingo']);
                        $inicio_funcionamento_domingo = 1440 * $dmt_number / 60 ;  //15.33;
                        $inicio_funcionamento_domingo = sprintf('%02d:%02d', (int) $inicio_funcionamento_domingo, fmod($inicio_funcionamento_domingo, 1) * 60).':00'; // 15:19
                    }
                    catch (\Exception $e) {
                        $inicio_funcionamento_domingo    = null;
                    }
                }
                else {
                    $inicio_funcionamento_domingo    = null;
                }

// trata hora  fim_funcionamento_domingo
                if(! Empty($registro['fim_funcionamento_domingo'])) {
                    try {
                        $dmt_number = floatVal($registro['fim_funcionamento_domingo']);
                        $fim_funcionamento_domingo = 1440 * $dmt_number / 60 ;  //15.33;
                        $fim_funcionamento_domingo = sprintf('%02d:%02d', (int) $fim_funcionamento_domingo, fmod($fim_funcionamento_domingo, 1) * 60).':00'; // 15:19
                    }
                    catch (\Exception $e) {
                        $fim_funcionamento_domingo    = null;
                    }
                }
                else {
                    $fim_funcionamento_domingo    = null;
                }

//trata hora  inicio_plantao_sabado
                if(! Empty($registro['inicio_plantao_sabado'])) {
                    try {
                        $dmt_number = floatVal($registro['inicio_plantao_sabado']);
                        $inicio_plantao_sabado = 1440 * $dmt_number / 60 ;  //15.33;
                        $inicio_plantao_sabado = sprintf('%02d:%02d', (int) $inicio_plantao_sabado, fmod($inicio_plantao_sabado, 1) * 60).':00'; // 15:19
                    }
                    catch (\Exception $e) {
                        $inicio_plantao_sabado    = null;
                    }
                }
                else {
                    $inicio_plantao_sabado    = null;
                }

//trata hora  fim_plantao_sabado
                if(! Empty($registro['fim_plantao_sabado'])) {
                    try {
                        $dmt_number = floatVal($registro['fim_plantao_sabado']);
                        $fim_plantao_sabado = 1440 * $dmt_number / 60 ;  //15.33;
                        $fim_plantao_sabado = sprintf('%02d:%02d', (int) $fim_plantao_sabado, fmod($fim_plantao_sabado, 1) * 60).':00'; // 15:19
                    }
                    catch (\Exception $e) {
                        $fim_plantao_sabado    = null;
                    }
                }
                else {
                    $fim_plantao_sabado    = null;
                }

//trata hora  inicio_plantao_domingo
                if(! Empty($registro['inicio_plantao_domingo'])) {
                    try {
                        $dmt_number = floatVal($registro['inicio_plantao_domingo']);
                        $inicio_plantao_domingo = 1440 * $dmt_number / 60 ;  //15.33;
                        $inicio_plantao_domingo = sprintf('%02d:%02d', (int) $inicio_plantao_domingo, fmod($inicio_plantao_domingo, 1) * 60).':00'; // 15:19
                    }
                    catch (\Exception $e) {
                        $inicio_plantao_domingo    = null;
                    }
                }
                else {
                    $inicio_plantao_domingo    = null;
                }

//trata hora  fim_plantao_domingo
                if(! Empty($registro['fim_plantao_domingo'])) {
                    try {
                        $dmt_number = floatVal($registro['fim_plantao_domingo']);
                        $fim_plantao_domingo = 1440 * $dmt_number / 60 ;  //15.33;
                        $fim_plantao_domingo = sprintf('%02d:%02d', (int) $fim_plantao_domingo, fmod($fim_plantao_domingo, 1) * 60).':00'; // 15:19
                    }
                    catch (\Exception $e) {
                        $fim_plantao_domingo    = null;
                    }
                }
                else {
                    $fim_plantao_domingo    = null;
                }

//trata hora  horario_inicio_distribuicao
                if(! Empty($registro['horario_inicio_distribuicao'])) {
                    try {
                        $dmt_number = floatVal($registro['horario_inicio_distribuicao']);
                        $horario_inicio_distribuicao = 1440 * $dmt_number / 60 ;  //15.33;
                        $horario_inicio_distribuicao = sprintf('%02d:%02d', (int) $horario_inicio_distribuicao, fmod($horario_inicio_distribuicao, 1) * 60).':00'; // 15:19
                    }
                    catch (\Exception $e) {
                        $horario_inicio_distribuicao    = null;
                    }
                }
                else {
                    $horario_inicio_distribuicao    = null;
                }

//trata hora  horario_fim_distribuicao
                if(! Empty($registro['horario_fim_distribuicao'])) {
                    try {
                        $dmt_number = floatVal($registro['horario_fim_distribuicao']);
                        $horario_fim_distribuicao = 1440 * $dmt_number / 60 ;  //15.33;
                        $horario_fim_distribuicao = sprintf('%02d:%02d', (int) $horario_fim_distribuicao, fmod($horario_fim_distribuicao, 1) * 60).':00'; // 15:19
                    }
                    catch (\Exception $e) {
                        $horario_fim_distribuicao    = null;
                    }
                }
                else {
                    $horario_fim_distribuicao    = null;
                }

//trata hora  dh_semana
                if(! Empty($registro['dh_semana'])) {
                    try {
                        $dmt_number = floatVal($registro['dh_semana']);
                        $dh_semana = 1440 * $dmt_number / 60 ;  //15.33;
                        $dh_semana = sprintf('%02d:%02d', (int) $dh_semana, fmod($dh_semana, 1) * 60).':00'; // 15:19
                    }
                    catch (\Exception $e) {
                        $dh_semana    = null;
                    }
                }
                else {
                    $dh_semana    = null;
                }

//trata hora  dh_fim_semana
                if(! Empty($registro['dh_fim_semana'])) {
                    try {
                        $dmt_number = floatVal($registro['dh_fim_semana']);
                        $dh_fim_semana = 1440 * $dmt_number / 60 ;  //15.33;
                        $dh_fim_semana = sprintf('%02d:%02d', (int) $dh_fim_semana, fmod($dh_fim_semana, 1) * 60).':00'; // 15:19
                    }
                    catch (\Exception $e) {
                        $dh_fim_semana    = null;
                    }
                }
                else {
                    $dh_fim_semana    = null;
                }

                $res = DB::table('unidades')
                    ->where('an8', '=',  (int)$registro['no_cad_geral'])
                    ->select(
                        'unidades.*'
                    )
                    ->first();
                if(!empty(  $res->id )) {

                    $unidade = Unidade::find($res->id);
                    $unidade->inicio_atendimento = $horario_inicio_expediente;
                    $unidade->final_atendimento = $horario_fim_expediente;
                    $unidade->inicio_expediente = $horario_inicio_expediente;
                    $unidade->final_expediente = $horario_fim_expediente;
                    $unidade->inicio_intervalo_refeicao = $horario_inicio_almoco;
                    $unidade->final_intervalo_refeicao = $horario_fim_almoco;
                    $unidade->trabalha_sabado =$registro['funciona_sabado'];
                    $unidade->trabalha_domingo =$registro['funciona_domingo'];
                    $unidade->tem_plantao = $registro['tem_plantao'];
                    $unidade->tem_distribuicao = $registro['tem_distribuicao'];
                    $unidade->inicio_expediente_sabado = $inicio_funcionamento_sabado;
                    $unidade->final_expediente_sabado = $fim_funcionamento_sabado;
                    $unidade->inicio_expediente_domingo = $inicio_funcionamento_domingo;
                    $unidade->final_expediente_domingo = $fim_funcionamento_domingo;
                    $unidade->inicio_plantao_sabado = $inicio_plantao_sabado;
                    $unidade->final_plantao_sabado = $fim_plantao_sabado;
                    $unidade->inicio_plantao_domingo = $inicio_plantao_domingo;
                    $unidade->final_plantao_domingo = $fim_plantao_domingo;
                    $unidade->inicio_distribuicao = $horario_inicio_distribuicao;
                    $unidade->final_distribuicao = $horario_fim_distribuicao;
                    $unidade->horario_lim_post_na_semana = $dh_semana;
                    $unidade->horario_lim_post_final_semana = $dh_fim_semana;
                    $unidade->update();
                }
            }
        }
    }
}
