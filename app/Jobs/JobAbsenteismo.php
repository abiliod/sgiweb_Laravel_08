<?php

namespace App\Jobs;

use App\Models\Correios\ModelsAuxiliares\Absenteismo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class JobAbsenteismo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $absenteismos, $dt_job, $dtmenos12meses;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($absenteismos, $dt_job, $dtmenos12meses)
    {
        $this->absenteismos = $absenteismos;
        $this->dt_job = $dt_job;
        $this->dtmenos12meses = $dtmenos12meses;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $absenteismos = $this->absenteismos;
        $dtmenos12meses = $this->dtmenos12meses;
        DB::table('absenteismos')
            ->where('data_evento', '<', $dtmenos12meses)
            ->delete();

        ini_set('memory_limit', '512M');
        foreach($absenteismos as $dados) {
            foreach($dados as $registro) {

                $dt = substr($registro['data_evento'],6,4).'-'. substr($registro['data_evento'],3,2) .'-'. substr($registro['data_evento'],0,2);
                $res = DB::table('absenteismos')
                    ->where('matricula', '=',  $registro['matricula'])
                    ->where('data_evento','=', $dt)
                    ->select(
                        'absenteismos.id'
                    )
                    ->first();

                if(!empty(  $res->id )) {
                    $absenteismos = Absenteismo::find($res->id);
                }
                else {
                    $absenteismos = new Absenteismo;
                }
                $absenteismos->matricula = $registro['matricula'];
                $absenteismos->nome = $registro['nome'];
                $absenteismos->lotacao = $registro['lotacao'];
                $absenteismos->cargo = $registro['cargo'];
                $absenteismos->motivo = $registro['motivo'];
                $absenteismos->dias = $registro['dias'];
                $absenteismos->data_evento = $dt;
                $absenteismos->save();

            }
        }
         ini_set('memory_limit', '128M');
    }
}
