<?php

namespace App\Jobs;

use App\Models\Correios\ModelsAuxiliares\Snci;

use http\Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;



class JobSnci implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected   $snci, $dt;

    public function __construct(  $snci , $dt )
    {
        $this->snci = $snci;
        $this->dt = $dt;
        ini_set('memory_limit', '512M');

    }
//php artisan queue:work --queue=importacao
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ini_set('memory_limit', '512M');
        $snci = $this->snci;

        foreach ($snci as $dados) {
            foreach ($dados as $registro) {

//                    if (!empty($registro['dt_inic_desloc'])) {
//                        try {
//                            $dt_inic_desloc = substr($registro['dt_inic_desloc'], 6, 4) . '-' . substr($registro['dt_inic_desloc'], 3, 2) . '-' . substr($registro['dt_inic_desloc'], 0, 2);
////                            $dt_inic_desloc = $this->transformDate($dt_inic_desloc)->format('Y-m-d');
//                        } catch (Exception $e) {
//                            $dt_inic_desloc = null;
//                        }
//                    }
//                    if (!empty($registro['dt_fim_desloc'])) {
//                        try {
//                            $dt_fim_desloc = substr($registro['dt_fim_desloc'], 6, 4) . '-' . substr($registro['dt_fim_desloc'], 3, 2) . '-' . substr($registro['dt_fim_desloc'], 0, 2);
////                            $dt_fim_desloc = $this->transformDate($dt_fim_desloc)->format('Y-m-d');
//                        } catch (Exception $e) {
//                            $dt_fim_desloc = null;
//                        }
//                    }
//                    if (!empty($registro['data_previsao_solucao'])) {
//                        try {
//                            $data_previsao_solucao = substr($registro['data_previsao_solucao'], 6, 4) . '-' . substr($registro['data_previsao_solucao'], 3, 2) . '-' . substr($registro['data_previsao_solucao'], 0, 2);
////                            $data_previsao_solucao = $this->transformDate($data_previsao_solucao)->format('Y-m-d');
//                        } catch (Exception $e) {
//                            $data_previsao_solucao = null;
//                        }
//
//                    }
//                    if (!empty($registro['dt_posicao'])) {
//                        try {
//                            $dt_posicao = substr($registro['dt_posicao'], 6, 4) . '-' . substr($registro['dt_posicao'], 3, 2) . '-' . substr($registro['dt_posicao'], 0, 2);
//                            $dt_posicao = $this->transformDate($dt_posicao)->format('Y-m-d');
//                        } catch (Exception $e) {
//                            $dt_posicao = null;
//                        }
//                    }
//

                if (!empty($registro['dtultatu'])) {
                    try {
                        $dtultatu = substr($registro['dtultatu'], 6, 4) . '-' . substr($registro['dtultatu'], 3, 2) . '-' . substr($registro['dtultatu'], 0, 2);
//                            $dtultatu = $this->transformDate($dtultatu)->format('Y-m-d');
                    } catch (\Exception $e) {
                        $dtultatu = null;
                    }
                }

                if (!empty($registro['dt_inic_inspecao'])) {
                    try {
                        $dt_inic_inspecao = substr($registro['dt_inic_inspecao'], 6, 4) . '-' . substr($registro['dt_inic_inspecao'], 3, 2) . '-' . substr($registro['dt_inic_inspecao'], 0, 2);
//                            $dt_inic_inspecao = $this->transformDate($dt_inic_inspecao)->format('Y-m-d');
                    } catch (\Exception $e) {
                        $dt_inic_inspecao = null;
                    }
                }

                if (!empty($registro['dt_fim_inspecao'])) {
                    try {
                        $dt_fim_inspecao = substr($registro['dt_fim_inspecao'], 6, 4) . '-' . substr($registro['dt_fim_inspecao'], 3, 2) . '-' . substr($registro['dt_fim_inspecao'], 0, 2);
//                            $dt_fim_inspecao = $this->transformDate($dt_fim_inspecao)->format('Y-m-d');
                    } catch (\Exception $e) {
                        $dt_fim_inspecao = null;
                    }
                }

                if (!empty($registro['dt_encerram'])) {
                    try {
                        $dt_encerram = substr($registro['dt_encerram'], 6, 4) . '-' . substr($registro['dt_encerram'], 3, 2) . '-' . substr($registro['dt_encerram'], 0, 2);
//                            $dt_encerram = $this->transformDate($dt_encerram)->format('Y-m-d');
                    } catch (\Exception $e) {
                        $dt_encerram = null;
                    }
                }

                if ($registro['falta'] > 0) {
                    $falta = str_replace(',', '.', $registro['falta']);
                } else {
                    $falta = 0.00;
                }
                if( $registro['sobra'] > 0){
                    $sobra = str_replace(',', '.', $registro['sobra']);
                }else{
                    $sobra=0.00;
                }
                if( $registro['emrisco'] > 0){
                    $risco = str_replace(',', '.', $registro['emrisco']);
                }else{
                    $risco=0.00;
                }

//                $valor_recuperado = 0.00;

                Snci::updateOrCreate([
                    'no_inspecao' =>  $registro['no_inspecao'],
                    'codigo_unidade' =>  $registro['codigo_unidade'],
                    'no_grupo' =>  $registro['no_grupo'],
                    'no_item' =>  $registro['no_item']
                ], [
                    'modalidade' => $registro['modalidade'],
                    'diretoria' => $registro['diretoria'],
                    'codigo_unidade' => $registro['codigo_unidade'],
                    'descricao_da_unidade' => $registro['descricao_da_unidade'],
                    'no_inspecao' => $registro['no_inspecao'],
                    'no_grupo' => $registro['no_grupo'],
                    'descricao_do_grupo' => $registro['descricao_do_grupo'],
                    'no_item' => $registro['no_item'],
                    'descricao_item' => $registro['descricao_item'],
                    'codigo_reate' => $registro['codigo_reate'],
                    'ano' => $registro['ano'],
                    'sto' => $registro['codigo_unidade'],
                    'resposta' => $registro['resposta'],
                    'valor' => $registro['valor'],
                    'caracteresvlr' => $registro['caracteresvlr'],
                    'situacao' => $registro['situacao'],
                    'status' => $registro['status'],
                    'comentario' => $registro['comentario'],
                    'sigla_do_status' => $registro['sigla_do_status'],
                    'descricao_do_status' => $registro['descricao_do_status'],
                    'dtultatu' => $dtultatu,
                    'dt_inic_inspecao' => $dt_inic_inspecao,
                    'dt_fim_inspecao' => $dt_fim_inspecao,
                    'dt_encerram' => $dt_encerram,
                    'falta' => $falta,
                    'sobra' => $sobra,
                    'emrisco' => $risco
//                    'valor_recuperado' => $valor_recuperado
                ]);

            }

        }

        ini_set('memory_limit', '64M');
    }

}
