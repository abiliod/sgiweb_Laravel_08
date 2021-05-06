<?php

namespace App\Jobs;

use App\Models\Correios\Inspecao;
use App\Models\Correios\Itensdeinspecao;
use App\Models\Correios\ModelsAuxiliares\SL02_bdf;
use App\Models\Correios\ModelsDto\AcessoFinalSemana;
use App\Models\Correios\ModelsDto\CompartilhaSenha;
use App\Models\Correios\ModelsDto\LancamentosSRO;
use App\Models\Correios\ModelsDto\PgtoAdicionaisTemp;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class JobAtualizaInspecao implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $id, $dt_data_programacao;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id, $dt_data_programacao)
    {
        $this->id = $id;
        $this->dt_data_programacao = $dt_data_programacao;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $id = $this->id;
        $dt_data_programacao  = $this->dt_data_programacao;
        $inspecao = Inspecao::find($id);
        $data_programacao = $inspecao->data_programacao;
        $periodo = CarbonPeriod::create($updated_atinspecao, $dt_data_programacao);

        $dtnow = new Carbon();
        $dtmenos30dias = new Carbon();
        $dtmenos60dias = new Carbon();
        $dtmenos90dias = new Carbon();
        $dtmenos3meses = new Carbon();
        $dtmenos110dias = new Carbon();
        $dtmenos120dias = new Carbon();
        $dtmenos150dias = new Carbon();
        $dtmenos4meses = new Carbon();
        $dtmenos6meses = new Carbon();
        $dtmenos12meses = new Carbon();
        $dtmenos30dias->subDays(30);
        $dtmenos60dias->subDays(60);
        $dtmenos90dias->subDays(90);
        $dtmenos3meses->subMonth(3);
        $dtmenos110dias->subDays(110);
        $dtmenos120dias->subDays(120);
        $dtmenos150dias->subDays(150);
        $dtmenos4meses->subMonth(4);
        $dtmenos6meses->subMonth(6);
        $dtmenos12meses->subMonth(12);

        $registros = DB::table('itensdeinspecoes')
            ->join('inspecoes', 'itensdeinspecoes.inspecao_id', '=', 'inspecoes.id')
            ->join('gruposdeverificacao', 'itensdeinspecoes.grupoVerificacao_id', '=', 'gruposdeverificacao.id')
            ->join('testesdeverificacao', 'itensdeinspecoes.testeVerificacao_id', '=', 'testesdeverificacao.id')
            ->select('inspecoes.*'
                , 'itensdeinspecoes.*'
                ,'gruposdeverificacao.numeroGrupoVerificacao'
                ,'gruposdeverificacao.nomegrupo'
                ,'testesdeverificacao.numeroDoTeste'
                ,'testesdeverificacao.teste'
            )
            ->where([['inspecao_id', '=', $id]])
            ->where([['situacao', '=', 'Em Inspeção' ]])
            ->orderBy('itensdeinspecoes.testeVerificacao_id' , 'asc')
            ->get();

        ini_set('memory_limit', '512M');

        if ($periodo->count() >30){
            $registros = DB::table('itensdeinspecoes')
                ->join('testesdeverificacao', 'itensdeinspecoes.testeVerificacao_id', '=', 'testesdeverificacao.id')
                ->join('gruposdeverificacao', 'itensdeinspecoes.grupoVerificacao_id', '=', 'gruposdeverificacao.id')
                ->join('unidades', 'itensdeinspecoes.unidade_id', '=', 'unidades.id')
                ->where([['itensdeinspecoes.inspecao_id', '=', $id]])
                ->get();
        }
        else{
            $registros = DB::table('itensdeinspecoes')
                ->join('testesdeverificacao', 'itensdeinspecoes.testeVerificacao_id', '=', 'testesdeverificacao.id')
                ->join('gruposdeverificacao', 'itensdeinspecoes.grupoVerificacao_id', '=', 'gruposdeverificacao.id')
                ->join('unidades', 'itensdeinspecoes.unidade_id', '=', 'unidades.id')
                ->where([['situacao', '=', 'Em Inspeção']])
                ->where([['itensdeinspecoes.inspecao_id', '=', $id]])
                ->get();
        }
        $contasituacao = $registros->count('situacao');

        foreach ($registros as $registro){

//      INICIO 230-1
            if((($registro->numeroGrupoVerificacao == 56)&&($registro->numeroDoTeste == 1))
                || (($registro->numeroGrupoVerificacao == 230)&&($registro->numeroDoTeste == 1)))  {

                $reincidencia = DB::table('snci')
                    ->select('no_inspecao',   'no_grupo',  'no_item','dt_fim_inspecao','dt_inic_inspecao')
                    ->where([['descricao_item',  'like', '%confere com o valor físico encontrado no Caixa Retaguarda%']])
                    ->where([['sto', '=', $registro->sto ]])
                    ->orderBy('no_inspecao' ,'desc')
                    ->first();

                if ($reincidencia === null){
                    $reinc = 'Não';
                    $codVerificacaoAnterior = null;
                    $numeroGrupoReincidente = null;
                    $numeroItemReincidente = null;
                }
                else{
                    $reinc = 'Sim';
                    $codVerificacaoAnterior = $reincidencia->no_inspecao;
                    $numeroGrupoReincidente = $reincidencia->no_grupo;
                    $numeroItemReincidente = $reincidencia->no_item;
                }
                DB::table('itensdeinspecoes')
                    ->where([['inspecao_id', '=', $registro->inspecao_id]])
                    ->where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                    ->update([
                        'codVerificacaoAnterior' => $codVerificacaoAnterior,
                        'numeroGrupoReincidente' => $numeroGrupoReincidente,
                        'numeroItemReincidente' => $numeroItemReincidente,
                        'reincidencia' => $reinc
                    ]);
            }
//      FIM 230-1

//      INICIO 230-2
            if((($registro->numeroGrupoVerificacao == 56)&&($registro->numeroDoTeste == 2))
                || (($registro->numeroGrupoVerificacao == 230)&&($registro->numeroDoTeste == 2)))  {

                $reincidencia = DB::table('snci')
                    ->select('no_inspecao',   'no_grupo',  'no_item','dt_fim_inspecao','dt_inic_inspecao')
                    ->where([['descricao_item',  'like', '%produtos em poder dos Caixas Retaguarda e de Atendimento%']])
                    ->where([['sto', '=', $registro->sto ]])
                    ->orderBy('no_inspecao' ,'desc')
                    ->first();

                if ($reincidencia === null){
                    $reinc = 'Não';
                    $codVerificacaoAnterior = null;
                    $numeroGrupoReincidente = null;
                    $numeroItemReincidente = null;
                }
                else{
                    $reinc = 'Sim';
                    $codVerificacaoAnterior = $reincidencia->no_inspecao;
                    $numeroGrupoReincidente = $reincidencia->no_grupo;
                    $numeroItemReincidente = $reincidencia->no_item;
                }
                DB::table('itensdeinspecoes')
                    ->where([['inspecao_id', '=', $registro->inspecao_id]])
                    ->where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                    ->update([
                        'codVerificacaoAnterior' => $codVerificacaoAnterior,
                        'numeroGrupoReincidente' => $numeroGrupoReincidente,
                        'numeroItemReincidente' => $numeroItemReincidente,
                        'reincidencia' => $reinc
                    ]);

            }
//      FIM 230-2

//      INICIO 230-3
            if((($registro->numeroGrupoVerificacao == 56)&&($registro->numeroDoTeste == 3))
                || (($registro->numeroGrupoVerificacao == 230)&&($registro->numeroDoTeste == 3))) {

                $reincidencia = DB::table('snci')
                    ->select('no_inspecao',   'no_grupo',  'no_item','dt_fim_inspecao','dt_inic_inspecao')
                    ->where([['descricao_item',  'like', '%etiquetas e documentos geradores de receita em poder dos Caixas Retaguarda%']])
                    ->where([['sto', '=', $registro->sto ]])
                    ->orderBy('no_inspecao' ,'desc')
                    ->first();

                if ($reincidencia === null){
                    $reinc = 'Não';
                    $codVerificacaoAnterior = null;
                    $numeroGrupoReincidente = null;
                    $numeroItemReincidente = null;
                }
                else{
                    $reinc = 'Sim';
                    $codVerificacaoAnterior = $reincidencia->no_inspecao;
                    $numeroGrupoReincidente = $reincidencia->no_grupo;
                    $numeroItemReincidente = $reincidencia->no_item;
                }
                DB::table('itensdeinspecoes')
                    ->where([['inspecao_id', '=', $registro->inspecao_id]])
                    ->where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                    ->update([
                        'codVerificacaoAnterior' => $codVerificacaoAnterior,
                        'numeroGrupoReincidente' => $numeroGrupoReincidente,
                        'numeroItemReincidente' => $numeroItemReincidente,
                        'reincidencia' => $reinc
                    ]);

            }
//      fim 230-3

//     INICIO 230-4 Início do teste WebCont
            if((($registro->numeroGrupoVerificacao == 56)&&($registro->numeroDoTeste == 4))
                || (($registro->numeroGrupoVerificacao == 230)&&($registro->numeroDoTeste == 4))
                || (($registro->numeroGrupoVerificacao == 270)&&($registro->numeroDoTeste == 1))) {

                $reinc = 'Não';
                $codVerificacaoAnterior = null;
                $numeroGrupoReincidente = null;
                $numeroItemReincidente = null;
                $itensdeinspecao = null;

                $preVerificar = DB::table('testesdeverificacao')
                    ->select('testesdeverificacao.*' )
                    ->where([['teste',  'like', '%A unidade regulariza os débitos de empregados lançados nas contas 914%']])
                    ->get();

                foreach($preVerificar as $pre){
                    DB::table('testesdeverificacao')
                        ->where([['id',  '=', $pre->id]])
                        ->update([
                            'preVerificar' => 'Não'
                        ]);
                }

                $reincidencia = DB::table('snci')
                    ->select('no_inspecao',   'no_grupo',  'no_item','dt_fim_inspecao','dt_inic_inspecao')
                    ->where([['descricao_item',  'like', '%3131)?%']])
                    ->where([['sto', '=', $registro->sto ]])
                    ->orderBy('no_inspecao' ,'desc')
                    ->first();
                try{
                    if( $reincidencia->no_inspecao > 1) {
                        $reincidente = 1;
                        $reinc = 'Sim';
                        $codVerificacaoAnterior = $reincidencia->no_inspecao;
                        $numeroGrupoReincidente = $reincidencia->no_grupo;
                        $numeroItemReincidente = $reincidencia->no_item;
                        $reincidencia_dt_fim_inspecao = new Carbon($reincidencia->dt_fim_inspecao);
                        $reincidencia_dt_inic_inspecao = new Carbon($reincidencia->dt_inic_inspecao);
                        $reincidencia_dt_fim_inspecao->subMonth(3);
                        $reincidencia_dt_inic_inspecao->subMonth(3);
                    }
                }
                catch (\Exception $e) {
                    $reincidente = 0;
                }
                $mescompetencia = DB::table('debitoempregados')
                    ->select('competencia')
                    ->where([['debitoempregados.competencia', '>=', 1 ]])
                    ->orderBy('competencia' ,'desc')
                    ->first();
                $competencia = substr($mescompetencia->competencia, 4, 2).'/'.substr($mescompetencia->competencia, 0, 4);

                if($reincidente === 1) {
                    $debitoempregados = DB::table('debitoempregados')
                        ->select('data', 'documento', 'historico', 'matricula', 'valor' )
                        ->where([['debitoempregados.data', '<=', $dtmenos90dias ]])
                        ->where([['debitoempregados.data', '>=', $reincidencia_dt_fim_inspecao ]])
                        ->Where([['debitoempregados.sto', '=', $registro->sto ]])
                        ->get();
                }
                else {
                    $debitoempregados = DB::table('debitoempregados')
                        ->select('data', 'documento', 'historico', 'matricula', 'valor' )
                        ->where([['debitoempregados.data', '<=', $dtmenos90dias ]])
                        ->Where([['debitoempregados.sto', '=', $registro->sto ]])
                        ->get();
                }

                if(! $debitoempregados->isEmpty()) {
                    $count = $debitoempregados->count('matricula');
                    $total = $debitoempregados->sum('valor'); // soma a coluna valor da coleção de dados
                    $quebra = DB::table('relevancias')
                        ->select('valor_final' )
                        ->where('fator_multiplicador', '=', 1 )
                        ->first();

                    $quebracaixa = $quebra->valor_final * 0.1;

                    $fm = DB::table('relevancias')
                        ->select('fator_multiplicador', 'valor_final', 'valor_inicio' )
                        ->where('valor_inicio', '<=', $total )
                        ->orderBy('valor_final' ,'desc')
                        ->first();

                    if(( $count >= 1 ) && ( $total > $quebracaixa ) ) {
                        $avaliacao = 'Não Conforme';
                        $pontuado = $registro->totalPontos * $fm->fator_multiplicador;
                        $oportunidadeAprimoramento = 'Em Análise aos dados do Sistema WebCont – Composição Analítica da conta 11202.994000, posição de '
                            . $competencia .', constatou-se a existência de '. $count . ' débitos de empregado sem regularização há mais de 90 dias, conforme relacionado a seguir:';
                        $evidencia = "\n".'Data'."\t".'Documento'."\t".'Histórico'."\t".'Matricula'."\t".'Valor';

                        foreach($debitoempregados as $debitoempregado){

                            $evidencia =  $evidencia ."\n". date( 'd/m/Y' , strtotime($debitoempregado->data))
                                ."\t". $debitoempregado->documento
                                ."\t". $debitoempregado->historico
                                ."\t". $debitoempregado->matricula
                                ."\t". ' R$ '. number_format($debitoempregado->valor, 2, ',', '.');
                        }

                        $evidencia =  $evidencia ."\n".'Total '."\t".'R$ '. number_format($total, 2, ',', '.');

                        $dto = DB::table('itensdeinspecoes')
                            ->Where([['inspecao_id', '=', $registro->inspecao_id]])
                            ->Where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                            ->select( 'itensdeinspecoes.*'  )
                            ->first();

                        $itensdeinspecao = Itensdeinspecao::find($dto->id);
                        $itensdeinspecao->avaliacao  = $avaliacao;
                        $itensdeinspecao->oportunidadeAprimoramento = $oportunidadeAprimoramento;
                        $itensdeinspecao->evidencia  = $evidencia;
                        $itensdeinspecao->valorFalta = $total;
                        //                        $itensdeinspecao->situacao   = 'Inspecionado'; // monitorada
                        $itensdeinspecao->situacao   = 'Em Inspeção'; // presencial/remota
                        $itensdeinspecao->pontuado   = $pontuado;
                        $itensdeinspecao->itemQuantificado = 'Sim';
                        $itensdeinspecao->orientacao = $registro->orientacao;
                        $itensdeinspecao->eventosSistema = 'Item avaliado Remotamente por Websgi em '.date( 'd/m/Y' , strtotime($dtnow)).'.';
                        $itensdeinspecao->reincidencia = $reinc;
                        $itensdeinspecao->codVerificacaoAnterior = $codVerificacaoAnterior;
                        $itensdeinspecao->numeroGrupoReincidente = $numeroGrupoReincidente;
                        $itensdeinspecao->numeroItemReincidente = $numeroItemReincidente;

                        dd( ' DEBUG -linha 348 INSPEÇÃO - >  '
                            . $registro->inspecao_id
                            .'  ITEM DA INSPEÇÃO  testeVerificacao_id - >  '
                            .$registro->testeVerificacao_id.'  reincidencia - >  '
                            .$reinc, '  DTO -> ',$dto , '  DTO -> $itensdeinspecao ->',$itensdeinspecao  ,$registro, );
                        $itensdeinspecao->update();
                    }
                }
                else {

                    //se não houve registro para a unidade o resultado é conforme
                    $avaliacao = 'Conforme';
                    $oportunidadeAprimoramento = 'Em Análise aos dados do Sistema WebCont – Composição Analítica da conta 11202.994000, verificada a posição do mês '. $competencia .' constatou-se que não havia histórico de pendências de débito de Empregados maior que 90 dias.';
                    $dto = DB::table('itensdeinspecoes')
                        ->Where([['inspecao_id', '=', $registro->inspecao_id]])
                        ->Where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                        ->select( 'itensdeinspecoes.*'  )
                        ->first();

                    $itensdeinspecao = Itensdeinspecao::find($dto->id);
                    $itensdeinspecao->avaliacao  = $avaliacao;
                    $itensdeinspecao->oportunidadeAprimoramento = $oportunidadeAprimoramento;
                    $itensdeinspecao->evidencia  = null;
                    $itensdeinspecao->valorFalta = 0.00;
                    //                        $itensdeinspecao->situacao   = 'Inspecionado'; // monitorada
                    $itensdeinspecao->situacao   = 'Em Inspeção'; // presencial/remota
                    $itensdeinspecao->pontuado   = 0.00;
                    $itensdeinspecao->itemQuantificado = 'Não';
                    $itensdeinspecao->orientacao= null;
                    $itensdeinspecao->eventosSistema = 'Item avaliado remotamente por Websgi em '.date( 'd/m/Y' , strtotime($dtnow)).'.';
                    $itensdeinspecao->update();
                }
            }
//     fim 230-4 Final do teste WebCont

//      Início 230-5 do teste PROTER
            if((($registro->numeroGrupoVerificacao == 202)&&($registro->numeroDoTeste == 1))
                || (($registro->numeroGrupoVerificacao == 332)&&($registro->numeroDoTeste ==1))
                || (($registro->numeroGrupoVerificacao == 213)&&($registro->numeroDoTeste ==1))
                || (($registro->numeroGrupoVerificacao == 230)&&($registro->numeroDoTeste == 5))
                || (($registro->numeroGrupoVerificacao == 270)&&($registro->numeroDoTeste == 2)))
            {

                $countproters_peso =0;
                $countproters_cep =0;

                $preVerificar = DB::table('testesdeverificacao')
                    ->select('testesdeverificacao.*' )
                    ->where([['teste',  'like', '%PROTER%']])
                    ->get();

                foreach($preVerificar as $pre){
                    DB::table('testesdeverificacao')
                        ->where([['id',  '=', $pre->id]])
                        ->update([
                            'preVerificar' => 'Não'
                        ]);
                }


                $reincidencia = DB::table('snci')
                    ->select('no_inspecao',   'no_grupo',  'no_item','dt_fim_inspecao','dt_inic_inspecao')
                    ->where([['descricao_item',  'like', '%PROTER%']])
                    ->where([['sto', '=', $registro->sto ]])
                    ->orderBy('no_inspecao' ,'desc')
                    ->first();
                try{
                    if( $reincidencia->no_inspecao > 1) {
                        $reincidente = 1;
                        $reinc = 'Sim';
                        $codVerificacaoAnterior = $reincidencia->no_inspecao;
                        $numeroGrupoReincidente = $reincidencia->no_grupo;
                        $numeroItemReincidente = $reincidencia->no_item;
                        $reincidencia_dt_fim_inspecao = new Carbon($reincidencia->dt_fim_inspecao);
                        $reincidencia_dt_inic_inspecao = new Carbon($reincidencia->dt_inic_inspecao);
                        $reincidencia_dt_fim_inspecao->subMonth(3);
                        $reincidencia_dt_inic_inspecao->subMonth(3);
                    }
                    else{
                        $reincidente = 0;
                        $reinc = 'Não';
                        $codVerificacaoAnterior = null;
                        $numeroGrupoReincidente = null;
                        $numeroItemReincidente = null;
                    }
                }
                catch (\Exception $e) {
                    $reincidente = 0;
                    $reinc = 'Não';
                    $codVerificacaoAnterior = null;
                    $numeroGrupoReincidente = null;
                    $numeroItemReincidente = null;
                }
//                          Inicio tem Reincidencia proter
                if($reincidente == 1) {
                    $proters_con = DB::table('proters')
                        ->select(
                            'tipo_de_pendencia'
                            , 'no_do_objeto'
                            , 'cep_entrega_sro'
                            ,'data_da_pendencia'
                        )
                        ->where([['mcu', '=', $registro->mcu]])
                        ->where([['tipo_de_pendencia', '=', 'CON']])
                        ->where([['data_da_pendencia', '>=', $reincidencia_dt_fim_inspecao ]])
                        ->where([['data_da_pendencia', '<=', $dtmenos90dias ]])
                        ->get();
                    $proters_peso = DB::table('proters')
                        ->select(
                            'tipo_de_pendencia'
                            , 'no_do_objeto'
                            , 'cep_destino'
                            , 'divergencia_peso'
                            , 'diferenca_a_recolher'
                            , 'data_da_pendencia'
                        )
                        ->where([['mcu', '=', $registro->mcu]])
                        ->where([['tipo_de_pendencia', '=', 'DPC']])
                        ->where([['divergencia_peso', '=', 'S']])
                        ->where([['data_da_pendencia', '>=', $reincidencia_dt_fim_inspecao ]])
                        ->where([['data_da_pendencia', '<=', $dtmenos90dias ]])
                        ->get();


                    $proters_cep = DB::table('proters')
                        ->select(
                            'tipo_de_pendencia'
                            , 'no_do_objeto'
                            , 'cep_destino'
                            , 'divergencia_cep'
                            , 'diferenca_a_recolher'
                            , 'data_da_pendencia'
                        )
                        ->where([['mcu', '=', $registro->mcu]])
                        ->where([['tipo_de_pendencia', '=', 'DPC']])
                        ->where([['divergencia_cep', '=', 'S']])
                        ->where([['data_da_pendencia', '>=', $reincidencia_dt_fim_inspecao ]])
                        ->where([['data_da_pendencia', '<=', $dtmenos90dias ]])
                        ->get();
//                                    dd('var -> ', $proters_cep,$registro->mcu, $reincidencia_dt_fim_inspecao,  $dtmenos90dias );

                    if(! $proters_con->isEmpty()){
                        $countproters_con = $proters_con->count('no_do_objeto');
                    }
                    else{
                        $countproters_con = 0;
                    }

                    if(! $proters_peso->isEmpty())
                    {
                        $countproters_peso = $proters_peso->count('no_do_objeto');
                        $total_proters_peso  = $proters_peso->sum('diferenca_a_recolher');
                    }
                    else
                    {
                        $total_proters_peso  = 0.00;
                    }

                    if(! $proters_cep->isEmpty())
                    {
                        $countproters_cep = $proters_cep->count('no_do_objeto');
                        $total_proters_cep  = $proters_cep->sum('diferenca_a_recolher');
                    }
                    else
                    {
                        $total_proters_cep  = 0.00;
                    }

                    $total = $total_proters_peso + $total_proters_cep;
                    $countproters = $countproters_con + $countproters_peso +$countproters_cep;
//              Inicio se tem pendencia proter com reincidencia
                    $quebra = DB::table('relevancias')
                        ->select('valor_final' )
                        ->where('fator_multiplicador', '=', 1 )
                        ->first();
                    $quebracaixa = $quebra->valor_final * 0.1;
                    if(($countproters_con >= 1) || ($total > 0.00) ){
                        $pontuado = 0;  //  verificar  declaração no inicio da rotina
                        if($total > 0.00) {
                            $fm = DB::table('relevancias')
                                ->select('fator_multiplicador', 'valor_final', 'valor_inicio' )
                                ->where('valor_inicio', '<=', $total )
                                ->orderBy('valor_final' ,'desc')
                                ->first();
                            if($avaliacao == 'Não Conforme') $pontuado = $registro->totalPontos * $fm->fator_multiplicador;
                        }

                        $avaliacao = 'Não Conforme';
                        $oportunidadeAprimoramento = 'Em Análise aos dados do Sistema Proter a partir de Jan/2017. Excetuando os ultimos 90 dias da data dessa inspeção, constatou-se a existência de pendências sem regularização há mais de 90 dias conforme relacionado a seguir:';
                        $evidencia ='';

                        if(! $proters_con->isEmpty()){
                            $countproters_con = $proters_con->count('no_do_objeto');
                            $evidencia = "\n".'Pendencia(s) de Contabilização: '.$countproters_con.' Pendência(s)';
                            $evidencia = $evidencia = "\n".'Data da Pendência'."\t".'Número do Objeto'."\t".'CEP Entrega';

                            foreach($proters_con as $proter_con){
                                $evidencia = $evidencia = "\n".date( 'd/m/Y' , strtotime($proter_con->data_da_pendencia))
                                    ."\t".$proter_con->no_do_objeto
                                    ."\t".$proter_con->cep_entrega_sro;
                            }
                        }

                        if ($total > $quebracaixa ) {

                            if (!$proters_peso->isEmpty()) {

                                $countproters_peso = $proters_peso->count('no_do_objeto');
                                $evidencia1 = "\n" . 'Divergência(s) de Peso: ' . $countproters_peso . ' Pendência(s)';
                                $evidencia1 = $evidencia1 = "\n" . 'Data da Pendência' . "\t" . 'Número do Objeto' . "\t" . 'Diferença na Tarifação (R$)';
                                foreach ($proters_peso as $proter_peso) {
                                    $evidencia1 = $evidencia1 = "\n" . date('d/m/Y', strtotime($proter_peso->data_da_pendencia))
                                        . "\t" . $proter_peso->no_do_objeto
                                        . "\t" . 'R$ ' . number_format($proter_peso->diferenca_a_recolher, 2, ',', '.');
                                }
                            }
                            if (!$proters_cep->isEmpty()) {

                                $countproters_cep = $proters_cep->count('no_do_objeto');
                                $evidencia2 = "\n" . 'Divergência(s) de CEP: ' . $countproters_cep . ' Pendência(s)';
                                $evidencia2 = $evidencia2 = "\n" . 'Data da Pendência' . "\t" . 'Número do Objeto' . "\t" . 'Diferença na Tarifação (R$)';
                                foreach ($proters_cep as $proter_cep) {
                                    $evidencia2 = $evidencia2 = "\n" . date('d/m/Y', strtotime($proter_cep->data_da_pendencia))
                                        . "\t" . $proter_cep->no_do_objeto
                                        . "\t" . 'R$ ' . number_format($proter_cep->diferenca_a_recolher, 2, ',', '.');
                                }
                            }

                            $evidencia3 = "\n" . 'Total ' . "\t" . 'R$ ' . number_format($total, 2, ',', '.');
                        }

                        if ((!empty($evidencia2)) && (!empty($evidencia1))) {
                            $evidencia = $evidencia . $evidencia1 . $evidencia2 . $evidencia3;
                        }
                        elseif (!empty($evidencia1)) {
                            $evidencia = $evidencia . $evidencia1 . $evidencia3;
                        }
                        elseif(!empty($evidencia2)){
                            $evidencia = $evidencia . $evidencia2 . $evidencia3;
                        }

                        $dto = DB::table('itensdeinspecoes')
                            ->Where([['inspecao_id', '=', $registro->inspecao_id]])
                            ->Where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                            ->select( 'itensdeinspecoes.*'  )
                            ->first();

                        $itensdeinspecao = Itensdeinspecao::find($dto->id);
                        $itensdeinspecao->avaliacao  = $avaliacao;
                        $itensdeinspecao->oportunidadeAprimoramento = $oportunidadeAprimoramento;
                        $itensdeinspecao->evidencia  = $evidencia;
                        $itensdeinspecao->valorFalta = $total;
//                        $itensdeinspecao->situacao   = 'Inspecionado';   // monitorada
                        $itensdeinspecao->situacao   = 'Em Inspeção';   // presencial/remota
                        $itensdeinspecao->pontuado   = $pontuado;
                        $itensdeinspecao->itemQuantificado = 'Sim';
                        $itensdeinspecao->orientacao = $registro->orientacao;
                        $itensdeinspecao->eventosSistema = 'Item avaliado Remotamente por Websgi em '.date( 'd/m/Y' , strtotime($dtnow)).'.';
                        $itensdeinspecao->reincidencia = $reinc;
                        $itensdeinspecao->codVerificacaoAnterior = $codVerificacaoAnterior;
                        $itensdeinspecao->numeroGrupoReincidente = $numeroGrupoReincidente;
                        $itensdeinspecao->numeroItemReincidente = $numeroItemReincidente;
                        $itensdeinspecao->update();
                    }
//                                  Fim se tem pendencia proter com reincidencia
//                                  Inicio Não tem pendencia proter com reincidencia
                    else{
                        $avaliacao = 'Conforme';
                        $oportunidadeAprimoramento = 'Em análise aos dados do Sistema Proter, do período de Janeiro/2017 a '. date( 'd/m/Y' , strtotime($dtmenos90dias)).', constatou-se que não havia pendências com mais de 90 dias.';
                        $dto = DB::table('itensdeinspecoes')
                            ->Where([['inspecao_id', '=', $registro->inspecao_id]])
                            ->Where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                            ->select( 'itensdeinspecoes.*'  )
                            ->first();
                        $itensdeinspecao = Itensdeinspecao::find($dto->id);
                        $itensdeinspecao->avaliacao  = $avaliacao;
                        $itensdeinspecao->oportunidadeAprimoramento = $oportunidadeAprimoramento;
                        $itensdeinspecao->evidencia  = null;
                        $itensdeinspecao->valorFalta = 0.00;
//                        $itensdeinspecao->situacao   = 'Inspecionado';   // monitorada
                        $itensdeinspecao->situacao   = 'Em Inspeção';   // presencial/remota
                        $itensdeinspecao->pontuado   = 0.00;
                        $itensdeinspecao->itemQuantificado = 'Não';
                        $itensdeinspecao->orientacao= null;
                        $itensdeinspecao->eventosSistema = 'Item avaliado remotamente por Websgi em '.date( 'd/m/Y' , strtotime($dtnow)).'.';
                        dd( ' DEBUG -linha 633 INSPEÇÃO - >  '
                            . $registro->inspecao_id
                            .'  ITEM DA INSPEÇÃO  testeVerificacao_id - >  '
                            .$registro->testeVerificacao_id.'  reincidencia - >  '
                            .$reinc, '  DTO -> ',$dto , '  DTO -> $itensdeinspecao ->',$itensdeinspecao  ,$registro, );

                        $itensdeinspecao->update();
                    }
//                                  fim Não tem pendencia proter com reincidencia
                }
//                          Fim se tem reincidencia
//                          Inicio se não reincidencia
                else {
                    $proters_con = DB::table('proters')
                        ->select(
                            'tipo_de_pendencia'
                            , 'no_do_objeto'
                            , 'cep_entrega_sro'
                            ,'data_da_pendencia'
                        )
                        ->where([['mcu', '=', $registro->mcu]])
                        ->where([['tipo_de_pendencia', '=', 'CON']])
//                                        ->where([['data_da_pendencia', '>=', $reincidencia_dt_fim_inspecao ]])
                        ->where([['data_da_pendencia', '<=', $dtmenos90dias ]])
                        ->get();

                    $proters_peso = DB::table('proters')
                        ->select(
                            'tipo_de_pendencia'
                            , 'no_do_objeto'
                            , 'cep_destino'
                            , 'divergencia_peso'
                            , 'diferenca_a_recolher'
                            , 'data_da_pendencia'
                        )
                        ->where([['mcu', '=', $registro->mcu]])
                        ->where([['tipo_de_pendencia', '=', 'DPC']])
                        ->where([['divergencia_peso', '=', 'S']])
//                                        ->where([['data_da_pendencia', '>=', $reincidencia_dt_fim_inspecao ]])
                        ->where([['data_da_pendencia', '<=', $dtmenos90dias ]])
                        ->get();

                    $proters_cep = DB::table('proters')
                        ->select(
                            'tipo_de_pendencia'
                            , 'no_do_objeto'
                            , 'cep_destino'
                            , 'divergencia_cep'
                            , 'diferenca_a_recolher'
                            , 'data_da_pendencia'
                        )
                        ->where([['mcu', '=', $registro->mcu]])
                        ->where([['tipo_de_pendencia', '=', 'DPC']])
                        ->where([['divergencia_cep', '=', 'S']])
//                                        ->where([['data_da_pendencia', '>=', $reincidencia_dt_fim_inspecao ]])
                        ->where([['data_da_pendencia', '<=', $dtmenos90dias ]])
                        ->get();

                    if(! $proters_con->isEmpty()){
                        $countproters_con = $proters_con->count('no_do_objeto');
                    }
                    else{
                        $countproters_con = 0;
                    }

                    if(! $proters_peso->isEmpty())
                    {
                        $countproters_peso = $proters_peso->count('no_do_objeto');
                        $total_proters_peso  = $proters_peso->sum('diferenca_a_recolher');
                    }
                    else
                    {
                        $total_proters_peso  = 0.00;
                    }

                    if(! $proters_cep->isEmpty())
                    {
                        $countproters_cep = $proters_cep->count('no_do_objeto');
                        $total_proters_cep  = $proters_cep->sum('diferenca_a_recolher');
                    }
                    else
                    {
                        $total_proters_cep  = 0.00;
                    }

                    $total = $total_proters_peso + $total_proters_cep;
                    $countproters = $countproters_con + $countproters_peso +$countproters_cep;

//                                  Inicio se tem pendencia proter sem reincidencia
                    if($countproters >= 1){

                        if(($countproters_con >= 1) || ($total > 0.00) ) {
                            $pontuado = 0;  //  verificar  declaração no inicio da rotina
                            if ($total > 0.00) {
                                $quebra = DB::table('relevancias')
                                    ->select('valor_final')
                                    ->where('fator_multiplicador', '=', 1)
                                    ->first();
                                $quebracaixa = $quebra->valor_final * 0.1;

                                $fm = DB::table('relevancias')
                                    ->select('fator_multiplicador', 'valor_final', 'valor_inicio')
                                    ->where('valor_inicio', '<=', $total)
                                    ->orderBy('valor_final', 'desc')
                                    ->first();
                                if($avaliacao == 'Não Conforme') $pontuado = $registro->totalPontos * $fm->fator_multiplicador;
                            }

                            $avaliacao = 'Não Conforme';
                            $oportunidadeAprimoramento = 'Em análise aos dados do Sistema Proter do período de  Janeiro/2017 a ' . date('d/m/Y', strtotime($dtmenos90dias)) . ' constatou-se as seguintes pendências com mais de 90 dias:';
                            $evidencia = '';
                            if (!$proters_con->isEmpty()) {

                                $countproters_con = $proters_con->count('no_do_objeto');
                                $evidencia = "\n" . 'Pendencia(s) de Contabilização: ' . $countproters_con . ' Pendência(s)';
                                $evidencia = $evidencia = "\n" . 'Data da Pendência' . "\t" . 'Número do Objeto' . "\t" . 'CEP Entrega';

                                foreach ($proters_con as $proter_con) {
                                    $evidencia = $evidencia = "\n" . date('d/m/Y', strtotime($proter_con->data_da_pendencia))
                                        . "\t" . $proter_con->no_do_objeto
                                        . "\t" . $proter_con->cep_entrega_sro;
                                }
                            }

                            if ($total > $quebracaixa) {

                                if (!$proters_peso->isEmpty()) {

                                    $countproters_peso = $proters_peso->count('no_do_objeto');
                                    $evidencia1 = "\n" . 'Divergência(s) de Peso: ' . $countproters_peso . ' Pendência(s)';
                                    $evidencia1 = $evidencia1 = "\n" . 'Data da Pendência' . "\t" . 'Número do Objeto' . "\t" . 'Diferença na Tarifação (R$)';
                                    foreach ($proters_peso as $proter_peso) {
                                        $evidencia1 = $evidencia1 = "\n" . date('d/m/Y', strtotime($proter_peso->data_da_pendencia))
                                            . "\t" . $proter_peso->no_do_objeto
                                            . "\t" . 'R$ ' . number_format($proter_peso->diferenca_a_recolher, 2, ',', '.');
                                    }
                                }
                                if (!$proters_cep->isEmpty()) {

                                    $countproters_cep = $proters_cep->count('no_do_objeto');
                                    $evidencia2 = "\n" . 'Divergência(s) de CEP: ' . $countproters_cep . ' Pendência(s)';
                                    $evidencia2 = $evidencia2 = "\n" . 'Data da Pendência' . "\t" . 'Número do Objeto' . "\t" . 'Diferença na Tarifação (R$)';
                                    foreach ($proters_cep as $proter_cep) {
                                        $evidencia2 = $evidencia2 = "\n" . date('d/m/Y', strtotime($proter_cep->data_da_pendencia))
                                            . "\t" . $proter_cep->no_do_objeto
                                            . "\t" . 'R$ ' . number_format($proter_cep->diferenca_a_recolher, 2, ',', '.');
                                    }
                                }

                                $evidencia3 = "\n" . 'Total ' . "\t" . 'R$ ' . number_format($total, 2, ',', '.');
                            }

                            if ((!empty($evidencia2)) && (!empty($evidencia1))) {
                                $evidencia = $evidencia . $evidencia1 . $evidencia2 . $evidencia3;
                            }
                            elseif (!empty($evidencia1)) {
                                $evidencia = $evidencia . $evidencia1 . $evidencia3;
                            }
                            elseif(!empty($evidencia2)){
                                $evidencia = $evidencia . $evidencia2 . $evidencia3;
                            }
                        }

                        $dto = DB::table('itensdeinspecoes')
                            ->Where([['inspecao_id', '=', $registro->inspecao_id]])
                            ->Where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                            ->select( 'itensdeinspecoes.*'  )
                            ->first();

                        $itensdeinspecao = Itensdeinspecao::find($dto->id);
                        $itensdeinspecao->avaliacao  = $avaliacao;
                        $itensdeinspecao->oportunidadeAprimoramento = $oportunidadeAprimoramento;
                        $itensdeinspecao->evidencia  = $evidencia;
                        $itensdeinspecao->valorFalta = $total;
//                        $itensdeinspecao->situacao   = 'Inspecionado';   // monitorada
                        $itensdeinspecao->situacao   = 'Em Inspeção';   // presencial/remota
                        $itensdeinspecao->pontuado   = $pontuado;
                        $itensdeinspecao->itemQuantificado = 'Sim';
                        $itensdeinspecao->orientacao = $registro->orientacao;
                        $itensdeinspecao->eventosSistema = 'Item avaliado Remotamente por Websgi em '.date( 'd/m/Y' , strtotime($dtnow)).'.';
                        $itensdeinspecao->reincidencia = $reinc;
                        $itensdeinspecao->codVerificacaoAnterior = $codVerificacaoAnterior;
                        $itensdeinspecao->numeroGrupoReincidente = $numeroGrupoReincidente;
                        $itensdeinspecao->numeroItemReincidente = $numeroItemReincidente;


                        dd( ' DEBUG -linha 819 INSPEÇÃO - >  '
                            . $registro->inspecao_id
                            .'  ITEM DA INSPEÇÃO  testeVerificacao_id - >  '
                            .$registro->testeVerificacao_id.'  reincidencia - >  '
                            .$reinc, '  DTO -> ',$dto , '  DTO -> $itensdeinspecao ->',$itensdeinspecao  ,$registro, );

                        $itensdeinspecao->update();
                    }
//                                  Fim se tem pendencia proter sem reincidencia
//                                  Inicio conforme
                    else{
                        $avaliacao = 'Conforme';
                        $oportunidadeAprimoramento = 'Em análise aos dados do Sistema Proter, do período de Janeiro/2017 a '. date( 'd/m/Y' , strtotime($dtmenos90dias)).', constatou-se que não havia pendências com mais de 90 dias.';
                        $dto = DB::table('itensdeinspecoes')
                            ->Where([['inspecao_id', '=', $registro->inspecao_id]])
                            ->Where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                            ->select( 'itensdeinspecoes.*'  )
                            ->first();
                        $itensdeinspecao = Itensdeinspecao::find($dto->id);
                        $itensdeinspecao->avaliacao  = $avaliacao;
                        $itensdeinspecao->oportunidadeAprimoramento = $oportunidadeAprimoramento;
                        $itensdeinspecao->evidencia  = null;
                        $itensdeinspecao->valorFalta = 0.00;
                        //                        $itensdeinspecao->situacao   = 'Inspecionado'; // monitorada
                        $itensdeinspecao->situacao   = 'Em Inspeção'; // presencial/remota
                        $itensdeinspecao->pontuado   = 0.00;
                        $itensdeinspecao->itemQuantificado = 'Não';
                        $itensdeinspecao->orientacao= null;
                        $itensdeinspecao->eventosSistema = 'Item avaliado remotamente por Websgi em '.date( 'd/m/Y' , strtotime($dtnow)).'.';

                        dd( ' DEBUG -linha 849 INSPEÇÃO - >  '
                            . $registro->inspecao_id
                            .'  ITEM DA INSPEÇÃO  testeVerificacao_id - >  '
                            .$registro->testeVerificacao_id.'  reincidencia - >  '
                            .$reinc, '  DTO -> ',$dto , '  DTO -> $itensdeinspecao ->',$itensdeinspecao  ,$registro, );

                        $itensdeinspecao->update();
                    }
//                                  Fim Conforme
                }
            }
//   Final 230-5 do teste PROTER

//   230-6 Inicio  do teste SMB_BDF
            if((($registro->numeroGrupoVerificacao == 230)&&($registro->numeroDoTeste == 6))
                || (($registro->numeroGrupoVerificacao == 270)&&($registro->numeroDoTeste== 3))) {
                $dtnow = new Carbon();
                $dtmenos90dias = new Carbon();
                $dtmenos90dias->subDays(90);
                $valorSobra = null;
                $valorFalta = null;
                $valorRisco = null;

                $preVerificar = DB::table('testesdeverificacao')
                    ->select('testesdeverificacao.*' )
                    ->where([['teste',  'like', '%valor depositado na conta bancária%']])
                    ->get();

                foreach($preVerificar as $pre){
                    DB::table('testesdeverificacao')
                        ->where([['id',  '=', $pre->id]])
                        ->update([
                            'preVerificar' => 'Não'
                        ]);
                }

                $reincidencia = DB::table('snci')
                    ->select('no_inspecao', 'no_grupo', 'no_item', 'dt_fim_inspecao', 'dt_inic_inspecao')
                    ->where([['descricao_item', 'like', '%valor depositado na conta bancária%']])
                    ->where([['sto', '=', $registro->sto]])
                    ->orderBy('no_inspecao', 'desc')
                    ->first();
                try {
                    if ($reincidencia->no_inspecao > 1) {
//                                        dd($reincidencia);
                        $reincidente = 1;
                        $reinc = 'Sim';
                        $codVerificacaoAnterior = $reincidencia->no_inspecao;
                        $numeroGrupoReincidente = $reincidencia->no_grupo;
                        $numeroItemReincidente = $reincidencia->no_item;
                        $reincidencia_dt_fim_inspecao = new Carbon($reincidencia->dt_fim_inspecao);
                        $reincidencia_dt_inic_inspecao = new Carbon($reincidencia->dt_inic_inspecao);
                        $reincidencia_dt_fim_inspecao->subMonth(3);
                        $reincidencia_dt_inic_inspecao->subMonth(3);
                        $evidencia=null;
                    }
                } catch (\Exception $e) {
                    $reincidente = 0;
                    $reinc = 'Não';
                    $codVerificacaoAnterior = null;
                    $numeroGrupoReincidente = null;
                    $numeroItemReincidente = null;
                    $evidencia=null;
                }
                $smb_bdf_naoconciliados = DB::table('smb_bdf_naoconciliados')
                    ->select(
                        'smb_bdf_naoconciliados.*'
                    )
                    ->where('mcu', '=', $registro->mcu)
                    ->where('Divergencia', '<>', 0)
                    ->where('Status', '=', 'Pendente')
                    ->where('Data', '>=', $dtmenos90dias)
                    ->orderBy('Data', 'asc')
                    ->get();
//                              Inicio  se tem registro de pendências SMB_BDF
                if (!$smb_bdf_naoconciliados->isEmpty()) {
                    $count = $smb_bdf_naoconciliados->count('id');
                    $dtfim = $smb_bdf_naoconciliados->max('Data');

//                              Inicio  se há divergencia
                    if ($count !== 0) {

                        $smb = $smb_bdf_naoconciliados->sum('SMBDinheiro') + $smb_bdf_naoconciliados->sum('SMBBoleto');
                        $bdf = $smb_bdf_naoconciliados->sum('BDFDinheiro') + $smb_bdf_naoconciliados->sum('BDFBoleto');
                        $divergencia = $smb_bdf_naoconciliados->sum('Divergencia');
//                                      Inicio  se divergencia é um valor diferente de zero
                        if ($divergencia !== 0.0) {

                            foreach ($smb_bdf_naoconciliados as $smb_bdf_naoconciliado) {
                                $smblast = $smb_bdf_naoconciliado->SMBDinheiro + $smb_bdf_naoconciliado->SMBBoleto;
                                $bdflast = $smb_bdf_naoconciliado->BDFDinheiro + $smb_bdf_naoconciliado->BDFBoleto;
                                $divergencialast = $smb_bdf_naoconciliado->Divergencia;
                                $total = ($smblast - $bdflast) - $divergencialast;
                            }
//                                          Inicio Testa ultimo registro se tem compensação
                            if (($smblast + $bdflast) == ($divergencialast * -1)) {

                                $avaliacao = 'Conforme';
                                $oportunidadeAprimoramento = 'Em análise ao sistema SDE – Sistema de Depósito Bancário, na opção "Contabilização", Conciliação SMB x BDF – dados “Não Conciliados”, referente ao período de '.date( 'd/m/Y' , strtotime($dtmenos90dias)) .' a ' . date( 'd/m/Y' , strtotime($dtnow)).', verificou-se a inexistência de divergências.';
                                $dto = DB::table('itensdeinspecoes')
                                    ->Where([['inspecao_id', '=', $registro->inspecao_id]])
                                    ->Where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                                    ->select( 'itensdeinspecoes.*'  )
                                    ->first();
                                $itensdeinspecao = Itensdeinspecao::find($dto->id);
                                $itensdeinspecao->avaliacao  = $avaliacao;
                                $itensdeinspecao->oportunidadeAprimoramento = $oportunidadeAprimoramento;
                                $itensdeinspecao->evidencia  = $evidencia;
                                $itensdeinspecao->valorFalta = $valorFalta;
                                $itensdeinspecao->valorSobra = $valorSobra;
                                $itensdeinspecao->valorRisco = $valorRisco;
                                $itensdeinspecao->consequencias = null;
                                //                        $itensdeinspecao->situacao   = 'Inspecionado'; // monitorada
                                $itensdeinspecao->situacao   = 'Em Inspeção'; // presencial/remota
                                $itensdeinspecao->pontuado   = 0.00;
                                $itensdeinspecao->itemQuantificado = 'Não';
                                $itensdeinspecao->orientacao= null;
                                $itensdeinspecao->eventosSistema = 'Item avaliado remotamente por Websgi em '.date( 'd/m/Y' , strtotime($dtnow)).'.';
 //                                            $itensdeinspecao->update();

                            }
//                                          Final Testa ultimo registro se tem compensação
//                                          Inicio Testa ultimo registro com compensação
                            else{

                                $avaliacao = 'Não Conforme';
                                $oportunidadeAprimoramento = 'Em análise ao sistema SDE – Sistema de Depósito Bancário, na opção "Contabilização", Conciliação SMB x BDF – dados “Não Conciliados”, referente ao período de ' . date( 'd/m/Y' , strtotime($dtmenos90dias)). ' a ' . date( 'd/m/Y' , strtotime($dtnow)) .', constatou-se a existência de divergências entre o valor depositado na conta bancária dos Correios pela Agência e o valor do bloqueto gerado no sistema SARA, no total de R$ ' .number_format($divergencia, 2, ',', '.').' , conforme relacionado a seguir:';

                                $evidencia = $evidencia ."\n".'Data'."\t".'Divergência'."\t".'Tipo';
                                foreach ($smb_bdf_naoconciliados as $smb_bdf_naoconciliado){
                                    $evidencia = $evidencia. "\n"
                                        .date( 'd/m/Y' , strtotime($smb_bdf_naoconciliado->Data))
                                        ."\t".'R$ '.number_format($smb_bdf_naoconciliado->Divergencia, 2, ',', '.');

                                    if(($smb_bdf_naoconciliado->BDFDinheiro<>0) && ($smb_bdf_naoconciliado->BDFCheque<>0) && ($smb_bdf_naoconciliado->BDFBoleto<>0)){
                                        $evidencia = $evidencia. "\t".'Dinheiro/Cheque/Boleto';
                                    }
                                    elseif (($smb_bdf_naoconciliado->BDFDinheiro<>0) && ($smb_bdf_naoconciliado->BDFBoleto<>0)){
                                        $evidencia = $evidencia. "\t".'Dinheiro/Boleto';
                                    }
                                    elseif (($smb_bdf_naoconciliado->BDFDinheiro<>0) && ($smb_bdf_naoconciliado->BDFCheque<>0)){
                                        $evidencia = $evidencia. "\t".'Dinheiro/Cheque';
                                    }
                                    elseif (($smb_bdf_naoconciliado->BDFBoleto<>0) && ($smb_bdf_naoconciliado->BDFCheque<>0)){
                                        $evidencia = $evidencia. "\t".'Boleto/Cheque';
                                    }
                                    elseif ($smb_bdf_naoconciliado->BDFDinheiro<>0){
                                        $evidencia = $evidencia . "\t".'Dinheiro';
                                    }
                                    elseif ($smb_bdf_naoconciliado->BDFBoleto<>0){
                                        $evidencia = $evidencia. "\t".'Boleto';
                                    }
                                    elseif ($smb_bdf_naoconciliado->BDFCheque<>0){
                                        $evidencia = $evidencia . "\t".'Cheque';
                                    }
                                    else{
                                        $evidencia = $evidencia . "\t".'Não identificado';
                                    }
                                }

                                if($divergencia > 0.00) {
                                    $total= $divergencia;
                                    $evidencia = $evidencia. "\n".'Em Sobra '.$divergencia;
                                    $valorFalta = null;
                                    $valorSobra = $total;
                                    $valorRisco= null;

                                }
                                else{
                                    $total= $divergencia *-1;
                                    $evidencia = $evidencia."\n".'Em Falta '.$total;
                                    $valorSobra = null;
                                    $valorFalta = $total;
                                    $valorRisco= null;
                                }
//                                                dd('line 876',  $smb , $bdf ,$divergencia, $total );

                                $quebra = DB::table('relevancias')
                                    ->select('valor_final' )
                                    ->where('fator_multiplicador', '=', 1 )
                                    ->first();
                                $quebracaixa = $quebra->valor_final * 0.1;
                                $fm = DB::table('relevancias')
                                    ->select('fator_multiplicador', 'valor_final', 'valor_inicio' )
                                    ->where('valor_inicio', '<=', $total )
                                    ->orderBy('valor_final' ,'desc')
                                    ->first();
                                if($avaliacao == 'Não Conforme') $pontuado = $registro->totalPontos * $fm->fator_multiplicador;

//                                                dd('line 821',  $smb , $bdf ,$divergencia, $total );

                                $dto = DB::table('itensdeinspecoes')
                                    ->Where([['inspecao_id', '=', $registro->inspecao_id]])
                                    ->Where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                                    ->select( 'itensdeinspecoes.*'  )
                                    ->first();

                                $itensdeinspecao = Itensdeinspecao::find($dto->id);
                                $itensdeinspecao->avaliacao  = $avaliacao;
                                $itensdeinspecao->oportunidadeAprimoramento = $oportunidadeAprimoramento;
                                $itensdeinspecao->evidencia  = $evidencia;
                                $itensdeinspecao->valorFalta = $valorFalta;
                                $itensdeinspecao->valorSobra = $valorSobra;
                                $itensdeinspecao->valorRisco = $valorRisco;
                                //                        $itensdeinspecao->situacao   = 'Inspecionado'; // monitorada
                                $itensdeinspecao->situacao   = 'Em Inspeção'; // presencial/remota
                                $itensdeinspecao->pontuado   = $pontuado;
                                $itensdeinspecao->itemQuantificado = 'Sim';
                                $itensdeinspecao->orientacao = $registro->orientacao;
                                $itensdeinspecao->eventosSistema = 'Item avaliado Remotamente por Websgi em '.date( 'd/m/Y' , strtotime($dtnow)).'.';
                                $itensdeinspecao->reincidencia = $reinc;
                                $itensdeinspecao->codVerificacaoAnterior = $codVerificacaoAnterior;
                                $itensdeinspecao->numeroGrupoReincidente = $numeroGrupoReincidente;
                                $itensdeinspecao->numeroItemReincidente = $numeroItemReincidente;
                                dd( ' DEBUG -linha 1063 INSPEÇÃO - >  '
                                    . $registro->inspecao_id
                                    .'  ITEM DA INSPEÇÃO  testeVerificacao_id - >  '
                                    .$registro->testeVerificacao_id.'  reincidencia - >  '
                                    .$reinc, '  DTO -> ',$dto , '  DTO -> $itensdeinspecao ->',$itensdeinspecao  ,$registro, );
                                $itensdeinspecao->update();
                            }
//                                          Final Testa ultimo registro com compensação
                        }
//                                      Final  se divergencia é um valor diferente de zero

//                                      Inicio  se divergencia é um valor igual zero
                        if ($divergencia == 0.0){

                            $dataanterior = null;
                            foreach ($smb_bdf_naoconciliados as $smb_bdf_naoconciliado) {
                                if ($dataanterior !== null) {

                                    $dataantual = $dataanterior;
                                    $dataantual->addDays(1);

                                    $unidade_enderecos = DB::table('unidade_enderecos')
                                        ->Where([['mcu', '=', $registro->mcu]])
                                        ->select( 'unidade_enderecos.*'  )
                                        ->first();
                                    $feriado = DB::table('feriados')
                                        ->Where([['data_do_feriado', '=', $dataantual]])
                                        ->Where([['nome_municipio', '=', $unidade_enderecos->cidade]])
                                        ->Where([['uf', '=', $unidade_enderecos->uf]])
                                        ->select( 'feriados.*'  )
                                        ->first();
                                    //  $dt$feriado = $feriado->count('id');

                                    if($feriado)  {
                                        $diasemana = $dataanterior;
                                        $diasemana->addDays(5);
                                    }
                                    else {
                                        // dayOfWeek returns a number between 0 (sunday) and 6 (saturday)
                                        $diasemana = $dataanterior->dayOfWeek;
                                        if ($diasemana == 5) { //Sexta
                                            $dataanterior->addDays(3);
                                        }
                                        if ($diasemana == 4) { //Quinta
                                            $dataanterior->addDays(4);
                                        }
                                        if ($diasemana <= 3) { // seg a quarta
                                            $dataanterior->addDays(2);
                                        }
                                    }


                                    $periodo = CarbonPeriod::create($dataanterior, $smb_bdf_naoconciliado->Data);

                                    if($periodo->count()>1){
                                        $avaliacao = 'Não Conforme';
                                        $oportunidadeAprimoramento = 'Em análise ao sistema SDE – Sistema de Depósito Bancário, na opção "Contabilização", Conciliação SMB x BDF – dados “Não Conciliados”,  referente ao período de '. date( 'd/m/Y' , strtotime($dtmenos90dias)). ' a ' . date( 'd/m/Y' , strtotime($dtnow)) .', constatou-se a existência de depositos na conta dos Correios pela Agência com prazo superior D+1. Evento em data anterior à '.date( 'd/m/Y' , strtotime($dataanterior)) ;
                                        $total = $smb_bdf_naoconciliado->BDFBoleto;
                                        $valorRisco = $smb_bdf_naoconciliado->BDFBoleto;
                                        break;
                                    }
                                }
                                $dataanterior = new Carbon($smb_bdf_naoconciliado->Data);
                            }
                            if($periodo->count()>1){
                                $quebra = DB::table('relevancias')
                                    ->select('valor_final' )
                                    ->where('fator_multiplicador', '=', 1 )
                                    ->first();
                                $quebracaixa = $quebra->valor_final * 0.1;

                                $fm = DB::table('relevancias')
                                    ->select('fator_multiplicador', 'valor_final', 'valor_inicio' )
                                    ->where('valor_inicio', '<=', $total )
                                    ->orderBy('valor_final' ,'desc')
                                    ->first();
                                if($avaliacao == 'Não Conforme') $pontuado = $registro->totalPontos * $fm->fator_multiplicador;

                                $evidencia = $evidencia."\n".'Data'."\t".'Valor do Boleto';
                                foreach ($smb_bdf_naoconciliados as $smb_bdf_naoconciliado) {
                                    $evidencia = $evidencia . "\n"
                                        . date('d/m/Y', strtotime($smb_bdf_naoconciliado->Data))
                                        . "\t" . 'R$ ' . number_format($smb_bdf_naoconciliado->BDFBoleto, 2, ',', '.');
                                }

                                $dto = DB::table('itensdeinspecoes')
                                    ->Where([['inspecao_id', '=', $registro->inspecao_id]])
                                    ->Where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                                    ->select( 'itensdeinspecoes.*'  )
                                    ->first();
                                $itensdeinspecao = Itensdeinspecao::find($dto->id);
                                $itensdeinspecao->avaliacao  = $avaliacao;
                                $itensdeinspecao->oportunidadeAprimoramento = $oportunidadeAprimoramento;
                                $itensdeinspecao->evidencia  = $evidencia;
                                $itensdeinspecao->valorFalta = $valorFalta;
                                $itensdeinspecao->valorSobra = $valorSobra;
                                $itensdeinspecao->valorRisco = $valorRisco;
                                //                        $itensdeinspecao->situacao   = 'Inspecionado'; // monitorada
                                $itensdeinspecao->situacao   = 'Em Inspeção'; // presencial/remota
                                $itensdeinspecao->pontuado   = $pontuado;
                                $itensdeinspecao->itemQuantificado = 'Sim';
                                $itensdeinspecao->orientacao = $registro->orientacao;
                                $itensdeinspecao->consequencias = null;
                                $itensdeinspecao->eventosSistema = 'Item avaliado Remotamente por Websgi em '.date( 'd/m/Y' , strtotime($dtnow)).'.';
                                $itensdeinspecao->reincidencia = $reinc;
                                $itensdeinspecao->codVerificacaoAnterior = $codVerificacaoAnterior;
                                $itensdeinspecao->numeroGrupoReincidente = $numeroGrupoReincidente;
                                $itensdeinspecao->numeroItemReincidente = $numeroItemReincidente;
                                dd( ' DEBUG -linha 1171 INSPEÇÃO - >  '
                                    . $registro->inspecao_id
                                    .'  ITEM DA INSPEÇÃO  testeVerificacao_id - >  '
                                    .$registro->testeVerificacao_id.'  reincidencia - >  '
                                    .$reinc, '  DTO -> ',$dto , '  DTO -> $itensdeinspecao ->',$itensdeinspecao  ,$registro, );
                                $itensdeinspecao->update();
                            }
                            else{
                                $avaliacao = 'Conforme';
                                $oportunidadeAprimoramento = 'Em análise ao sistema SDE – Sistema de Depósito Bancário, na opção "Contabilização", Conciliação SMB x BDF – dados “Não Conciliados”, referente ao período de '.date( 'd/m/Y' , strtotime($dtmenos90dias)) .' a ' . date( 'd/m/Y' , strtotime($dtnow)).', verificou-se a inexistência de divergências.';
                                $dto = DB::table('itensdeinspecoes')
                                    ->Where([['inspecao_id', '=', $registro->inspecao_id]])
                                    ->Where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                                    ->select( 'itensdeinspecoes.*'  )
                                    ->first();
                                $itensdeinspecao = Itensdeinspecao::find($dto->id);
                                $itensdeinspecao->avaliacao  = $avaliacao;
                                $itensdeinspecao->oportunidadeAprimoramento = $oportunidadeAprimoramento;
                                $itensdeinspecao->evidencia  = null;
                                $itensdeinspecao->valorFalta = 0.00;
                                //                        $itensdeinspecao->situacao   = 'Inspecionado'; // monitorada
                                $itensdeinspecao->situacao   = 'Em Inspeção'; // presencial/remota
                                $itensdeinspecao->pontuado   = 0.00;
                                $itensdeinspecao->itemQuantificado = 'Não';
                                $itensdeinspecao->orientacao= null;
                                $itensdeinspecao->eventosSistema = 'Item avaliado remotamente por Websgi em '.date( 'd/m/Y' , strtotime($dtnow)).'.';
                                dd( ' DEBUG -linha 1197 INSPEÇÃO - >  '
                                    . $registro->inspecao_id
                                    .'  ITEM DA INSPEÇÃO  testeVerificacao_id - >  '
                                    .$registro->testeVerificacao_id.'  reincidencia - >  '
                                    .$reinc, '  DTO -> ',$dto , '  DTO -> $itensdeinspecao ->',$itensdeinspecao  ,$registro, );
                                $itensdeinspecao->update();
                            }
                        }
//                                      Final  se divergencia é um valor igual zero
                    }
//                              Final  se há divergencia

                }
//                              Final  se tem registro de pendências SMB_BDF
//                              Inicio  se Não tem registro de pendências SMB_BDF
                else{
                    $avaliacao = 'Conforme';
                    $oportunidadeAprimoramento = 'Em análise ao sistema SDE – Sistema de Depósito Bancário, na opção "Contabilização", Conciliação SMB x BDF – dados “Não Conciliados”, referente ao período de '.date( 'd/m/Y' , strtotime($dtmenos90dias)) .' a ' . date( 'd/m/Y' , strtotime($dtnow)).', verificou-se a inexistência de divergências.';

                    $dto = DB::table('itensdeinspecoes')
                        ->Where([['inspecao_id', '=', $registro->inspecao_id]])
                        ->Where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                        ->select( 'itensdeinspecoes.*'  )
                        ->first();
                    $itensdeinspecao = Itensdeinspecao::find($dto->id);
                    $itensdeinspecao->avaliacao  = $avaliacao;
                    $itensdeinspecao->oportunidadeAprimoramento = $oportunidadeAprimoramento;
                    $itensdeinspecao->evidencia  = $evidencia;
                    $itensdeinspecao->valorFalta = $valorFalta;
                    $itensdeinspecao->valorSobra = $valorSobra;
                    $itensdeinspecao->valorRisco = $valorRisco;
                    //                        $itensdeinspecao->situacao   = 'Inspecionado'; // monitorada
                    $itensdeinspecao->situacao   = 'Em Inspeção'; // presencial/remota
                    $itensdeinspecao->pontuado   = 0.00;
                    $itensdeinspecao->itemQuantificado = 'Não';
                    $itensdeinspecao->consequencias = null;
                    $itensdeinspecao->orientacao= null;
                    $itensdeinspecao->eventosSistema = 'Item avaliado remotamente por Websgi em '.date( 'd/m/Y' , strtotime($dtnow)).'.';
                    $itensdeinspecao->update();
                }


//                              Final  se Não tem registro de pendências SMB_BD
            }
//      Final  do teste SMB_BDF

//         230-7              Inicio  do teste SLD-02-BDF
            if ((($registro->numeroGrupoVerificacao == 230) && ($registro->numeroDoTeste == 7))
                || (($registro->numeroGrupoVerificacao == 270) && ($registro->numeroDoTeste == 4))) {

                $acumulados30 = 0;
                $acumulados60 = 0;
                $acumulados90 = 0;
                $ocorrencias30 = 0;
                $ocorrencias60 = 0;
                $ocorrencias90 = 0;
                $codVerificacaoAnterior = null;
                $numeroGrupoReincidente = null;
                $numeroItemReincidente = null;
                $evidencia = null;
                $valorSobra = null;
                $valorFalta = null;
                $valorRisco = null;
                $total = 0;
                $pontuado = null;
                $itemQuantificado='Não';
                $reincidente = 0;
                $reinc = 'Não';


                $preVerificar = DB::table('testesdeverificacao')
                    ->select('testesdeverificacao.*' )
                    ->where([['teste',  'like', '%Saldo que Passa%']])
                    ->get();

                foreach($preVerificar as $pre){
                    DB::table('testesdeverificacao')
                        ->where([['id',  '=', $pre->id]])
                        ->update([
                            'preVerificar' => 'Não'
                        ]);
                }

                $sl02bdfsMaxdata = SL02_bdf::where('cod_orgao', $registro->sto)->max('dt_movimento');

                if(! empty($sl02bdfsMaxdata))
                {
                    $sl02bdfsMaxdata = new Carbon($sl02bdfsMaxdata);
                    $dtmenos30dias = new Carbon($sl02bdfsMaxdata);
                    $dtmenos60dias = new Carbon($sl02bdfsMaxdata);
                    $dtmenos90dias = new Carbon($sl02bdfsMaxdata);
                    $dtmenos30dias = $dtmenos30dias->subDays(30);
                    $dtmenos60dias = $dtmenos60dias->subDays(60);
                    $dtmenos90dias = $dtmenos90dias->subDays(90);
                    $evidencia = null;

                    $sl02bdfs30 = DB::table('sl02bdfs')
                        ->select('sl02bdfs.*')
                        ->where('cod_orgao', '=', $registro->sto)
                        ->where('dt_movimento', '>=', $dtmenos30dias)
                        ->where('diferenca', '>=', 1)
                        ->orderBy('dt_movimento', 'desc')
                        ->get();

                    if (! $sl02bdfs30->isEmpty()) {
                        $acumulados30 = $sl02bdfs30->sum('diferenca'); // soma a coluna valor da coleção de dados
                        $ocorrencias30 = $sl02bdfs30->count('diferenca');
                        $evidencia = $evidencia. "\n" . 'Período '
                            . date('d/m/Y', strtotime($sl02bdfsMaxdata)).', até '
                            . date('d/m/Y', strtotime($dtmenos30dias)).'.';
                        $evidencia = $evidencia. "\n" . 'Data' . "\t" . 'Saldo de Numerário' . "\t" . 'Limite de Saldo' . "\t" . 'Diferença' ;
                        $row=1;
                        foreach ($sl02bdfs30 as $tabela) {

                            $evidencia = $evidencia . "\n" . date('d/m/Y', strtotime($tabela->dt_movimento))
                                . "\t" . 'R$'.number_format($tabela->saldo_atual, 2, ',', '.')
                                . "\t" . 'R$'.number_format($tabela->limite, 2, ',', '.')
                                . "\t" . 'R$'.number_format($tabela->diferenca, 2, ',', '.');
                            $row ++;
                        }
                        $acumulados30 = $acumulados30 / $row;
                        $evidencia = $evidencia . "\n" .'Quantidade de ocorrências em 30 dias ' .$ocorrencias30
                            .'. Quantidade média de ocorrências em 30 dias '
                            .number_format((($ocorrencias30 / 23) * 100), 2, ',', '.')
                            .'. Valor médio ultrapassado R$ '
                            .number_format($acumulados30, 2, ',', '.');

                    }

                    $sl02bdfs60 = DB::table('sl02bdfs')
                        ->select('sl02bdfs.*')
                        ->where('cod_orgao', '=', $registro->sto)
                        ->where('dt_movimento', '<', $dtmenos30dias)
                        ->where('dt_movimento', '>=', $dtmenos60dias)
                        ->where('diferenca', '>=', 1)
                        ->orderBy('dt_movimento', 'desc')
                        ->get();

                    if (! $sl02bdfs60->isEmpty()) {
                        $acumulados60 = $sl02bdfs60->sum('diferenca'); // soma a coluna valor da coleção de dados
                        $ocorrencias60 = $sl02bdfs60->count('diferenca');
                        $evidencia = $evidencia. "\n" . 'Período '
                            . date('d/m/Y', strtotime($dtmenos30dias)).', até '
                            . date('d/m/Y', strtotime($dtmenos60dias)).'.';
                        $evidencia = $evidencia. "\n" . 'Data' . "\t" . 'Saldo de Numerário' . "\t" . 'Limite de Saldo' . "\t" . 'Diferença' ;
                        $row=1;
                        foreach ($sl02bdfs60 as $tabela) {

                            $evidencia = $evidencia . "\n" . date('d/m/Y', strtotime($tabela->dt_movimento))
                                . "\t" . 'R$'.number_format($tabela->saldo_atual, 2, ',', '.')
                                . "\t" . 'R$'.number_format($tabela->limite, 2, ',', '.')
                                . "\t" . 'R$'.number_format($tabela->diferenca, 2, ',', '.');
                            $row ++;
                        }
                        $acumulados60 = $acumulados60 / $row;
                        $evidencia = $evidencia . "\n" .'Quantidade de ocorrências em 30 dias ' .$ocorrencias60
                            .'. Quantidade média de ocorrências em 30 dias '
                            .number_format((($ocorrencias60 / 23) * 100), 2, ',', '.')
                            .'. Valor médio ultrapassado R$ '
                            .number_format($acumulados60, 2, ',', '.');

                    }

                    $sl02bdfs90 = DB::table('sl02bdfs')
                        ->select('sl02bdfs.*')
                        ->where('cod_orgao', '=', $registro->sto)
                        ->where('dt_movimento', '<', $dtmenos60dias)
                        ->where('dt_movimento', '>=', $dtmenos90dias)
                        ->where('diferenca', '>=', 1)
                        ->orderBy('dt_movimento', 'desc')
                        ->get();

                    if (! $sl02bdfs90->isEmpty()) {
                        $acumulados90 = $sl02bdfs90->sum('diferenca'); // soma a coluna valor da coleção de dados
                        $ocorrencias90 = $sl02bdfs90->count('diferenca');
                        $evidencia = $evidencia. "\n" . 'Período '
                            . date('d/m/Y', strtotime($dtmenos60dias)).', até '
                            . date('d/m/Y', strtotime($dtmenos90dias)).'.';
                        $evidencia = $evidencia. "\n" . 'Data' . "\t" . 'Saldo de Numerário' . "\t" . 'Limite de Saldo' . "\t" . 'Diferença' ;
                        $row=1;
                        foreach ($sl02bdfs90 as $tabela) {

                            $evidencia = $evidencia . "\n" . date('d/m/Y', strtotime($tabela->dt_movimento))
                                . "\t" . 'R$'.number_format($tabela->saldo_atual, 2, ',', '.')
                                . "\t" . 'R$'.number_format($tabela->limite, 2, ',', '.')
                                . "\t" . 'R$'.number_format($tabela->diferenca, 2, ',', '.');
                            $row ++;
                        }
                        $acumulados90 = $acumulados90 / $row;
                        $evidencia = $evidencia . "\n" .'Quantidade de ocorrências em 30 dias ' .$ocorrencias90
                            .'. Quantidade média de ocorrências em 30 dias '
                            .number_format((($ocorrencias90 / 23) * 100), 2, ',', '.')
                            .'. Valor médio ultrapassado R$ '
                            .number_format($acumulados90, 2, ',', '.');

                    }

                    if(($acumulados30 >= 1) && ($acumulados60 >= 1) && ($acumulados90 >= 1)){
                        $total = ($acumulados30 + $acumulados60 + $acumulados90)/3;
                        $ocorrencias = $ocorrencias30 + $ocorrencias60 + $ocorrencias90;
                    }

                    if(($acumulados30 >= 1) && ($acumulados60 >= 1) && ($acumulados90 == 0)){
                        $total = ($acumulados30 + $acumulados60)/2;
                        $ocorrencias = $ocorrencias30 + $ocorrencias60;
                    }

                    if(($acumulados30 >= 1) && ($acumulados60 == 0) && ($acumulados90 == 0)){
                        $total = $acumulados30;
                        $ocorrencias = $ocorrencias30;
                    }

                    if(($acumulados30 == 0) && ($acumulados60 >= 1) && ($acumulados90 == 0)){
                        $total = $acumulados60;
                        $ocorrencias = $ocorrencias60;
                    }

                    if(($acumulados30 == 0) && ($acumulados60 == 0) && ($acumulados90 >= 1)){
                        $total = $acumulados90;
                        $ocorrencias = $ocorrencias90;
                    }
//                                  if ( ((($ocorrencias30 / 23) * 100) > 20)  || ((($ocorrencias60 / 23) * 100) > 20) || ((($ocorrencias90 / 23) * 100) > 20))  // 20%
                    if (($ocorrencias30 >= 7) || ($ocorrencias60 >= 7) || ($ocorrencias90 >= 7))   // maior ou igul 7 ocorrências imprime tudo
                    {
                        $avaliacao = 'Não Conforme';
                        $oportunidadeAprimoramento = 'Em análise ao Relatório "Saldo de Numerário em relação
                                         ao Limite de Saldo", do sistema BDF, referente ao período de ' . date('d/m/Y', strtotime($dtnow))
                            . ' a ' . date('d/m/Y', strtotime($dtmenos90dias)) . ',
                                            constatou-se que que o limite do saldo estabelecido para a unidade foi descumprido em '
                            . $ocorrencias . ' dias, o que corresponde a uma média de ' . $ocorrencias/3 . ' ocorrências por mês, considerando o período, conforme detalhado a seguir:';

                        $reincidencia = DB::table('snci')
                            ->select('no_inspecao', 'no_grupo', 'no_item', 'dt_fim_inspecao', 'dt_inic_inspecao')
                            ->where([['descricao_item', 'like', '%Saldo que Passa%']])
                            ->where([['sto', '=', $registro->sto]])
                            ->orderBy('no_inspecao', 'desc')
                            ->first();

                        try {
                            if ($reincidencia->no_inspecao > 1) {
//                                        dd($reincidencia);
                                $reincidente = 1;
                                $reinc = 'Sim';
                                $codVerificacaoAnterior = $reincidencia->no_inspecao;
                                $numeroGrupoReincidente = $reincidencia->no_grupo;
                                $numeroItemReincidente = $reincidencia->no_item;
                                $reincidencia_dt_fim_inspecao = new Carbon($reincidencia->dt_fim_inspecao);
                                $reincidencia_dt_inic_inspecao = new Carbon($reincidencia->dt_inic_inspecao);
                                $reincidencia_dt_fim_inspecao->subMonth(3);
                                $reincidencia_dt_inic_inspecao->subMonth(3);
                                $evidencia = null;
                            }
                        }
                        catch (\Exception $e) {
                            $reincidente = 0;
                            $reinc = 'Não';
                        }
                        if ($total > 0.00) {
                            $itemQuantificado ='Sim';
                            $evidencia  = $evidencia . "\n" . 'Em Risco ' .number_format($total, 2, ',', '.');
                            $valorFalta = null;
                            $valorSobra = null;
                            $valorRisco = $total;
                        }

                        $quebra = DB::table('relevancias')
                            ->select('valor_final')
                            ->where('fator_multiplicador', '=', 1)
                            ->first();
                        $quebracaixa = $quebra->valor_final * 0.1;

                        if( $valorFalta > $quebracaixa){
                            $fm = DB::table('relevancias')
                                ->select('fator_multiplicador', 'valor_final', 'valor_inicio')
                                ->where('valor_inicio', '<=', $total)
                                ->orderBy('valor_final', 'desc')
                                ->first();

                            if($avaliacao == 'Não Conforme') $pontuado = $registro->totalPontos * $fm->fator_multiplicador;
                        }
                        else{
                            if($avaliacao == 'Não Conforme') $pontuado = $registro->totalPontos * 1;
                        }
                    }
                    else {
                        $avaliacao = 'Conforme';
                        $oportunidadeAprimoramento = 'Em análise ao Relatório "Saldo de Numerário em relação ao Limite
                                         de Saldo", do sistema BDF, referente ao período de ' . date('d/m/Y', strtotime($dtnow)) . ' a '
                            . date('d/m/Y', strtotime($dtmenos90dias)) . ',
                                            constatou-se que não houve descumprimento do limite de saldo estabelecido para a unidade.';
                    }
                }
                else {
                    $avaliacao = 'Nao Verificado';
                    $oportunidadeAprimoramento = 'Não há Registros na base de dados para avaliar a unidade.';
                }

                $dto = DB::table('itensdeinspecoes')
                    ->Where([['inspecao_id', '=', $registro->inspecao_id]])
                    ->Where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                    ->select('itensdeinspecoes.*')
                    ->first();

                $itensdeinspecao = Itensdeinspecao::find($dto->id);
                $itensdeinspecao->avaliacao = $avaliacao;
                $itensdeinspecao->oportunidadeAprimoramento = $oportunidadeAprimoramento;
                $itensdeinspecao->evidencia = $evidencia;
                $itensdeinspecao->valorFalta = $valorFalta;
                $itensdeinspecao->valorSobra = $valorSobra;
                $itensdeinspecao->valorRisco = $valorRisco;
                //                        $itensdeinspecao->situacao   = 'Inspecionado'; // monitorada
                $itensdeinspecao->situacao   = 'Em Inspeção'; // presencial/remota
                $itensdeinspecao->pontuado = $pontuado;
                $itensdeinspecao->itemQuantificado = $itemQuantificado;
                $itensdeinspecao->orientacao = $registro->orientacao;
                $itensdeinspecao->eventosSistema = 'Item avaliado Remotamente por Websgi em ' . date('d/m/Y', strtotime($dtnow)) . '.';
                $itensdeinspecao->reincidencia = $reinc;
                $itensdeinspecao->codVerificacaoAnterior = $codVerificacaoAnterior;
                $itensdeinspecao->numeroGrupoReincidente = $numeroGrupoReincidente;
                $itensdeinspecao->numeroItemReincidente = $numeroItemReincidente;
                $itensdeinspecao->update();
            }
//                       Final  do teste SLD-02-BDF

//       231-1               Inicio  do teste Extravio Responsabilidade Definida
            if((($registro->numeroGrupoVerificacao == 205)&&($registro->numeroDoTeste == 2))
                || (($registro->numeroGrupoVerificacao==334)&&($registro->numeroDoTeste==1))
                || (($registro->numeroGrupoVerificacao==372)&&($registro->numeroDoTeste==1))
                || (($registro->numeroGrupoVerificacao==286)&&($registro->numeroDoTeste==2))
                || (($registro->numeroGrupoVerificacao==221)&&($registro->numeroDoTeste==2))
                || (($registro->numeroGrupoVerificacao==57)&&($registro->numeroDoTeste==2))
                || (($registro->numeroGrupoVerificacao==117)&&($registro->numeroDoTeste==2))
                || (($registro->numeroGrupoVerificacao==79)&&($registro->numeroDoTeste==2))
                || (($registro->numeroGrupoVerificacao==303)&&($registro->numeroDoTeste==2))
                || (($registro->numeroGrupoVerificacao==354)&&($registro->numeroDoTeste==1))
                || (($registro->numeroGrupoVerificacao == 231)&&($registro->numeroDoTeste == 1))
                || (($registro->numeroGrupoVerificacao==271)&&($registro->numeroDoTeste==1))) {

                $codVerificacaoAnterior = null;
                $numeroGrupoReincidente = null;
                $numeroItemReincidente = null;
                $consequencias = null;
                $orientacao = null;
                $evidencia = null;
                $valorSobra = null;
                $valorFalta = null;
                $valorRisco = null;
                $total = 0;
                $pontuado = null;
                $itemQuantificado = 'Não';
                $reincidente = 0;
                $reinc = 'Não';
                $dtmin = $dtnow;
                $count = 0;


                $preVerificar = DB::table('testesdeverificacao')
                    ->select('testesdeverificacao.*' )
                    ->where([['teste',  'like', '%objetos indenizados por extravio%']])
                    ->get();

                foreach($preVerificar as $pre){
                    DB::table('testesdeverificacao')
                        ->where([['id',  '=', $pre->id]])
                        ->update([
                            'preVerificar' => 'Não'
                        ]);
                }

                //verifica histórico de inspeções
                $reincidencia = DB::table('snci')
                    ->select('no_inspecao', 'no_grupo', 'no_item', 'dt_fim_inspecao', 'dt_inic_inspecao')
                    ->where([['descricao_item', 'like', '%objetos indenizados por extravio%']])
                    ->where([['sto', '=', $registro->sto]])
                    ->orderBy('no_inspecao', 'desc')
                    ->first();

                try {

                    if ($reincidencia->no_inspecao > 1) {
//                                        dd($reincidencia);
                        $reincidente = 1;
                        $reinc = 'Sim';
                        $codVerificacaoAnterior = $reincidencia->no_inspecao;
                        $numeroGrupoReincidente = $reincidencia->no_grupo;
                        $numeroItemReincidente = $reincidencia->no_item;
                        $reincidencia_dt_fim_inspecao = new Carbon($reincidencia->dt_fim_inspecao);
                        $reincidencia_dt_inic_inspecao = new Carbon($reincidencia->dt_inic_inspecao);
                        $reincidencia_dt_fim_inspecao->subMonth(3);
                        $reincidencia_dt_inic_inspecao->subMonth(3);

                        //se houver registros de inspeções anteriores  consulta  com range  entre datas
                        $resp_definidas = DB::table('resp_definidas')
                            ->select('mcu', 'unidade', 'data_pagamento', 'objeto', 'nu_sei', 'data', 'situacao', 'valor_da_indenizacao')
                            ->where('mcu', '=', $registro->mcu)
                            ->where('data_pagamento', '<=', $dtmenos90dias)
                            ->where('data_pagamento', '>=', $reincidencia_dt_fim_inspecao)
                            ->where('nu_sei', '=', '')

                            ->get();

                    }
                    else{
                        $resp_definidas = DB::table('resp_definidas')
                            ->select('mcu', 'unidade', 'data_pagamento', 'objeto', 'nu_sei', 'data', 'situacao', 'valor_da_indenizacao')
                            ->where('mcu', '=', $registro->mcu)
                            ->where('data_pagamento', '<=', $dtmenos90dias)
                            ->where('nu_sei', '=', '')
                            ->get();
                    }
                }
                catch (\Exception $e) {

                    $resp_definidas = DB::table('resp_definidas')
                        ->select('mcu', 'unidade', 'data_pagamento', 'objeto', 'nu_sei', 'data', 'situacao', 'valor_da_indenizacao')
                        ->where('mcu', '=', $registro->mcu)
                        ->where('data_pagamento', '<=', $dtmenos90dias)
                        ->where('nu_sei', '=', '')
                        ->get();
                }

                if (!$resp_definidas->isEmpty()) {
                    $count = $resp_definidas->count('objeto');
                    $total = $resp_definidas->sum('valor_da_indenizacao');
                    $dtmax = $dtmenos90dias;
                    $avaliacao = 'Não Conforme';
                    $oportunidadeAprimoramento = 'Em análise à planilha de controle de processos de apuração de extravios de objetos indenizados com responsabilidade definida, disponibilizada pela área de Segurança da Superintendência Regional CSEP, que detem informações a partir de 2015 até ' . date('d/m/Y', strtotime($dtmax)) . ', constatou-se a existência de ' . $count . ' processos pendentes de conclusão há mais de 90 dias sob responsabilidade da unidade, conforme relacionado a seguir:';
                    $consequencias = $registro->consequencias;
                    $orientacao = $registro->orientacao;
                    $valorFalta =  $total;
                    $evidencia = $evidencia. "\n" . 'Número Objeto' . "\t" . 'Número Processo' . "\t" . 'Data Processo' . "\t" . 'Data Atualização' . "\t" . 'Última Atualização' . "\t" . 'Valor' ;

                    foreach ($sl02bdfs90 as $tabela) {
//      ########## ATENÇÃO ##########
// 01/04/2020 Abilio esse trecho de código precisa ser testado não havia dados suficiete para implementar o
// teste no desenvolvimento caso houver algum ajuste  aualizar o controller InspeçãoController para esse item.

                        $evidencia = $evidencia . "\n" . $tabela->objeto . "\t"
                            . (isset($tabela->nu_sei) && $tabela->nu_sei == ''  ? '   ----------  ' : $tabela->nu_sei)
                            . "\t" . (isset($tabela->data_pagamento) && $tabela->data_pagamento == ''  ? '   ----------  '
                                : date('d/m/Y', strtotime($tabela->data_pagamento)))
                            . "\t" . (isset($tabela->data) && $tabela->data == ''  ? '   ----------  '
                                : date('d/m/Y', strtotime($tabela->data)))
                            . "\t" . (isset($tabela->situacao) && $tabela->situacao == ''  ? '   ----------  '
                                : $tabela->situacao)
                            . "\t" .  'R$'.number_format($tabela->valor_da_indenizacao, 2, ',', '.');
                    }
                    $evidencia = $evidencia . "\n" . 'Valor em Falta :'. "\t" .  'R$'.number_format($valorFalta, 2, ',', '.');
//        ####################
                } else {
                    $dtmax = $dtmenos90dias;
                    $avaliacao = 'Conforme';
                    $oportunidadeAprimoramento = 'Em análise à planilha de controle de processos de apuração de extravios de objetos indenizados com responsabilidade definida, disponibilizada pela área de Segurança da Superintendência Regional CSEP, que detem informações a partir de 2015 até ' . date('d/m/Y', strtotime($dtmax)) . ', constatou-se a inexistência de processos pendentes de conclusão há mais de 90 dias sob responsabilidade da unidade.';
                }

                $quebra = DB::table('relevancias')
                    ->select('valor_final')
                    ->where('fator_multiplicador', '=', 1)
                    ->first();
                $quebracaixa = $quebra->valor_final * 0.1;

                if( $valorFalta > $quebracaixa){
                    $fm = DB::table('relevancias')
                        ->select('fator_multiplicador', 'valor_final', 'valor_inicio')
                        ->where('valor_inicio', '<=', $total)
                        ->orderBy('valor_final', 'desc')
                        ->first();
                    if($avaliacao == 'Não Conforme') $pontuado = $registro->totalPontos * $fm->fator_multiplicador;
                }
                else{
                    if($avaliacao == 'Não Conforme') $pontuado = $registro->totalPontos * 1;
                }
                $dto = DB::table('itensdeinspecoes')
                    ->Where([['inspecao_id', '=', $registro->inspecao_id]])
                    ->Where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                    ->select('itensdeinspecoes.*')
                    ->first();
                $itensdeinspecao = Itensdeinspecao::find($dto->id);
                $itensdeinspecao->avaliacao = $avaliacao;
                $itensdeinspecao->oportunidadeAprimoramento = $oportunidadeAprimoramento;
                $itensdeinspecao->evidencia = $evidencia;
                $itensdeinspecao->valorFalta = $valorFalta;
                $itensdeinspecao->valorSobra = $valorSobra;
                $itensdeinspecao->valorRisco = $valorRisco;
                //                        $itensdeinspecao->situacao   = 'Inspecionado'; // monitorada
                $itensdeinspecao->situacao   = 'Em Inspeção'; // presencial/remota
                $itensdeinspecao->pontuado = $pontuado;
                $itensdeinspecao->itemQuantificado = $itemQuantificado;
                $itensdeinspecao->orientacao = $registro->orientacao;
                $itensdeinspecao->eventosSistema = 'Item avaliado Remotamente por Websgi em ' . date('d/m/Y', strtotime($dtnow)) . '.';
                $itensdeinspecao->reincidencia = $reinc;
                $itensdeinspecao->consequencias = $consequencias;
                $itensdeinspecao->orientacao = $orientacao;
                $itensdeinspecao->codVerificacaoAnterior = $codVerificacaoAnterior;
                $itensdeinspecao->numeroGrupoReincidente = $numeroGrupoReincidente;
                $itensdeinspecao->numeroItemReincidente = $numeroItemReincidente;
                $itensdeinspecao->update();
            }
//                      Final  do teste Extravio Responsabilidade Definida


//      INICIO 231-2
            if((($registro->numeroGrupoVerificacao == 57)&&($registro->numeroDoTeste == 1))
                || (($registro->numeroGrupoVerificacao == 117)&&($registro->numeroDoTeste == 1))
                || (($registro->numeroGrupoVerificacao == 79)&&($registro->numeroDoTeste == 1))
                || (($registro->numeroGrupoVerificacao == 303)&&($registro->numeroDoTeste == 1))
                || (($registro->numeroGrupoVerificacao == 90)&&($registro->numeroDoTeste == 1))
                || (($registro->numeroGrupoVerificacao == 221)&&($registro->numeroDoTeste == 1))
                || (($registro->numeroGrupoVerificacao == 231)&&($registro->numeroDoTeste == 2)) ) {

                $reincidencia = DB::table('snci')
                    ->select('no_inspecao',   'no_grupo',  'no_item','dt_fim_inspecao','dt_inic_inspecao')
                    ->where([['descricao_item',  'like', '%Auto de Irregularidade%']])
                    ->where([['sto', '=', $registro->sto ]])
                    ->orderBy('no_inspecao' ,'desc')
                    ->first();

                if ($reincidencia === null){
                    $reinc = 'Não';
                    $codVerificacaoAnterior = null;
                    $numeroGrupoReincidente = null;
                    $numeroItemReincidente = null;
                }
                else{
                    $reinc = 'Sim';
                    $codVerificacaoAnterior = $reincidencia->no_inspecao;
                    $numeroGrupoReincidente = $reincidencia->no_grupo;
                    $numeroItemReincidente = $reincidencia->no_item;
                }
                DB::table('itensdeinspecoes')
                    ->where([['inspecao_id', '=', $registro->inspecao_id]])
                    ->where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                    ->update([
                        'codVerificacaoAnterior' => $codVerificacaoAnterior,
                        'numeroGrupoReincidente' => $numeroGrupoReincidente,
                        'numeroItemReincidente' => $numeroItemReincidente,
                        'reincidencia' => $reinc
                    ]);
            }
//      fim 231-2

//      INICIO 232-1
            if((($registro->numeroGrupoVerificacao == 59)&&($registro->numeroDoTeste == 1))
                || (($registro->numeroGrupoVerificacao == 232)&&($registro->numeroDoTeste == 1)) ) {

                $reincidencia = DB::table('snci')
                    ->select('no_inspecao',   'no_grupo',  'no_item','dt_fim_inspecao','dt_inic_inspecao')
                    ->where([['descricao_item',  'like', '%numerário da unidade é recolhido no final do fechamento do dia anterior e mantido no cofre%']])
                    ->where([['sto', '=', $registro->sto ]])
                    ->orderBy('no_inspecao' ,'desc')
                    ->first();

                if ($reincidencia === null){
                    $reinc = 'Não';
                    $codVerificacaoAnterior = null;
                    $numeroGrupoReincidente = null;
                    $numeroItemReincidente = null;
                }
                else{
                    $reinc = 'Sim';
                    $codVerificacaoAnterior = $reincidencia->no_inspecao;
                    $numeroGrupoReincidente = $reincidencia->no_grupo;
                    $numeroItemReincidente = $reincidencia->no_item;
                }
                DB::table('itensdeinspecoes')
                    ->where([['inspecao_id', '=', $registro->inspecao_id]])
                    ->where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                    ->update([
                        'codVerificacaoAnterior' => $codVerificacaoAnterior,
                        'numeroGrupoReincidente' => $numeroGrupoReincidente,
                        'numeroItemReincidente' => $numeroItemReincidente,
                        'reincidencia' => $reinc
                    ]);
            }
//      FIM 232-1


//      INICIO 232-2
            if((($registro->numeroGrupoVerificacao == 59)&&($registro->numeroDoTeste == 2))
                || (($registro->numeroGrupoVerificacao == 232)&&($registro->numeroDoTeste == 2)) ) {

                $reincidencia = DB::table('snci')
                    ->select('no_inspecao',   'no_grupo',  'no_item','dt_fim_inspecao','dt_inic_inspecao')
                    ->where([['descricao_item',  'like', '%utiliza corretamente todos mecanismos de proteção disponíveis no cofre%']])
                    ->where([['sto', '=', $registro->sto ]])
                    ->orderBy('no_inspecao' ,'desc')
                    ->first();

                if ($reincidencia === null){
                    $reinc = 'Não';
                    $codVerificacaoAnterior = null;
                    $numeroGrupoReincidente = null;
                    $numeroItemReincidente = null;
                }
                else{
                    $reinc = 'Sim';
                    $codVerificacaoAnterior = $reincidencia->no_inspecao;
                    $numeroGrupoReincidente = $reincidencia->no_grupo;
                    $numeroItemReincidente = $reincidencia->no_item;
                }
                $afected = DB::table('itensdeinspecoes')
                    ->where([['inspecao_id', '=', $registro->inspecao_id]])
                    ->where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                    ->update([
                        'codVerificacaoAnterior' => $codVerificacaoAnterior,
                        'numeroGrupoReincidente' => $numeroGrupoReincidente,
                        'numeroItemReincidente' => $numeroItemReincidente,
                        'reincidencia' => $reinc
                    ]);
            }
//      FIM 232-2

//  Inicio 232-3  do teste Alarme Arme/desarme
            if((($registro->numeroGrupoVerificacao == 206 )&&($registro->numeroDoTeste == 1 ))
                || (( $registro->numeroGrupoVerificacao == 335 ) && ( $registro->numeroDoTeste == 1 ))
                || (( $registro->numeroGrupoVerificacao == 2 ) && ( $registro->numeroDoTeste == 1 ))
                || (( $registro->numeroGrupoVerificacao == 30 ) && ( $registro->numeroDoTeste == 1 ))
                || (( $registro->numeroGrupoVerificacao == 59 ) && ( $registro->numeroDoTeste == 3 ))
                || (( $registro->numeroGrupoVerificacao == 119 ) && ( $registro->numeroDoTeste == 1 ))
                || (( $registro->numeroGrupoVerificacao == 309 ) && ( $registro->numeroDoTeste == 1 ))
                || (($registro->numeroGrupoVerificacao == 232)&&($registro->numeroDoTeste == 3 ))
                || (( $registro->numeroGrupoVerificacao == 272 ) && ( $registro->numeroDoTeste == 2 ))) {

                $now=$dtnow->format('Y-m-d');
                $rowAberturaFinalSemana=0;
                $tempoDesarme='';
                $tempoDePermanencia=0;
                $acessosEmFeriados='';
                $dataultimoevento='';
                $aviso='';
                $diferencaAbertura ='';
                $tempoAbertura ='';//armazena tempo de abertura menor que o previsto
                $riscoAbertura ='';//armazena risco abertura fora do horário de atendimento
                $rowtempoAbertura=0;
                $rowriscoAbertura=0;
                $rowtempoAberturaAntecipada=0;
                $rowtempoAberturaPosExpediente=0;
                $tempoAberturaPosExpediente='';
                $tempoAberturaAntecipada='';
                $naoMonitorado='';
                $acessos_final_semana ='';
                $codVerificacaoAnterior = null;
                $numeroGrupoReincidente = null;
                $numeroItemReincidente = null;
                $evidencia = null;
                $valorSobra = null;
                $valorFalta = null;
                $valorRisco = null;
                $total = 0;
                $pontuado = null;
                $itemQuantificado = 'Não';
                $reincidente = 0;
                $reinc = 'Não';
                $dtmin = $dtnow;
                $count = 0;
                $naoMonitorado = null;
                $evidencia = null;

                $preVerificar = DB::table('testesdeverificacao')
                    ->select('testesdeverificacao.*' )
                    ->where([['teste',  'like', '%alarme funciona corretamente%']])
                    ->get();

                foreach($preVerificar as $pre){
                    DB::table('testesdeverificacao')
                        ->where([['id',  '=', $pre->id]])
                        ->update([
                            'preVerificar' => 'Não'
                        ]);
                }

                //verifica histórico de inspeções
                $reincidencia = DB::table('snci')
                    ->select('no_inspecao', 'no_grupo', 'no_item', 'dt_fim_inspecao', 'dt_inic_inspecao')
                    ->where([['descricao_item', 'like', '%sistema de alarme funciona corretamente e permanece ativado%']])
                    ->where([['sto', '=', $registro->sto]])
                    ->orderBy('no_inspecao', 'desc')
                    ->first();

                try {
                    if ($reincidencia->no_inspecao > 1) {
//                                        dd($reincidencia);
                        $reincidente = 1;
                        $reinc = 'Sim';
                        $codVerificacaoAnterior = $reincidencia->no_inspecao;
                        $numeroGrupoReincidente = $reincidencia->no_grupo;
                        $numeroItemReincidente = $reincidencia->no_item;
                        $reincidencia_dt_fim_inspecao = new Carbon($reincidencia->dt_fim_inspecao);
                        $reincidencia_dt_inic_inspecao = new Carbon($reincidencia->dt_inic_inspecao);
                        $reincidencia_dt_fim_inspecao->subMonth(3);
                        $reincidencia_dt_inic_inspecao->subMonth(3);
                        //se houver registros de inspeções anteriores  consulta  com range  entre datas

//                                        ############   Finais de semana  #################
                        $alarmesFinalSemana = DB::table('alarmes')
                            ->select('alarmes.*')
                            ->where('mcu', '=', $registro->mcu)
                            ->where('data', '>=', $reincidencia_dt_fim_inspecao)
                            ->whereIn('diaSemana', [0, 6])
                            ->orderBy('data' ,'asc')
                            ->orderBy('hora' ,'asc')
                            ->get();

//                                  ############   Feriados #################
                        $feriadoporUnidades = DB::table('unidades')
                            ->join('unidade_enderecos', 'unidades.mcu', '=', 'unidade_enderecos.mcu')
                            ->join('feriados', 'unidade_enderecos.cidade', '=', 'feriados.nome_municipio')
                            ->select(
                                'feriados.*'
                            )
                            ->where([['unidades.mcu', '=', $registro->mcu]])
                            ->where('data_do_feriado', '>=', $reincidencia_dt_fim_inspecao)
                            ->get();


                        $eventosf = DB::table('alarmes')
                            ->select('alarmes.*')
                            ->where('mcu', '=', $registro->mcu)
                            ->where('data', '>=', $reincidencia_dt_fim_inspecao)
                            ->where('armedesarme', '=', 'Desarme')
                            ->orderBy('data' ,'asc')
                            ->orderBy('hora' ,'asc')
                            ->get();

//                                  ############   Início Acessos fora do Padrão   #################
                        $eventos = DB::table('alarmes')
                            ->select('alarmes.*')
                            ->where('mcu', '=', $registro->mcu)
                            ->where('data', '>', $reincidencia_dt_fim_inspecao)
                            ->orderBy('data' ,'asc')
                            ->orderBy('hora' ,'asc')
                            ->get();

                    }
                    else{

//                                        ############   Finais de semana  #################
                        $alarmesFinalSemana = DB::table('alarmes')
                            ->select('alarmes.*')
                            ->where('mcu', '=', $registro->mcu)
                            ->where('data', '>', $dtmenos12meses)
                            ->whereIn('diaSemana', [0, 6])
                            ->orderBy('data' ,'asc')
                            ->orderBy('hora' ,'asc')
                            ->get();

                        ############   Feriados #################
                        $feriadoporUnidades = DB::table('unidades')
                            ->join('unidade_enderecos', 'unidades.mcu', '=', 'unidade_enderecos.mcu')
                            ->join('feriados', 'unidade_enderecos.cidade', '=', 'feriados.nome_municipio')
                            ->select(
                                'feriados.*'
                            )
                            ->where([['unidades.mcu', '=', $registro->mcu]])
                            ->where('data_do_feriado', '>=', $dtmenos12meses)
                            ->get();

                        $eventosf = DB::table('alarmes')
                            ->select('alarmes.*')
                            ->where('mcu', '=', $registro->mcu)
                            ->where('data', '>=', $dtmenos12meses)
                            ->where('armedesarme', '=', 'Desarme')
                            ->orderBy('data' ,'asc')
                            ->orderBy('hora' ,'asc')
                            ->get();
//                                  ############   Início Acessos fora do Padrão   #################
                        $eventos = DB::table('alarmes')
                            ->select('alarmes.*')
                            ->where('mcu', '=', $registro->mcu)
                            ->where('data', '>=', $dtmenos12meses)
                            ->where('armedesarme', '=', 'Desarme')
                            ->orderBy('data' ,'asc')
                            ->orderBy('hora' ,'asc')
                            ->get();
                    }
                }

                catch (\Exception $e) {

//                              ############   Finais de semana  #################
                    $alarmesFinalSemana = DB::table('alarmes')
                        ->select('alarmes.*')
                        ->where('mcu', '=', $registro->mcu)
                        ->where('data', '>', $dtmenos12meses)
                        ->whereIn('diaSemana', [0, 6])
                        ->orderBy('data' ,'asc')
                        ->orderBy('hora' ,'asc')
                        ->get();

                    ############   Feriados #################
                    $feriadoporUnidades = DB::table('unidades')
                        ->join('unidade_enderecos', 'unidades.mcu', '=', 'unidade_enderecos.mcu')
                        ->join('feriados', 'unidade_enderecos.cidade', '=', 'feriados.nome_municipio')
                        ->select(
                            'feriados.*'
                        )
                        ->where([['unidades.mcu', '=', $registro->mcu]])
                        ->where('data_do_feriado', '>=', $dtmenos12meses)
                        ->get();

                    $eventosf = DB::table('alarmes')
                        ->select('alarmes.*')
                        ->where('mcu', '=', $registro->mcu)
                        ->where('data', '>=', $dtmenos12meses)
                        ->where('armedesarme', '=', 'Desarme')
                        ->orderBy('data' ,'asc')
                        ->orderBy('hora' ,'asc')
                        ->get();

//                              ############   Início Acessos fora do Padrão   #################
                    $eventos = DB::table('alarmes')
                        ->select('alarmes.*')
                        ->where('mcu', '=', $registro->mcu)
                        ->where('data', '>=', $dtmenos12meses)
                        ->where('armedesarme', '=', 'Desarme')
                        ->orderBy('data' ,'asc')
                        ->orderBy('hora' ,'asc')
                        ->get();
                }

                $countFinalsemana = $alarmesFinalSemana->count('id');
                $countFeriados = $eventosf->count('id');
                $countEventos = $eventos->count('id');

                if(($countFinalsemana>=1) || ($countFeriados>=1)|| ($countEventos>=1)){
//                          ############   Início Finais de semana  #################
                    if(! $alarmesFinalSemana->isEmpty()) {
                        $count_alarmesFinalSemana  = $alarmesFinalSemana->count('armedesarme');
                        DB::table('acessos_final_semana')
                            ->where('mcu', '=', $registro->mcu)
                            ->delete();
                        foreach ($alarmesFinalSemana  as $tabela) {
                            if($tabela->armedesarme == 'Desarme' ) {

                                $tempoDesarme = (substr($tabela->hora,0,2)*60)+substr($tabela->hora,3,2);
                                $horaDesarme = $tabela->hora;
                                $dataDesarme = $tabela->data;
                                $eventodesarme = $tabela->armedesarme;
                                $diaEvento = $tabela->diaSemana;
                            }
                            else { // Evento Desarme
//
                                if (( isset($diaEvento) )
                                    && ( $diaEvento == $tabela->diaSemana )
                                    && ( $tabela->armedesarme == 'Arme' )) {

                                    try{
                                        $horaFechamento = $tabela->hora;
                                        $tempoArme = (substr($tabela->hora, 0, 2) * 60) + substr($tabela->hora, 3, 2);
                                        $tempoDePermanencia = $tempoArme - $tempoDesarme;
                                        $h = intdiv($tempoDePermanencia, 60);
                                        $m = $tempoDePermanencia % 60;
                                    }
                                    catch (\Exception $e){
                                        $h = 0;
                                        $m = 0;
                                    }

                                    if ($tabela->diaSemana == 6) {
                                        $diasemana = 'Sábado';
                                    }
                                    else {
                                        $diasemana = 'Domingo';
                                    }
                                    $peranencia = ($h < 10 ? '0' . $h : $h) . ':' . ($m < 10 ? '0' . $m : $m);
                                    $dataDesarme = date("d/m/Y", strtotime($dataDesarme));

                                    $acessoFinalSemana = new AcessoFinalSemana();
                                    $acessoFinalSemana->mcu = $registro->mcu;
                                    $acessoFinalSemana->evAbertura = $eventodesarme;
                                    $acessoFinalSemana->evDataAbertura = $dataDesarme;
                                    $acessoFinalSemana->evHoraAbertura = $horaDesarme;
                                    $acessoFinalSemana->evFechamento = $tabela->armedesarme;
                                    $acessoFinalSemana->evHoraFechamento = $horaFechamento;
                                    $acessoFinalSemana->diaSemana = $diasemana;
                                    $acessoFinalSemana->tempoPermanencia = $peranencia;

                                    if ($registro->trabalha_sabado =='Não') {
                                        $acessoFinalSemana->save();
                                        $rowAberturaFinalSemana++;
                                    }
                                    elseif ($registro->trabalha_domingo =='Não') {
                                        $acessoFinalSemana->save();
                                        $rowAberturaFinalSemana++;
                                    }
                                }
                            }
                        }
                        if ( $rowAberturaFinalSemana >= 1 ) {
                            $acessos_final_semana = DB::table('acessos_final_semana')
                                ->where('mcu',  '=', $registro->mcu)
                                ->select(
                                    'acessos_final_semana.*'
                                )
                                ->get();
                        }
                        else { $acessos_final_semana = '';}
                    }
//                          ############   Final Finais de semana  #################

//                          ############   Início Feriados  #################
                    $row = 1;
                    foreach ($feriadoporUnidades  as $feriadoporUnidade) {
                        foreach ($eventosf  as $evento) {
                            if($feriadoporUnidade->data_do_feriado   ==  $evento->data) {
                                $acessosEmFeriados = ([
                                    $row => ['Acesso' => Carbon::parse($evento->data)->format('d/m/Y'), 'hora' => $evento->hora],
                                ]);

                            }
                            $row++;
                        }
                    }
//                          ############   Final Feriados   #################

//                          ############   Início Acessos fora do Padrão   #################
                    if(! $eventos->isEmpty()) {
                        $dtmax = $eventos->max('data');
                        if((isset($registro->inicio_expediente)) && (!empty($registro->inicio_expediente))
                            ||(isset($registro->final_expediente)) && (!empty($registro->final_expediente)) ) {

                            $minutosinicioExpediente = (substr($registro->inicio_expediente,0,2)*60)+substr($registro->inicio_expediente,3,2);
                            $minutosfinalExpediente = (substr($registro->final_expediente,0,2)*60)+substr($registro->final_expediente,3,2);
                            foreach ($eventos  as $evento) {
                                $eventominutos = (substr($evento->hora,0,2)*60)+substr($evento->hora,3,2);
                                if ($evento->armedesarme =='Desarme' ) {

                                    if (($eventominutos < ($minutosinicioExpediente-90))) {

                                        $diferencaAbertura =  $minutosinicioExpediente - $eventominutos;

                                        if($diferencaAbertura <0) {
                                            $diferencaAbertura = $diferencaAbertura *-1;
                                        }
                                        $h = intdiv ($diferencaAbertura,60);
                                        if ($h<10) {
                                            $h='0'.$h;
                                        }
                                        $m = $diferencaAbertura % 60;
                                        if ($m<10) {
                                            $m='0'.$m;
                                        }
                                        $diferencaAbertura = $h.':'.$m.':'.substr($evento->hora,6,2);
                                        $tempoAbertura = ([
                                            $rowtempoAbertura => ['dataInicioExpediente' => $evento->data,
                                                'InicioExpediente' => $registro->inicio_expediente,
                                                'HorárioDeAbertura' => $evento->hora,
                                                'DiferencaTempoDeAbertura' => $diferencaAbertura],
                                        ]);
                                        $rowtempoAbertura++;
                                    }
                                    ///////////////////////   TEMPO DE ABERTURA   //////////////////////////

                                    ///////////////////////   risco  DE ABERTURA   //////////////////////////
                                    if (($eventominutos < ($minutosinicioExpediente-120))){
                                        $diferencaAbertura =  $minutosinicioExpediente - $eventominutos;
                                        $h = intdiv ($diferencaAbertura,60);
                                        if ($h<10){
                                            $h='0'.$h;
                                        }
                                        $m = $diferencaAbertura % 60;
                                        if ($m<10){
                                            $m='0'.$m;
                                        }
                                        $diferencaAbertura = $h.':'.$m.':'.substr($evento->hora,6,2);
                                        $tempoAberturaAntecipada = ([
                                            $rowtempoAbertura => ['dataInicioExpediente' => $evento->data,
                                                'InicioExpediente' => $registro->inicio_expediente,
                                                'HorárioDeAbertura' => $evento->hora,
                                                'DiferencaTempoDeAbertura' => $diferencaAbertura],
                                        ]);
                                        $rowtempoAberturaAntecipada++;
                                    }
                                    if (($eventominutos > ($minutosfinalExpediente+50))) {
                                        $diferencaAbertura =  $eventominutos - $minutosfinalExpediente;
                                        $h = intdiv ($diferencaAbertura,60);
                                        if ($h<10) {
                                            $h='0'.$h;
                                        }
                                        $m = $diferencaAbertura % 60;
                                        if ($m<10) {
                                            $m='0'.$m;
                                        }
                                        $diferencaAbertura = $h.':'.$m.':'.substr($evento->hora,6,2);
                                        $tempoAberturaPosExpediente = ([
                                            $rowtempoAbertura => ['dataFinalExpediente' => $evento->data,
                                                'FinalExpediente' => $registro->final_expediente,
                                                'HorárioDeAbertura' => $evento->hora,
                                                'DiferencaTempoDeAbertura' => $diferencaAbertura],
                                        ]);
                                        $rowtempoAberturaPosExpediente++;
                                    }
                                }
                                $periodo = CarbonPeriod::create($dtmax ,  $now );

                                $dataultimoevento = date('d/m/Y', strtotime($evento->data));

                                if ($periodo->count()>=15) {
                                    $aviso = 'a) Não foi possível avaliar eventos recente da utilização do alarme monitorado dado que a unidade não está sendo monitorada há '. $periodo->count().' dias. Incluindo a data da Inspeção. Adicionalmente verificaram que o último evento transmitido foi no dia ' .$dataultimoevento. '.';
                                }
                            }
                        }
                        else {
                            $avaliacao = 'Não Verificado';
                            $oportunidadeAprimoramento = 'A Base de Dados da Unidade não está atualizada, atualize os registros da Unidade principalmente  horários de funcionamento. ';
                            $consequencias = $registro->consequencias;
                            $orientacao = $registro->orientacao;
                        }

                    }

                    if(($rowAberturaFinalSemana==0) && ($row ==0) && ($rowtempoAbertura==0) && ($rowtempoAberturaAntecipada ==0) && ($rowtempoAberturaPosExpediente ==0) ) {
                        $maxdata = DB::table('alarmes')
                            ->where('mcu', '=', $registro->mcu )
                            ->max('data');
                        if(!empty($maxdata )) {
                            $dataultimoevento = date('d/m/Y', strtotime($evento->data));
                        }
                        else {
                            $dataultimoevento = 'data não localizada nos parâmetros dessa pesquisa de inspeção';
                        }
                        $naoMonitorado = 'Não foi possível avaliar eventos recente da utilização do alarme monitorado dado que a unidade não está sendo monitorada. Aicionalmente verificaram que o último evento transmitido foi em ' .$dataultimoevento. '.';
                    }
//                          ############   Final Acessos fora do Padrão    #################

                }
                else{
                    $avaliacao = 'Não Verificado';
                    $oportunidadeAprimoramento = 'Não há registro de eventos de alarme na base de dados para avaliar a unidade.';

                    $consequencias = $registro->consequencias;
                    $orientacao = $registro->orientacao;

                }
//dd($registro);
                if ( !empty($naoMonitorado) ){
                    $avaliacao = 'Não Conforme';
                    $oportunidadeAprimoramento = 'Em análise aos dados do Relatório de “Arme e Desarme” do Sistema de Alarme do período de '. date('d/m/Y', strtotime($dtmenos12meses)) .' a ' . date('d/m/Y', strtotime($now)) .' , constatou-se que:';
                    $evidencia = $naoMonitorado;

                    $consequencias = $registro->consequencias;
                    $orientacao = $registro->orientacao;

                }
                else  if( ($rowAberturaFinalSemana >= 1) ||  (isset($tempoAbertura)&&(!empty($tempoAbertura))) || (isset($tempoAberturaPosExpediente)&&(!empty($tempoAberturaPosExpediente))) || (isset($acessosEmFeriados)&&(!empty($acessosEmFeriados))) || (isset($tempoAberturaAntecipada)&&(!empty($tempoAberturaAntecipada)))  ){
                    $avaliacao = 'Não Conforme';
                    $oportunidadeAprimoramento = 'Em análise aos dados do Relatório de “Arme e Desarme” do Sistema de Alarme em '. date('d/m/Y', strtotime($now)) . ' referente ao período de ' . date('d/m/Y', strtotime($dtmenos12meses)) . ' a '. date('d/m/Y', strtotime($now)) .', constatou-se que o sistema permaneceu desativado e fora de funcionamento nos períodos relacionados a seguir:';

                    $consequencias = $registro->consequencias;
                    $orientacao = $registro->orientacao;
                }
                else{
                    $avaliacao = 'Conforme';
                    $oportunidadeAprimoramento = 'Em análise aos dados do Relatório de “Arme e Desarme” do Sistema de Alarme em '. date('d/m/Y', strtotime($now)) . ' referente ao período de ' . date('d/m/Y', strtotime($dtmenos12meses)) . ' a '. date('d/m/Y', strtotime($now)) .', constatou-se que o sistema de alarme permaneceu ativado quando dos horários fora de atendimento inclusive finais de semana.:';
                    $orientacao = null;
                    $consequencias = null;

                }

                if($rowAberturaFinalSemana >= 1){
                    $evidencia = $evidencia."\n" . $rowAberturaFinalSemana .' - Ocorrências de desativação do alarme em períodos de finais de semana conforme a seguir:';
                    $evidencia = $evidencia . "\n" . 'Evento Abertura' . "\t" . 'Data Abertura' . "\t" . 'Hora Abertura' . "\t" . 'Evento Fechamento' . "\t" . 'Hora Fechamento' . "\t" . 'Dia Semana' . "\t" . 'Tempo Permanência';
                    foreach ($acessos_final_semana as $tabela){
                        $evidencia = $evidencia  . "\n" . $tabela->evAbertura
                            . "\t" . $tabela->evDataAbertura
                            . "\t" . $tabela->evHoraAbertura
                            . "\t" . $tabela->evFechamento
                            . "\t" . $tabela->evHoraFechamento
                            . "\t" . $tabela->diaSemana
                            . "\t" . $tabela->tempoPermanencia;
                    }
                }

                if(isset($tempoAbertura)&&(!empty($tempoAbertura))){

                    $evidencia = $evidencia."\n\n" .'Tempo de abertura em Relação ao Horário de Atendimento conforme a seguir:';
                    $evidencia = $evidencia . "\n" . 'Data' . "\t" . 'Horário Atendimento' . "\t" . 'Horário da Abertura' . "\t" . 'Tempo Abertura';
                    foreach ($tempoAbertura  as $tempo => $mdaData){
                        $evidencia = $evidencia . "\n"
                            . date('d/m/Y', strtotime($mdaData["dataInicioExpediente"]))
                            . "\t" . $mdaData["InicioExpediente"] . "\t" . $mdaData["HorárioDeAbertura"]
                            . "\t" . $mdaData["DiferencaTempoDeAbertura"];
                    }

                }

                if(isset($tempoAberturaAntecipada)&&(!empty($tempoAberturaAntecipada))){

                    $evidencia = $evidencia."\n\n" .' - Unidade em Risco. Abertura da Unidade em horário fora do padrão em relação ao horário de abertura da unidade conforme a seguir';
                    $evidencia = $evidencia . "\n" . 'Data' . "\t" . 'Data Abertura' . "\t" . 'Horário de Atendimento' . "\t" . 'Hora da Abertura' . "\t" . 'Tempo Abertura';
                    foreach ($tempoAberturaAntecipada  as $tempo => $mdaData){

                        $evidencia = $evidencia . "\n"
                            . date('d/m/Y', strtotime($mdaData["dataInicioExpediente"]))
                            . "\t" . $mdaData["InicioExpediente"] . "\t" . $mdaData["HorárioDeAbertura"]
                            . "\t" . $mdaData["DiferencaTempoDeAbertura"];

                    }
                }

                if(isset($tempoAberturaPosExpediente)&&(!empty($tempoAberturaPosExpediente))){
                    $evidencia = $evidencia."\n\n".'Unidade em Risco. Abertura da unidade em horário fora do padrão em relação ao horário de fechamento da unidade conforme a seguir:';
                    $evidencia = $evidencia
                        . "\n" . 'Data'
                        . "\t" . 'Horário Fechamento'
                        . "\t" . 'Horário da Abertura'
                        . "\t" . 'Tempo Abertura';

                    foreach ($tempoAberturaPosExpediente  as $tempo => $mdaData){
                        $evidencia = $evidencia . "\n"
                            . date('d/m/Y', strtotime($mdaData["dataFinalExpediente"]))
                            . "\t" . $mdaData["FinalExpediente"]
                            . "\t" . $mdaData["HorárioDeAbertura"]
                            . "\t" . $mdaData["DiferencaTempoDeAbertura"];
                    }
                }

                if(isset($acessosEmFeriados)&&(!empty($acessosEmFeriados))){
                    $evidencia = $evidencia."\n\n".'Unidade em Risco. Abertura da unidade em dia de feriado conforme a seguir:';
                    $evidencia = $evidencia
                        . "\n" . 'Data'
                        . "\t" . 'Hora';
                    foreach ($acessosEmFeriados  as $acessosEmFeriado => $mdaData){
                        $evidencia = $evidencia . "\n"
                            . date('d/m/Y', strtotime($mdaData["Acesso"]))
                            . "\t" . $mdaData["hora"];
                    }
                }

                if(isset($aviso)&&(!empty($aviso))){
                    $evidencia = $evidencia . "\n". $aviso;
                }

                $quebra = DB::table('relevancias')
                    ->select('valor_final')
                    ->where('fator_multiplicador', '=', 1)
                    ->first();
                $quebracaixa = $quebra->valor_final * 0.1;

                if( $valorFalta > $quebracaixa){
                    $fm = DB::table('relevancias')
                        ->select('fator_multiplicador', 'valor_final', 'valor_inicio')
                        ->where('valor_inicio', '<=', $total)
                        ->orderBy('valor_final', 'desc')
                        ->first();
                    $pontuado = $registro->totalPontos * $fm->fator_multiplicador;
                }
                else{
                    if($avaliacao == 'Não Conforme') $pontuado = $registro->totalPontos * 1;
                }
                $dto = DB::table('itensdeinspecoes')
                    ->Where([['inspecao_id', '=', $registro->inspecao_id]])
                    ->Where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                    ->select('itensdeinspecoes.*')
                    ->first();
                $itensdeinspecao = Itensdeinspecao::find($dto->id);
                $itensdeinspecao->avaliacao = $avaliacao;
                $itensdeinspecao->oportunidadeAprimoramento = $oportunidadeAprimoramento;
                $itensdeinspecao->evidencia = $evidencia;
                $itensdeinspecao->valorFalta = $valorFalta;
                $itensdeinspecao->valorSobra = $valorSobra;
                $itensdeinspecao->valorRisco = $valorRisco;
//                $itensdeinspecao->situacao = 'Inspecionado';
                $itensdeinspecao->situacao   = 'Em Inspeção'; // presencial/remota
                $itensdeinspecao->pontuado = $pontuado;
                $itensdeinspecao->itemQuantificado = $itemQuantificado;
                $itensdeinspecao->orientacao = $registro->orientacao;
                $itensdeinspecao->eventosSistema = 'Item avaliado Remotamente por Websgi em ' . date('d/m/Y', strtotime($dtnow)) . '.';
                $itensdeinspecao->reincidencia = $reinc;
                $itensdeinspecao->consequencias = $consequencias;
                $itensdeinspecao->orientacao = $orientacao;
                $itensdeinspecao->codVerificacaoAnterior = $codVerificacaoAnterior;
                $itensdeinspecao->numeroGrupoReincidente = $numeroGrupoReincidente;
                $itensdeinspecao->numeroItemReincidente = $numeroItemReincidente;
                $itensdeinspecao->update();
            }
//  Final  232-3 do teste Alarme Arme/desarme

// Início 232-4  teste Compartilhamento de Senhas
            if((($registro->numeroGrupoVerificacao==206) && ($registro->numeroDoTeste==2))
                || (($registro->numeroGrupoVerificacao==335) && ($registro->numeroDoTeste==2))
                || (($registro->numeroGrupoVerificacao==59) && ($registro->numeroDoTeste==4))
                || (($registro->numeroGrupoVerificacao==119) && ($registro->numeroDoTeste==2))
                || (($registro->numeroGrupoVerificacao==97) && ($registro->numeroDoTeste==2))
                || (($registro->numeroGrupoVerificacao==232) && ($registro->numeroDoTeste==4))
                || (($registro->numeroGrupoVerificacao==272) && ($registro->numeroDoTeste==3))) {

                $aviso = null;
                $periodo = array();
                $codVerificacaoAnterior = null;
                $numeroGrupoReincidente = null;
                $numeroItemReincidente = null;
                $evidencia = null;
                $valorSobra = null;
                $valorFalta = null;
                $valorRisco = null;
                $total = 0;
                $pontuado = null;
                $itemQuantificado = 'Não';
                $reincidente = 0;
                $reinc = 'Não';
                $dtmin = $dtnow;
                $count = 0;
                $naoMonitorado = null;

                $preVerificar = DB::table('testesdeverificacao')
                    ->select('testesdeverificacao.*' )
                    ->where([['teste',  'like', '%As senhas do sistema de alarme são pessoais%']])
                    ->get();

                foreach($preVerificar as $pre){
                    DB::table('testesdeverificacao')
                        ->where([['id',  '=', $pre->id]])
                        ->update([
                            'preVerificar' => 'Não'
                        ]);
                }

                $reincidencia = DB::table('snci')
                    ->select('no_inspecao', 'no_grupo', 'no_item', 'dt_fim_inspecao', 'dt_inic_inspecao')
                    ->where([['descricao_item', 'like', '%As senhas do sistema de alarme são pessoais%']])
                    ->where([['sto', '=', $registro->sto]])
                    ->orderBy('no_inspecao', 'desc')
                    ->first();

                try {
                    if ($reincidencia->no_inspecao > 1) {
//                                        dd($reincidencia);
                        $reincidente = 1;
                        $reinc = 'Sim';
                        $codVerificacaoAnterior = $reincidencia->no_inspecao;
                        $numeroGrupoReincidente = $reincidencia->no_grupo;
                        $numeroItemReincidente = $reincidencia->no_item;
                        $reincidencia_dt_fim_inspecao = new Carbon($reincidencia->dt_fim_inspecao);
                        $reincidencia_dt_inic_inspecao = new Carbon($reincidencia->dt_inic_inspecao);
                        $reincidencia_dt_fim_inspecao->subMonth(3);
                        $reincidencia_dt_inic_inspecao->subMonth(3);
                        //se houver registros de inspeções anteriores  consulta  com range  entre datas

//                                        ############   Eventos   #################
                        $eventos = DB::table('alarmes')
                            ->select('alarmes.*')
                            ->where('mcu', '=', $registro->mcu)
                            ->where('data', '>', $reincidencia_dt_fim_inspecao)
                            ->orderBy('data', 'asc')
                            ->orderBy('hora', 'asc')
                            ->get();

                    } else {
//                                  ############   Eventos  #################
                        $eventos = DB::table('alarmes')
                            ->select('alarmes.*')
                            ->where('mcu', '=', $registro->mcu)
                            ->where('data', '>', $dtmenos12meses)
                            ->orderBy('data', 'asc')
                            ->orderBy('hora', 'asc')
                            ->get();
                    }

                } catch (\Exception $e) {
//                                  ############   Eventos  #################
                    $eventos = DB::table('alarmes')
                        ->select('alarmes.*')
                        ->where('mcu', '=', $registro->mcu)
                        ->where('data', '>', $dtmenos12meses)
                        ->orderBy('data', 'asc')
                        ->orderBy('hora', 'asc')
                        ->get();
                }

                if ($eventos->isEmpty()) {
                    $naoMonitorado = 'Não foi possível confrontar os dados obtidos com as informações de férias e afastamentos, com objetivo de identificar possíveis compartilhamentos de  senha recente da utilização do alarme monitorado. Dado que, a unidade não está sendo monitorada há pelo menos 12 meses. Adicionalmente, registra-se que foi pesquisado  a partir do dia ' . date("d/m/Y", strtotime($dtmenos12meses)) . '.';
                } else {

                    $dtmax = $eventos->max('data');
                    $periodo = CarbonPeriod::create($dtmax, $dtnow);
                    $dataultimoevento = Carbon::parse($dtmax)->format('d/m/Y');
                    $insp = Inspecao::find($registro->inspecao_id);
                    if ($periodo->count() >= 15) $aviso = 'a) A unidade inspecionada não está
                                    sendo monitorada há '
                        . $periodo->count() . ' dias. Adicionalmente, verificaram que o
                                         último evento transmitido foi no dia '
                        . $dataultimoevento . '.';

                    // Se tem dados de alarme obter a lista de ferias por empregados da unidade
                    $ferias_por_mcu = DB::table('ferias_por_mcu')
                        ->join('cadastral', 'ferias_por_mcu.matricula', '=', 'cadastral.matricula')
                        ->select('ferias_por_mcu.*', 'cadastral.mcu')
                        ->where([['cadastral.mcu', '=', $registro->mcu]])
                        ->where([['ferias_por_mcu.inicio_fruicao', '<>', null]])
                        ->where([['ferias_por_mcu.inicio_fruicao', '>', $dtmenos12meses]])
                        ->orderBy('ferias_por_mcu.inicio_fruicao', 'asc')
                        ->get();

                    if (!$ferias_por_mcu->isEmpty()) {

                        foreach ($ferias_por_mcu as $ferias) {

                            $inicio_fruicao = Carbon::parse($ferias->inicio_fruicao)->format('Y-m-d');
                            $termino_fruicao = Carbon::parse($ferias->termino_fruicao)->format('Y-m-d');


//                            dd($insp, $registro  , '4577');
                            $compartilhaSenha = DB::table('compartilhasenhas')
                                ->where('codigo', '=', $insp->codigo)
                                ->where('numeroGrupoVerificacao', '=', $registro->numeroGrupoVerificacao)
                                ->where('numeroDoTeste', '=', $registro->numeroDoTeste)
                                ->delete();

                            $res = DB::table('alarmes')
                                ->select('alarmes.*')
                                ->where([['mcu', '=', $registro->mcu]])
                                ->where([['matricula', '=', $ferias->matricula]])
                                ->whereBetween('data', [$inicio_fruicao, $termino_fruicao])
                                ->orderBy('data', 'asc')
                                ->orderBy('hora', 'asc')
                                ->get();

                            if ($res->count('matricula') >= 1) {

                                $motivo = 'Férias';
                                foreach ($res as $dado) {
                                    $compartilhaSenha = new CompartilhaSenha();
                                    $compartilhaSenha->codigo = $insp->codigo;
                                    $compartilhaSenha->numeroGrupoVerificacao = $registro->numeroGrupoVerificacao;
                                    $compartilhaSenha->numeroDoTeste = $registro->numeroDoTeste;
                                    $compartilhaSenha->matricula = $dado->matricula;
                                    $compartilhaSenha->evento = $dado->armedesarme;
                                    $compartilhaSenha->data = $dado->data;
                                    $compartilhaSenha->tipoafastamento = $motivo;
                                    $compartilhaSenha->save();
                                }
                            }
                        }
                    }
                    // Se tem dados de alarme obter a lista de absenteísmo por empregados da unidade
                    $frequencias = DB::table('absenteismos')
                        ->join('cadastral', 'absenteismos.matricula', '=', 'cadastral.matricula')
                        ->select('absenteismos.*', 'cadastral.mcu')
                        ->where([['cadastral.mcu', '=', $registro->mcu]])
                        ->where([['data_evento', '>', $dtmenos12meses]])
                        ->whereBetween('data_evento', [$dtmenos12meses, $dtnow])
                        ->get();

                    if (!$frequencias->isEmpty()) {

                        foreach ($frequencias as $frequencia) {

                            $dt = new Carbon($frequencia->data_evento);

                            if ($frequencia->dias > 1) {

                                $dt = $dt->addDays($frequencia->dias);
                                $dt = $dt->format('Y-m-d');
                            }

                            $res = DB::table('alarmes')
                                ->select('alarmes.*')
                                ->where([['mcu', '=', $registro->mcu]])
                                ->where([['matricula', '=', $frequencia->matricula]])
                                ->whereBetween('data', [$frequencia->data_evento, $dt])
                                ->orderBy('data', 'asc')
                                ->orderBy('hora', 'asc')
                                ->get();

                            if (!$res->isEmpty()) {
                                foreach ($res as $dado) {
                                    $compartilhaSenha = new CompartilhaSenha();
                                    $compartilhaSenha->codigo = $registro->codigo;
                                    $compartilhaSenha->numeroGrupoVerificacao = $registro->numeroGrupoVerificacao;
                                    $compartilhaSenha->numeroDoTeste = $registro->numeroDoTeste;
                                    $compartilhaSenha->matricula = $dado->matricula;
                                    $compartilhaSenha->evento = $dado->armedesarme;
                                    $compartilhaSenha->data = $dado->data;
                                    $compartilhaSenha->tipoafastamento = $frequencia->motivo;
                                    $compartilhaSenha->save();
                                }
                            }
                        }
                    }
                    $insp = Inspecao::find($registro->inspecao_id);
                    $compartilhaSenhas = DB::table('compartilhasenhas')
                        ->where('codigo', '=', $insp->codigo)
                        ->where('numeroGrupoVerificacao', '=', $registro->numeroGrupoVerificacao)
                        ->where('numeroDoTeste', '=', $registro->numeroDoTeste)
                        ->orderBy('data', 'asc')
                        ->get();

                    $count = $compartilhaSenhas->count('codigo');
                    $dtmax = Carbon::parse($eventos->max('data'))->format('d/m/Y');
                } //tem dados de alarme
                if ($count == null) $compartilhaSenhas = null;
                if ($count >= 1) {
                    $avaliacao = 'Não Conforme';
                    $oportunidadeAprimoramento = 'Em análise aos dados do Relatório de Arme/Desarme do Sistema de Alarme ' . date("d/m/Y", strtotime($dtmenos12meses)) . ' a ' . date("d/m/Y", strtotime($dtnow)) . ' , constatou-se a utilização da senha de empregado em que no período mencionado não se encontrava na unidade. O que indicava a prática de compartilhamento de senha de alarme para acesso à unidade. Encontraram ' . $count . ' - ocorrência(s) em períodos oficiais de ausência do trabalho conforme a seguir:';
                    $evidencia = $evidencia . "\n"
                        . 'Evento' . "\t"
                        . 'Matricula' . "\t"
                        . 'Data' . "\t"
                        . 'Tipo Afastamento';
                    foreach ($compartilhaSenhas as $compartilhaSenha) {
                        $evidencia = $evidencia . "\n"
                            . $compartilhaSenha->evento . "\t"
                            . $compartilhaSenha->matricula . "\t"
                            . date('d/m/Y', strtotime($compartilhaSenha->data)) . "\t"
                            . $compartilhaSenha->tipoafastamento;
                    }
                    $consequencias = $registro->consequencias;
                    $orientacao = $registro->orientacao;
                }
                else{
                    $avaliacao = 'Conforme';
                    $oportunidadeAprimoramento = 'Em análise aos dados do Relatório de Arme/Desarme do Sistema de Alarme, do Controle de Férias CEGEP e do Sistema PGP. Período de ' . date( 'd/m/Y' , strtotime($dtmenos12meses)).' a ' .date( 'd/m/Y' , strtotime($dtnow)).', constatou-se que não havia indícios de prática de compartilhamento de senha de alarme para acesso à unidade.';
                    $evidencia = null;
                    $orientacao = null;
                    $consequencias = null;
                }
                if (!empty($naoMonitorado)) {
                    $avaliacao = 'Não Conforme';
                    $oportunidadeAprimoramento = 'Em análise aos dados do Relatório de “Arme e Desarme” do Sistema de Alarme do período ' . date("d/m/Y", strtotime($dtmenos12meses)) . ' a ' . date("d/m/Y", strtotime($dtnow)) . ' , constatou-se que:';
                    $evidencia = $naoMonitorado;
                    $consequencias = $registro->consequencias;
                    $orientacao = $registro->orientacao;
                }

                $quebra = DB::table('relevancias')
                    ->select('valor_final')
                    ->where('fator_multiplicador', '=', 1)
                    ->first();
                $quebracaixa = $quebra->valor_final * 0.1;
                if( $valorFalta > $quebracaixa){
                    $fm = DB::table('relevancias')
                        ->select('fator_multiplicador', 'valor_final', 'valor_inicio')
                        ->where('valor_inicio', '<=', $total)
                        ->orderBy('valor_final', 'desc')
                        ->first();
                    $pontuado = $registro->totalPontos * $fm->fator_multiplicador;
                }
                else{
                    if($avaliacao == 'Não Conforme') $pontuado = $registro->totalPontos * 1;
                }
                $dto = DB::table('itensdeinspecoes')
                    ->Where([['inspecao_id', '=', $registro->inspecao_id]])
                    ->Where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                    ->select('itensdeinspecoes.*')
                    ->first();
                $itensdeinspecao = Itensdeinspecao::find($dto->id);
                $itensdeinspecao->avaliacao = $avaliacao;
                $itensdeinspecao->oportunidadeAprimoramento = $oportunidadeAprimoramento;
                $itensdeinspecao->evidencia = $evidencia;
                $itensdeinspecao->valorFalta = $valorFalta;
                $itensdeinspecao->valorSobra = $valorSobra;
                $itensdeinspecao->valorRisco = $valorRisco;

//                $itensdeinspecao->situacao = 'Inspecionado';
                $itensdeinspecao->situacao   = 'Em Inspeção'; // presencial/remota

                $itensdeinspecao->pontuado = $pontuado;
                $itensdeinspecao->itemQuantificado = $itemQuantificado;
                $itensdeinspecao->orientacao = $registro->orientacao;
                $itensdeinspecao->eventosSistema = 'Item avaliado Remotamente por Websgi em ' . date('d/m/Y', strtotime($dtnow)) . '.';
                $itensdeinspecao->reincidencia = $reinc;
                $itensdeinspecao->consequencias = $consequencias;
                $itensdeinspecao->orientacao = $orientacao;
                $itensdeinspecao->codVerificacaoAnterior = $codVerificacaoAnterior;
                $itensdeinspecao->numeroGrupoReincidente = $numeroGrupoReincidente;
                $itensdeinspecao->numeroItemReincidente = $numeroItemReincidente;
                $itensdeinspecao->update();


            }
//   Final 232-4 teste Compartilhamento de Senhas

//      INICIO 232-5
            if((($registro->numeroGrupoVerificacao == 59)&&($registro->numeroDoTeste == 5))
                || (($registro->numeroGrupoVerificacao == 2)&&($registro->numeroDoTeste == 4))
                || (($registro->numeroGrupoVerificacao == 309)&&($registro->numeroDoTeste == 4))
                || (($registro->numeroGrupoVerificacao == 30)&&($registro->numeroDoTeste == 4))
                || (($registro->numeroGrupoVerificacao == 206)&&($registro->numeroDoTeste == 4))
                || (($registro->numeroGrupoVerificacao == 232)&&($registro->numeroDoTeste == 5)) ) {

                $reincidencia = DB::table('snci')
                    ->select('no_inspecao',   'no_grupo',  'no_item','dt_fim_inspecao','dt_inic_inspecao')
                    ->where([['descricao_item',  'like', '%sensores do sistema de alarme cobrem%']])
                    ->where([['sto', '=', $registro->sto ]])
                    ->orderBy('no_inspecao' ,'desc')
                    ->first();

                if ($reincidencia === null){
                    $reinc = 'Não';
                    $codVerificacaoAnterior = null;
                    $numeroGrupoReincidente = null;
                    $numeroItemReincidente = null;
                }
                else{
                    $reinc = 'Sim';
                    $codVerificacaoAnterior = $reincidencia->no_inspecao;
                    $numeroGrupoReincidente = $reincidencia->no_grupo;
                    $numeroItemReincidente = $reincidencia->no_item;
                }
                DB::table('itensdeinspecoes')
                    ->where([['inspecao_id', '=', $registro->inspecao_id]])
                    ->where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                    ->update([
                        'codVerificacaoAnterior' => $codVerificacaoAnterior,
                        'numeroGrupoReincidente' => $numeroGrupoReincidente,
                        'numeroItemReincidente' => $numeroItemReincidente,
                        'reincidencia' => $reinc
                    ]);
            }

//      FIM 232-5

// Início 232-6 teste CFTV - NAO INSPEÇÃO AUTOMÁTICA
            if((($registro->numeroGrupoVerificacao==206) && ($registro->numeroDoTeste==3))
                || (($registro->numeroGrupoVerificacao==335) && ($registro->numeroDoTeste==3))
                || (($registro->numeroGrupoVerificacao==2) && ($registro->numeroDoTeste==5))
                || (($registro->numeroGrupoVerificacao==30) && ($registro->numeroDoTeste==5))
                || (($registro->numeroGrupoVerificacao==59) && ($registro->numeroDoTeste==6))
                || (($registro->numeroGrupoVerificacao==119) && ($registro->numeroDoTeste==3))
                || (($registro->numeroGrupoVerificacao==309) && ($registro->numeroDoTeste==5))
                || (($registro->numeroGrupoVerificacao==97) && ($registro->numeroDoTeste==5))
                || (($registro->numeroGrupoVerificacao==232) && ($registro->numeroDoTeste==6))
                || (($registro->numeroGrupoVerificacao==272) && ($registro->numeroDoTeste==4))) {
                $reincidencia = DB::table('snci')
                    ->select('no_inspecao',   'no_grupo',  'no_item','dt_fim_inspecao','dt_inic_inspecao')
                    ->where([['descricao_item',  'like', '%CFTV está funcionando perfeitamente%']])
                    ->where([['sto', '=', $registro->sto ]])
                    ->orderBy('no_inspecao' ,'desc')
                    ->first();

                if ($reincidencia === null){
                    $reinc = 'Não';
                    $codVerificacaoAnterior = null;
                    $numeroGrupoReincidente = null;
                    $numeroItemReincidente = null;
                }
                else{
                    $reinc = 'Sim';
                    $codVerificacaoAnterior = $reincidencia->no_inspecao;
                    $numeroGrupoReincidente = $reincidencia->no_grupo;
                    $numeroItemReincidente = $reincidencia->no_item;
                }
                DB::table('itensdeinspecoes')
                    ->where([['inspecao_id', '=', $registro->inspecao_id]])
                    ->where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                    ->update([
                        'codVerificacaoAnterior' => $codVerificacaoAnterior,
                        'numeroGrupoReincidente' => $numeroGrupoReincidente,
                        'numeroItemReincidente' => $numeroItemReincidente,
                        'reincidencia' => $reinc
                    ]);
            }
// Final 232-6 teste CFTV

// Início  233-1 teste Controle de Bens Móveis
            if((($registro->numeroGrupoVerificacao == 60)&&($registro->numeroDoTeste == 1))
                || (($registro->numeroGrupoVerificacao == 120)&&($registro->numeroDoTeste == 1))
                || (($registro->numeroGrupoVerificacao == 306)&&($registro->numeroDoTeste == 1))
                || (($registro->numeroGrupoVerificacao == 207)&&($registro->numeroDoTeste == 1))
                || (($registro->numeroGrupoVerificacao == 94)&&($registro->numeroDoTeste == 1))
                || (($registro->numeroGrupoVerificacao == 233)&&($registro->numeroDoTeste == 1)) ) {

                $reincidencia = DB::table('snci')
                    ->select('no_inspecao',   'no_grupo',  'no_item','dt_fim_inspecao','dt_inic_inspecao')
                    ->where([['descricao_item',  'like', '%dados atualizados no TRP%']])
                    ->where([['sto', '=', $registro->sto ]])
                    ->orderBy('no_inspecao' ,'desc')
                    ->first();

                if ($reincidencia === null){
                    $reinc = 'Não';
                    $codVerificacaoAnterior = null;
                    $numeroGrupoReincidente = null;
                    $numeroItemReincidente = null;
                }
                else{
                    $reinc = 'Sim';
                    $codVerificacaoAnterior = $reincidencia->no_inspecao;
                    $numeroGrupoReincidente = $reincidencia->no_grupo;
                    $numeroItemReincidente = $reincidencia->no_item;
                }
                DB::table('itensdeinspecoes')
                    ->where([['inspecao_id', '=', $registro->inspecao_id]])
                    ->where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                    ->update([
                        'codVerificacaoAnterior' => $codVerificacaoAnterior,
                        'numeroGrupoReincidente' => $numeroGrupoReincidente,
                        'numeroItemReincidente' => $numeroItemReincidente,
                        'reincidencia' => $reinc
                    ]);


            }
            // FIM  233-1 teste Controle de Bens Móveis


// Início  233-2
            if((($registro->numeroGrupoVerificacao == 60)&&($registro->numeroDoTeste == 2))
                || (($registro->numeroGrupoVerificacao == 120)&&($registro->numeroDoTeste == 2))
                || (($registro->numeroGrupoVerificacao == 306)&&($registro->numeroDoTeste == 2))
                || (($registro->numeroGrupoVerificacao == 207)&&($registro->numeroDoTeste == 2))
                || (($registro->numeroGrupoVerificacao == 94)&&($registro->numeroDoTeste == 2))
                || (($registro->numeroGrupoVerificacao == 233)&&($registro->numeroDoTeste == 2)) ) {

                $reincidencia = DB::table('snci')
                    ->select('no_inspecao',   'no_grupo',  'no_item','dt_fim_inspecao','dt_inic_inspecao')
                    ->where([['descricao_item',  'like', '%bens da unidade estão em uso%']])
                    ->where([['sto', '=', $registro->sto ]])
                    ->orderBy('no_inspecao' ,'desc')
                    ->first();

                if ($reincidencia === null){
                    $reinc = 'Não';
                    $codVerificacaoAnterior = null;
                    $numeroGrupoReincidente = null;
                    $numeroItemReincidente = null;
                }
                else{
                    $reinc = 'Sim';
                    $codVerificacaoAnterior = $reincidencia->no_inspecao;
                    $numeroGrupoReincidente = $reincidencia->no_grupo;
                    $numeroItemReincidente = $reincidencia->no_item;
                }
                DB::table('itensdeinspecoes')
                    ->where([['inspecao_id', '=', $registro->inspecao_id]])
                    ->where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                    ->update([
                        'codVerificacaoAnterior' => $codVerificacaoAnterior,
                        'numeroGrupoReincidente' => $numeroGrupoReincidente,
                        'numeroItemReincidente' => $numeroItemReincidente,
                        'reincidencia' => $reinc
                    ]);

            }
// fim  233-2
//
            // inicio  234-1
            if((($registro->numeroGrupoVerificacao==61) && ($registro->numeroDoTeste==1))
                || (($registro->numeroGrupoVerificacao == 234)&&($registro->numeroDoTeste == 1)) ){

                $reincidencia = DB::table('snci')
                    ->select('no_inspecao',   'no_grupo',  'no_item','dt_fim_inspecao','dt_inic_inspecao')
                    ->where([['descricao_item',  'like', '%imóvel que abriga a unidade está em condições de conservação%']])
                    ->where([['sto', '=', $registro->sto ]])
                    ->orderBy('no_inspecao' ,'desc')
                    ->first();

                if ($reincidencia === null){
                    $reinc = 'Não';
                    $codVerificacaoAnterior = null;
                    $numeroGrupoReincidente = null;
                    $numeroItemReincidente = null;
                }
                else{
                    $reinc = 'Sim';
                    $codVerificacaoAnterior = $reincidencia->no_inspecao;
                    $numeroGrupoReincidente = $reincidencia->no_grupo;
                    $numeroItemReincidente = $reincidencia->no_item;
                }
                DB::table('itensdeinspecoes')
                    ->where([['inspecao_id', '=', $registro->inspecao_id]])
                    ->where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                    ->update([
                        'codVerificacaoAnterior' => $codVerificacaoAnterior,
                        'numeroGrupoReincidente' => $numeroGrupoReincidente,
                        'numeroItemReincidente' => $numeroItemReincidente,
                        'reincidencia' => $reinc
                    ]);
            }
            // fim  234-1

            // inicio  235-1
            if(($registro->numeroGrupoVerificacao==62) && ($registro->numeroDoTeste==1))
                if((($registro->numeroGrupoVerificacao==86) && ($registro->numeroDoTeste==3))
                    || (($registro->numeroGrupoVerificacao==294) && ($registro->numeroDoTeste==4))
                    || (($registro->numeroGrupoVerificacao==228) && ($registro->numeroDoTeste==3))
                    || (($registro->numeroGrupoVerificacao==44) && ($registro->numeroDoTeste==6))
                    || (($registro->numeroGrupoVerificacao==89) && ($registro->numeroDoTeste==3))
                    || (($registro->numeroGrupoVerificacao==235) && ($registro->numeroDoTeste==1))) {
                    dd('debug 2885');
                    $reincidencia = DB::table('snci')
                        ->select('no_inspecao',   'no_grupo',  'no_item','dt_fim_inspecao','dt_inic_inspecao')
                        ->where([['descricao_item',  'like', '%cumpre os procedimentos de aceitação%']])
                        ->where([['sto', '=', $registro->sto ]])
                        ->orderBy('no_inspecao' ,'desc')
                        ->first();

                    if ($reincidencia === null){
                        $reinc = 'Não';
                        $codVerificacaoAnterior = null;
                        $numeroGrupoReincidente = null;
                        $numeroItemReincidente = null;
                    }
                    else{
                        $reinc = 'Sim';
                        $codVerificacaoAnterior = $reincidencia->no_inspecao;
                        $numeroGrupoReincidente = $reincidencia->no_grupo;
                        $numeroItemReincidente = $reincidencia->no_item;
                    }
                    DB::table('itensdeinspecoes')
                        ->where([['inspecao_id', '=', $registro->inspecao_id]])
                        ->where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                        ->update([
                            'codVerificacaoAnterior' => $codVerificacaoAnterior,
                            'numeroGrupoReincidente' => $numeroGrupoReincidente,
                            'numeroItemReincidente' => $numeroItemReincidente,
                            'reincidencia' => $reinc
                        ]);

                    dd( ' DEBUG -linha 2915 INSPEÇÃO - >  '
                        . $registro->inspecao_id
                        .'  ITEM DA INSPEÇÃO  testeVerificacao_id - >  '
                        .$registro->testeVerificacao_id.'  reincidencia - >  '
                        .$reinc, '  DTO -> ',$dto , '  DTO -> $itensdeinspecao ->',$itensdeinspecao  ,$registro, );

                }

            // fim  235-1

// inicio  235-2
            if(($registro->numeroGrupoVerificacao==235) && ($registro->numeroDoTeste==2)) {

                $reincidencia = DB::table('snci')
                    ->select('no_inspecao',   'no_grupo',  'no_item','dt_fim_inspecao','dt_inic_inspecao')
                    ->where([['descricao_item',  'like', '%assinaturas dos clientes nos comprovantes para confirmação das postagens%']])
                    ->where([['sto', '=', $registro->sto ]])
                    ->orderBy('no_inspecao' ,'desc')
                    ->first();

                if ($reincidencia === null){
                    $reinc = 'Não';
                    $codVerificacaoAnterior = null;
                    $numeroGrupoReincidente = null;
                    $numeroItemReincidente = null;
                }
                else{
                    $reinc = 'Sim';
                    $codVerificacaoAnterior = $reincidencia->no_inspecao;
                    $numeroGrupoReincidente = $reincidencia->no_grupo;
                    $numeroItemReincidente = $reincidencia->no_item;
                }
                DB::table('itensdeinspecoes')
                    ->where([['inspecao_id', '=', $registro->inspecao_id]])
                    ->where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                    ->update([
                        'codVerificacaoAnterior' => $codVerificacaoAnterior,
                        'numeroGrupoReincidente' => $numeroGrupoReincidente,
                        'numeroItemReincidente' => $numeroItemReincidente,
                        'reincidencia' => $reinc
                    ]);

            }
// fim  235-2

            if(($registro->numeroGrupoVerificacao==235) && ($registro->numeroDoTeste==3)) {
                $reincidencia = DB::table('snci')
                    ->select('no_inspecao',   'no_grupo',  'no_item','dt_fim_inspecao','dt_inic_inspecao')
                    ->where([['descricao_item',  'like', '%tarifados de forma correta%']])
                    ->where([['sto', '=', $registro->sto ]])
                    ->orderBy('no_inspecao' ,'desc')
                    ->first();

                if ($reincidencia === null){
                    $reinc = 'Não';
                    $codVerificacaoAnterior = null;
                    $numeroGrupoReincidente = null;
                    $numeroItemReincidente = null;
                }
                else{
                    $reinc = 'Sim';
                    $codVerificacaoAnterior = $reincidencia->no_inspecao;
                    $numeroGrupoReincidente = $reincidencia->no_grupo;
                    $numeroItemReincidente = $reincidencia->no_item;
                }
                DB::table('itensdeinspecoes')
                    ->where([['inspecao_id', '=', $registro->inspecao_id]])
                    ->where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                    ->update([
                        'codVerificacaoAnterior' => $codVerificacaoAnterior,
                        'numeroGrupoReincidente' => $numeroGrupoReincidente,
                        'numeroItemReincidente' => $numeroItemReincidente,
                        'reincidencia' => $reinc
                    ]);
            }


// Início teste PLPs Pendentes
            if((($registro->numeroGrupoVerificacao==212) && ($registro->numeroDoTeste==2))
                || (($registro->numeroGrupoVerificacao==350) && ($registro->numeroDoTeste==1))
                || (($registro->numeroGrupoVerificacao==235) && ($registro->numeroDoTeste==4))
                || (($registro->numeroGrupoVerificacao==274) && ($registro->numeroDoTeste==1))) {

                $codVerificacaoAnterior = null;
                $numeroGrupoReincidente = null;
                $numeroItemReincidente = null;
                $evidencia = null;
                $valorSobra = null;
                $valorFalta = null;
                $valorRisco = null;
                $total = 0;
                $pontuado = null;
                $itemQuantificado = 'Não';
                $reincidente = 0;
                $reinc = 'Não';
                $dtmin = $dtnow;
                $count = 0;

                $preVerificar = DB::table('testesdeverificacao')
                    ->select('testesdeverificacao.*' )
                    ->where([['teste',  'like',  '%constantes nas PLP%']])
                    ->get();

                foreach($preVerificar as $pre){
                    DB::table('testesdeverificacao')
                        ->where([['id',  '=', $pre->id]])
                        ->update([
                            'preVerificar' => 'Não'
                        ]);
                }

                $reincidencia = DB::table('snci')
                    ->select('no_inspecao', 'no_grupo', 'no_item', 'dt_fim_inspecao', 'dt_inic_inspecao')
                    ->where([['descricao_item', 'like', '%constantes nas PLP%']])
                    ->where([['sto', '=', $registro->sto]])
                    ->orderBy('no_inspecao', 'desc')
                    ->first();

                try {
                    if ($reincidencia->no_inspecao > 1) {
//                                        dd($reincidencia);
                        $reincidente = 1;
                        $reinc = 'Sim';
                        $codVerificacaoAnterior = $reincidencia->no_inspecao;
                        $numeroGrupoReincidente = $reincidencia->no_grupo;
                        $numeroItemReincidente = $reincidencia->no_item;
                        $reincidencia_dt_fim_inspecao = new Carbon($reincidencia->dt_fim_inspecao);
                        $reincidencia_dt_inic_inspecao = new Carbon($reincidencia->dt_inic_inspecao);
//                                        $reincidencia_dt_fim_inspecao->subMonth(3);
//                                        $reincidencia_dt_inic_inspecao->subMonth(3);
                        //se houver registros de inspeções anteriores  consulta  com data superior ao termino da inspeção reincidente
                        $plplistapendentes = DB::table('plplistapendentes')
                            ->select( 'plplistapendentes.*' )
                            ->where([['stomcu', '=',  $registro->mcu  ]])
                            ->where([['dh_lista_postagem', '>=',  $reincidencia_dt_fim_inspecao ]])
                            ->get();
                    } else {

                        $plplistapendentes = DB::table('plplistapendentes')
                            ->select( 'plplistapendentes.*' )
                            ->where([['stomcu', '=',  $registro->mcu  ]])
                            ->get();
                    }
                } catch (\Exception $e) {

                    $plplistapendentes = DB::table('plplistapendentes')
                        ->select( 'plplistapendentes.*' )
                        ->where([['stomcu', '=',  $registro->mcu  ]])
                        ->get();

                }

                if( !empty($plplistapendentes->dh_lista_postagem )) {

                    $count = $plplistapendentes->count('lista');
                    $dtfim = $plplistapendentes->max('dh_lista_postagem');
                }
                else {
                    $dtfim = Carbon::now();
                }

                if ($count >= 1) {

                    if (! $plplistapendentes->isEmpty()) {

                        $avaliacao = 'Não Conforme';
                        $oportunidadeAprimoramento = 'Em análise à Relação de Listas Pendentes do sistema SARA, consulta em ' .date( 'd/m/Y' , strtotime($dtfim)).', e aos eventos registrados no sistema SRO, constatou-se as inconsistências relacionadas a seguir:';
                        $evidencia = $evidencia . "\n"
                            . 'Lista' . "\t"
                            . 'PLP' . "\t"
                            . 'Cliente' . "\t"
                            . 'Data da Postagem' . "\t"
                            . 'Situação';

                        foreach($plplistapendentes as $plplistapendente) {
                            $evidencia = $evidencia . "\n"
                                . $plplistapendente->lista . "\t"
                                . $plplistapendente->plp . "\t"
                                . $plplistapendente->objeto . "\t"
                                . $plplistapendente->cliente . "\t"
                                . $plplistapendente->matricula . "\t"
                                . (isset($plplistapendente->dh_lista_postagem) && $plplistapendente->dh_lista_postagem == '' ? '   ----------  ' : date( 'd/m/Y' , strtotime($plplistapendente->dh_lista_postagem))) . "\t"
                                . 'Falta de Conferencia ou Sem Contabilização';
                        }
                        $consequencias = $registro->consequencias;
                        $orientacao = $registro->orientacao;
                    }
                }
                else {
                    $avaliacao = 'Conforme';
                    $oportunidadeAprimoramento = 'Em análise à Relação de Listas Pendentes do sistema SARA, Planilha disponibilizada em \sac3063\INSTITUCIONAL\DIOPE\DERAT\PUBLICO\GMAT_pub\LISTA_PENDENTE, planilha acessada em data anterior à ' . date( 'd/m/Y' , strtotime($dtfim)). ' e aos eventos registrados no sistema SRO, constatou-se que não havia pendência para a unidade inspecionada.';
                    $evidencia = null;
                    $orientacao = null;
                    $consequencias = null;
                }

                $quebra = DB::table('relevancias')
                    ->select('valor_final')
                    ->where('fator_multiplicador', '=', 1)
                    ->first();
                $quebracaixa = $quebra->valor_final * 0.1;

                if( $valorFalta > $quebracaixa){

                    $fm = DB::table('relevancias')
                        ->select('fator_multiplicador', 'valor_final', 'valor_inicio')
                        ->where('valor_inicio', '<=', $total)
                        ->orderBy('valor_final', 'desc')
                        ->first();
                    $pontuado = $registro->totalPontos * $fm->fator_multiplicador;
                }
                else{

                    if($avaliacao == 'Não Conforme') $pontuado = $registro->totalPontos * 1;
                }

                $dto = DB::table('itensdeinspecoes')
                    ->Where([['inspecao_id', '=', $registro->inspecao_id]])
                    ->Where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                    ->select('itensdeinspecoes.*')
                    ->first();

                $itensdeinspecao = Itensdeinspecao::find($dto->id);
                $itensdeinspecao->avaliacao = $avaliacao;
                $itensdeinspecao->oportunidadeAprimoramento = $oportunidadeAprimoramento;
                $itensdeinspecao->evidencia = $evidencia;
                $itensdeinspecao->valorFalta = $valorFalta;
                $itensdeinspecao->valorSobra = $valorSobra;
                $itensdeinspecao->valorRisco = $valorRisco;

//                $itensdeinspecao->situacao = 'Inspecionado';
                $itensdeinspecao->situacao   = 'Em Inspeção'; // presencial/remota

                $itensdeinspecao->pontuado = $pontuado;
                $itensdeinspecao->itemQuantificado = $itemQuantificado;
                $itensdeinspecao->orientacao = $registro->orientacao;
                $itensdeinspecao->eventosSistema = 'Item avaliado Remotamente por Websgi em ' . date('d/m/Y', strtotime($dtnow)) . '.';
                $itensdeinspecao->reincidencia = $reinc;
                $itensdeinspecao->consequencias = $consequencias;
                $itensdeinspecao->orientacao = $orientacao;
                $itensdeinspecao->codVerificacaoAnterior = $codVerificacaoAnterior;
                $itensdeinspecao->numeroGrupoReincidente = $numeroGrupoReincidente;
                $itensdeinspecao->numeroItemReincidente = $numeroItemReincidente;
                $itensdeinspecao->update();

            }
// Final teste PLPs Pendentes

            if(($registro->numeroGrupoVerificacao==236) && ($registro->numeroDoTeste==1)) {
                $reincidencia = DB::table('snci')
                    ->select('no_inspecao',   'no_grupo',  'no_item','dt_fim_inspecao','dt_inic_inspecao')
                    ->where([['descricao_item',  'like', '%Caixas Postais em uso%']])
                    ->where([['sto', '=', $registro->sto ]])
                    ->orderBy('no_inspecao' ,'desc')
                    ->first();

                if ($reincidencia === null){
                    $reinc = 'Não';
                    $codVerificacaoAnterior = null;
                    $numeroGrupoReincidente = null;
                    $numeroItemReincidente = null;
                }
                else{
                    $reinc = 'Sim';
                    $codVerificacaoAnterior = $reincidencia->no_inspecao;
                    $numeroGrupoReincidente = $reincidencia->no_grupo;
                    $numeroItemReincidente = $reincidencia->no_item;
                }
                DB::table('itensdeinspecoes')
                    ->where([['inspecao_id', '=', $registro->inspecao_id]])
                    ->where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                    ->update([
                        'codVerificacaoAnterior' => $codVerificacaoAnterior,
                        'numeroGrupoReincidente' => $numeroGrupoReincidente,
                        'numeroItemReincidente' => $numeroItemReincidente,
                        'reincidencia' => $reinc
                    ]);
            }


//        entrega interna
            if(($registro->numeroGrupoVerificacao==237) && ($registro->numeroDoTeste==1)) {
                $reincidencia = DB::table('snci')
                    ->select('no_inspecao',   'no_grupo',  'no_item','dt_fim_inspecao','dt_inic_inspecao')
                    ->where([['descricao_item',  'like', '%cumpre os procedimentos referentes a entrega interna%']])
                    ->where([['sto', '=', $registro->sto ]])
                    ->orderBy('no_inspecao' ,'desc')
                    ->first();

                if ($reincidencia === null){
                    $reinc = 'Não';
                    $codVerificacaoAnterior = null;
                    $numeroGrupoReincidente = null;
                    $numeroItemReincidente = null;
                }
                else{
                    $reinc = 'Sim';
                    $codVerificacaoAnterior = $reincidencia->no_inspecao;
                    $numeroGrupoReincidente = $reincidencia->no_grupo;
                    $numeroItemReincidente = $reincidencia->no_item;
                }
                DB::table('itensdeinspecoes')
                    ->where([['inspecao_id', '=', $registro->inspecao_id]])
                    ->where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                    ->update([
                        'codVerificacaoAnterior' => $codVerificacaoAnterior,
                        'numeroGrupoReincidente' => $numeroGrupoReincidente,
                        'numeroItemReincidente' => $numeroItemReincidente,
                        'reincidencia' => $reinc
                    ]);

            }

            if(($registro->numeroGrupoVerificacao==238) && ($registro->numeroDoTeste==1)) {
                $reincidencia = DB::table('snci')
                    ->select('no_inspecao',   'no_grupo',  'no_item','dt_fim_inspecao','dt_inic_inspecao')
                    ->where([['descricao_item',  'like', '%Código de Defesa do Consumidor%']])
                    ->where([['sto', '=', $registro->sto ]])
                    ->orderBy('no_inspecao' ,'desc')
                    ->first();

                if ($reincidencia === null){
                    $reinc = 'Não';
                    $codVerificacaoAnterior = null;
                    $numeroGrupoReincidente = null;
                    $numeroItemReincidente = null;
                }
                else{
                    $reinc = 'Sim';
                    $codVerificacaoAnterior = $reincidencia->no_inspecao;
                    $numeroGrupoReincidente = $reincidencia->no_grupo;
                    $numeroItemReincidente = $reincidencia->no_item;
                }
                DB::table('itensdeinspecoes')
                    ->where([['inspecao_id', '=', $registro->inspecao_id]])
                    ->where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                    ->update([
                        'codVerificacaoAnterior' => $codVerificacaoAnterior,
                        'numeroGrupoReincidente' => $numeroGrupoReincidente,
                        'numeroItemReincidente' => $numeroItemReincidente,
                        'reincidencia' => $reinc
                    ]);

            }

// Inicio Controle de viagem
            if((($registro->numeroGrupoVerificacao==200) && ($registro->numeroDoTeste==1))
                || (($registro->numeroGrupoVerificacao==330) && ($registro->numeroDoTeste==1))
                || (($registro->numeroGrupoVerificacao==287) && ($registro->numeroDoTeste==2))
                || (($registro->numeroGrupoVerificacao==222) && ($registro->numeroDoTeste==4))
                || (($registro->numeroGrupoVerificacao==239) && ($registro->numeroDoTeste==1))
                || (($registro->numeroGrupoVerificacao==276) && ($registro->numeroDoTeste==1))) {


                $codVerificacaoAnterior = null;
                $numeroGrupoReincidente = null;
                $numeroItemReincidente = null;
                $evidencia = null;
                $valorSobra = null;
                $valorFalta = null;
                $valorRisco = null;
                $total = 0;
                $pontuado = null;
                $itemQuantificado = 'Não';
                $reincidente = 0;
                $reinc = 'Não';
                $dtmin = $dtnow;
                $count = 0;

                $dtfim = $dtnow;
                $dtini = $dtmenos3meses;
                $reg=0;
                $evidencia = null;
                $orientacao = null;
                $consequencias = null;

                $preVerificar = DB::table('testesdeverificacao')
                    ->select('testesdeverificacao.*' )
                    ->where([['teste',  'like', '%embarque e desembarque%']])
                    ->get();

                foreach($preVerificar as $pre){
                    DB::table('testesdeverificacao')
                        ->where([['id',  '=', $pre->id]])
                        ->update([
                            'preVerificar' => 'Não'
                        ]);
                }
                $reincidencia = DB::table('snci')
                    ->select('no_inspecao', 'no_grupo', 'no_item', 'dt_fim_inspecao', 'dt_inic_inspecao')
                    ->where([['descricao_item', 'like', '%embarque e desembarque%']])
                    ->where([['sto', '=', $registro->sto]])
                    ->orderBy('no_inspecao', 'desc')
                    ->first();

                try {
                    if ($reincidencia->no_inspecao > 1) {
//                                        dd($reincidencia);
                        $reincidente = 1;
                        $reinc = 'Sim';
                        $periodo = new CarbonPeriod();
                        $codVerificacaoAnterior = $reincidencia->no_inspecao;
                        $numeroGrupoReincidente = $reincidencia->no_grupo;
                        $numeroItemReincidente = $reincidencia->no_item;
                        $reincidencia_dt_fim_inspecao = new Carbon($reincidencia->dt_fim_inspecao);
                        $reincidencia_dt_inic_inspecao = new Carbon($reincidencia->dt_inic_inspecao);
//                                        $reincidencia_dt_fim_inspecao->subMonth(3);
//                                        $reincidencia_dt_inic_inspecao->subMonth(3);
                        //se houver registros de inspeções anteriores  consulta  com data superior ao termino da inspeção reincidente

                        $controle_de_viagens = DB::table('controle_de_viagens')
                            ->select('controle_de_viagens.*')
                            ->where('ponto_parada', '=', $registro->an8)
                            ->where('inicio_viagem', '>=', $reincidencia_dt_fim_inspecao)
                            ->get();
                        $dtini = $reincidencia_dt_fim_inspecao;
                    } else {
                        $controle_de_viagens = DB::table('controle_de_viagens')
                            ->select('controle_de_viagens.*')
                            ->where('ponto_parada', '=', $registro->an8)
                            ->where('inicio_viagem', '=', $dtmenos3meses)
                            ->get();
                        $dtini = $dtmenos3meses;
                    }
                }
                catch (\Exception $e) {
                    $controle_de_viagens = DB::table('controle_de_viagens')
                        ->select('controle_de_viagens.*')
                        ->where('ponto_parada', '=', $registro->an8)
                        ->where('inicio_viagem', '>=', $dtmenos3meses)
                        ->get();
                    $dtini = $dtmenos3meses;
                }

                if(! $controle_de_viagens->isEmpty()) {
                    $count = $controle_de_viagens->count('ponto_parada');
                    foreach($controle_de_viagens as $dados){
                        if( ( $controle_de_viagen->tipo_de_operacao == '' )
                            || ($controle_de_viagen->quantidade == '')
                            || ($controle_de_viagen->peso == '' )
                            || ($controle_de_viagen->unitizador == '')
                            || ($controle_de_viagen->descricao_do_servico == '')
                            || ($controle_de_viagen->local_de_destino == '')){

                            $reg ++;
                        }
                    }
                    $periodo = CarbonPeriod::create($dtini, $dtfim);
                    $dias = $periodo->count() - 1;
                    if ($dias >= 7) {
                        $dias = intdiv($dias, 7) * 5;
                    } elseif ($dias == 6) {
                        $dias = 5;
                    }

                    $viagens = $dias * 2;
                    $viagemNaorealizada = $viagens - $count;

                    if($reg >=1){

                        $avaliacao = 'Não Conforme';
                        $oportunidadeAprimoramento = ' Em análise aos dados do sistema ERP - GESTÃO DE LINHAS DE TRANSPORTE, constatou-se o descumprimento dos procedimentos de embarque e desembarque da carga, conforme relatado a seguir: ' . $viagens . ' - viagens prevista(s).';

                        $evidencia = $evidencia
                            . "\n"
                            . 'Data Viagem' . "\t"
                            . 'Número da Viagem' . "\t"
                            . 'Tipo Operação' . "\t"
                            . 'Quantidade Unitizadores' . "\t"
                            . 'Peso Unitizadores' . "\t"
                            . 'Tipo Unitizador' . "\t"
                            . 'Tipo Serviço' . "\t"
                            . 'Destino';
                        foreach ($controle_de_viagens as $controle_de_viagen) {
                            $evidencia = $evidencia
                                . "\n"
                                . ($controle_de_viagen->inicio_viagem == '' ? '   ----------  ' : Carbon::parse($controle_de_viagen->inicio_viagem)->format('d/m/Y')) . "\t"
                                . ($controle_de_viagen->controle_viagem == '' ? '   ----------  ' : $controle_de_viagen->controle_viagem) . "\t"
                                . ($controle_de_viagen->tipo_de_operacao == '' ? '   ----------  ' : $controle_de_viagen->tipo_de_operacao) . "\t"
                                . ($controle_de_viagen->quantidade == '' ? '   ----------  ' : $controle_de_viagen->quantidade) . "\t"
                                . ($controle_de_viagen->peso == '' ? '   ----------  ' : $controle_de_viagen->peso) . "\t"
                                . ($controle_de_viagen->unitizador == '' ? '   ----------  ' : $controle_de_viagen->unitizador) . "\t"
                                . ($controle_de_viagen->descricao_do_servico == '' ? '   ----------  ' : $controle_de_viagen->descricao_do_servico) . "\t"
                                . ($controle_de_viagen->local_de_destino == '' ? '   ----------  ' : $controle_de_viagen->local_de_destino);
                        }

                        $evidencia = $evidencia. "\n" . ' Foram verificada(s) todas programações de viagens no período do dia '
                            .  Carbon::parse($dtini)->format('d/m/Y') . ' até ' . Carbon::parse($dtfim)->format('d/m/Y')
                            . "\n" . ' a) verificou-se que houve ' . $count . ' viagen(s) realizadas e com possíveis operações de Embarque/Desembarque a serem realizadas.'
                            . "\n" . ' b) Verificou a necessidade de aprimoramento na qualidade do apontamento colunas com falhas ou informações genéricas/incompletas.';

                        if (intval($viagemNaorealizada / $viagens) >= 10) {
                            // como trabalhamos com previsões se houver 10 % de viagens não realizadas adiciona a letra C.
                            $evidencia = $evidencia  . "\n" . 'c) Adicionalmente no período havia '
                                . $viagens. ' viagen(s) prevista(s) sendo que não houve apontamento de EMBARQUE/DESEMBARQUE para '
                                . $viagemNaorealizada . ' viagens.';
                        }
//
                    }
                    else{
                        $avaliacao = 'Conforme';
                        $oportunidadeAprimoramento = 'Em análise aos dados do sistema ERP - GESTÃO DE LINHAS DE TRANSPORTE, constatou-se que não havia operações de Embarque/Desembarque em falta ou incompletas.' .' Foram verificada(s) todas programações de viagens no período do dia ' .Carbon::parse($dtini)->format('d/m/Y').' até '.Carbon::parse($dtfim)->format('d/m/Y').'.';

                    }

                }
                else  {
                    $avaliacao = 'Não Conforme';
                    $oportunidadeAprimoramento = 'Em análise aos dados do sistema ERP - GESTÃO DE LINHAS DE TRANSPORTE, constatou-se o descumprimento dos procedimentos de embarque e desembarque da carga, conforme relatado a seguir:';
                    $evidencia = $evidencia . "\n" . '- Verificou-se que a unidade não está executando os lançamentos obrigatórios das informações de embarque e desembarque da carga no Sistema ERP. Não há histórico de registro de embarque/desembarque para troca de expedições. - Foram verificadas as programações de viagens no período de ' . Carbon::parse($dtini)->format('d/m/Y') . ' até ' . Carbon::parse($dtfim)->format('d/m/Y') .', sendo que em 100% das viagens não foi encontrado registros de apontamentos de Embarque/Desembarque.';
                }

                $quebra = DB::table('relevancias')
                    ->select('valor_final')
                    ->where('fator_multiplicador', '=', 1)
                    ->first();
                $quebracaixa = $quebra->valor_final * 0.1;

                if( $valorFalta > $quebracaixa){

                    $fm = DB::table('relevancias')
                        ->select('fator_multiplicador', 'valor_final', 'valor_inicio')
                        ->where('valor_inicio', '<=', $total)
                        ->orderBy('valor_final', 'desc')
                        ->first();
                    $pontuado = $registro->totalPontos * $fm->fator_multiplicador;
                }
                else{

                    if($avaliacao == 'Não Conforme') $pontuado = $registro->totalPontos * 1;
                }

                $dto = DB::table('itensdeinspecoes')
                    ->Where([['inspecao_id', '=', $registro->inspecao_id]])
                    ->Where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                    ->select('itensdeinspecoes.*')
                    ->first();
                $itensdeinspecao = Itensdeinspecao::find($dto->id);
                $itensdeinspecao->avaliacao = $avaliacao;
                $itensdeinspecao->oportunidadeAprimoramento = $oportunidadeAprimoramento;
                $itensdeinspecao->evidencia = $evidencia;
                $itensdeinspecao->valorFalta = $valorFalta;
                $itensdeinspecao->valorSobra = $valorSobra;
                $itensdeinspecao->valorRisco = $valorRisco;
//                $itensdeinspecao->situacao = 'Inspecionado';
                $itensdeinspecao->situacao   = 'Em Inspeção'; // presencial/remota
                $itensdeinspecao->pontuado = $pontuado;
                $itensdeinspecao->itemQuantificado = $itemQuantificado;
                $itensdeinspecao->orientacao = $registro->orientacao;
                $itensdeinspecao->eventosSistema = 'Item avaliado Remotamente por Websgi em ' . date('d/m/Y', strtotime($dtnow)) . '.';
                $itensdeinspecao->reincidencia = $reinc;
                $itensdeinspecao->consequencias = $consequencias;
                $itensdeinspecao->orientacao = $orientacao;
                $itensdeinspecao->codVerificacaoAnterior = $codVerificacaoAnterior;
                $itensdeinspecao->numeroGrupoReincidente = $numeroGrupoReincidente;
                $itensdeinspecao->numeroItemReincidente = $numeroItemReincidente;
                $itensdeinspecao->update();

            }
// Final Controle de viagem

            if(($registro->numeroGrupoVerificacao==239) && ($registro->numeroDoTeste==2)) {
                $reincidencia = DB::table('snci')
                    ->select('no_inspecao',   'no_grupo',  'no_item','dt_fim_inspecao','dt_inic_inspecao')
                    ->where([['descricao_item',  'like', '%compactação e mensuração da carga%']])
                    ->where([['sto', '=', $registro->sto ]])
                    ->orderBy('no_inspecao' ,'desc')
                    ->first();

                if ($reincidencia === null){
                    $reinc = 'Não';
                    $codVerificacaoAnterior = null;
                    $numeroGrupoReincidente = null;
                    $numeroItemReincidente = null;
                }
                else{
                    $reinc = 'Sim';
                    $codVerificacaoAnterior = $reincidencia->no_inspecao;
                    $numeroGrupoReincidente = $reincidencia->no_grupo;
                    $numeroItemReincidente = $reincidencia->no_item;
                }
                DB::table('itensdeinspecoes')
                    ->where([['inspecao_id', '=', $registro->inspecao_id]])
                    ->where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                    ->update([
                        'codVerificacaoAnterior' => $codVerificacaoAnterior,
                        'numeroGrupoReincidente' => $numeroGrupoReincidente,
                        'numeroItemReincidente' => $numeroItemReincidente,
                        'reincidencia' => $reinc
                    ]);

            }

            if(($registro->numeroGrupoVerificacao==239) && ($registro->numeroDoTeste==3)) {
                $reincidencia = DB::table('snci')
                    ->select('no_inspecao',   'no_grupo',  'no_item','dt_fim_inspecao','dt_inic_inspecao')
                    ->where([['descricao_item',  'like', '%utilização e a devolução dos unitizadores%']])
                    ->where([['sto', '=', $registro->sto ]])
                    ->orderBy('no_inspecao' ,'desc')
                    ->first();

                if ($reincidencia === null){
                    $reinc = 'Não';
                    $codVerificacaoAnterior = null;
                    $numeroGrupoReincidente = null;
                    $numeroItemReincidente = null;
                }
                else{
                    $reinc = 'Sim';
                    $codVerificacaoAnterior = $reincidencia->no_inspecao;
                    $numeroGrupoReincidente = $reincidencia->no_grupo;
                    $numeroItemReincidente = $reincidencia->no_item;
                }
                DB::table('itensdeinspecoes')
                    ->where([['inspecao_id', '=', $registro->inspecao_id]])
                    ->where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                    ->update([
                        'codVerificacaoAnterior' => $codVerificacaoAnterior,
                        'numeroGrupoReincidente' => $numeroGrupoReincidente,
                        'numeroItemReincidente' => $numeroItemReincidente,
                        'reincidencia' => $reinc
                    ]);

            }

// Inicio SGDO Distribuição
            if((($registro->numeroGrupoVerificacao==201) && ($registro->numeroDoTeste==1))
                || (($registro->numeroGrupoVerificacao==331) && ($registro->numeroDoTeste==1))
                || (($registro->numeroGrupoVerificacao==240) && ($registro->numeroDoTeste==1))
                || (($registro->numeroGrupoVerificacao==277) && ($registro->numeroDoTeste==1))) {

                $codVerificacaoAnterior = null;
                $numeroGrupoReincidente = null;
                $numeroItemReincidente = null;
                $evidencia = null;
                $valorSobra = null;
                $valorFalta = null;
                $valorRisco = null;
                $total = 0;
                $pontuado = null;
                $aviso = null;
                $itemQuantificado = 'Não';
                $reincidente = 0;
                $reinc = 'Não';

                $dtini = $dtmenos120dias;
                $dtfim = $dtnow;
                $count = 0;

                $preVerificar = DB::table('testesdeverificacao')
                    ->select('testesdeverificacao.*' )
                    ->where([['teste',  'like', '%quantidade recebida no SGDO%']])
                    ->get();

                foreach($preVerificar as $pre){
                    DB::table('testesdeverificacao')
                        ->where([['id',  '=', $pre->id]])
                        ->update([
                            'preVerificar' => 'Não'
                        ]);

                }


                if( substr($registro->tem_distribuicao, 0, 4) !== 'Não') {

                    $reincidencia = DB::table('snci')
                        ->select('no_inspecao', 'no_grupo', 'no_item', 'dt_fim_inspecao', 'dt_inic_inspecao')
                        ->where([['descricao_item', 'like', '%quantidade recebida no SGDO%']])
                        ->where([['sto', '=', $registro->sto]])
                        ->orderBy('no_inspecao', 'desc')
                        ->first();

                    try {

                        if ( $reincidencia->no_inspecao > 1) {
//                                        dd($reincidencia);
                            $reincidente = 1;
                            $reinc = 'Sim';
                            $periodo = new CarbonPeriod();
                            $codVerificacaoAnterior = $reincidencia->no_inspecao;
                            $numeroGrupoReincidente = $reincidencia->no_grupo;
                            $numeroItemReincidente = $reincidencia->no_item;
                            $reincidencia_dt_fim_inspecao = new Carbon($reincidencia->dt_fim_inspecao);
                            $reincidencia_dt_inic_inspecao = new Carbon($reincidencia->dt_inic_inspecao);

                            $sgdo_distribuicao = DB::table('sgdo_distribuicao')
                                ->select( 'sgdo_distribuicao.*' )
                                ->where([['mcu', '=',  $registro->mcu  ]])
                                ->where([['data_incio_atividade', '>=',  $reincidencia_dt_fim_inspecao  ]])
                                ->get();
                            $dtini = $reincidencia_dt_fim_inspecao;

                        } else {
                            $sgdo_distribuicao = DB::table('sgdo_distribuicao')
                                ->select( 'sgdo_distribuicao.*' )
                                ->where([['mcu', '=',  $registro->mcu  ]])
                                ->where([['data_incio_atividade', '>=',  $dtmenos120dias  ]])
                                ->get();
                        }
                    } catch (\Exception $e) {

                        $sgdo_distribuicao = DB::table('sgdo_distribuicao')
                            ->select( 'sgdo_distribuicao.*' )
                            ->where([['mcu', '=',  $registro->mcu  ]])
                            ->where([['data_incio_atividade', '>=',  $dtmenos120dias  ]])
                            ->get();
                    }
                    if(! $sgdo_distribuicao->isEmpty()) {
                        $count = $sgdo_distribuicao->count('mcu');
                        $dtfim = $sgdo_distribuicao->max('data_incio_atividade');
                        $dtini = $sgdo_distribuicao->min('data_incio_atividade');

                        if ($count >= 1){
                            $avaliacao = 'Não Conforme';
                            $oportunidadeAprimoramento = 'Em análise aos registros do sistema SGDO (Relatório Detalhado das Distribuições), período de '. date('d/m/Y', strtotime( $dtini )) .' até '. date('d/m/Y', strtotime( $dtfim )) .' , constatou-se as seguintes inconsistências relacionadas aos lançamentos obrigatórios:';

                            $evidencia = $evidencia
                                . "\n" . 'Matricula'
                                . "\t" . 'Data Início Atividade'
                                . "\t" . 'Data Saída'
                                . "\t" . 'Data Retorno'
                                . "\t" . 'Data TPC'
                                . "\t" . 'Data Término Atividade';

                            foreach($sgdo_distribuicao as $dados) {

                                $data_incio_atividade = ''. ($dados->data_incio_atividade == '' ? 'Falta Lançamento' : date('d/m/Y', strtotime($dados->data_incio_atividade)));

                                if ((!empty($dados->data_saida)) && ($dados->data_saida <> $dados->data_incio_atividade)) {
                                    $data_saida = 'Data Saída Diferente '.date('d/m/Y', strtotime($dados->data_saida));
                                }
                                else{
                                    $data_saida = ''. ($dados->data_saida == '' ? 'Falta Lançamento' : date('d/m/Y', strtotime($dados->data_saida)));
                                }

                                if ((!empty($dados->data_retorno)) && ($dados->data_retorno <> $dados->data_incio_atividade)) {
                                    $data_retorno = 'Data Saída Diferente '.date('d/m/Y', strtotime($dados->data_retorno));
                                }
                                else{
                                    $data_retorno = ''. ($dados->data_retorno == '' ? 'Falta Lançamento' : date('d/m/Y', strtotime($dados->data_retorno)));
                                }

                                if ((!empty($dados->data_tpc)) && ($dados->data_tpc <> $dados->data_incio_atividade)) {
                                    $data_tpc = 'Data Saída Diferente '.date('d/m/Y', strtotime($dados->data_tpc));
                                }
                                else{
                                    $data_tpc = ''. ($dados->data_tpc == '' ? 'Falta Lançamento' : date('d/m/Y', strtotime($dados->data_tpc)));
                                }


                                if ((!empty($dados->data_termino_atividade)) && ($dados->data_termino_atividade <> $dados->data_incio_atividade)) {
                                    $data_termino_atividade = 'Data Saída Diferente '.date('d/m/Y', strtotime($dados->data_termino_atividade));
                                }
                                else{
                                    $data_termino_atividade = ''. ($dados->data_termino_atividade == '' ? 'Falta Lançamento' : date('d/m/Y', strtotime($dados->data_termino_atividade)));
                                }
                                $evidencia = $evidencia
                                    . "\n" . $dados->matricula
                                    . "\t" . $data_incio_atividade
                                    . "\t" . $data_saida
                                    . "\t" . $data_retorno
                                    . "\t" . $data_tpc
                                    . "\t" . $data_termino_atividade;
                            }

                            $quebra = DB::table('relevancias')
                                ->select('valor_final')
                                ->where('fator_multiplicador', '=', 1)
                                ->first();
                            $quebracaixa = $quebra->valor_final * 0.1;

                            if( $valorFalta > $quebracaixa){
                                $fm = DB::table('relevancias')
                                    ->select('fator_multiplicador', 'valor_final', 'valor_inicio')
                                    ->where('valor_inicio', '<=', $total)
                                    ->orderBy('valor_final', 'desc')
                                    ->first();
                                $pontuado = $registro->totalPontos * $fm->fator_multiplicador;
                            }
                            else{
                                if($avaliacao == 'Não Conforme') $pontuado = $registro->totalPontos * 1;
                            }



                        }
                        else{
                            $avaliacao = 'Conforme';
                            $oportunidadeAprimoramento = 'Em análise aos registros do sistema SGDO (Relatório Detalhado das Distribuições), período de '. date('d/m/Y', strtotime( $dtini )) .' até '. date('d/m/Y', strtotime( $dtfim )) .' , constatou-se que não havia inconsistências relacionadas aos lançamentos obrigatórios na unidade.';
                            $consequencias = null;
                            $orientacao = null;
                        }
                    }
                    else {
//                                      sgdo não verificado, unidade não tem dados na tabela SGDO
                        $avaliacao = 'Não Verificado'; // não avalia o item  terá uma segunda etapa na presencial
                        $oportunidadeAprimoramento = 'Não foi possível avaliar informações referente a unidade no Sistema SGDO dado que não há lançamentos sobre as rotinas da Distribuição. Verificaram o período a partir do dia' . date('d/m/Y', strtotime( $dtini )) .' até '. date('d/m/Y', strtotime( $dtfim )) .'.';
                        $consequencias = $registro->consequencias;
                        $orientacao = $registro->orientacao;
                    }

                }
                else {
                    //                                   sgdo não verificado, unidade não tem distribuição
                    $avaliacao = 'Não Executa Tarefa'; // não avalia o item  terá uma segunda etapa na presencial
                    $oportunidadeAprimoramento = 'A unidade não executa essa tarefa.';
                    $consequencias = null;
                    $orientacao = null;
                }

                $dto = DB::table('itensdeinspecoes')
                    ->Where([['inspecao_id', '=', $registro->inspecao_id]])
                    ->Where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                    ->select('itensdeinspecoes.*')
                    ->first();
                $itensdeinspecao = Itensdeinspecao::find($dto->id);
                $itensdeinspecao->avaliacao = $avaliacao;
                $itensdeinspecao->oportunidadeAprimoramento = $oportunidadeAprimoramento;
                $itensdeinspecao->evidencia = $evidencia;
                $itensdeinspecao->valorFalta = $valorFalta;
                $itensdeinspecao->valorSobra = $valorSobra;
                $itensdeinspecao->valorRisco = $valorRisco;

//                $itensdeinspecao->situacao = 'Inspecionado';
                $itensdeinspecao->situacao   = 'Em Inspeção'; // presencial/remota

                $itensdeinspecao->pontuado = $pontuado;
                $itensdeinspecao->itemQuantificado = $itemQuantificado;
                $itensdeinspecao->orientacao = $registro->orientacao;
                $itensdeinspecao->eventosSistema = 'Item avaliado Remotamente por Websgi em ' . date('d/m/Y', strtotime($dtnow)) . '.';
                $itensdeinspecao->reincidencia = $reinc;
                $itensdeinspecao->consequencias = $consequencias;
                $itensdeinspecao->orientacao = $orientacao;
                $itensdeinspecao->codVerificacaoAnterior = $codVerificacaoAnterior;
                $itensdeinspecao->numeroGrupoReincidente = $numeroGrupoReincidente;
                $itensdeinspecao->numeroItemReincidente = $numeroItemReincidente;
                $itensdeinspecao->update();

            }
// Fim SGDO Distribuição

            if(($registro->numeroGrupoVerificacao==240) && ($registro->numeroDoTeste==2)) {
                $reincidencia = DB::table('snci')
                    ->select('no_inspecao',   'no_grupo',  'no_item','dt_fim_inspecao','dt_inic_inspecao')
                    ->where([['descricao_item',  'like', '%informa no SGDO a quantidade real de objetos não distribuídos%']])
                    ->where([['sto', '=', $registro->sto ]])
                    ->orderBy('no_inspecao' ,'desc')
                    ->first();

                if ($reincidencia === null){
                    $reinc = 'Não';
                    $codVerificacaoAnterior = null;
                    $numeroGrupoReincidente = null;
                    $numeroItemReincidente = null;
                }
                else{
                    $reinc = 'Sim';
                    $codVerificacaoAnterior = $reincidencia->no_inspecao;
                    $numeroGrupoReincidente = $reincidencia->no_grupo;
                    $numeroItemReincidente = $reincidencia->no_item;
                }
                DB::table('itensdeinspecoes')
                    ->where([['inspecao_id', '=', $registro->inspecao_id]])
                    ->where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                    ->update([
                        'codVerificacaoAnterior' => $codVerificacaoAnterior,
                        'numeroGrupoReincidente' => $numeroGrupoReincidente,
                        'numeroItemReincidente' => $numeroItemReincidente,
                        'reincidencia' => $reinc
                    ]);

            }

// Inicio Atividades com SRO  verificação de imagens
            if((($registro->numeroGrupoVerificacao==201) && ($registro->numeroDoTeste==5))
                || (($registro->numeroGrupoVerificacao==331) && ($registro->numeroDoTeste==4))
                || (($registro->numeroGrupoVerificacao==240) && ($registro->numeroDoTeste==3))
                || (($registro->numeroGrupoVerificacao==277) && ($registro->numeroDoTeste==2))) {
                $dtini=null;
                $dtfim=null;
                $media=null;
                $random=null;
                $amostra=null;
                $res=null;
                $qtd_falhas=null;
                $percentagem_falhas=null;
                $insp = Inspecao::find($registro->inspecao_id);
                $lancamentossros =  DB::table('lancamentossro')
                    ->where('codigo', '=', $insp->codigo)
                    ->where('numeroGrupoVerificacao', '=', $registro->numeroGrupoVerificacao)
                    ->where('numeroDoTeste', '=', $registro->numeroDoTeste)
                    ->get();
                if( $lancamentossros->count('codigo') >= 1)      // játem lançamentos para avaliar...
                {
                    $pend = 0;
                    $aval = 0;
                    foreach ($lancamentossros as $register)
                    {
                        if ($register->estado == 'Pendente')
                        {
                            $pend++;
                        }
                        else
                        {
                            $aval++;
                        }
                    }
                    $mostra = $pend + $aval;
                    if ($aval == $mostra) // não existem objetos pendentes de avaliação
                    {
                        $avaliados = DB::table('lancamentossro')
                            ->where('codigo', '=', $registro->codigo)
                            ->where('numeroGrupoVerificacao', '=', $registro->numeroGrupoVerificacao)
                            ->where('numeroDoTeste', '=', $registro->numeroDoTeste)
                            ->where('estado', '=', 'Avaliado')
                            ->get();
                        $lancamentossro = DB::table('lancamentossro')
                            ->where('codigo', '=', $registro->codigo)
                            ->where('numeroGrupoVerificacao', '=', $registro->numeroGrupoVerificacao)
                            ->where('numeroDoTeste', '=', $registro->numeroDoTeste)
                            ->get();
                        $qtd_falhas = 0;
                        $amostra = 0;
                        foreach ($lancamentossro as $lancamento)
                        {
                            if (($lancamento->falhaDetectada <> 'Ok') && ($lancamento->estado == 'Avaliado'))
                            {
                                $qtd_falhas++;
                            }
                            if ($lancamento->estado == 'Avaliado')
                            {
                                $amostra++;
                            }
                        }
                        $percentagem_falhas = (($qtd_falhas / $amostra) * 100);
                        $percentagem_falhas = number_format($percentagem_falhas, 2, ',', '.');
                        $res = DB::table('lancamentossro')
                            ->where('codigo', '=', $registro->codigo)
                            ->where('numeroGrupoVerificacao', '=', $registro->numeroGrupoVerificacao)
                            ->where('numeroDoTeste', '=', $registro->numeroDoTeste)
                            ->where('falhaDetectada', '<>', 'Ok')
                            ->where('estado', '=', 'Avaliado')
                            ->get();
                        $micro_strategys = DB::table('micro_strategys')
                            ->where('nome_da_unidade', 'like', '%' . trim($registro->descricao) . '%')  //trim($registro->descricao)
                            ->where([['data_do_evento', '>=', $dtmenos120dias]])
                            ->where(function ($query) {
                                $query
                                    ->where('codigo_do_objeto', 'not like', 'B%')
                                    ->where('codigo_do_objeto', 'not like', 'E%')
                                    ->where('codigo_do_objeto', 'not like', 'F%')
                                    ->where('codigo_do_objeto', 'not like', 'I%')
                                    ->where('codigo_do_objeto', 'not like', 'J%')
                                    ->where('codigo_do_objeto', 'not like', 'L%')
                                    ->where('codigo_do_objeto', 'not like', 'M%')
                                    ->where('codigo_do_objeto', 'not like', 'N%')
                                    ->where('codigo_do_objeto', 'not like', 'R%')
                                    ->where('codigo_do_objeto', 'not like', 'T%')
                                    ->where('codigo_do_objeto', 'not like', 'U%')
                                    ->where('descricao_do_evento', '=', 'ENTREGUE')
                                    ->orWhere('descricao_do_evento', '=', 'DISTRIBUÍDO AO REMETENTE')
                                    ->orWhere('descricao_do_evento', '=', 'DESTINATÁRIO MUDOU-SE')
                                    ->orWhere('descricao_do_evento', '=', 'DESTINATÁRIO DESCONHECIDO NO ENDEREÇO')
                                    ->orderBy('data_do_evento', 'asc')
                                    ->groupBy('codigo_do_objeto');
                            })
                            ->get();

                        $count = $micro_strategys->count('codigo_do_objeto');
                        $dtini = $micro_strategys->min('data_do_evento');
                        $dtfim = $micro_strategys->max('data_do_evento');
                        $periodo = CarbonPeriod::create($dtini, $dtfim);
                        $dias = $periodo->count() - 1;

                    }
                    if ($pend <= $mostra) // existem objetos pendentes de avaliação
                    {
                        $res = DB::table('lancamentossro')
                            ->where('codigo', '=', $registro->codigo)
                            ->where('numeroGrupoVerificacao', '=', $registro->numeroGrupoVerificacao)
                            ->where('numeroDoTeste', '=', $registro->numeroDoTeste)
                            ->where('estado', '=', 'Pendente')
                            ->get();
                    }
                }
                else  // ainda não tem lançamentos para avaliar...
                {
                    $micro_strategys = DB::table('micro_strategys')
                        ->where('nome_da_unidade', 'like', '%' . trim($registro->descricao) . '%')  //trim($registro->descricao)
                        ->where([['data_do_evento', '>=', $dtmenos120dias]])
                        // ->where([['dr_de_destino', '>=', $registro->se]])
                        ->where(function ($query) {
                            $query
                                ->where('codigo_do_objeto', 'not like', 'B%')
                                ->where('codigo_do_objeto', 'not like', 'E%')
                                ->where('codigo_do_objeto', 'not like', 'F%')
                                ->where('codigo_do_objeto', 'not like', 'I%')
                                ->where('codigo_do_objeto', 'not like', 'J%')
                                ->where('codigo_do_objeto', 'not like', 'L%')
                                ->where('codigo_do_objeto', 'not like', 'M%')
                                ->where('codigo_do_objeto', 'not like', 'N%')
                                ->where('codigo_do_objeto', 'not like', 'R%')
                                ->where('codigo_do_objeto', 'not like', 'T%')
                                ->where('codigo_do_objeto', 'not like', 'U%')
                                //    ->where('descricao_do_evento', '=', 'DESTINATARIO AUSENTE')
                                ->where('descricao_do_evento', '=', 'ENTREGUE')
                                ->orWhere('descricao_do_evento', '=', 'DISTRIBUÍDO AO REMETENTE')
                                ->orWhere('descricao_do_evento', '=', 'DESTINATÁRIO MUDOU-SE')
                                ->orWhere('descricao_do_evento', '=', 'DESTINATÁRIO DESCONHECIDO NO ENDEREÇO')
                                ->orderBy('data_do_evento', 'asc')
                                ->groupBy('codigo_do_objeto');
                        })
                        ->get();
                    if(! $micro_strategys->isEmpty())
                    {
                        $count = $micro_strategys->count('codigo_do_objeto');
                        $dtini = $micro_strategys->min('data_do_evento');
                        $dtfim = $micro_strategys->max('data_do_evento');
                        $periodo = CarbonPeriod::create($dtini, $dtfim);
                        $dias = $periodo->count() - 1;
                        // $media = intval($count / $dias);
                        $amostra = 0;  //- tamanho da amostra
                        $N = intval($count / $dias) * 30;   //N =  tamanho universo da população;
                        $z = 1.9;   //Z = nível de confiança desejado 90%
                        $e = 900;   // e = a margem de erro máximo que é admitida;
                        $d = 4000;  //d Desvio padrão 4000 da população determinado
                        //  formula       (z^2*desvio^2*N)/(z^2*desvio^2+e^2*(N-1))
                        $dividendo = (pow($z, 2) * pow($d, 2) * $N);
                        $divisor = (pow($z, 2) * pow($d, 2) + pow($e, 2) * ($N - 1));
                        $amostra = intval($dividendo / $divisor);

                        // InvalidArgumentException
                        //Você solicitou 53 itens, mas há apenas 43 itens disponíveis.
                        if( $amostra  > $count  ) $amostra = $count ;
                        //dd($amostra, $count);
                        if ($amostra >= 1)
                        {
                            $random = $micro_strategys->random($amostra);
                        }
                        if ($amostra == 0)
                        {
                            if ($count <= 60)
                                $random = $micro_strategys->random($count);
                            else
                                $random = $micro_strategys->random(25);
                        }
                        $random->all();
                        foreach ($random as $dado)
                        {
                            $lancamentossro = new LancamentosSRO();
                            $lancamentossro->codigo = $registro->codigo;
                            $lancamentossro->numeroGrupoVerificacao = $registro->numeroGrupoVerificacao;
                            $lancamentossro->numeroDoTeste = $registro->numeroDoTeste;
                            $lancamentossro->objeto = $dado->codigo_do_objeto;
                            $lancamentossro->data = $dado->data_do_evento;
                            $lancamentossro->localBaixa1tentativa = $dado->descricao_do_evento;
                            $lancamentossro->estado = 'Pendente';
                            $lancamentossro->save();
                        }

                    }


                }
                $reincidencia = DB::table('snci')
                    ->select('no_inspecao',   'no_grupo',  'no_item','dt_fim_inspecao','dt_inic_inspecao')
                    ->where([['descricao_item',  'like', '%regras estabelecidas no MANDIS 6/1 quanto as anotações e lançamentos no SRO%']])
                    ->where([['sto', '=', $registro->sto ]])
                    ->orderBy('no_inspecao' ,'desc')
                    ->first();

                if ($reincidencia === null){
                    $reinc = 'Não';
                    $codVerificacaoAnterior = null;
                    $numeroGrupoReincidente = null;
                    $numeroItemReincidente = null;
                }
                else{
                    $reinc = 'Sim';
                    $codVerificacaoAnterior = $reincidencia->no_inspecao;
                    $numeroGrupoReincidente = $reincidencia->no_grupo;
                    $numeroItemReincidente = $reincidencia->no_item;
                }
                DB::table('itensdeinspecoes')
                    ->where([['inspecao_id', '=', $registro->inspecao_id]])
                    ->where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                    ->update([
                        'codVerificacaoAnterior' => $codVerificacaoAnterior,
                        'numeroGrupoReincidente' => $numeroGrupoReincidente,
                        'numeroItemReincidente' => $numeroItemReincidente,
                        'reincidencia' => $reinc
                    ]);
            }
// Final Atividades com SRO verificação de imagens

// Inicio Atividades com SRO a desenvolver para monitorada aguardando pareer do departamento
            if((($registro->numeroGrupoVerificacao==201) && ($registro->numeroDoTeste==6))
                || (($registro->numeroGrupoVerificacao==331) && ($registro->numeroDoTeste==5))
                || (($registro->numeroGrupoVerificacao==240) && ($registro->numeroDoTeste==4))
                || (($registro->numeroGrupoVerificacao==277) && ($registro->numeroDoTeste==3))) {

                $reincidencia = DB::table('snci')
                    ->select('no_inspecao',   'no_grupo',  'no_item','dt_fim_inspecao','dt_inic_inspecao')
                    ->where([['descricao_item',  'like', '%tentativas de entrega lançadas no SRO foram efetivamente realizadas pelo Carteiro%']])
                    ->where([['sto', '=', $registro->sto ]])
                    ->orderBy('no_inspecao' ,'desc')
                    ->first();

                if ($reincidencia === null){
                    $reinc = 'Não';
                    $codVerificacaoAnterior = null;
                    $numeroGrupoReincidente = null;
                    $numeroItemReincidente = null;
                }
                else{
                    $reinc = 'Sim';
                    $codVerificacaoAnterior = $reincidencia->no_inspecao;
                    $numeroGrupoReincidente = $reincidencia->no_grupo;
                    $numeroItemReincidente = $reincidencia->no_item;
                }
                DB::table('itensdeinspecoes')
                    ->where([['inspecao_id', '=', $registro->inspecao_id]])
                    ->where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                    ->update([
                        'codVerificacaoAnterior' => $codVerificacaoAnterior,
                        'numeroGrupoReincidente' => $numeroGrupoReincidente,
                        'numeroItemReincidente' => $numeroItemReincidente,
                        'reincidencia' => $reinc
                    ]);

            }

// Final Atividades com SRO

            if(($registro->numeroGrupoVerificacao==240) && ($registro->numeroDoTeste==5)) {
                $reincidencia = DB::table('snci')
                    ->select('no_inspecao',   'no_grupo',  'no_item','dt_fim_inspecao','dt_inic_inspecao')
                    ->where([['descricao_item',  'like', '%objetos qualificados são tempestivamente baixados no sistema SRO%']])
                    ->where([['sto', '=', $registro->sto ]])
                    ->orderBy('no_inspecao' ,'desc')
                    ->first();

                if ($reincidencia === null){
                    $reinc = 'Não';
                    $codVerificacaoAnterior = null;
                    $numeroGrupoReincidente = null;
                    $numeroItemReincidente = null;
                }
                else{
                    $reinc = 'Sim';
                    $codVerificacaoAnterior = $reincidencia->no_inspecao;
                    $numeroGrupoReincidente = $reincidencia->no_grupo;
                    $numeroItemReincidente = $reincidencia->no_item;
                }
                DB::table('itensdeinspecoes')
                    ->where([['inspecao_id', '=', $registro->inspecao_id]])
                    ->where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                    ->update([
                        'codVerificacaoAnterior' => $codVerificacaoAnterior,
                        'numeroGrupoReincidente' => $numeroGrupoReincidente,
                        'numeroItemReincidente' => $numeroItemReincidente,
                        'reincidencia' => $reinc
                    ]);

            }

            if(($registro->numeroGrupoVerificacao==240) && ($registro->numeroDoTeste==6)) {
                $reincidencia = DB::table('snci')
                    ->select('no_inspecao',   'no_grupo',  'no_item','dt_fim_inspecao','dt_inic_inspecao')
                    ->where([['descricao_item',  'like', '%devolução física e devolução eletrônica - CEDO%']])
                    ->where([['sto', '=', $registro->sto ]])
                    ->orderBy('no_inspecao' ,'desc')
                    ->first();

                if ($reincidencia === null){
                    $reinc = 'Não';
                    $codVerificacaoAnterior = null;
                    $numeroGrupoReincidente = null;
                    $numeroItemReincidente = null;
                }
                else{
                    $reinc = 'Sim';
                    $codVerificacaoAnterior = $reincidencia->no_inspecao;
                    $numeroGrupoReincidente = $reincidencia->no_grupo;
                    $numeroItemReincidente = $reincidencia->no_item;
                }
                DB::table('itensdeinspecoes')
                    ->where([['inspecao_id', '=', $registro->inspecao_id]])
                    ->where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                    ->update([
                        'codVerificacaoAnterior' => $codVerificacaoAnterior,
                        'numeroGrupoReincidente' => $numeroGrupoReincidente,
                        'numeroItemReincidente' => $numeroItemReincidente,
                        'reincidencia' => $reinc
                    ]);

            }

// Inicio Pre Alerta gestão automatica unidade sem supervisor
            if((($registro->numeroGrupoVerificacao==240) && ($registro->numeroDoTeste==7))
                || (($registro->numeroGrupoVerificacao==277) && ($registro->numeroDoTeste==6))) {

                $codVerificacaoAnterior = null;
                $numeroGrupoReincidente = null;
                $numeroItemReincidente = null;
                $evidencia = null;
                $valorSobra = null;
                $valorFalta = null;
                $valorRisco = null;
                $total = 0;
                $pontuado = null;
                $aviso = null;
                $itemQuantificado = 'Não';
                $reincidente = 0;
                $reinc = 'Não';

                $dtini = $dtmenos150dias;
                $countSupervisor = 0;
                $count = 0;
                $preVerificar = DB::table('testesdeverificacao')
                    ->select('testesdeverificacao.*' )
                    ->where([['teste',  'like',  '%UNIDADE SEM SUPERVISOR OPERACIONAL - É realizada diariamente a gestão das pendências de objetos%']])
                    ->get();

                foreach($preVerificar as $pre){
                    DB::table('testesdeverificacao')
                        ->where([['id',  '=', $pre->id]])
                        ->update([
                            'preVerificar' => 'Não'
                        ]);
                }

                switch ($registro->se) {

                    case 1 :{ $superintendencia = 'CS'; } break;
                    case 4 :{ $superintendencia = 'AL'; } break;
                    case 6 :{ $superintendencia = 'AM'; } break;
                    case 8 :{ $superintendencia = 'BA'; } break;
                    case 10 :{ $superintendencia = 'BSB'; } break;
                    case 12 :{ $superintendencia = 'CE'; } break;
                    case 14 :{ $superintendencia = 'ES'; } break;
                    case 16 :{ $superintendencia = 'GO'; } break;
                    case 18 :{ $superintendencia = 'MA'; } break;
                    case 20 :{ $superintendencia = 'MG'; } break;
                    case 22 :{ $superintendencia = 'MS'; } break;
                    case 24 :{ $superintendencia = 'MT'; } break;
                    case 26 :{ $superintendencia = 'RO'; } break;
                    case 28 :{ $superintendencia = 'PA'; } break;
                    case 30 :{ $superintendencia = 'PB'; } break;
                    case 32 :{ $superintendencia = 'PE'; } break;
                    case 34 :{ $superintendencia = 'PI'; } break;
                    case 36 :{ $superintendencia = 'PR'; } break;
                    case 50 :{ $superintendencia = 'RJ'; } break;
                    case 60 :{ $superintendencia = 'RN'; } break;
                    case 64 :{ $superintendencia = 'RS'; } break;
                    case 68 :{ $superintendencia = 'SC'; } break;
                    case 72 :{ $superintendencia = 'SPM'; } break;
                    case 74 :{ $superintendencia = 'SPI'; } break;
                    case 75 :{ $superintendencia = 'TO'; } break;
                }

                $reincidencia = DB::table('snci')
                    ->select('no_inspecao', 'no_grupo', 'no_item', 'dt_fim_inspecao', 'dt_inic_inspecao')
                    ->where([['descricao_item', 'like', '%UNIDADE SEM SUPERVISOR OPERACIONAL - É realizada diariamente a gestão das pendências de objetos%']])
                    ->where([['sto', '=', $registro->sto]])
                    ->orderBy('no_inspecao', 'desc')
                    ->first();

                try {

                    if ( $reincidencia->no_inspecao > 1) {
//                                        dd($reincidencia);
                        $reincidente = 1;
                        $reinc = 'Sim';
                        $periodo = new CarbonPeriod();
                        $codVerificacaoAnterior = $reincidencia->no_inspecao;
                        $numeroGrupoReincidente = $reincidencia->no_grupo;
                        $numeroItemReincidente = $reincidencia->no_item;
                        $reincidencia_dt_fim_inspecao = new Carbon($reincidencia->dt_fim_inspecao);
                        $reincidencia_dt_inic_inspecao = new Carbon($reincidencia->dt_inic_inspecao);

                        $painel_extravios = DB::table('painel_extravios')
                            ->select( 'painel_extravios.*' )
                            ->where([['painel_extravios.data_evento', '>=',  $reincidencia_dt_fim_inspecao  ]])
                            ->where([['painel_extravios.dr_destino', '=',  $superintendencia  ]])//o relatório não tem mcu
                            ->where([['painel_extravios.unid_destino_apelido', '=',  $registro->descricao  ]])
                            ->where([['painel_extravios.gestao_prealerta', '=',  'Gestão Automática' ]])
                            ->get();
                        $dtini = $reincidencia_dt_fim_inspecao;

                    } else {
                        $painel_extravios = DB::table('painel_extravios')
                            ->select( 'painel_extravios.*' )
                            ->where([['painel_extravios.data_evento', '>=',  $dtmenos150dias  ]])
                            ->where([['painel_extravios.dr_destino', '=',  $superintendencia  ]])//o relatório não tem mcu
                            ->where([['painel_extravios.unid_destino_apelido', '=',  $registro->descricao  ]])
                            ->where([['painel_extravios.gestao_prealerta', '=',  'Gestão Automática' ]])
                            ->get();
                    }
                } catch (\Exception $e) {

                    $painel_extravios = DB::table('painel_extravios')
                        ->select( 'painel_extravios.*' )
                        ->where([['painel_extravios.data_evento', '>=',  $dtmenos150dias  ]])
                        ->where([['painel_extravios.dr_destino', '=',  $superintendencia  ]])//o relatório não tem mcu
                        ->where([['painel_extravios.unid_destino_apelido', '=',  $registro->descricao  ]])
                        ->where([['painel_extravios.gestao_prealerta', '=',  'Gestão Automática' ]])
                        ->get();
                }



                $count = $painel_extravios->count('unid_destino_apelido');
                $dtfim = $painel_extravios->max('data_evento');
                $cadastral = DB::table('cadastral')
                    ->select( 'cadastral.*' )
                    ->where([['cadastral.mcu', '=',   $registro->mcu  ]])
                    ->where('cadastral.funcao',  'like', '%' . 'SUPERVISOR' . '%')
                    ->get();
                $countSupervisor = $cadastral->count('funcao');

                if($countSupervisor >= 1){
                    $avaliacao = 'Não Verificado';
                    $oportunidadeAprimoramento = 'Unidade avaliada em outro item Pois Possui Supervisor.';
                    $consequencias = null;
                    $orientacao = null;

                }
                else{
                    if ($count >= 1){
                        if(! $painel_extravios->isEmpty()){
                            $avaliacao = 'Não Conforme';
                            $oportunidadeAprimoramento = 'Em análise aos relatórios disponíveis no Sistema Painel de Extravios referente ao período de '. date('d/m/Y', strtotime($dtini)) .' até ' .date('d/m/Y', strtotime($dtfim)) .', identificou-se por meio dos dados contidos na coluna "gesto pré-alerta" a ocorrência de "Gestão Automática" para {{$count}} objeto(s), indicando que não era realizada a "gestão diária" do Pré-Alerta na unidade, conforme relatado a seguir:';

                            $evidencia = $evidencia
                                . "\n" . 'Objeto'
                                . "\t" . 'Data Último Evento';

                            foreach($painel_extravios as $dados){
                                $ultimoEvento = $dados->ultimo_evento_data == '' ? 'Data não Registrada' : date('d/m/Y', strtotime($dados->ultimo_evento_data));
                                $evidencia = $evidencia
                                    . "\n" . $dados->objeto
                                    . "\t" . $ultimoEvento;
                            }
                            $consequencias = $registro->consequencias;
                            $orientacao = $registro->orientacao;
                        }

                        $quebra = DB::table('relevancias')
                            ->select('valor_final')
                            ->where('fator_multiplicador', '=', 1)
                            ->first();
                        $quebracaixa = $quebra->valor_final * 0.1;

                        if( $valorFalta > $quebracaixa){
                            $fm = DB::table('relevancias')
                                ->select('fator_multiplicador', 'valor_final', 'valor_inicio')
                                ->where('valor_inicio', '<=', $total)
                                ->orderBy('valor_final', 'desc')
                                ->first();
                            $pontuado = $registro->totalPontos * $fm->fator_multiplicador;
                        }
                        else{
                            if($avaliacao == 'Não Conforme') $pontuado = $registro->totalPontos * 1;
                        }
                    }
                    else{
                        $avaliacao = 'Conforme';
                        $oportunidadeAprimoramento = 'Em análise aos relatórios disponíveis no Sistema Painel de Extravios referente ao período de '. date('d/m/Y', strtotime($dtini)) .' até ' .date('d/m/Y', strtotime($dtfim)) .', identificou-se por meio dos dados contidos na coluna Gestão Pré-alerta que não havia ocorrência alusiva à Gestão Automática que sugerisse falha na Gestão do diária da Conferência Eletrônica da unidade inspecionada.';
                        $consequencias = null;
                        $orientacao =  null;
                    }
                }

                $dto = DB::table('itensdeinspecoes')
                    ->Where([['inspecao_id', '=', $registro->inspecao_id]])
                    ->Where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                    ->select('itensdeinspecoes.*')
                    ->first();

                $itensdeinspecao = Itensdeinspecao::find($dto->id);
                $itensdeinspecao->avaliacao = $avaliacao;
                $itensdeinspecao->oportunidadeAprimoramento = $oportunidadeAprimoramento;
                $itensdeinspecao->evidencia = $evidencia;
                $itensdeinspecao->valorFalta = $valorFalta;
                $itensdeinspecao->valorSobra = $valorSobra;
                $itensdeinspecao->valorRisco = $valorRisco;

//                $itensdeinspecao->situacao = 'Inspecionado';
                $itensdeinspecao->situacao   = 'Em Inspeção'; // presencial/remota

                $itensdeinspecao->pontuado = $pontuado;
                $itensdeinspecao->itemQuantificado = $itemQuantificado;
                $itensdeinspecao->orientacao = $registro->orientacao;
                $itensdeinspecao->eventosSistema = 'Item avaliado Remotamente por Websgi em ' . date('d/m/Y', strtotime($dtnow)) . '.';
                $itensdeinspecao->reincidencia = $reinc;
                $itensdeinspecao->consequencias = $consequencias;
                $itensdeinspecao->orientacao = $orientacao;
                $itensdeinspecao->codVerificacaoAnterior = $codVerificacaoAnterior;
                $itensdeinspecao->numeroGrupoReincidente = $numeroGrupoReincidente;
                $itensdeinspecao->numeroItemReincidente = $numeroItemReincidente;
                $itensdeinspecao->update();

            }
// fim Pre Alerta gestão automatica unidade sem supervisor

// Inicio Pre Alerta gestão automatica unidade com supervisor

            if((($registro->numeroGrupoVerificacao==201) && ($registro->numeroDoTeste==15))
                || (($registro->numeroGrupoVerificacao==331) && ($registro->numeroDoTeste==11))
                || (($registro->numeroGrupoVerificacao==240) && ($registro->numeroDoTeste==8))
                || (($registro->numeroGrupoVerificacao==277) && ($registro->numeroDoTeste==5))) {


                $codVerificacaoAnterior = null;
                $numeroGrupoReincidente = null;
                $numeroItemReincidente = null;
                $evidencia = null;
                $valorSobra = null;
                $valorFalta = null;
                $valorRisco = null;
                $total = 0;
                $pontuado = null;
                $aviso = null;
                $itemQuantificado = 'Não';
                $reincidente = 0;
                $reinc = 'Não';

                $dtini = $dtmenos150dias;
                $countSupervisor = 0;
                $count = 0;

                $preVerificar = DB::table('testesdeverificacao')
                    ->select('testesdeverificacao.*' )
                    ->where([['teste',  'like', '%UNIDADE COM SUPERVISOR OPERACIONAL (SO) - É realizada diariamente a gestão das pendências de objetos%']])
                    ->get();

                foreach($preVerificar as $pre){
                    DB::table('testesdeverificacao')
                        ->where([['id',  '=', $pre->id]])
                        ->update([
                            'preVerificar' => 'Não'
                        ]);
                }


                switch ($registro->se) {

                    case 1 :{ $superintendencia = 'CS'; } break;
                    case 4 :{ $superintendencia = 'AL'; } break;
                    case 6 :{ $superintendencia = 'AM'; } break;
                    case 8 :{ $superintendencia = 'BA'; } break;
                    case 10 :{ $superintendencia = 'BSB'; } break;
                    case 12 :{ $superintendencia = 'CE'; } break;
                    case 14 :{ $superintendencia = 'ES'; } break;
                    case 16 :{ $superintendencia = 'GO'; } break;
                    case 18 :{ $superintendencia = 'MA'; } break;
                    case 20 :{ $superintendencia = 'MG'; } break;
                    case 22 :{ $superintendencia = 'MS'; } break;
                    case 24 :{ $superintendencia = 'MT'; } break;
                    case 26 :{ $superintendencia = 'RO'; } break;
                    case 28 :{ $superintendencia = 'PA'; } break;
                    case 30 :{ $superintendencia = 'PB'; } break;
                    case 32 :{ $superintendencia = 'PE'; } break;
                    case 34 :{ $superintendencia = 'PI'; } break;
                    case 36 :{ $superintendencia = 'PR'; } break;
                    case 50 :{ $superintendencia = 'RJ'; } break;
                    case 60 :{ $superintendencia = 'RN'; } break;
                    case 64 :{ $superintendencia = 'RS'; } break;
                    case 68 :{ $superintendencia = 'SC'; } break;
                    case 72 :{ $superintendencia = 'SPM'; } break;
                    case 74 :{ $superintendencia = 'SPI'; } break;
                    case 75 :{ $superintendencia = 'TO'; } break;
                }

                $reincidencia = DB::table('snci')
                    ->select('no_inspecao', 'no_grupo', 'no_item', 'dt_fim_inspecao', 'dt_inic_inspecao')
                    ->where([['descricao_item', 'like', '%UNIDADE COM SUPERVISOR OPERACIONAL (SO) - É realizada diariamente a gestão das pendências de objetos%']])
                    ->where([['sto', '=', $registro->sto]])
                    ->orderBy('no_inspecao', 'desc')
                    ->first();

                try {

                    if ( $reincidencia->no_inspecao > 1) {
//                                        dd($reincidencia);
                        $reincidente = 1;
                        $reinc = 'Sim';
                        $periodo = new CarbonPeriod();
                        $codVerificacaoAnterior = $reincidencia->no_inspecao;
                        $numeroGrupoReincidente = $reincidencia->no_grupo;
                        $numeroItemReincidente = $reincidencia->no_item;
                        $reincidencia_dt_fim_inspecao = new Carbon($reincidencia->dt_fim_inspecao);
                        $reincidencia_dt_inic_inspecao = new Carbon($reincidencia->dt_inic_inspecao);

                        $painel_extravios = DB::table('painel_extravios')
                            ->select( 'painel_extravios.*' )
                            ->where([['painel_extravios.data_evento', '>=',  $reincidencia_dt_fim_inspecao  ]])
                            ->where([['painel_extravios.dr_destino', '=',  $superintendencia  ]])//o relatório não tem mcu
                            ->where([['painel_extravios.unid_destino_apelido', '=',  $registro->descricao  ]])
                            ->where([['painel_extravios.gestao_prealerta', '=',  'Gestão Automática' ]])
                            ->get();
                        $dtini = $reincidencia_dt_fim_inspecao;

                    } else {
                        $painel_extravios = DB::table('painel_extravios')
                            ->select( 'painel_extravios.*' )
                            ->where([['painel_extravios.data_evento', '>=',  $dtmenos150dias  ]])
                            ->where([['painel_extravios.dr_destino', '=',  $superintendencia  ]])//o relatório não tem mcu
                            ->where([['painel_extravios.unid_destino_apelido', '=',  $registro->descricao  ]])
                            ->where([['painel_extravios.gestao_prealerta', '=',  'Gestão Automática' ]])
                            ->get();
                    }
                } catch (\Exception $e) {

                    $painel_extravios = DB::table('painel_extravios')
                        ->select( 'painel_extravios.*' )
                        ->where([['painel_extravios.data_evento', '>=',  $dtmenos150dias  ]])
                        ->where([['painel_extravios.dr_destino', '=',  $superintendencia  ]])//o relatório não tem mcu
                        ->where([['painel_extravios.unid_destino_apelido', '=',  $registro->descricao  ]])
                        ->where([['painel_extravios.gestao_prealerta', '=',  'Gestão Automática' ]])
                        ->get();
                }



                $count = $painel_extravios->count('unid_destino_apelido');
                $dtfim = $painel_extravios->max('data_evento');
                $cadastral = DB::table('cadastral')
                    ->select( 'cadastral.*' )
                    ->where([['cadastral.mcu', '=',   $registro->mcu  ]])
                    ->where('cadastral.funcao',  'like', '%' . 'SUPERVISOR' . '%')
                    ->get();
                $countSupervisor = $cadastral->count('funcao');

                if($countSupervisor == 0){
                    $avaliacao = 'Não Verificado';
                    $oportunidadeAprimoramento = 'Unidade avaliada em outro item dado a existênci de Supervisor no quadro de lotação.';
                    $consequencias = null;
                    $orientacao = null;

                }
                else{
                    if ($count >= 1){
                        if(! $painel_extravios->isEmpty()){
                            $avaliacao = 'Não Conforme';
                            $oportunidadeAprimoramento = 'Em análise aos relatórios disponíveis no Sistema Painel de Extravios referente ao período de '. date('d/m/Y', strtotime($dtini)) .' até ' .date('d/m/Y', strtotime($dtfim)) .', identificou-se por meio dos dados contidos na coluna "gesto pré-alerta" a ocorrência de "Gestão Automática" para {{$count}} objeto(s), indicando que não era realizada a "gestão diária" do Pré-Alerta na unidade, conforme relatado a seguir:';

                            $evidencia = $evidencia
                                . "\n" . 'Objeto'
                                . "\t" . 'Data Último Evento';

                            foreach($painel_extravios as $dados){
                                $ultimoEvento = $dados->ultimo_evento_data == '' ? 'Data não Registrada' : date('d/m/Y', strtotime($dados->ultimo_evento_data));
                                $evidencia = $evidencia
                                    . "\n" . $dados->objeto
                                    . "\t" . $ultimoEvento;
                            }
                            $consequencias = $registro->consequencias;
                            $orientacao = $registro->orientacao;
                        }

                        $quebra = DB::table('relevancias')
                            ->select('valor_final')
                            ->where('fator_multiplicador', '=', 1)
                            ->first();
                        $quebracaixa = $quebra->valor_final * 0.1;

                        if( $valorFalta > $quebracaixa){
                            $fm = DB::table('relevancias')
                                ->select('fator_multiplicador', 'valor_final', 'valor_inicio')
                                ->where('valor_inicio', '<=', $total)
                                ->orderBy('valor_final', 'desc')
                                ->first();
                            $pontuado = $registro->totalPontos * $fm->fator_multiplicador;
                        }
                        else{
                            if($avaliacao == 'Não Conforme') $pontuado = $registro->totalPontos * 1;
                        }
                    }
                    else{
                        $avaliacao = 'Conforme';
                        $oportunidadeAprimoramento = 'Em análise aos relatórios disponíveis no Sistema Painel de Extravios referente ao período de '. date('d/m/Y', strtotime($dtini)) .' até ' .date('d/m/Y', strtotime($dtfim)) .', identificou-se por meio dos dados contidos na coluna Gestão Pré-alerta que não havia ocorrência alusiva à Gestão Automática que sugerisse falha na Gestão do diária da Conferência Eletrônica da unidade inspecionada.';
                        $consequencias = null;
                        $orientacao =  null;
                    }
                }

                $dto = DB::table('itensdeinspecoes')
                    ->Where([['inspecao_id', '=', $registro->inspecao_id]])
                    ->Where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                    ->select('itensdeinspecoes.*')
                    ->first();

                $itensdeinspecao = Itensdeinspecao::find($dto->id);
                $itensdeinspecao->avaliacao = $avaliacao;
                $itensdeinspecao->oportunidadeAprimoramento = $oportunidadeAprimoramento;
                $itensdeinspecao->evidencia = $evidencia;
                $itensdeinspecao->valorFalta = $valorFalta;
                $itensdeinspecao->valorSobra = $valorSobra;
                $itensdeinspecao->valorRisco = $valorRisco;

//                $itensdeinspecao->situacao = 'Inspecionado';
                $itensdeinspecao->situacao   = 'Em Inspeção'; // presencial/remota

                $itensdeinspecao->pontuado = $pontuado;
                $itensdeinspecao->itemQuantificado = $itemQuantificado;
                $itensdeinspecao->orientacao = $registro->orientacao;
                $itensdeinspecao->eventosSistema = 'Item avaliado Remotamente por Websgi em ' . date('d/m/Y', strtotime($dtnow)) . '.';
                $itensdeinspecao->reincidencia = $reinc;
                $itensdeinspecao->consequencias = $consequencias;
                $itensdeinspecao->orientacao = $orientacao;
                $itensdeinspecao->codVerificacaoAnterior = $codVerificacaoAnterior;
                $itensdeinspecao->numeroGrupoReincidente = $numeroGrupoReincidente;
                $itensdeinspecao->numeroItemReincidente = $numeroItemReincidente;
                $itensdeinspecao->update();

            }
// fim Pre Alerta gestão automatica unidade com supervisor

// Inicio CIE Eletrônica
            if((($registro->numeroGrupoVerificacao==201) && ($registro->numeroDoTeste==9))
                || (($registro->numeroGrupoVerificacao==331) && ($registro->numeroDoTeste==8))
                || (($registro->numeroGrupoVerificacao==240) && ($registro->numeroDoTeste==9))
                || (($registro->numeroGrupoVerificacao==277) && ($registro->numeroDoTeste==7))) {

                $codVerificacaoAnterior = null;
                $numeroGrupoReincidente = null;
                $numeroItemReincidente = null;
                $evidencia = null;
                $valorSobra = null;
                $valorFalta = null;
                $valorRisco = null;
                $total = 0;
                $pontuado = null;
                $aviso = null;
                $itemQuantificado = 'Não';
                $reincidente = 0;
                $reinc = 'Não';
                $dtini = $dtmenos150dias;
                $count = 0;

                $preVerificar = DB::table('testesdeverificacao')
                    ->select('testesdeverificacao.*' )
                    ->where([['teste',  'like', '%CIE Eletrônica%']])
                    ->get();

                foreach($preVerificar as $pre){
                    DB::table('testesdeverificacao')
                        ->where([['id',  '=', $pre->id]])
                        ->update([
                            'preVerificar' => 'Não'
                        ]);
                }

                switch ($registro->se) {
                    case 1 :{ $superintendencia = 'CS'; } break;
                    case 4 :{ $superintendencia = 'AL'; } break;
                    case 6 :{ $superintendencia = 'AM'; } break;
                    case 8 :{ $superintendencia = 'BA'; } break;
                    case 10 :{ $superintendencia = 'BSB'; } break;
                    case 12 :{ $superintendencia = 'CE'; } break;
                    case 14 :{ $superintendencia = 'ES'; } break;
                    case 16 :{ $superintendencia = 'GO'; } break;
                    case 18 :{ $superintendencia = 'MA'; } break;
                    case 20 :{ $superintendencia = 'MG'; } break;
                    case 22 :{ $superintendencia = 'MS'; } break;
                    case 24 :{ $superintendencia = 'MT'; } break;
                    case 26 :{ $superintendencia = 'RO'; } break;
                    case 28 :{ $superintendencia = 'PA'; } break;
                    case 30 :{ $superintendencia = 'PB'; } break;
                    case 32 :{ $superintendencia = 'PE'; } break;
                    case 34 :{ $superintendencia = 'PI'; } break;
                    case 36 :{ $superintendencia = 'PR'; } break;
                    case 50 :{ $superintendencia = 'RJ'; } break;
                    case 60 :{ $superintendencia = 'RN'; } break;
                    case 64 :{ $superintendencia = 'RS'; } break;
                    case 68 :{ $superintendencia = 'SC'; } break;
                    case 72 :{ $superintendencia = 'SPM'; } break;
                    case 74 :{ $superintendencia = 'SPI'; } break;
                    case 75 :{ $superintendencia = 'TO'; } break;
                }

                $reincidencia = DB::table('snci')
                    ->select('no_inspecao', 'no_grupo', 'no_item', 'dt_fim_inspecao', 'dt_inic_inspecao')
                    ->where([['descricao_item', 'like', '%CIE Eletrônica%']])
                    ->where([['sto', '=', $registro->sto]])
                    ->orderBy('no_inspecao', 'desc')
                    ->first();

                try {
                    if ( $reincidencia->no_inspecao > 1) {
//                                        dd($reincidencia);
                        $reincidente = 1;
                        $reinc = 'Sim';
                        $periodo = new CarbonPeriod();
                        $codVerificacaoAnterior = $reincidencia->no_inspecao;
                        $numeroGrupoReincidente = $reincidencia->no_grupo;
                        $numeroItemReincidente = $reincidencia->no_item;
                        $reincidencia_dt_fim_inspecao = new Carbon($reincidencia->dt_fim_inspecao);
                        $reincidencia_dt_inic_inspecao = new Carbon($reincidencia->dt_inic_inspecao);

                        $cie_eletronicas = DB::table('cie_eletronicas')
                            ->select( 'cie_eletronicas.*' )
                            ->where([['cie_eletronicas.emissao', '>=',  $reincidencia_dt_fim_inspecao  ]])
                            ->where([['cie_eletronicas.se_destino', '=',   $superintendencia   ]])
                            ->where([['cie_eletronicas.destino',  'like', '%' . $registro->descricao . '%']])
//                                            ->where([['cie_eletronicas.respondida', '=',  'N' ]])
                            ->get();
                        $dtini = $reincidencia_dt_fim_inspecao;

                    } else {
                        $cie_eletronicas = DB::table('cie_eletronicas')
                            ->select( 'cie_eletronicas.*' )
                            ->where([['cie_eletronicas.emissao', '>=',  $dtmenos12meses  ]])
                            ->where([['cie_eletronicas.se_destino', '=',   $superintendencia   ]])
                            ->where([['cie_eletronicas.destino',  'like', '%' . $registro->descricao . '%']])
//                                            ->where([['cie_eletronicas.respondida', '=',  'N' ]])
                            ->get();
                    }
                } catch (\Exception $e) {
                    $cie_eletronicas = DB::table('cie_eletronicas')
                        ->select( 'cie_eletronicas.*' )
                        ->where([['cie_eletronicas.emissao', '>=',  $dtmenos12meses  ]])
                        ->where([['cie_eletronicas.se_destino', '=',   $superintendencia   ]])
                        ->where([['cie_eletronicas.destino',  'like', '%' . $registro->descricao . '%']])
                        ->get();
                }
                $count = $cie_eletronicas->count('id');
                $dtfim = $cie_eletronicas->max('emissao');
                if($count>=1)
                    $nlida=0;
                $nlidaNresp=0;
                $lidaNresp=0;
                $respForaprazo3dias=0;
                $ocorrencias = 0;
                foreach($cie_eletronicas as $dados) {
                    if (($dados->lida == 'N') && ($dados->respondida == 'S')) {
                        $nlida ++;
                        $ocorrencias ++;
                    }
                    if (($dados->lida == 'N') && ($dados->respondida == 'N')) {
                        $nlidaNresp ++;
                        $ocorrencias ++;
                    }
                    if (($dados->lida == 'S') && ($dados->respondida == 'N')) {
                        $ocorrencias ++;
                        $lidaNresp ++;
                    }
                    if (($dados->lida == 'S') && ($dados->respondida == 'S')) {
                        if ($dados->data_de_resposta) {
                            $periodo = CarbonPeriod::create($dados->emissao, $dados->data_de_resposta);
                            $respostaforaprazo = $periodo->count() - 1;
                            if ($respostaforaprazo > 3) {
                                $ocorrencias ++;
                                $respForaprazo3dias ++;
                            }
                        }
                    }
                }

                if($ocorrencias >=1){

                    //       a) Documentos respondidos acima do prazo de 03 dias úteis;
                    //       b) Se há CIEs sem registro das providências adotadas ou com ações genéricas, que não demonstrem assertividade ou não comprovem efetividade, como por exemplo: ""Empregado orientado"", ""Estamos apurando o ocorrido"";
                    //  letra ( C )   24/04/2021 Falta padrão de lançamento  fica ruim de contar a quantidade repetida
                    //     c) A ocorrência de reincidência. Considerar a existência de 03 CIEs recebidas pelos mesmos Motivos dentro do período de 01 mês;
                    //     d) Comunicados de Irregularidades com status ""Pendente"" e/ou ""Não Lido"".

                    $avaliacao = 'Não Conforme';
                    $oportunidadeAprimoramento = 'Em consulta realizada ao sistema de CIE Eletrônica do período de '. date('d/m/Y', strtotime($dtini)) .' até ' .date('d/m/Y', strtotime($dtfim)) .', constatou-se as seguintes situações:'. "\n" ;
                    if ($nlida >=1){
                        $oportunidadeAprimoramento = $oportunidadeAprimoramento . "\t" . ' - Comunicados não lidos '.$nlida. "\n" ;
                    }
                    if ($nlidaNresp >=1){
                        $oportunidadeAprimoramento = $oportunidadeAprimoramento . "\t" . ' - Comunicados não lidos e não respondidos '.$nlidaNresp. "\n" ;
                    }

                    if ($lidaNresp >=1){
                        $oportunidadeAprimoramento = $oportunidadeAprimoramento . "\t" . ' - Comunicados lidos e não respondidos '.$lidaNresp. "\n" ;
                    }
                    if ($respForaprazo3dias >=1){
                        $oportunidadeAprimoramento = $oportunidadeAprimoramento . "\t" . ' - Comunicados respondidos com prazo superior à 3 dias '.$respForaprazo3dias. "\n" ;
                    }
                    $evidencia = $evidencia
                        . "\n" . 'Nº CIE'
                        . "\t" . 'Data'
                        . "\t" . 'Origem'
                        . "\t" . 'Irregularidade'
                        . "\t" . 'Categoria';

                    foreach($cie_eletronicas as $dados) {
                        $data =  date('d/m/Y', strtotime($dados->emissao));
                        $evidencia = $evidencia
                            . "\n" . $dados->numero
                            . "\t" . $data
                            . "\n" . $dados->se_origem .' '. $dados->origem
                            . "\n" . $dados->irregularidade
                            . "\n" . $dados->categoria;
                    }
                    $consequencias = $registro->consequencias;
                    $orientacao = $registro->orientacao;

                    $quebra = DB::table('relevancias')
                        ->select('valor_final')
                        ->where('fator_multiplicador', '=', 1)
                        ->first();
                    $quebracaixa = $quebra->valor_final * 0.1;

                    if( $valorFalta > $quebracaixa){
                        $fm = DB::table('relevancias')
                            ->select('fator_multiplicador', 'valor_final', 'valor_inicio')
                            ->where('valor_inicio', '<=', $total)
                            ->orderBy('valor_final', 'desc')
                            ->first();
                        $pontuado = $registro->totalPontos * $fm->fator_multiplicador;
                    }
                    else{
                        if($avaliacao == 'Não Conforme') $pontuado = $registro->totalPontos * 1;
                    }

                }
                else{
                    $avaliacao = 'Conforme';
                    $oportunidadeAprimoramento = 'Em consulta realizada no Sistema de CIE Eletrônica do período de  '. date('d/m/Y', strtotime($dtini)) .' até ' .date('d/m/Y', strtotime($dtfim)) . ', não foi constatado inconformidades.';
                    $consequencias = null;
                    $orientacao =  null;
                }

                $dto = DB::table('itensdeinspecoes')
                    ->Where([['inspecao_id', '=', $registro->inspecao_id]])
                    ->Where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                    ->select('itensdeinspecoes.*')
                    ->first();

                $itensdeinspecao = Itensdeinspecao::find($dto->id);
                $itensdeinspecao->avaliacao = $avaliacao;
                $itensdeinspecao->oportunidadeAprimoramento = $oportunidadeAprimoramento;
                $itensdeinspecao->evidencia = $evidencia;
                $itensdeinspecao->valorFalta = $valorFalta;
                $itensdeinspecao->valorSobra = $valorSobra;
                $itensdeinspecao->valorRisco = $valorRisco;

//                $itensdeinspecao->situacao = 'Inspecionado';
                $itensdeinspecao->situacao   = 'Em Inspeção'; // presencial/remota

                $itensdeinspecao->pontuado = $pontuado;
                $itensdeinspecao->itemQuantificado = $itemQuantificado;
                $itensdeinspecao->orientacao = $registro->orientacao;
                $itensdeinspecao->eventosSistema = 'Item avaliado Remotamente por Websgi em ' . date('d/m/Y', strtotime($dtnow)) . '.';
                $itensdeinspecao->reincidencia = $reinc;
                $itensdeinspecao->consequencias = $consequencias;
                $itensdeinspecao->orientacao = $orientacao;
                $itensdeinspecao->codVerificacaoAnterior = $codVerificacaoAnterior;
                $itensdeinspecao->numeroGrupoReincidente = $numeroGrupoReincidente;
                $itensdeinspecao->numeroItemReincidente = $numeroItemReincidente;
                $itensdeinspecao->update();

            }
// Fim CIE Eletrônica

            if(($registro->numeroGrupoVerificacao==240) && ($registro->numeroDoTeste==10)) {
                $reincidencia = DB::table('snci')
                    ->select('no_inspecao',   'no_grupo',  'no_item','dt_fim_inspecao','dt_inic_inspecao')
                    ->where([['descricao_item',  'like', '%pendências de AR Digital no sistema SGD%']])
                    ->where([['sto', '=', $registro->sto ]])
                    ->orderBy('no_inspecao' ,'desc')
                    ->first();

                if ($reincidencia === null){
                    $reinc = 'Não';
                    $codVerificacaoAnterior = null;
                    $numeroGrupoReincidente = null;
                    $numeroItemReincidente = null;
                }
                else{
                    $reinc = 'Sim';
                    $codVerificacaoAnterior = $reincidencia->no_inspecao;
                    $numeroGrupoReincidente = $reincidencia->no_grupo;
                    $numeroItemReincidente = $reincidencia->no_item;
                }
                DB::table('itensdeinspecoes')
                    ->where([['inspecao_id', '=', $registro->inspecao_id]])
                    ->where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                    ->update([
                        'codVerificacaoAnterior' => $codVerificacaoAnterior,
                        'numeroGrupoReincidente' => $numeroGrupoReincidente,
                        'numeroItemReincidente' => $numeroItemReincidente,
                        'reincidencia' => $reinc
                    ]);

            }

            if(($registro->numeroGrupoVerificacao==240) && ($registro->numeroDoTeste==11)) {
                $reincidencia = DB::table('snci')
                    ->select('no_inspecao',   'no_grupo',  'no_item','dt_fim_inspecao','dt_inic_inspecao')
                    ->where([['descricao_item',  'like', '%alterações recorrentes por parte dos gestores nos lançamentos de resto realizados pelos carteiros%']])
                    ->where([['sto', '=', $registro->sto ]])
                    ->orderBy('no_inspecao' ,'desc')
                    ->first();

                if ($reincidencia === null){
                    $reinc = 'Não';
                    $codVerificacaoAnterior = null;
                    $numeroGrupoReincidente = null;
                    $numeroItemReincidente = null;
                }
                else{
                    $reinc = 'Sim';
                    $codVerificacaoAnterior = $reincidencia->no_inspecao;
                    $numeroGrupoReincidente = $reincidencia->no_grupo;
                    $numeroItemReincidente = $reincidencia->no_item;
                }
                DB::table('itensdeinspecoes')
                    ->where([['inspecao_id', '=', $registro->inspecao_id]])
                    ->where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                    ->update([
                        'codVerificacaoAnterior' => $codVerificacaoAnterior,
                        'numeroGrupoReincidente' => $numeroGrupoReincidente,
                        'numeroItemReincidente' => $numeroItemReincidente,
                        'reincidencia' => $reinc
                    ]);
            }

            if(($registro->numeroGrupoVerificacao==240) && ($registro->numeroDoTeste==12)) {
                $reincidencia = DB::table('snci')
                    ->select('no_inspecao',   'no_grupo',  'no_item','dt_fim_inspecao','dt_inic_inspecao')
                    ->where([['descricao_item',  'like', '%AR Digitais com a qualidade%']])
                    ->where([['sto', '=', $registro->sto ]])
                    ->orderBy('no_inspecao' ,'desc')
                    ->first();

                if ($reincidencia === null){
                    $reinc = 'Não';
                    $codVerificacaoAnterior = null;
                    $numeroGrupoReincidente = null;
                    $numeroItemReincidente = null;
                }
                else{
                    $reinc = 'Sim';
                    $codVerificacaoAnterior = $reincidencia->no_inspecao;
                    $numeroGrupoReincidente = $reincidencia->no_grupo;
                    $numeroItemReincidente = $reincidencia->no_item;
                }
                DB::table('itensdeinspecoes')
                    ->where([['inspecao_id', '=', $registro->inspecao_id]])
                    ->where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                    ->update([
                        'codVerificacaoAnterior' => $codVerificacaoAnterior,
                        'numeroGrupoReincidente' => $numeroGrupoReincidente,
                        'numeroItemReincidente' => $numeroItemReincidente,
                        'reincidencia' => $reinc
                    ]);
            }

            if(($registro->numeroGrupoVerificacao==241) && ($registro->numeroDoTeste==1)) {
                $reincidencia = DB::table('snci')
                    ->select('no_inspecao',   'no_grupo',  'no_item','dt_fim_inspecao','dt_inic_inspecao')
                    ->where([['descricao_item',  'like', '%controle sobre os cartões de pontos dos empregados%']])
                    ->where([['sto', '=', $registro->sto ]])
                    ->orderBy('no_inspecao' ,'desc')
                    ->first();

                if ($reincidencia === null){
                    $reinc = 'Não';
                    $codVerificacaoAnterior = null;
                    $numeroGrupoReincidente = null;
                    $numeroItemReincidente = null;
                }
                else{
                    $reinc = 'Sim';
                    $codVerificacaoAnterior = $reincidencia->no_inspecao;
                    $numeroGrupoReincidente = $reincidencia->no_grupo;
                    $numeroItemReincidente = $reincidencia->no_item;
                }
                DB::table('itensdeinspecoes')
                    ->where([['inspecao_id', '=', $registro->inspecao_id]])
                    ->where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                    ->update([
                        'codVerificacaoAnterior' => $codVerificacaoAnterior,
                        'numeroGrupoReincidente' => $numeroGrupoReincidente,
                        'numeroItemReincidente' => $numeroItemReincidente,
                        'reincidencia' => $reinc
                    ]);
            }


// Inicio controle sobre a realização de horas-extras
            if((($registro->numeroGrupoVerificacao==209) && ($registro->numeroDoTeste==2))
                || (($registro->numeroGrupoVerificacao==337) && ($registro->numeroDoTeste==1))
                || (($registro->numeroGrupoVerificacao==241) && ($registro->numeroDoTeste==2))
                || (($registro->numeroGrupoVerificacao==278) && ($registro->numeroDoTeste==1)))  {

                $codVerificacaoAnterior = null;
                $numeroGrupoReincidente = null;
                $numeroItemReincidente = null;
                $evidencia = null;
                $valorSobra = null;
                $valorFalta = null;
                $valorRisco = null;
                $total = 0;
                $pontuado = null;
                $aviso = null;
                $itemQuantificado = 'Não';
                $reincidente = 0;
                $reinc = 'Não';
                $count = 0;

                $preVerificar = DB::table('testesdeverificacao')
                    ->select('testesdeverificacao.*' )
                    ->where([['teste',  'like', '%realização de horas-extras%']])
                    ->get();

                foreach($preVerificar as $pre){
                    DB::table('testesdeverificacao')
                        ->where([['id',  '=', $pre->id]])
                        ->update([
                            'preVerificar' => 'Não'
                        ]);
                }

                $ref = substr($dtmenos12meses,0,4). substr($dtmenos12meses,5,2);
                $dtini = substr($dtmenos12meses,0,4).'-'. substr($dtmenos12meses,5,2).'-01';

                $dtini = date('d/m/Y', strtotime($dtini));
                $rowtfs=0;
                $situacao =null;
                $pgtoAdicionais='';
                $counteventostfs=0;

                $pgtadd = DB::table('pagamentos_adicionais')
                    ->select(
                        'pagamentos_adicionais.ref'
                    )
                    ->get();

                if(! $pgtadd->isEmpty()) {

                    $reffinal = $pgtadd->max('ref');
                    if(substr($reffinal,5,2)<10){
                        $dt='0'.substr($reffinal,5,2);
                    }
                    else{
                        $dt= substr($reffinal,5,2);
                    }
                    $reffinal =  substr($reffinal,0,4).'-'.$dt;
                    $reffinal = new Carbon($reffinal);
                    $reffinal = $reffinal->lastOfMonth();
                    $reffinal = date('d/m/Y', strtotime($reffinal));
                }
                else {
                    $reffinal=null;
                }


                $reincidencia = DB::table('snci')
                    ->select('no_inspecao', 'no_grupo', 'no_item', 'dt_fim_inspecao', 'dt_inic_inspecao')
                    ->where([['descricao_item', 'like', '%realização de horas-extras%']])
                    ->where([['sto', '=', $registro->sto]])
                    ->orderBy('no_inspecao', 'desc')
                    ->first();

                try {

                    if ( $reincidencia->no_inspecao > 1) {
//                                        dd($reincidencia);
                        $reincidente = 1;
                        $reinc = 'Sim';
                        $periodo = new CarbonPeriod();
                        $codVerificacaoAnterior = $reincidencia->no_inspecao;
                        $numeroGrupoReincidente = $reincidencia->no_grupo;
                        $numeroItemReincidente = $reincidencia->no_item;
                        $reincidencia_dt_fim_inspecao = new Carbon($reincidencia->dt_fim_inspecao);
                        $reincidencia_dt_inic_inspecao = new Carbon($reincidencia->dt_inic_inspecao);

                        $ref =  date('Ym', strtotime($reincidencia_dt_fim_inspecao));

                        $pagamentos_adicionais = DB::table('pagamentos_adicionais')
                            ->where('sigla_lotacao',  'like', '%' . trim($registro->descricao) . '%')  //trim($registro->descricao)
                            ->where('ref', '>=', $ref) //
                            ->where(function ($query) {
                                $query
                                    ->where('rubrica', '=', 'Trab. Fins Semana - Proporcional')
                                    ->where('rubrica', '=', 'Trabalho Fins Semana')
                                    ->orWhere('rubrica', '=', 'Hora Extra   70% - Norm')
                                    ->orWhere('rubrica', '=', 'Hora Extra 100% - Norm')
                                    ->orWhere('rubrica', '=', 'Hora Extra Not.70% - Norm')
                                    ->orderBy('ref' ,'asc');
                            })
                            ->get();

                        $dtini = date('d/m/Y', strtotime($reincidencia_dt_fim_inspecao));
                    } else {
                        $pagamentos_adicionais = DB::table('pagamentos_adicionais')
                            ->where('sigla_lotacao',  'like', '%' . trim($registro->descricao) . '%')  //trim($registro->descricao)
                            ->where('ref', '>=', $ref) //
                            ->where(function ($query) {
                                $query
                                    ->where('rubrica', '=', 'Trab. Fins Semana - Proporcional')
                                    ->where('rubrica', '=', 'Trabalho Fins Semana')
                                    ->orWhere('rubrica', '=', 'Hora Extra   70% - Norm')
                                    ->orWhere('rubrica', '=', 'Hora Extra 100% - Norm')
                                    ->orWhere('rubrica', '=', 'Hora Extra Not.70% - Norm')
                                    ->orderBy('ref' ,'asc');
                            })
                            ->get();

                    }
                } catch (\Exception $e) {
                    $pagamentos_adicionais = DB::table('pagamentos_adicionais')
                        ->where('sigla_lotacao',  'like', '%' . trim($registro->descricao) . '%')  //trim($registro->descricao)
                        ->where('ref', '>=', $ref) //
                        ->where(function ($query) {
                            $query
                                ->where('rubrica', '=', 'Trab. Fins Semana - Proporcional')
                                ->where('rubrica', '=', 'Trabalho Fins Semana')
                                ->orWhere('rubrica', '=', 'Hora Extra   70% - Norm')
                                ->orWhere('rubrica', '=', 'Hora Extra 100% - Norm')
                                ->orWhere('rubrica', '=', 'Hora Extra Not.70% - Norm')
                                ->orderBy('ref' ,'asc');
                        })
                        ->get();
                }

                if(! $pagamentos_adicionais->isEmpty()) {
                    $count = $pagamentos_adicionais->count('sigla_lotacao');
                }
                $insp = Inspecao::find($registro->inspecao_id);
                if  ( $count >= 1 ) {
                    DB::table('pgto_adicionais_temp')
                        ->where('codigo', '=', $insp->codigo)
                        ->where('numeroGrupoVerificacao', '=', $registro->numeroGrupoVerificacao)
                        ->where('numeroDoTeste', '=', $registro->numeroDoTeste)
                        ->delete();
                    foreach ($pagamentos_adicionais  as $adicional){
                        $situacao = null;
                        $periodo = new Carbon(substr($adicional->ref,0,4).'-'. substr($adicional->ref,5,2));
                        $periodo->subMonth(1);
                        $month = $periodo->month;
                        $year = $periodo->year;
                        if($adicional-> rubrica == 'Trab. Fins Semana - Proporcional' ){
                            $eventos = DB::table('alarmes')
                                ->where('mcu', '=', $registro->mcu)
                                ->whereYear('data', $year)
                                ->whereMonth('data',  $month)
                                ->where('diaSemana', '=', 6)
                                ->select(
                                    'alarmes.*'
                                )
                                ->orderBy('data' ,'asc')
                                ->get();

                            if(! $eventos->isEmpty())
                            {
                                $counteventostfs = $eventos->count('data');
                            }
                            else
                            {
                                $counteventostfs = 0;
                            }
                            if( $counteventostfs == 0){
                                $situacao = 'Provento registrado em período que não houve registro de Desarme do Sistema de Alarme.';
                            }else{
                                $rowtfs=0;
                                foreach ($eventos  as $evento){
                                    $rowtfs++;
                                }
                                $situacao=null;
                            }
                        }
                        elseif
                        (($adicional-> rubrica    == 'Hora Extra   70% - Norm')
                            || ($adicional-> rubrica == 'Hora Extra 100% - Norm')
                            || ($adicional-> rubrica == 'Hora Extra Not.70% - Norm') ) {

                            $inicio_expediente = new Carbon($registro->inicio_expediente); //$registro->inicio_expediente;
                            $final_expediente = new Carbon($registro->final_expediente); //$registro->inicio_expediente;
                            $inicio_expediente  = $inicio_expediente->subHours(3);
                            $final_expediente   = $final_expediente->addHours(3);// 2012-02-04 00:00:00
                            $inicio_expediente   = $inicio_expediente->toTimeString();
                            $final_expediente   = $final_expediente->toTimeString(); //14:15:16

                            $eventos = DB::table('alarmes')
                                ->where('mcu', '=', $registro->mcu)
                                ->whereYear('data', $year)
                                ->whereMonth('data',  $month)
                                ->whereTime('hora', '>', $inicio_expediente)
                                ->whereTime('hora', '<', $final_expediente)
                                ->whereNotIn('diaSemana', [0])
                                ->select(
                                    'alarmes.*'
                                )
                                ->orderBy('data' ,'asc')
                                ->orderBy('hora' ,'asc')
                                ->get();

                            if(! $eventos->isEmpty()) {
                                $counteventoshe = $eventos->count('data');
                            }
                            else {
                                $counteventoshe = 0;
                            }

                            if( $counteventoshe == 0) {
                                $situacao = 'Provento registrado em período e horários que não houve registro de Arme/Desarme do Sistema de Alarme.';
                            }
                            else {
                                $rowhe=0;
                                foreach ($eventos  as $evento){
                                    $rowhe++;
                                }
                                $situacao = null;
                            }

                        }
                        if(($adicional-> rubrica == 'Trabalho Fins Semana' )&&($pgtoAdicionaisTemp->ref > '202008')){
                            $situacao = 'O  Acórdão do Dissídio Coletivo 2020/2021, vigente a partir de 01/08/2020, não prevê a manutenção do pagamento do Adicional de Fim de Semana.';
                        }
                        $insp = Inspecao::find($registro->inspecao_id);
                        if (!$situacao==null) {
                            $pgtoAdicionaisTemp = new PgtoAdicionaisTemp();
                            $pgtoAdicionaisTemp->sto = $registro->sto;
                            $pgtoAdicionaisTemp->mcu = $registro->mcu;
                            $pgtoAdicionaisTemp->codigo = $insp->codigo;
                            $pgtoAdicionaisTemp->numeroGrupoVerificacao = $registro->numeroGrupoVerificacao;
                            $pgtoAdicionaisTemp->numeroDoTeste = $registro->numeroDoTeste;
                            $pgtoAdicionaisTemp->matricula = $adicional->matricula;
                            $pgtoAdicionaisTemp->cargo = $adicional->cargo;
                            $pgtoAdicionaisTemp->rubrica = $adicional->rubrica;
                            $pgtoAdicionaisTemp->ref = $adicional->ref;
                            $pgtoAdicionaisTemp->quantidade = $adicional->qtd/2;
                            $pgtoAdicionaisTemp->valor = $adicional->valor;
                            $pgtoAdicionaisTemp->situacao = $situacao;
                            $pgtoAdicionaisTemp->save();
                            $situacao=null;
                        }

                    }
                    $insp = Inspecao::find($registro->inspecao_id);
                    $pgtoAdicionais = DB::table('pgto_adicionais_temp')
                        ->where('sto',  '=', $registro->sto)
                        ->where('mcu',  '=', $registro->mcu)
                        ->where('codigo',  '=', $insp->codigo)
                        ->where('numeroGrupoVerificacao',  '=', $registro->numeroGrupoVerificacao)
                        ->where('numeroDoTeste',  '=', $registro->numeroDoTeste)
                        ->select(
                            'pgto_adicionais_temp.*'
                        )
                        ->get();
                    if(! $pgtoAdicionais->isEmpty()) {
                        $total = $pgtoAdicionais->sum('valor');
//                                        $count = $pgtoAdicionais->count('matricula');
                        $avaliacao = 'Não Conforme';
                        $oportunidadeAprimoramento = 'Em análise aos registros do Sistema PGP e aos dados do Relatório “Arme e Desarme” do sistema de alarme, no período de '. $dtini .' a '.$reffinal .', constatou-se  a (as) seguinte (s) inconsistência (s):';
                        $evidencia = $evidencia. "\n" .'- Constatou-se '.$count.' - ocorrência(s) de pagamentos conforme a Seguir:';
                        $evidencia = $evidencia
                            . "\n" . 'Matricula'
                            . "\t" . 'Cargo'
                            . "\t" . 'Tipo do Provento'
                            . "\t" . 'Período de Rec. Adicional'
                            . "\t" . 'Valor ATT Recebido (R$)'
                            . "\t" . 'Situação Encontrada';

                        foreach($pgtoAdicionais as $dados){
                            $evidencia = $evidencia
                                . "\n" . $dados->matricula
                                . "\n" . $dados->cargo
                                . "\n" . $dados->rubrica
                                . "\n" . $dados->ref
                                . "\n" . $dados->valor
                                . "\n" . $dados->situacao;
                        }
                        $consequencias = $registro->consequencias;
                        $orientacao = $registro->orientacao;

                        $quebra = DB::table('relevancias')
                            ->select('valor_final')
                            ->where('fator_multiplicador', '=', 1)
                            ->first();
                        $quebracaixa = $quebra->valor_final * 0.1;

                        if( $valorFalta > $quebracaixa){
                            $fm = DB::table('relevancias')
                                ->select('fator_multiplicador', 'valor_final', 'valor_inicio')
                                ->where('valor_inicio', '<=', $total)
                                ->orderBy('valor_final', 'desc')
                                ->first();
                            $pontuado = $registro->totalPontos * $fm->fator_multiplicador;
                        }
                        else{
                            if($avaliacao == 'Não Conforme') $pontuado = $registro->totalPontos * 1;
                        }
                    }
                    else{
                        $avaliacao = 'Conforme';
                        $oportunidadeAprimoramento = 'Em análise aos registros do Sistema PGP e aos dados do Relatório “Arme e Desarme” do Sistema Monitorado de Alarme, constatou-se que não há inconsistências quanto ao lançamento de serviços extras no período de '.  $dtini .' a '. $reffinal .'.';
                        $consequencias = null;
                        $orientacao =  null;
                    }
                }
                else{
                    $avaliacao = 'Conforme';
                    $oportunidadeAprimoramento = 'Em análise aos registros do Sistema PGP e aos dados do Relatório “Arme e Desarme” do Sistema Monitorado de Alarme, constatou-se que não há inconsistências quanto ao lançamento de serviços extras no período de '.  $dtini .' a '. $reffinal .'.';
                    $consequencias = null;
                    $orientacao =  null;
                }
                $dto = DB::table('itensdeinspecoes')
                    ->Where([['inspecao_id', '=', $registro->inspecao_id]])
                    ->Where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                    ->select('itensdeinspecoes.*')
                    ->first();

                $itensdeinspecao = Itensdeinspecao::find($dto->id);
                $itensdeinspecao->avaliacao = $avaliacao;
                $itensdeinspecao->oportunidadeAprimoramento = $oportunidadeAprimoramento;
                $itensdeinspecao->evidencia = $evidencia;
                $itensdeinspecao->valorFalta = $valorFalta;
                $itensdeinspecao->valorSobra = $valorSobra;
                $itensdeinspecao->valorRisco = $valorRisco;

//                $itensdeinspecao->situacao = 'Inspecionado';
                $itensdeinspecao->situacao   = 'Em Inspeção'; // presencial/remota

                $itensdeinspecao->pontuado = $pontuado;
                $itensdeinspecao->itemQuantificado = $itemQuantificado;
                $itensdeinspecao->orientacao = $registro->orientacao;
                $itensdeinspecao->eventosSistema = 'Item avaliado Remotamente por Websgi em ' . date('d/m/Y', strtotime($dtnow)) . '.';
                $itensdeinspecao->reincidencia = $reinc;
                $itensdeinspecao->consequencias = $consequencias;
                $itensdeinspecao->orientacao = $orientacao;
                $itensdeinspecao->codVerificacaoAnterior = $codVerificacaoAnterior;
                $itensdeinspecao->numeroGrupoReincidente = $numeroGrupoReincidente;
                $itensdeinspecao->numeroItemReincidente = $numeroItemReincidente;
                $itensdeinspecao->update();

            }

// Final controle sobre a realização de horas-extras


//inicio direito ao recebimento do provento
            if((($registro->numeroGrupoVerificacao==209)&&($registro->numeroDoTeste==3))
                || (($registro->numeroGrupoVerificacao==337)&&($registro->numeroDoTeste==2))
                || (($registro->numeroGrupoVerificacao==241)&&($registro->numeroDoTeste==3))
                || (($registro->numeroGrupoVerificacao==278)&&($registro->numeroDoTeste==2))) {

                $codVerificacaoAnterior = null;
                $numeroGrupoReincidente = null;
                $numeroItemReincidente = null;
                $evidencia = null;
                $valorSobra = null;
                $valorFalta = null;
                $valorRisco = null;
                $total = 0;
                $pontuado = null;
                $aviso = null;
                $itemQuantificado = 'Não';
                $reincidente = 0;
                $reinc = 'Não';
                $count = 0;


                $preVerificar = DB::table('testesdeverificacao')
                    ->select('testesdeverificacao.*' )
                    ->where([['teste',  'like', '%Adicionais de Atividade%']])
                    ->get();

                foreach($preVerificar as $pre){
                    DB::table('testesdeverificacao')
                        ->where([['id',  '=', $pre->id]])
                        ->update([
                            'preVerificar' => 'Não'
                        ]);
                }

                $ref = substr($dtmenos4meses,0,4). substr($dtmenos4meses,5,2);
                $count_atend = 0;
                $count_dist = 0;
                $count = 0;
                $refini = DB::table('pagamentos_adicionais')
                    ->select( 'pagamentos_adicionais.ref' )
                    ->where('ref', '>=', $ref)
                    ->get();
                $dtini = $refini->min('ref');
                $dtfim = $refini->max('ref');

                switch ($registro->se) {
                    case 1 :{ $superintendencia = 'CS'; } break;
                    case 4 :{ $superintendencia = 'AL'; } break;
                    case 6 :{ $superintendencia = 'AM'; } break;
                    case 8 :{ $superintendencia = 'BA'; } break;
                    case 10 :{ $superintendencia = 'BSB'; } break;
                    case 12 :{ $superintendencia = 'CE'; } break;
                    case 14 :{ $superintendencia = 'ES'; } break;
                    case 16 :{ $superintendencia = 'GO'; } break;
                    case 18 :{ $superintendencia = 'MA'; } break;
                    case 20 :{ $superintendencia = 'MG'; } break;
                    case 22 :{ $superintendencia = 'MS'; } break;
                    case 24 :{ $superintendencia = 'MT'; } break;
                    case 26 :{ $superintendencia = 'RO'; } break;
                    case 28 :{ $superintendencia = 'PA'; } break;
                    case 30 :{ $superintendencia = 'PB'; } break;
                    case 32 :{ $superintendencia = 'PE'; } break;
                    case 34 :{ $superintendencia = 'PI'; } break;
                    case 36 :{ $superintendencia = 'PR'; } break;
                    case 50 :{ $superintendencia = 'RJ'; } break;
                    case 60 :{ $superintendencia = 'RN'; } break;
                    case 64 :{ $superintendencia = 'RS'; } break;
                    case 68 :{ $superintendencia = 'SC'; } break;
                    case 72 :{ $superintendencia = 'SPM'; } break;
                    case 74 :{ $superintendencia = 'SPI'; } break;
                    case 75 :{ $superintendencia = 'TO'; } break;
                }

                $reincidencia = DB::table('snci')
                    ->select('no_inspecao', 'no_grupo', 'no_item', 'dt_fim_inspecao', 'dt_inic_inspecao')
                    ->where([['descricao_item', 'like', '%Adicionais de Atividade%']])
                    ->where([['sto', '=', $registro->sto]])
                    ->orderBy('no_inspecao', 'desc')
                    ->first();

                try {

                    if ( $reincidencia->no_inspecao > 1) {
//                                        dd($reincidencia);
                        $reincidente = 1;
                        $reinc = 'Sim';
                        $periodo = new CarbonPeriod();
                        $codVerificacaoAnterior = $reincidencia->no_inspecao;
                        $numeroGrupoReincidente = $reincidencia->no_grupo;
                        $numeroItemReincidente = $reincidencia->no_item;
                        $reincidencia_dt_fim_inspecao = new Carbon($reincidencia->dt_fim_inspecao);
                        $reincidencia_dt_inic_inspecao = new Carbon($reincidencia->dt_inic_inspecao);

                        $ref =  date('Ym', strtotime($reincidencia_dt_fim_inspecao));

                        $pagamentos_adicionais_dist = DB::table('pagamentos_adicionais')
                            ->select( 'pagamentos_adicionais.*' )
                            ->where([['pagamentos_adicionais.se', '>=', 'SE/'.$superintendencia ]])
                            ->where([['pagamentos_adicionais.sigla_lotacao', 'like', '%' . trim($registro->descricao) . '%' ]])
                            ->where([['pagamentos_adicionais.ref', '>=', $ref ]])
                            ->where([['pagamentos_adicionais.rubrica', '=',  'AADC-Adic.Ativ. Distrib/Coleta Ext.' ]])
                            ->get();

                        $pagamentos_adicionais_atend = DB::table('pagamentos_adicionais')
                            ->select( 'pagamentos_adicionais.*' )
                            ->where([['pagamentos_adicionais.se', '>=', 'SE/'.$superintendencia ]])
                            ->where([['pagamentos_adicionais.sigla_lotacao', 'like', '%' . trim($registro->descricao) . '%' ]])
                            ->where([['pagamentos_adicionais.ref', '>=', $ref ]])
                            ->where([['pagamentos_adicionais.rubrica', '=',  'AAG - Adic. de Atend. em Guichê' ]])
                            ->get();
                        $dtini = date('d/m/Y', strtotime($reincidencia_dt_fim_inspecao));
                    } else {
                        $pagamentos_adicionais_dist = DB::table('pagamentos_adicionais')
                            ->select( 'pagamentos_adicionais.*' )
                            ->where([['pagamentos_adicionais.se', '>=', 'SE/'.$superintendencia ]])
                            ->where([['pagamentos_adicionais.sigla_lotacao', 'like', '%' . trim($registro->descricao) . '%' ]])
                            ->where([['pagamentos_adicionais.ref', '>=', $ref ]])
                            ->where([['pagamentos_adicionais.rubrica', '=',  'AADC-Adic.Ativ. Distrib/Coleta Ext.' ]])
                            ->get();
                        $pagamentos_adicionais_atend = DB::table('pagamentos_adicionais')
                            ->select( 'pagamentos_adicionais.*' )
                            ->where([['pagamentos_adicionais.se', '>=', 'SE/'.$superintendencia ]])
                            ->where([['pagamentos_adicionais.sigla_lotacao', 'like', '%' . trim($registro->descricao) . '%' ]])
                            ->where([['pagamentos_adicionais.ref', '>=', $ref ]])
                            ->where([['pagamentos_adicionais.rubrica', '=',  'AAG - Adic. de Atend. em Guichê' ]])
                            ->get();

                    }
                } catch (\Exception $e) {
                    $pagamentos_adicionais_dist = DB::table('pagamentos_adicionais')
                        ->select( 'pagamentos_adicionais.*' )
                        ->where([['pagamentos_adicionais.se', '>=', 'SE/'.$superintendencia ]])
                        ->where([['pagamentos_adicionais.sigla_lotacao', 'like', '%' . trim($registro->descricao) . '%' ]])
                        ->where([['pagamentos_adicionais.ref', '>=', $ref ]])
                        ->where([['pagamentos_adicionais.rubrica', '=',  'AADC-Adic.Ativ. Distrib/Coleta Ext.' ]])
                        ->get();
                    $pagamentos_adicionais_atend = DB::table('pagamentos_adicionais')
                        ->select( 'pagamentos_adicionais.*' )
                        ->where([['pagamentos_adicionais.se', '>=', 'SE/'.$superintendencia ]])
                        ->where([['pagamentos_adicionais.sigla_lotacao', 'like', '%' . trim($registro->descricao) . '%' ]])
                        ->where([['pagamentos_adicionais.ref', '>=', $ref ]])
                        ->where([['pagamentos_adicionais.rubrica', '=',  'AAG - Adic. de Atend. em Guichê' ]])
                        ->get();
                }

                //                  #######################   inicio          Distribuição ########################

                if(! $pagamentos_adicionais_dist->isEmpty()) {
                    $count_dist = $pagamentos_adicionais_dist->count('sigla_lotacao');
                }
                else {
                    $count_dist = 0;
                }
                if( $count_dist >= 1) {
                    DB::table('pgto_adicionais_temp')
                        ->where('codigo', '=', $registro->codigo)
                        ->where('numeroGrupoVerificacao', '=', $registro->numeroGrupoVerificacao)
                        ->where('numeroDoTeste', '=', $registro->numeroDoTeste)
                        ->delete(); // limpa dados anteriores existentes do empregado da tabela temporária
                }

                foreach ($pagamentos_adicionais_dist  as $adicionais) {

                    $situacao="Sem eventos de Distribuição Domiciliária.";
                    $mes = intval(substr($adicionais->ref,4,2));
                    $sgdo_distribuicao = DB::table('sgdo_distribuicao')
                        ->select('sgdo_distribuicao.*')
                        ->where([[ 'mcu', '>=', $registro->mcu ]])
                        ->where([[ 'matricula', '=', $adicionais->matricula ]])
                        ->whereMonth('data_termino_atividade', $mes)
                        ->get();
                    if(! $sgdo_distribuicao->isEmpty()) {
                        $count_sgdo = $sgdo_distribuicao->count('matricula');
                    }
                    else {
                        $count_sgdo = 0;
                    }
                    if(! $sgdo_distribuicao->isEmpty()) {
                        $pgtoAdicionaisTemp = new PgtoAdicionaisTemp();
                        $pgtoAdicionaisTemp->sto = $registro->sto;
                        $pgtoAdicionaisTemp->mcu = $registro->mcu;
                        $pgtoAdicionaisTemp->codigo = $registro->codigo;
                        $pgtoAdicionaisTemp->numeroGrupoVerificacao = $registro->numeroGrupoVerificacao;
                        $pgtoAdicionaisTemp->numeroDoTeste = $registro->numeroDoTeste;
                        $pgtoAdicionaisTemp->matricula = $adicionais->matricula;
                        $pgtoAdicionaisTemp->cargo = $adicionais->cargo;
                        $pgtoAdicionaisTemp->rubrica = $adicionais->rubrica;
                        $pgtoAdicionaisTemp->ref = $adicionais->ref;
                        $pgtoAdicionaisTemp->valor = $adicionais->valor;
                        $pgtoAdicionaisTemp->situacao = $situacao;

                        $ferias_por_mcu = DB::table('ferias_por_mcu')
                            ->select('ferias_por_mcu.*')
                            ->where([[ 'matricula', '=', $adicionais->matricula ]])
                            ->whereMonth('inicio_fruicao', $mes-1)
                            ->whereYaer('inicio_fruicao', $registro->ciclo)
                            ->count();

                        if ($ferias_por_mcu == 0){
                            $pgtoAdicionaisTemp->save();
                        }
                        else{
                            unset($pgtoAdicionaisTemp);
                        }
                    }
                }
//                  #######################  fim           Distribuição ########################

//                  #######################    inicio         Atendimento ########################
                if(! $pagamentos_adicionais_atend->isEmpty()) {
                    $count_atend = $pagamentos_adicionais_atend->count('matricula');
                }
                else {
                    $count_atend = 0;
                }

                foreach ($pagamentos_adicionais_atend  as $adicionais) {

                    $situacao="Sem eventos de atendimento a clientes.";
                    $mes = intval(substr($adicionais->ref,4,2));
                    $bdf_fat_02 = DB::table('bdf_fat_02')
                        ->select('bdf_fat_02.*')
                        ->where([[ 'cd_orgao', '>=', $registro->sto ]])
                        ->where([[ 'atendimento', '=', $adicionais->matricula ]])
                        ->whereMonth('dt_mov', $mes)
                        ->get();
                    if( ! $bdf_fat_02->isEmpty() ){

                        $pgtoAdicionaisTemp = new PgtoAdicionaisTemp();
                        $pgtoAdicionaisTemp->sto = $registro->sto;
                        $pgtoAdicionaisTemp->mcu = $registro->mcu;
                        $pgtoAdicionaisTemp->codigo = $registro->codigo;
                        $pgtoAdicionaisTemp->numeroGrupoVerificacao = $registro->numeroGrupoVerificacao;
                        $pgtoAdicionaisTemp->numeroDoTeste = $registro->numeroDoTeste;
                        $pgtoAdicionaisTemp->matricula = $adicionais->matricula;
                        $pgtoAdicionaisTemp->cargo = $adicionais->cargo;
                        $pgtoAdicionaisTemp->rubrica = $adicionais->rubrica;
                        $pgtoAdicionaisTemp->ref = $adicionais->ref;
                        $pgtoAdicionaisTemp->valor = $adicionais->valor;
                        $pgtoAdicionaisTemp->situacao = $situacao;

                        $ferias_por_mcu = DB::table('ferias_por_mcu')
                            ->select('ferias_por_mcu.*')
                            ->where([[ 'matricula', '=', $adicionais->matricula ]])
                            ->whereMonth('inicio_fruicao', $mes-1)
                            ->whereYear('inicio_fruicao', $registro->ciclo)
                            ->first();

                        if ($ferias_por_mcu->isEmpty()) {
                            $pgtoAdicionaisTemp->save();
                        }
                        else{
                            unset($pgtoAdicionaisTemp);
                        }
                    }
                }
//                  #######################    fim         Atendimento ########################

                if (( $count_atend >= 1 ) || ( $count_dist >= 1 )) {

                    $pgtoAdicionais = DB::table('pgto_adicionais_temp')
                        ->where('sto', '=', $registro->sto)
                        ->where('mcu', '=', $registro->mcu)
                        ->where('codigo', '=', $registro->codigo)
                        ->where('numeroGrupoVerificacao', '=', $registro->numeroGrupoVerificacao)
                        ->where('numeroDoTeste', '=', $registro->numeroDoTeste)
                        ->select(
                            'pgto_adicionais_temp.*'
                        )
                        ->get();
                    $total = $pgtoAdicionais->sum('valor');
                    $count = $pgtoAdicionais->count('matricula');

                    if ($count >= 1) {
                        $avaliacao = 'Não Conforme';
                        $oportunidadeAprimoramento = 'Em análise dos registros dos empregados contemplados com Adicionais de Distribuição e Coleta e de Atendimento em Guichê, do período de ' . substr($dtini, 4, 2) . '/' . substr($dtini, 0, 4) . ' até ' . substr($dtfim, 4, 2) . '/' . substr($dtfim, 0, 4) . ', constatou-se a existência de empregados que recebiam tais dicionais/funções sem desempenhar as atividades que lhes davam o direito ao recebimento.';

                        $evidencia = $evidencia . "\n" . '- Houve ' . $count . ' ocorrência(s) de pagamentos conforme a Seguir:';
                        $evidencia = $evidencia
                            . "\n" . 'Matricula'
                            . "\t" . 'Cargo'
                            . "\t" . 'Adicional'
                            . "\t" . 'Período de Rec. Adicional'
                            . "\t" . 'Valor ATT Recebido (R$)'
                            . "\t" . 'Situação Encontrada';
                        foreach ($pgtoAdicionais as $dados) {
                            $evidencia = $evidencia
                                . "\n" . $dados->matricula
                                . "\n" . $dados->cargo
                                . "\n" . $dados->rubrica
                                . "\n" . $dados->ref
                                . "\n" . $dados->valor
                                . "\n" . $dados->situacao;
                        }
                        $consequencias = $registro->consequencias;
                        $orientacao = $registro->orientacao;

                        $quebra = DB::table('relevancias')
                            ->select('valor_final')
                            ->where('fator_multiplicador', '=', 1)
                            ->first();
                        $quebracaixa = $quebra->valor_final * 0.1;

                        if ($valorFalta > $quebracaixa) {
                            $fm = DB::table('relevancias')
                                ->select('fator_multiplicador', 'valor_final', 'valor_inicio')
                                ->where('valor_inicio', '<=', $total)
                                ->orderBy('valor_final', 'desc')
                                ->first();
                            $pontuado = $registro->totalPontos * $fm->fator_multiplicador;
                        } else {
                            if ($avaliacao == 'Não Conforme') $pontuado = $registro->totalPontos * 1;
                        }

                    }
                    else{
                        $avaliacao = 'Conforme';
                        $oportunidadeAprimoramento =  'Em análise dos registros dos empregados contemplados com Adicionais de Distribuição e Coleta e de Atendimento em Guichê, período de '.substr($dtini,4,2).'/'.substr($dtini,0,4) .'até '.substr($dtfim,4,2).'/'.substr($dtfim,0,4).' Não foi identificado empregado(s) com recebimento(s) pela(s) Rubricas AADC-Adic.Ativ. Distrib/Coleta Ext. Bem como, Adic. de Atend. em Guichê na unidade.';
                        $consequencias = null;
                        $orientacao =  null;
                    }

                }

                else {
                    $avaliacao = 'Conforme';
                    $oportunidadeAprimoramento =  'Em análise dos registros dos empregados contemplados com Adicionais de Distribuição e Coleta e de Atendimento em Guichê, período de '.substr($dtini,4,2).'/'.substr($dtini,0,4) .'até '.substr($dtfim,4,2).'/'.substr($dtfim,0,4).' Não foi identificado empregado(s) com recebimento(s) pela(s) Rubricas AADC-Adic.Ativ. Distrib/Coleta Ext. Bem como, Adic. de Atend. em Guichê na unidade.';
                    $consequencias = null;
                    $orientacao =  null;

                }

                $dto = DB::table('itensdeinspecoes')
                    ->Where([['inspecao_id', '=', $registro->inspecao_id]])
                    ->Where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                    ->select('itensdeinspecoes.*')
                    ->first();

                $itensdeinspecao = Itensdeinspecao::find($dto->id);
                $itensdeinspecao->avaliacao = $avaliacao;
                $itensdeinspecao->oportunidadeAprimoramento = $oportunidadeAprimoramento;
                $itensdeinspecao->evidencia = $evidencia;
                $itensdeinspecao->valorFalta = $valorFalta;
                $itensdeinspecao->valorSobra = $valorSobra;
                $itensdeinspecao->valorRisco = $valorRisco;

//                $itensdeinspecao->situacao = 'Inspecionado';
                $itensdeinspecao->situacao   = 'Em Inspeção'; // presencial/remota

                $itensdeinspecao->pontuado = $pontuado;
                $itensdeinspecao->itemQuantificado = $itemQuantificado;
                $itensdeinspecao->orientacao = $registro->orientacao;
                $itensdeinspecao->eventosSistema = 'Item avaliado Remotamente por Websgi em ' . date('d/m/Y', strtotime($dtnow)) . '.';
                $itensdeinspecao->reincidencia = $reinc;
                $itensdeinspecao->consequencias = $consequencias;
                $itensdeinspecao->orientacao = $orientacao;
                $itensdeinspecao->codVerificacaoAnterior = $codVerificacaoAnterior;
                $itensdeinspecao->numeroGrupoReincidente = $numeroGrupoReincidente;
                $itensdeinspecao->numeroItemReincidente = $numeroItemReincidente;
                $itensdeinspecao->update();

            }
//final direito ao recebimento do provento

            if(($registro->numeroGrupoVerificacao==242) && ($registro->numeroDoTeste==1)) {
                $reincidencia = DB::table('snci')
                    ->select('no_inspecao',   'no_grupo',  'no_item','dt_fim_inspecao','dt_inic_inspecao')
                    ->where([['descricao_item',  'like', '%Exame Médico Periódico conforme frequência estabelecida na Norma%']])
                    ->where([['sto', '=', $registro->sto ]])
                    ->orderBy('no_inspecao' ,'desc')
                    ->first();

                if ($reincidencia === null){
                    $reinc = 'Não';
                    $codVerificacaoAnterior = null;
                    $numeroGrupoReincidente = null;
                    $numeroItemReincidente = null;
                }
                else{
                    $reinc = 'Sim';
                    $codVerificacaoAnterior = $reincidencia->no_inspecao;
                    $numeroGrupoReincidente = $reincidencia->no_grupo;
                    $numeroItemReincidente = $reincidencia->no_item;
                }
                DB::table('itensdeinspecoes')
                    ->where([['inspecao_id', '=', $registro->inspecao_id]])
                    ->where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                    ->update([
                        'codVerificacaoAnterior' => $codVerificacaoAnterior,
                        'numeroGrupoReincidente' => $numeroGrupoReincidente,
                        'numeroItemReincidente' => $numeroItemReincidente,
                        'reincidencia' => $reinc
                    ]);
            }

            if(($registro->numeroGrupoVerificacao==242) && ($registro->numeroDoTeste==2)) {
                $reincidencia = DB::table('snci')
                    ->select('no_inspecao',   'no_grupo',  'no_item','dt_fim_inspecao','dt_inic_inspecao')
                    ->where([['descricao_item',  'like', '%equipamentos de prevenção de incêndio%']])
                    ->where([['sto', '=', $registro->sto ]])
                    ->orderBy('no_inspecao' ,'desc')
                    ->first();

                if ($reincidencia === null){
                    $reinc = 'Não';
                    $codVerificacaoAnterior = null;
                    $numeroGrupoReincidente = null;
                    $numeroItemReincidente = null;
                }
                else{
                    $reinc = 'Sim';
                    $codVerificacaoAnterior = $reincidencia->no_inspecao;
                    $numeroGrupoReincidente = $reincidencia->no_grupo;
                    $numeroItemReincidente = $reincidencia->no_item;
                }
                DB::table('itensdeinspecoes')
                    ->where([['inspecao_id', '=', $registro->inspecao_id]])
                    ->where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                    ->update([
                        'codVerificacaoAnterior' => $codVerificacaoAnterior,
                        'numeroGrupoReincidente' => $numeroGrupoReincidente,
                        'numeroItemReincidente' => $numeroItemReincidente,
                        'reincidencia' => $reinc
                    ]);
            }

            if(($registro->numeroGrupoVerificacao==242) && ($registro->numeroDoTeste==3)) {
                $reincidencia = DB::table('snci')
                    ->select('no_inspecao',   'no_grupo',  'no_item','dt_fim_inspecao','dt_inic_inspecao')
                    ->where([['descricao_item',  'like', '%Alvará de Funcionamento%']])
                    ->where([['sto', '=', $registro->sto ]])
                    ->orderBy('no_inspecao' ,'desc')
                    ->first();

                if ($reincidencia === null){
                    $reinc = 'Não';
                    $codVerificacaoAnterior = null;
                    $numeroGrupoReincidente = null;
                    $numeroItemReincidente = null;
                }
                else{
                    $reinc = 'Sim';
                    $codVerificacaoAnterior = $reincidencia->no_inspecao;
                    $numeroGrupoReincidente = $reincidencia->no_grupo;
                    $numeroItemReincidente = $reincidencia->no_item;
                }
                DB::table('itensdeinspecoes')
                    ->where([['inspecao_id', '=', $registro->inspecao_id]])
                    ->where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                    ->update([
                        'codVerificacaoAnterior' => $codVerificacaoAnterior,
                        'numeroGrupoReincidente' => $numeroGrupoReincidente,
                        'numeroItemReincidente' => $numeroItemReincidente,
                        'reincidencia' => $reinc
                    ]);
            }

            if(($registro->numeroGrupoVerificacao==242) && ($registro->numeroDoTeste==4)) {
                $reincidencia = DB::table('snci')
                    ->select('no_inspecao',   'no_grupo',  'no_item','dt_fim_inspecao','dt_inic_inspecao')
                    ->where([['descricao_item',  'like', '%PPRA está vigente%']])
                    ->where([['sto', '=', $registro->sto ]])
                    ->orderBy('no_inspecao' ,'desc')
                    ->first();

                if ($reincidencia === null){
                    $reinc = 'Não';
                    $codVerificacaoAnterior = null;
                    $numeroGrupoReincidente = null;
                    $numeroItemReincidente = null;
                }
                else{
                    $reinc = 'Sim';
                    $codVerificacaoAnterior = $reincidencia->no_inspecao;
                    $numeroGrupoReincidente = $reincidencia->no_grupo;
                    $numeroItemReincidente = $reincidencia->no_item;
                }
                DB::table('itensdeinspecoes')
                    ->where([['inspecao_id', '=', $registro->inspecao_id]])
                    ->where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                    ->update([
                        'codVerificacaoAnterior' => $codVerificacaoAnterior,
                        'numeroGrupoReincidente' => $numeroGrupoReincidente,
                        'numeroItemReincidente' => $numeroItemReincidente,
                        'reincidencia' => $reinc
                    ]);
            }

            if(($registro->numeroGrupoVerificacao==242) && ($registro->numeroDoTeste==5)) {
                $reincidencia = DB::table('snci')
                    ->select('no_inspecao',   'no_grupo',  'no_item','dt_fim_inspecao','dt_inic_inspecao')
                    ->where([['descricao_item',  'like', '%Equipamentos de Proteção Individual%']])
                    ->where([['sto', '=', $registro->sto ]])
                    ->orderBy('no_inspecao' ,'desc')
                    ->first();

                if ($reincidencia === null){
                    $reinc = 'Não';
                    $codVerificacaoAnterior = null;
                    $numeroGrupoReincidente = null;
                    $numeroItemReincidente = null;
                }
                else{
                    $reinc = 'Sim';
                    $codVerificacaoAnterior = $reincidencia->no_inspecao;
                    $numeroGrupoReincidente = $reincidencia->no_grupo;
                    $numeroItemReincidente = $reincidencia->no_item;
                }
                DB::table('itensdeinspecoes')
                    ->where([['inspecao_id', '=', $registro->inspecao_id]])
                    ->where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                    ->update([
                        'codVerificacaoAnterior' => $codVerificacaoAnterior,
                        'numeroGrupoReincidente' => $numeroGrupoReincidente,
                        'numeroItemReincidente' => $numeroItemReincidente,
                        'reincidencia' => $reinc
                    ]);
            }

            if(($registro->numeroGrupoVerificacao==242) && ($registro->numeroDoTeste==6)) {
                $reincidencia = DB::table('snci')
                    ->select('no_inspecao',   'no_grupo',  'no_item','dt_fim_inspecao','dt_inic_inspecao')
                    ->where([['descricao_item',  'like', '%Livro de Inspeção do Trabalho%']])
                    ->where([['sto', '=', $registro->sto ]])
                    ->orderBy('no_inspecao' ,'desc')
                    ->first();

                if ($reincidencia === null){
                    $reinc = 'Não';
                    $codVerificacaoAnterior = null;
                    $numeroGrupoReincidente = null;
                    $numeroItemReincidente = null;
                }
                else{
                    $reinc = 'Sim';
                    $codVerificacaoAnterior = $reincidencia->no_inspecao;
                    $numeroGrupoReincidente = $reincidencia->no_grupo;
                    $numeroItemReincidente = $reincidencia->no_item;
                }
                DB::table('itensdeinspecoes')
                    ->where([['inspecao_id', '=', $registro->inspecao_id]])
                    ->where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                    ->update([
                        'codVerificacaoAnterior' => $codVerificacaoAnterior,
                        'numeroGrupoReincidente' => $numeroGrupoReincidente,
                        'numeroItemReincidente' => $numeroItemReincidente,
                        'reincidencia' => $reinc
                    ]);
            }

        }

        DB::table('itensdeinspecoes')
            ->where([['inspecao_id', '=', $registro->inspecao_id]])
            ->where([['situacao', '=', 'Concluido']])
            ->Orwhere([['situacao', '=', 'Não Respondido']])
            ->update([
                'situacao' => 'Em Inspeção'
            ]);

        ini_set('memory_limit', '128M');
    }
}
