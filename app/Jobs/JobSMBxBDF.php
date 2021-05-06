<?php

namespace App\Jobs;

use App\Models\Correios\ModelsAuxiliares\SMBxBDF_NaoConciliado;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class JobSMBxBDF implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
protected $SMBxBDF_NaoConciliado,$dtmenos120dias, $dt_job;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($SMBxBDF_NaoConciliado,$dtmenos120dias, $dt_job)
    {
       $this->SMBxBDF_NaoConciliado = $SMBxBDF_NaoConciliado;
       $this->dtmenos120dias = $dtmenos120dias;
       $this->dt_job =  $dt_job;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $SMBxBDF_NaoConciliado = $this->SMBxBDF_NaoConciliado;
        $dtmenos120dias = $this->dtmenos120dias;

        foreach($SMBxBDF_NaoConciliado as $registros) {
            foreach($registros as $dado) {

                //trata data   $data
                if(! Empty($dado['data'])) {
                    try {
                        $dt_number = intVal($dado['data']);
                        if (is_numeric($dt_number)) {
                            $dt = new Carbon('1899-12-30');
                            $dt = $dt->addDays($dt_number);
                        }
                    }
                    catch (\Exception $e) {
                        $dt       =  null;
                    }
                }
                else {
                    $dt    = null;
                }

                if($dado['smbdinheiro'] != 0) {
                    $smbdinheiro = str_replace(",", ".", $dado['smbdinheiro']);
                }
                else {
                    $smbdinheiro = 0.00;
                }

                if($dado['smbcheque'] != 0) {
                    $smbcheque = str_replace(",", ".", $dado['smbcheque']);
                }
                else {
                    $smbcheque = 0.00;
                }

                if($dado['smbboleto'] != 0) {
                    $smbboleto = str_replace(",", ".", $dado['smbboleto']);
                }
                else {
                    $smbboleto = 0.00;
                }

                if($dado['smbestorno'] != 0) {
                    $smbestorno = str_replace(",", ".", $dado['smbestorno']);
                }
                else {
                    $smbestorno = 0.00;
                }

                if($dado['bdfdinheiro'] != 0) {
                    $bdfdinheiro = str_replace(",", ".", $dado['bdfdinheiro']);
                }
                else {
                    $bdfdinheiro = 0.00;
                }

                if($dado['bdfcheque'] != 0) {
                    $bdfcheque = str_replace(",", ".", $dado['bdfcheque']);
                }
                else {
                    $bdfcheque = 0.00;
                }

                if($dado['bdfboleto'] != 0) {
                    $bdfboleto = str_replace(",", ".", $dado['bdfboleto']);
                }
                else {
                    $bdfboleto = 0.00;
                }

                if($dado['divergencia'] != 0) {
                    $divergencia = str_replace(",", ".", $dado['divergencia']);
                }
                else {
                    $divergencia = 0.00;
                }

                $res = DB::table('smb_bdf_naoconciliados')
                    ->where('mcu', '=',  $dado['mcu'])
                    ->where('data','=', $dt )
                    ->select(
                        'smb_bdf_naoconciliados.id'
                    )
                    ->first();
                if( ! empty( $res->id )) {
                    $smb_bdf_naoconciliados = SMBxBDF_NaoConciliado::find($res->id);
                    $smb_bdf_naoconciliados->mcu  = $dado['mcu'];
                    $smb_bdf_naoconciliados->agencia      = $dado['agencia'];
                    $smb_bdf_naoconciliados->cnpj  = $dado['cnpj'];
                    $smb_bdf_naoconciliados->status  = $dado['status'];
                    $smb_bdf_naoconciliados->Data = $dt;
                    $smb_bdf_naoconciliados->smbdinheiro = $smbdinheiro;
                    $smb_bdf_naoconciliados->smbcheque = $smbcheque;
                    $smb_bdf_naoconciliados->smbboleto = $smbboleto;
                    $smb_bdf_naoconciliados->smbestorno = $smbestorno;
                    $smb_bdf_naoconciliados->bdfdinheiro = $bdfdinheiro;
                    $smb_bdf_naoconciliados->bdfcheque = $bdfcheque;
                    $smb_bdf_naoconciliados->bdfboleto = $bdfboleto;
                    $smb_bdf_naoconciliados->divergencia = $divergencia;
                }
                else {
                    if($dado['status'] == 'Pendente' ) {
                        $smb_bdf_naoconciliados = new SMBxBDF_NaoConciliado;
                        $smb_bdf_naoconciliados->mcu  = $dado['mcu'];
                        $smb_bdf_naoconciliados->agencia      = $dado['agencia'];
                        $smb_bdf_naoconciliados->cnpj  = $dado['cnpj'];
                        $smb_bdf_naoconciliados->status  = $dado['status'];
                        $smb_bdf_naoconciliados->Data = $dt;
                        $smb_bdf_naoconciliados->smbdinheiro = $smbdinheiro;
                        $smb_bdf_naoconciliados->smbcheque = $smbcheque;
                        $smb_bdf_naoconciliados->smbboleto = $smbboleto;
                        $smb_bdf_naoconciliados->smbestorno = $smbestorno;
                        $smb_bdf_naoconciliados->bdfdinheiro = $bdfdinheiro;
                        $smb_bdf_naoconciliados->bdfcheque = $bdfcheque;
                        $smb_bdf_naoconciliados->bdfboleto = $bdfboleto;
                        $smb_bdf_naoconciliados->divergencia = $divergencia;
                    }
                }
                $smb_bdf_naoconciliados->save();
//                    $row ++;
            }
        }
        DB::table('smb_bdf_naoconciliados')
            ->where('data', '<', $dtmenos120dias)
            ->delete();
    }
}
