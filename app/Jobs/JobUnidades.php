<?php

namespace App\Jobs;

use App\Models\Correios\Unidade;
use App\Models\Correios\UnidadeEndereco;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;


class JobUnidades implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected   $unidades;

    public function __construct()
    {
        $this->unidades = $unidades;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $unidades = $this->unidades;
        ini_set('memory_limit', '512M');
        foreach($unidades as $dados) {
            foreach($dados as $registro) {

                $res = DB::table('unidades')
                    ->where('an8', '=',  (int)$registro['no_cad_geral'])
                    ->select(
                        'unidades.*'
                    )
                    ->first();

                $tipodeunidade = DB::table('tiposdeunidade')
                    ->where('codigo', '=',  (int)$registro['tipo_do_orgao'])
                    ->orWhere('tipodescricao', '=',  $registro['descricao_tp_orgao'])
                    ->select(
                        'tiposdeunidade.id'
                    )
                    ->first();
                //***  gravar somente se tiver tipo de unidade prevista para inspeção
                if(! empty( $tipodeunidade )) {
                    $enderecounidades = DB::table('unidade_enderecos')
                        ->where('mcu', '=',  (int)$registro['unidades_de_negocios'])
                        ->select(
                            'unidade_enderecos.id'
                        )
                        ->first();
                    if(! empty(  $enderecounidades)) {
                        $enderecos = UnidadeEndereco::find($enderecounidades->id);
                        $enderecos->codIbge =	$registro['codigo_ibge_do_municipio'];
                        $enderecos->endereco = $registro['endereco'];
                        $enderecos->complemento =	$registro['complemento_endereco'];
                        $enderecos->bairro =	$registro['bairro'];
                        $enderecos->cidade =	$registro['cidade'];
                        $enderecos->uf =	$registro['uf'];
                        $enderecos->cep =	$registro['cep'];
                    }
                    else  {
                        $enderecos = new UnidadeEndereco();
                        $enderecos->mcu =	$registro['unidades_de_negocios'];
                        $enderecos->codIbge =	$registro['codigo_ibge_do_municipio'];
                        $enderecos->endereco = $registro['endereco'];
                        $enderecos->complemento =	$registro['complemento_endereco'];
                        $enderecos->bairro =	$registro['bairro'];
                        $enderecos->cidade =	$registro['cidade'];
                        $enderecos->uf =	$registro['uf'];
                        $enderecos->cep =	$registro['cep'];
                    }
                    if (! $res) {
                        $unidade = new Unidade;
                        $unidade->tipoUnidade_id      = $tipodeunidade->id;
                        $unidade->mcu =$registro['unidades_de_negocios'];
                        $unidade->se =$registro['dr'];
                        $unidade->seDescricao =$registro['descricao_dr'];
                        $unidade->an8 =$registro['no_cad_geral'];
                        $unidade->sto =$registro['sto'];
                        $unidade->status_unidade =$registro['status_do_orgao'];
                        $unidade->status_unidadeDesc =$registro['descricao_status'];
                        $unidade->descricao =$registro['nome_fantasia'];
                        $unidade->tipoOrgaoCod =$registro['tipo_do_orgao'];
                        $unidade->tipoOrgaoDesc =$registro['descricao_tp_orgao'];
                        $unidade->cnpj =$registro['cnpj'];
                        $unidade->categoria =$registro['categoria'];
                        $unidade->mecanizacao =$registro['descricao_do_tp_mecanizacao'];
                        $unidade->faixaCepIni =$registro['faixa_ini_cep'];
                        $unidade->faixaCepFim =$registro['faixa_fim_cep'];
                        $unidade->tem_distribuicao =$registro['distribuicao'];
                        $unidade->quantidade_guiches =$registro['quantidade_guiches'];
                        $unidade->guiches_ocupados =$registro['guiches_ocupados'];
                        $unidade->ddd =$registro['ddd'];
                        $unidade->telefone =$registro['telefone_principal'];
                        $unidade->mcu_subordinacaoAdm =$registro['subordinacao_administrativa'];
                        $unidade->desc_subordinacaoAdm =$registro['descricao_subordinacao_adm'];
                        $unidade->nomeResponsavelUnidade =$registro['nome_responsavel'];
                        $unidade->documentRespUnidade =$registro['matricula_responsavel'];
                        $unidade->email=$registro['email_da_unidade'];
                        $unidade->tipoEstrutura = $registro['tipo_de_estrutura'];
                        $unidade->subordinacao_tecnica =$registro['subordinacao_tecnica'];

                        if(!empty($registro['inicio_expediente'])) {
                            $unidade->inicio_expediente =$registro['inicio_expediente'];
                            $unidade->final_expediente =$registro['final_expediente'];
                            $unidade->inicio_intervalo_refeicao =$registro['inicio_intervalo_refeicao'];
                            $unidade->final_intervalo_refeicao =$registro['final_intervalo_refeicao'];
                            $unidade->trabalha_sabado =$registro['trabalha_sabado'];
                            $unidade->inicio_expediente_sabado =$registro['inicio_expediente_sabado'];
                            $unidade->final_expediente_sabado =$registro['final_expediente_sabado'];
                            $unidade->trabalha_domingo =$registro['trabalha_domingo'];
                            $unidade->inicio_expediente_domingo =$registro['inicio_expediente_domingo'];
                            $unidade->final_expediente_domingo =$registro['final_expediente_domingo'];
                            $unidade->tem_plantao =$registro['tem_plantao'];
                            $unidade->inicio_plantao_sabado =$registro['inicio_plantao_sabado'];
                            $unidade->final_plantao_sabado =$registro['final_plantao_sabado'];
                            $unidade->inicio_plantao_domingo =$registro['inicio_plantao_domingo'];
                            $unidade->final_plantao_domingo =$registro['final_plantao_domingo'];
                            $unidade->inicio_distribuicao =$registro['inicio_distribuicao'];
                            $unidade->final_distribuicao =$registro['final_distribuicao'];
                            $unidade->horario_lim_post_na_semana =$registro['horario_lim_post_na_semana'];
                            $unidade->horario_lim_post_final_semana =$registro['horario_lim_post_final_semana'];
                        }
                        $unidade->save();
                        $enderecos->save();
                    }
                    else {
                        $unidade = Unidade::find($res->id);
                        $unidade->tipoUnidade_id      = $tipodeunidade->id;
                        $unidade->mcu =$registro['unidades_de_negocios'];
                        $unidade->se =$registro['dr'];
                        $unidade->seDescricao =$registro['descricao_dr'];
                        $unidade->sto =$registro['sto'];
                        $unidade->status_unidade =$registro['status_do_orgao'];
                        $unidade->status_unidadeDesc =$registro['descricao_status'];
                        $unidade->descricao =$registro['nome_fantasia'];
                        $unidade->tipoOrgaoCod =$registro['tipo_do_orgao'];
                        $unidade->tipoOrgaoDesc =$registro['descricao_tp_orgao'];
                        $unidade->cnpj =$registro['cnpj'];
                        $unidade->categoria =$registro['categoria'];
                        $unidade->mecanizacao =$registro['descricao_do_tp_mecanizacao'];
                        $unidade->faixaCepIni =$registro['faixa_ini_cep'];
                        $unidade->faixaCepFim =$registro['faixa_fim_cep'];
                        $unidade->tem_distribuicao =$registro['distribuicao'];
                        $unidade->quantidade_guiches =$registro['quantidade_guiches'];
                        $unidade->guiches_ocupados =$registro['guiches_ocupados'];
                        $unidade->ddd =$registro['ddd'];
                        $unidade->telefone =$registro['telefone_principal'];
                        $unidade->mcu_subordinacaoAdm =$registro['subordinacao_administrativa'];
                        $unidade->desc_subordinacaoAdm =$registro['descricao_subordinacao_adm'];
                        $unidade->nomeResponsavelUnidade =$registro['nome_responsavel'];
                        $unidade->documentRespUnidade =$registro['matricula_responsavel'];
                        $unidade->email=$registro['email_da_unidade'];
                        $unidade->tipoEstrutura = $registro['tipo_de_estrutura'];
                        $unidade->subordinacao_tecnica =$registro['subordinacao_tecnica'];
//   10/02/2021 - Abilio - Não atualizar dados de horário
//                            if(!empty($registro['inicio_expediente']))
//                            {
//                                $unidade->inicio_atendimento =$registro['inicio_atendimento'];
//                                $unidade->final_atendimento =$registro['final_atendimento'];
//                                $unidade->inicio_expediente =$registro['inicio_expediente'];
//                                $unidade->final_expediente =$registro['final_expediente'];
//                                $unidade->inicio_intervalo_refeicao =$registro['inicio_intervalo_refeicao'];
//                                $unidade->final_intervalo_refeicao =$registro['final_intervalo_refeicao'];
//                                $unidade->trabalha_sabado =$registro['trabalha_sabado'];
//                                $unidade->inicio_expediente_sabado =$registro['inicio_expediente_sabado'];
//                                $unidade->final_expediente_sabado =$registro['final_expediente_sabado'];
//                                $unidade->trabalha_domingo =$registro['trabalha_domingo'];
//                                $unidade->inicio_expediente_domingo =$registro['inicio_expediente_domingo'];
//                                $unidade->final_expediente_domingo =$registro['final_expediente_domingo'];
//                                $unidade->tem_plantao =$registro['tem_plantao'];
//                                $unidade->inicio_plantao_sabado =$registro['inicio_plantao_sabado'];
//                                $unidade->final_plantao_sabado =$registro['final_plantao_sabado'];
//                                $unidade->inicio_plantao_domingo =$registro['inicio_plantao_domingo'];
//                                $unidade->final_plantao_domingo =$registro['final_plantao_domingo'];
//                                $unidade->inicio_distribuicao =$registro['inicio_distribuicao'];
//                                $unidade->final_distribuicao =$registro['final_distribuicao'];
//                                $unidade->horario_lim_post_na_semana =$registro['horario_lim_post_na_semana'];
//                                $unidade->horario_lim_post_final_semana =$registro['horario_lim_post_final_semana'];
//                            }
                        $unidade->update();
                        $enderecos->update();
                    }
                }
            }
        }
        ini_set('memory_limit', '64M');
    }
}
