<?php

namespace App\Jobs;

use App\Models\Correios\ModelsAuxiliares\Alarme;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\DB;

class JobAlarmes implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $alarmes, $dt_job, $dtmenos12meses;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($alarmes, $dt_job, $dtmenos12meses)
    {
        $this->alarmes = $alarmes;
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
        $alarmes = $this->alarmes;
        $dtmenos12meses = $this->dtmenos12meses;

        DB::table('alarmes')
            ->where('data', '<', $dtmenos12meses)
        ->delete();
        ini_set('memory_limit', '512M');
        foreach($alarmes as $registros) {
            foreach($registros as $dado) {

                $diaSemana    = null;
                if(!empty($dado['data'])) {
                    try {
                        $dateTimeString = $dado['data'] . $dado['hora'];
                        $format = 'Y-m-d';

                        $dt = Carbon::createFromFormat($format, $dateTimeString, 'America/São Paulo');
                        $diaSemana    =  $dt->dayOfWeek;
                    }
                    catch (\Exception $e) {
                        $dt = null;
                    }
                }
                else {
//                        outra rotina  codigos  refatorar todos jobs  para padronizar algumas inportações
                }
                $matricula =   $this->deixarNumero($dado['matricula']);
                if( $dt !== null){
                    Alarme :: updateOrCreate([
                        'mcu' => $dado['mcu'],
                        'data' => $dt,
                        'hora' => $dado['hora'],
                    ],[
                        'mcu' => $dado['mcu'],
                        'data' => $dt,
                        'hora' => $dado['hora'],
                        'diaSemana' => $diaSemana,
                        'cliente' => $dado['cliente'],
                        'armedesarme' => $dado['armedesarme'],
                        'usuario' => $dado['usuario'],
                        'matricula' => $matricula,

                    ]);
                 }
            }
        }

        ini_set('memory_limit', '128M');
    }
    function deixarNumero($string){
        return preg_replace("/[^0-9]/", "", $string);
    }
}
