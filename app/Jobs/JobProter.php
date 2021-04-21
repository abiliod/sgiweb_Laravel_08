<?php

namespace App\Jobs;

use App\Models\Correios\ModelsAuxiliares\Proter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class JobProter implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $proters, $dt_job;
//    public $dateWanted;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dt_job, $proters)
    {
        $this->proters = $proters;
        $this->dt_job = $dt_job;

//        $this-> dateWanted = $dateWanted;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()  {
        $proters = $this->proters;

        ini_set('memory_limit', '512M');

//  Inicio importar PROTERS

        foreach($proters as $registros)  {
            foreach($registros as $dado) {

                if(! $dado['data_da_pendencia']=='') {
                    $data_da_pendencia = $this->transformDate($dado['data_da_pendencia']);
                }
                else {
                    $data_da_pendencia = null;
                }

                if(! $dado['data_da_entrega']=='') {
                    $data_da_entrega = $this->transformDate($dado['data_da_entrega']);
                }
                else {
                    $data_da_entrega = null;
                }

                if(! $dado['data_da_postagem']=='') {
                    $data_da_postagem = $this->transformDate($dado['data_da_postagem']);
                }
                else {
                    $data_da_postagem = null;
                }

                if(! $dado['data_de_leitura']=='') {
                    $data_de_leitura = $this->transformDate($dado['data_de_leitura']);
                }
                else {
                    $data_de_leitura = null;
                }

                if($dado['valor_tarifado_financeiro'] == '---------') {
                    $valor_tarifado_financeiro = 0.00;
                }
                else  {
                    try {
                        $valor_tarifado_financeiro  = str_replace(",", ".", $dado['valor_tarifado_financeiro']);
                    }
                    catch (\Exception $e) {
                        $valor_tarifado_financeiro = 0.00;
                    }
                }

                if($dado['valor_tarifado_mectri'] == '---------') {
                    $valor_tarifado_mectri = 0.00;
                }
                else {
                    try {
                        $valor_tarifado_mectri = str_replace(",", ".", $dado['valor_tarifado_mectri']);
                    }
                    catch (\Exception $e) {
                        $valor_tarifado_mectri = 0.00;
                    }
                }

                if($dado['diferenca_a_recolher'] == '---------') {
                    $diferenca_a_recolher =0.00;
                }
                else {
                    try {
                        $diferenca_a_recolher = str_replace(",", ".", $dado['diferenca_a_recolher']);
                    }
                    catch (\Exception $e) {
                        $diferenca_a_recolher = 0.00;
                    }
                }

                Proter :: updateOrCreate([
                    'no_do_objeto' => $dado['no_do_objeto']
                ],
                    [
                    'data_da_pendencia' => $data_da_pendencia
                    ,'data_da_entrega' => $data_da_entrega
                    ,'data_da_postagem' => $data_da_postagem
                    ,'data_de_leitura' => $data_de_leitura
                    ,'tipo_de_pendencia' => $dado['tipo_de_pendencia']
                    ,'divergencia_peso' => $dado['divergencia_peso']
                    ,'divergencia_cep' => $dado['divergencia_cep']
                    ,'origem_pendencia' => $dado['origem_pendencia']
                    ,'se' => $dado['se']
                    ,'tipo_de_unidade' => $dado['tipo_de_unidade']
                    ,'mcu' => $dado['stomcu']
                    ,'nome_da_unidade' => $dado['nome_da_unidade']
                    ,'tipo_de_atendimento' => $dado['tipo_de_atendimento']
                    ,'matricula_atendente' => $dado['matricula_atendente']
                    ,'no_do_objeto' => $dado['no_do_objeto']
                    ,'status_da_pendencia' => $dado['status_da_pendencia']
                    ,'status_da_unidade' => $dado['status_da_unidade']
                    ,'codigo_do_servico' => $dado['codigo_do_servico']
                    ,'cep_contabilizado_sara' => $dado['cep_contabilizado_sara']
                    ,'cep_entrega_sro' => $dado['cep_entrega_sro']
                    ,'peso_tarifado_financeiro' => $dado['peso_tarifado_financeiro']
                    ,'comprimento_financeiro' => $dado['comprimento_financeiro']
                    ,'largura_financeiro' => $dado['largura_financeiro']
                    ,'altura_financeiro' => $dado['altura_financeiro']
                    ,'peso_cubico_financeiro' => $dado['peso_cubico_financeiro']
                    ,'peso_real_mectri' => $dado['peso_real_mectri']
                    ,'comprimento_mectri' => $dado['comprimento_mectri']
                    ,'largura_mectri' => $dado['largura_mectri']
                    ,'altura_mectri' => $dado['altura_mectri']
                    ,'peso_cubico_mectri' => $dado['peso_cubico_mectri']
                    ,'peso_tarifado_mectri' => $dado['peso_tarifado_mectri']
                    ,'cnpj_do_cliente' => $dado['cnpj_do_cliente']
                    ,'contrato' => $dado['contrato']
                    ,'cartao_postagem' => $dado['cartao_postagem']
                    ,'nome_do_cliente' => $dado['nome_do_cliente']
                    ,'qtd_duplicidades' => $dado['qtd_duplicidades']
                    ,'valor_tarifado_financeiro' => $valor_tarifado_financeiro
                    ,'valor_tarifado_mectri' => $valor_tarifado_mectri
                    ,'diferenca_a_recolher' => $diferenca_a_recolher
                ]);
            }
        }
//          Final importar PROTERS
        DB::table('proters')
            ->where('status_da_pendencia', '<>', 'Pendente')
            ->delete();
        //    Higieniza tabela PROTER
//   FIM importar PROTERS

        ini_set('memory_limit', '128M');
    }

}
