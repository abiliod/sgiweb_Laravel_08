<?php

namespace App\Jobs;

use App\Models\Correios\ModelsAuxiliares\MicroStrategy;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class JobMicroStrategy implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected  $micro_strategy, $dt_job, $dtmenos210dias;
    public function __construct($micro_strategy, $dt_job, $dtmenos210dias)
    {
        $this->micro_strategy =  $micro_strategy;
        $this->dt_job = $dt_job;
        $this->dtmenos210dias = $dtmenos210dias;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $micro_strategy = $this->micro_strategy;
        $dtmenos210dias = $this->dtmenos210dias;

        foreach($micro_strategy as $dados)
        {
            foreach($dados as $registro)
            {
//                importa somente eventos BDE
                if ($registro['codigo_do_evento'] == 'BDE') {

//trata data   data_do_evento
                    if(! Empty($registro['data_do_evento'])) {

                        try {
                            $dt_number = intVal($registro['data_do_evento']);
                            if (is_numeric($dt_number)) {
                                $data_do_evento = new Carbon('1899-12-30');
                                $data_do_evento = $data_do_evento->addDays($dt_number);
                            }
                        }
                        catch (\Exception $e) {
                            $data_do_evento       =  null;
                        }
                    }
                    else {
                        $data_do_evento    = null;
                    }

                    MicroStrategy::updateOrCreate([
                        'codigo_do_objeto' => $registro['codigo_do_objeto']
                        , 'dr_de_destino' => $registro['dr_de_destino']
                        , 'nome_da_unidade' => $registro['nome_da_unidade']
                    ],[
                            'codigo_do_objeto' => $registro['codigo_do_objeto']
                            , 'dr_de_destino' => $registro['dr_de_destino']
                            , 'nome_da_unidade' => $registro['nome_da_unidade']
                            , 'descricao_do_evento' => $registro['descricao_do_evento']
                            , 'codigo_do_evento' => $registro['codigo_do_evento']
                            , 'data_do_evento' => $data_do_evento
                        ]
                    );
                }
            }
        }
        DB::table('micro_strategys')
            ->where('data_do_evento', '<=', $dtmenos210dias)
        ->delete();
    }
}
