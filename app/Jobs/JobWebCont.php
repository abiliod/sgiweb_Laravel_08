<?php

namespace App\Jobs;

use App\Models\Correios\ModelsAuxiliares\DebitoEmpregado;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;




class JobWebCont implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected   $debitoEmpregados, $dt_job ;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($debitoEmpregados, $dt_job)
    {
        $this->debitoEmpregados = $debitoEmpregados;
        $this->dt_job = $dt_job;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $debitoEmpregados = $this->debitoEmpregados;
        $dt_job = $this->dt_job;
        ini_set('memory_limit', '512M');

        foreach($debitoEmpregados as $dados) {
            foreach($dados as $dado) {

//                if(! $dado['data']=='') {
////                    $data = $this->transformDate($dado['data']);
//                    $data = substr($dado['data'],6,4).'-'. substr($dado['data'],3,2) .'-'. substr($dado['data'],0,2);
//                }
//                else {
//                    $data = null;
//                }

                $dt_number = intVal($dado['data']);
                if (is_numeric($dt_number)) {
                    $dt = new Carbon('1899-12-30');
                    $dt = $dt->addDays($dt_number);
                }

                if( ! empty($dado['valor'])) {
                    try {
                        $valor = str_replace(",", ".", $dado['valor']);
                    }
                    catch (\Exception $e) {
                        $valor = 0.00;
                    }
                }
                else{
                    $valor = 0.00;
                }

                DebitoEmpregado :: updateOrCreate([
                    'conta' => $dado['conta']
                    , 'matricula' => $dado['matricula_ref2']
                    , 'data' => $dt
                ],
                    [
                        'conta' => $dado['conta']
                        , 'matricula' => $dado['matricula_ref2']
                        , 'data' => $dt
                        , 'cia' => $dado['cia']
                        , 'competencia' => $dado['competencia']
                        , 'lote' => $dado['lote']
                        , 'tp' => $dado['tp']
                        , 'sto' => $dado['mcu_doc1']
                        , 'nome_unidade' => $dado['nome_agencia_doc2']
                        , 'historico' => $dado['historico']
                        , 'observacoes' => $dado['observacoes']
                        , 'documento' => $dado['documento_ref1']
                        , 'nomeEmpregado' => $dado['nome_empregado_ref3']
                        , 'justificativa' => $dado['justificativa_ad1']
                        , 'regularizacao' => $dado['regularizacao']
                        , 'acao' => $dado['acao']
                        , 'anexo' => $dado['anexo']
                        , 'valor' => $valor
                    ]);
            }
//              higieniza a base de dados  excluindo por regional registros de
//              competências anteriores que nao foram atualiados.
            DB::table('debitoempregados')
                ->where('cia', '=', $dado['cia'])
                ->where('conta', '=', $dado['conta'])
                ->where('competencia', '<', $dado['competencia'])
                ->where('updated_at', '<', $dt_job)
                ->delete();
        }

        ini_set('memory_limit', '128M');

    }

}
