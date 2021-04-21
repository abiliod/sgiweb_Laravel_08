<?php

namespace App\Jobs;

use App\Models\Correios\ModelsAuxiliares\FeriasPorMcu;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
//use Carbon\Carbon;

class JobFeriasPorMCU implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $ferias, $dt_job;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($ferias, $dt_job)
    {
        $this->ferias = $ferias;
        $this->dt_job = $dt_job;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $ferias = $this->ferias;
        $dt_job = $this->dt_job;
        foreach($ferias as $dados) {
            foreach($dados as $registro) {

                if(!empty($registro['inicio_fruicao'])) {
                    try {
                        $dtini =  substr($registro['inicio_fruicao'],6,4)
                            .'-'. substr($registro['inicio_fruicao'],3,2)
                            .'-'. substr($registro['inicio_fruicao'],0,2);
                    }
                    catch (Exception $e) {
                        $dtini ="";
                    }
                }

                if(!empty($registro['termino_fruicao'])) {
                    try {
                        $dtfim =  substr($registro['termino_fruicao'],6,4)
                            .'-'. substr($registro['termino_fruicao'],3,2)
                            .'-'. substr($registro['termino_fruicao'],0,2);
                    } catch (Exception $e) {
                        $dtfim ="";
                    }
                }
                $reg = new FeriasPorMcu;
                $reg->matricula      = $registro['matricula'];
                $reg->nome      = $registro['nome'];
                $reg->lotacao      = $registro['lotacao'];
                $reg->funcao      = $registro['funcao'];
                $reg->inicio_fruicao      =  $dtini;
                $reg->termino_fruicao      =  $dtfim;
                $reg->dias      = $registro['dias'];
                $reg->save();

                DB::table('ferias_por_mcu')
                    ->where('matricula', '=',   $reg->matricula)
                    ->where('created_at', '<',   $dt_job)
                ->delete();
            }
        }
    }
}
