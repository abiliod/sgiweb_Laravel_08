<?php

namespace App\Jobs;

use App\Models\Correios\ModelsAuxiliares\BDF_FAT_02;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class Job_BDF_FAT_02 implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
protected $bdf_fat_02,$dtmenos210dias, $dt_job;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($bdf_fat_02,$dtmenos210dias, $dt_job)
    {
        $this->bdf_fat_02 = $bdf_fat_02 ;
        $this->dtmenos210dias = $dtmenos210dias ;
        $this->dt_job = $dt_job;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $bdf_fat_02 = $this->bdf_fat_02;
        $dtmenos210dias = $this->dtmenos210dias;

        foreach($bdf_fat_02 as $dados) {
            foreach($dados as $registro) {
                //trata data   dt_postagem
                if(! Empty($registro['dt_postagem'])) {
                    try {
                        $dt_number = intVal($registro['dt_postagem']);
                        if (is_numeric($dt_number)) {
                            $dt_postagem = new Carbon('1899-12-30');
                            $dt_postagem = $dt_postagem->addDays($dt_number);
                        }
                    }
                    catch (\Exception $e) {
                        $dt_postagem       =  null;
                    }
                }
                else {
                    $dt_postagem    = null;
                }

                //trata data   dt_mov
                if(! Empty($registro['dt_mov'])) {
                    try {
                        $dt_number = intVal($registro['dt_mov']);
                        if (is_numeric($dt_number)) {
                            $dt_mov = new Carbon('1899-12-30');
                            $dt_mov = $dt_mov->addDays($dt_number);
                        }
                    }
                    catch (\Exception $e) {
                        $dt_mov       =  null;
                    }
                }
                else {
                    $dt_mov    = null;
                }

                while( $dt_mov !== null ) {

                    BDF_FAT_02::updateOrCreate([
                        'dt_mov' => $dt_mov
                        ,'cd_orgao' => $registro['cd_orgao']
                        ,'servico' => $registro['servico']
                        ,'nome_servico' => $registro['nome_servico']
                        ,'vlr_final' => $registro['vlr_final']
                    ],[
                        'dt_mov' => $dt_mov
                        ,'dr' => $registro['dr']
                        ,'atendimento' => $registro['atendimento']
                        ,'ag_postagem' => $registro['ag_postagem']
                        ,'cd_orgao' => $registro['cd_orgao']
                        ,'servico' => $registro['servico']
                        ,'nome_servico' => $registro['nome_servico']
                        ,'vlr_final' => $registro['vlr_final']
                        , 'dt_postagem' => $dt_postagem
                        ,'orgao' => $registro['orgao']
                        ,'etiqueta' => $registro['etiqueta']
                        ,'vlr_medida' => $registro['vlr_medida']
                        ,'cd_grupo_pais_destino' => $registro['cd_grupo_pais_destino']
                        ,'cep_destino' => $registro['cep_destino']
                        ,'vlr_cobrado_destinatario' => $registro['vlr_cobrado_destinatario']
                        ,'vlr_declarado' => $registro['vlr_declarado']
                        ,'cod_adm' => $registro['cod_adm']
                        ,'produto' => $registro['produto']
                        ,'qtde_prestada' => $registro['qtde_prestada']
                        ,'vlr_servico' => $registro['vlr_servico']
                        ,'vlr_desconto' => $registro['vlr_desconto']
                        ,'acrescimo' => $registro['acrescimo']
                        ,'cartao' => $registro['cartao']
                        ,'documento' => $registro['documento']
                        ,'servico_adicional' => $registro['servio_adicional']
                        ,'contrato' => $registro['contrato']
                    ]);
                    $dt_mov = null;
                }
            }
        }
        DB::table('bdf_fat_02')->where('dt_postagem', '<=', $dtmenos210dias)->delete();
    }
}
