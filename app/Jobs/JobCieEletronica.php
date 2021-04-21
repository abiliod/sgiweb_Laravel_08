<?php

namespace App\Jobs;

use App\Models\Correios\ModelsAuxiliares\CieEletronica;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class JobCieEletronica implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $cie_eletronicas, $dt_job, $dtmenos365dias;

    public function __construct($cie_eletronicas, $dt_job, $dtmenos365dias)
    {
        $this->cie_eletronicas =  $cie_eletronicas;
        $this->dt_job = $dt_job;
        $this->dtmenos365dias = $dtmenos365dias;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $cie_eletronicas = $this->cie_eletronicas;
        $dtmenos365dias = $this->dtmenos365dias;

        foreach($cie_eletronicas as $dados) {
            foreach ($dados as $registro) {
                //    dd($registro);
                if (!empty($registro['emissao'])) {
                    if (strlen($registro['emissao']) > 11) {
                        try {
                            //  31/07/2020 22:10:29
                            //$emissao=substr($registro['emissao'],6,4);
                            //$emissao=substr($registro['emissao'],3,2);
                            //$emissao=substr($registro['emissao'],0,2).'/'.substr($registro['emissao'],3,2).'/'.substr($registro['emissao'],6,4).' '.$emissao=substr($registro['emissao'],11,8);
                            //$emissao=substr($registro['emissao'],0,2).'/'.substr($registro['emissao'],3,2).'/'.substr($registro['emissao'],6,4).' '.$emissao=substr($registro['emissao'],11,8);
                            $emissao = substr($registro['emissao'], 6, 4) . '-' . $emissao = substr($registro['emissao'], 3, 2) . '-' . $emissao = substr($registro['emissao'], 0, 2) . ' ' . $emissao = substr($registro['emissao'], 11, 8);
                            //var_dump($emissao );
                            //->toDateTimeString();
                            //$emissao = $this->transformDate($registro['emissao']).' '.$this->transformTime($registro['emissao']);
                            //Carbon::createFromFormat('m/d/Y H:i:s', $registro['emissao'])->format('Y-m-d H:i:s');
                        } catch (\Exception $e) {
                            $emissao = null;
                        }
                    }

                    //trata data   data_de_resposta
                    if(! Empty($registro['data_de_resposta'])) {
                        try {
                            $dt_number = intVal($registro['data_de_resposta']);
                            if (is_numeric($dt_number)) {
                                $data_de_resposta = new Carbon('1899-12-30');
                                $data_de_resposta = $data_de_resposta->addDays($dt_number);
                            }
                        }
                        catch (\Exception $e) {
                            $data_de_resposta       =  null;
                        }
                    }
                    else {
                        $data_de_resposta    = null;
                    }

                    if ($emissao !== null){

                        CieEletronica::updateOrCreate([
                            'emissao' => $emissao
                            , 'numero' => $registro['numero']
                            , 'se_origem' => $registro['se_origem']
                            , 'origem' => $registro['origem']
                            , 'destino' => $registro['destino']
                            , 'se_destino' => $registro['se_destino']
                            , 'irregularidade' => $registro['irregularidade']
                            , 'categoria' => $registro['categoria']
                            , 'numero_objeto' => $registro['numero_objeto']
                        ], [
                            'emissao' => $emissao
                            , 'numero' => $registro['numero']
                            , 'se_origem' => $registro['se_origem']
                            , 'origem' => $registro['origem']
                            , 'destino' => $registro['destino']
                            , 'se_destino' => $registro['se_destino']
                            , 'irregularidade' => $registro['irregularidade']
                            , 'categoria' => $registro['categoria']
                            , 'numero_objeto' => $registro['numero_objeto']
                            , 'lida' => $registro['lida']
                            , 'respondida' => $registro['respondida']
                            , 'fora_do_prazo' => $registro['fora_do_prazo']
                            , 'resposta' => $registro['resposta']
                            , 'data_de_resposta' => $data_de_resposta
                        ]);

                    }

                }
            }
        }
        DB::table('cie_eletronicas')
            ->where('created_at', '<=', $dtmenos365dias)
        ->delete();
    }
}
