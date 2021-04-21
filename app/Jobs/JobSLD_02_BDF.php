<?php

namespace App\Jobs;

use App\Models\Correios\ModelsAuxiliares\SL02_bdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class JobSLD_02_BDF implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $SL02_bdfs, $dt_job, $dtmenos150dias;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($SL02_bdfs, $dt_job,$dtmenos150dias)
    {
        $this->SL02_bdfs = $SL02_bdfs;
        $this->dt_job = $dt_job;
        $this->dtmenos150dias = $dtmenos150dias;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $SL02_bdfs = $this->SL02_bdfs;
        $dtmenos150dias = $this->dtmenos150dias;

        DB::table('sl02bdfs')
            ->where('dt_movimento', '<', $dtmenos150dias)
        ->delete();

        ini_set('memory_limit', '512M');

        foreach($SL02_bdfs as $registros) {
            foreach($registros as $dado) {

                if(!empty($dado['dta'])) {
                    try {
                        $dt = substr($dado['dta'], 6, 4) . '-' . substr($dado['dta'], 3, 2) . '-' . substr($dado['dta'], 0, 2);
                    }
                    catch (\Exception $e) {
                        $dt = null;
                    }
                }
                else {
//                        outra rotina  codigos  refatorar todos jobs  para padronizar algumas inportações
                }

                $saldo_atual = floatval($dado['saldo']);
                $limite = floatval($dado['limite']);
                if($saldo_atual > $limite){
                    $diferenca = $saldo_atual - $limite;
                }
                else{
                    $diferenca = 0;
                }


                if( $dt !== null){
//                   DB::enableQueryLog();
                   SL02_bdf :: updateOrCreate([
                        'cod_orgao' => $dado['sto'],
                        'dt_movimento' => $dt,
                    ],[
                        'cod_orgao' => $dado['sto'],
                        'dt_movimento' => $dt,
                        'dr' => $dado['dr'],
                        'reop' => $dado['reven'],
                        'orgao' => $dado['agncia'],
                        'saldo_atual' => $saldo_atual,
                        'limite' => $limite,
                        'diferenca' => $diferenca,
                    ]);
//                    dd( DB::getQueryLog());
                }

            }
        }

        ini_set('memory_limit', '128M');
    }
}
