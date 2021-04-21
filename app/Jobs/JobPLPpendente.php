<?php

namespace App\Jobs;

use App\Models\Correios\ModelsAuxiliares\PLPListaPendente;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class JobPLPpendente implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
protected  $plpListaPendentes, $dt_job;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($plpListaPendentes, $dt_job)
    {
        $this->plpListaPendentes = $plpListaPendentes;
        $this->dt_job=$dt_job;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $plpListaPendentes = $this->plpListaPendentes;
        $dt_job = $this->dt_job;
        foreach ($plpListaPendentes as $dados) {
            foreach ($dados as $registro) {
                if (!empty($registro['dh_lista_postagem'])) {
                    try {
//                        $dh_lista_postagem = $this->transformDate($registro['dh_lista_postagem']);
                        $dateTimeString = $registro['dh_lista_postagem'] . '00:00:00';
                        $format = 'Y-m-d';
                        $dh_lista_postagem = Carbon::createFromFormat($format, $dateTimeString, 'America/São Paulo');
                    } catch (\Exception $e) {
                        $dh_lista_postagem = null;
                    }
                }

                if ($dh_lista_postagem !== null) {
                    PLPListaPendente :: updateOrCreate([
                        'objeto' => $registro['objeto'],
                    ],[
                        'dh_lista_postagem' => $dh_lista_postagem,
                        'objeto' => $registro['objeto'],
                        'dr' => $registro['dr'],
                        'stomcu' => $registro['stomcu'],
                        'lista' => $registro['lista'],
                        'plp' => $registro['plp'],
                        'cliente' => $registro['cliente'],
                        'nome_agencia' => $registro['nome_agencia'],
                    ]);
                }
            }
            DB::table('plplistapendentes')
                ->where('dr', '=', $registro['dr'])
                ->where('updated_at', '<', $dt_job)
                ->delete();
        }
    }
}
