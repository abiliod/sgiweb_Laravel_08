<?php

namespace App\Jobs;

use App\Models\Correios\Itensdeinspecao;
use App\Models\Correios\ModelsAuxiliares\SL02_bdf;
use App\Models\Correios\ModelsDto\AcessoFinalSemana;
use App\Models\Correios\ModelsDto\CompartilhaSenha;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class AvaliaInspecao implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */


    protected $superintendencias, $tipodeunidade, $ciclo;

    public function __construct($superintendencias, $tipodeunidade, $ciclo)
    {
        $this->superintendencias = $superintendencias;
        $this->tipodeunidade = $tipodeunidade;
        $this->ciclo = $ciclo;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ini_set('memory_limit', '512M');

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
        $pontuado = 0.00;
        $valorRisco = 0.00;
        $valorSobra = 0.00;
        $valorFalta = 0.00;

        $periodo = new CarbonPeriod();
        $total = 0.00;
        $ocorrencias = 0;
        $row = 0;
        $dtmax = null;
        $count = 0;
        $avaliacao = 'Conforme';
        $oportunidadeAprimoramento = '';

        $superintendencias = $this->superintendencias;
        $tipodeunidade = $this->tipodeunidade;
        $ciclo = $this->ciclo;

        foreach ($superintendencias as $res) { // request é Array de indice para Superintendências
            foreach ($res as $superintendencia) { // percorre o Array de objeto Superintendências

                // testa se o primeiro parâmetro é para todas superintendecia onde SE == 1
                // Inicio do teste para todas superintendencias
                if ($superintendencia == 1) {
                    // se verdadeiro se SE == 1 seleciona todas superintendência cujo a SE > 1
                    $registros = DB::table('itensdeinspecoes')
                        ->join('inspecoes', 'itensdeinspecoes.inspecao_id', '=', 'inspecoes.id')
                        ->join('unidades', 'itensdeinspecoes.unidade_id', '=', 'unidades.id')
                        ->join('testesdeverificacao', 'itensdeinspecoes.testeVerificacao_id', '=', 'testesdeverificacao.id')
                        ->join('gruposdeverificacao', 'itensdeinspecoes.grupoVerificacao_id', '=', 'gruposdeverificacao.id')
                        ->select('itensdeinspecoes.*', 'inspecoes.*', 'unidades.*', 'testesdeverificacao.*', 'gruposdeverificacao.*')
                        ->where([['situacao', '=', 'Em Inspeção']])
                        ->where([['se', '=', 1]])
                        ->where([['inspecoes.ciclo', '=', $ciclo]])
                        ->where([['itensdeinspecoes.tipoUnidade_id', '=', $tipodeunidade]])
                        ->get();

//                      Inicio processamento da aavaliação
                    foreach ($registros as $registro) {
                        $consequencias = $registro->consequencias;
                        $orientacao = $registro->orientacao;

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

                            $reincidencia = DB::table('snci')
                                ->select('no_inspecao', 'no_grupo', 'no_item', 'dt_fim_inspecao', 'dt_inic_inspecao')
                                ->where([['descricao_item', 'like', '%Os procedimentos de embarque e desembarque da carga%']])
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
                                foreach($controle_de_viagens as $controle_de_viagen){
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
                                            . ($controle_de_viagen->inicio_viagem == '' ? '   ----------  ' : \Carbon\Carbon::parse($controle_de_viagen->inicio_viagem)->format('d/m/Y')) . "\t"
                                            . ($controle_de_viagen->controle_viagem == '' ? '   ----------  ' : $controle_de_viagen->controle_viagem) . "\t"
                                            . ($controle_de_viagen->tipo_de_operacao == '' ? '   ----------  ' : $controle_de_viagen->tipo_de_operacao) . "\t"
                                            . ($controle_de_viagen->quantidade == '' ? '   ----------  ' : $controle_de_viagen->quantidade) . "\t"
                                            . ($controle_de_viagen->peso == '' ? '   ----------  ' : $controle_de_viagen->peso) . "\t"
                                            . ($controle_de_viagen->unitizador == '' ? '   ----------  ' : $controle_de_viagen->unitizador) . "\t"
                                            . ($controle_de_viagen->descricao_do_servico == '' ? '   ----------  ' : $controle_de_viagen->descricao_do_servico) . "\t"
                                            . ($controle_de_viagen->local_de_destino == '' ? '   ----------  ' : $controle_de_viagen->local_de_destino);
                                    }

                                    $evidencia = $evidencia. "\n" . ' Foram verificada(s) todas programações de viagens no período do dia '
                                        . \Carbon\Carbon::parse($dtini)->format('d/m/Y') . ' até ' . \Carbon\Carbon::parse($dtfim)->format('d/m/Y')
                                        . "\n" . ' a) verificou-se que houve ' . $count . ' viagen(s) realizadas e com possíveis operações de Embarque/Desembarque a serem realizadas.'
                                        . "\n" . ' b) Verificou a necessidade de aprimoramento na qualidade do apontamento colunas com falhas ou informações genéricas/incompletas.';

                                    if (intval($viagemNaorealizada / $viagens) >= 10) {
                                        // como trabalhamos com previsões se houver 10 % de viagens não realizadas adiciona a letra C.
                                        $evidencia = $evidencia  . "\n" . 'c) Adicionalmente no período havia '
                                            . $viagens. ' viagen(s) prevista(s) sendo que não houve apontamento de EMBARQUE/DESEMBARQUE para '
                                            . $viagemNaorealizada . ' viagens.';
                                    }

                                }
                                else{
                                    $avaliacao = 'Conforme';
                                    $oportunidadeAprimoramento = 'Em análise aos dados do sistema ERP - GESTÃO DE LINHAS DE TRANSPORTE, constatou-se que não havia operações de Embarque/Desembarque em falta ou incompletas.' .' Foram verificada(s) todas programações de viagens no período do dia ' .\Carbon\Carbon::parse($dtini)->format('d/m/Y').' até '.\Carbon\Carbon::parse($dtfim)->format('d/m/Y').'.';



                                }

                            }
                            else  {
                                $avaliacao = 'Não Conforme';
                                $oportunidadeAprimoramento = 'Em análise aos dados do sistema ERP - GESTÃO DE LINHAS DE TRANSPORTE, constatou-se o descumprimento dos procedimentos de embarque e desembarque da carga, conforme relatado a seguir:';
                                $evidencia = $evidencia . "\n" . '- Verificou-se que a unidade não está executando os lançamentos obrigatórios das informações de embarque e desembarque da carga no Sistema ERP. Não há histórico de registro de embarque/desembarque para troca de expedições. - Foram verificadas as programações de viagens no período de ' . \Carbon\Carbon::parse($dtini)->format('d/m/Y') . ' até ' . \Carbon\Carbon::parse($dtfim)->format('d/m/Y') .', sendo que em 100% das viagens não foi encontrado registros de apontamentos de Embarque/Desembarque.';
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
                            $itensdeinspecao->situacao = 'Inspecionado';
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

//                              echo $avaliacao , $dto->id.' - '.$avaliacao.' ,';
//                              if ($avaliacao == 'Não Conforme') dd('line 2495 -> Não Conforme ', $itensdeinspecao);
//                              if ($avaliacao == 'Conforme') dd('line 2496 -> Conforme ', $itensdeinspecao);
//                              dd($registro->sto , $reincidencia);

                            $itensdeinspecao->update();
                        }
// Final Controle de viagem

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
                            $itensdeinspecao->situacao = 'Inspecionado';
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

//                                echo $avaliacao , $dto->id.' - '.$avaliacao.' ,';
//                                if ($avaliacao == 'Não Conforme') dd('line 2495 -> Não Conforme ', $itensdeinspecao);
//                                if ($avaliacao == 'Conforme') dd('line 2496 -> Conforme ', $itensdeinspecao);
//                                dd($registro->sto , $reincidencia);

                            $itensdeinspecao->update();

//                                return view('compliance.inspecao.editar',
//                                    compact(
//                                        'registro'
//                                        , 'id'
//                                        , 'total'
//                                        , 'plplistapendentes'
//                                        ,'count'
//                                        ,'dtfim'
//                                    ));
                        }
// Final teste PLPs Pendentes

//              Início teste Compartilhamento de Senhas
                        if((($registro->numeroGrupoVerificacao==206) && ($registro->numeroDoTeste==2))
                            || (($registro->numeroGrupoVerificacao==335) && ($registro->numeroDoTeste==2))
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
                            //verifica histórico de inspeções

//                                DB::enableQueryLog();
//                                dd( DB::getQueryLog());

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
                                $dataultimoevento = \Carbon\Carbon::parse($dtmax)->format('d/m/Y');

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
                                        $compartilhaSenha = DB::table('compartilhasenhas')
                                            ->where('codigo', '=', $registro->codigo)
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
                                                $compartilhaSenha->codigo = $registro->codigo;
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

                                $compartilhaSenhas = DB::table('compartilhasenhas')
                                    ->where('codigo', '=', $registro->codigo)
                                    ->where('numeroGrupoVerificacao', '=', $registro->numeroGrupoVerificacao)
                                    ->where('numeroDoTeste', '=', $registro->numeroDoTeste)
                                    ->orderBy('data', 'asc')
                                    ->get();

                                $count = $compartilhaSenhas->count('codigo');
                                $dtmax = \Carbon\Carbon::parse($eventos->max('data'))->format('d/m/Y');
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
                            $itensdeinspecao->situacao = 'Inspecionado';
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

//                                if ($avaliacao == 'Não Conforme') dd('line 2325 -> Não Conforme ', $itensdeinspecao);
//                                if ($avaliacao == 'Conforme') dd('line 2326 -> Conforme ', $itensdeinspecao);
//                                if ($naoMonitorado <>'') dd('line 2326 -> Conforme ', $itensdeinspecao);
//                                dd($registro->sto , $reincidencia, $eventos, $compartilhaSenhas);

                            $itensdeinspecao->update();


//                                return view('compliance.inspecao.editar',compact
//                                (
//                                    'registro'
//                                    , 'id'
//                                    , 'total'
//                                    , 'compartilhaSenhas'
//                                    , '$count'
//                                    , '$dtmenos12meses'
//                                    , 'dtnow'
//                                    , 'naoMonitorado'
//                                ));

                        }
//             Final teste Compartilhamento de Senhas


//  Inicio  do teste Alarme Arme/desarme

                        if((($registro->numeroGrupoVerificacao == 206 )&&($registro->numeroDoTeste == 1 ))
                            || (( $registro->numeroGrupoVerificacao == 335 ) && ( $registro->numeroDoTeste == 1 ))
                            || (($registro->numeroGrupoVerificacao == 232)&&($registro->numeroDoTeste == 3 ))
                            || (( $registro->numeroGrupoVerificacao == 272 ) && ( $registro->numeroDoTeste == 2 )))
                        {
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

                            //verifica histórico de inspeções
                            $reincidencia = DB::table('snci')
                                ->select('no_inspecao', 'no_grupo', 'no_item', 'dt_fim_inspecao', 'dt_inic_inspecao')
                                ->where([['descricao_item', 'like', '%alarme funciona corretamente%']])
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
                                                $row => ['Acesso' => \Carbon\Carbon::parse($evento->data)->format('d/m/Y'), 'hora' => $evento->hora],
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


//                                            \Session::flash('mensagem',['msg'=> auth()->user()->name.', Base de Dados da Unidade não atualizada.
//                                                                         Atualize os horários de funcionamento.'
//                                                ,'class'=>'red white-text']);
//                                            return redirect()->route('compliance.unidades.editar',$registro->unidade_id);

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
                                    $evidencia = $evidencia  . "\n" . '$tabela->evAbertura'
                                        . "\t" . '$tabela->evDataAbertura' . "\t"
                                        . '$tabela->evHoraAbertura'
                                        . "\t"
                                        . '$tabela->evFechamento'
                                        . "\t"
                                        . '$tabela->evHoraFechamento'
                                        . "\t" . '$tabela->diaSemana'
                                        . "\t" . '$tabela->tempoPermanencia';
                                }
                            }

                            if(isset($tempoAbertura)&&(!empty($tempoAbertura))){

                                $evidencia = $evidencia."\n" . $rowAberturaFinalSemana .'Tempo de abertura em Relação ao Horário de Atendimento conforme a seguir:';
                                $evidencia = $evidencia . "\n" . 'Data' . "\t" . 'Horário Atendimento' . "\t" . 'Horário da Abertura' . "\t" . 'Tempo Abertura';
                                foreach ($tempoAbertura  as $tempo => $mdaData){
                                    $evidencia = $evidencia . "\n"
                                        . date('d/m/Y', strtotime($mdaData["dataInicioExpediente"]))
                                        . "\t" . $mdaData["InicioExpediente"] . "\t" . $mdaData["HorárioDeAbertura"]
                                        . "\t" . $mdaData["DiferencaTempoDeAbertura"];
                                }

                            }

                            if(isset($tempoAberturaAntecipada)&&(!empty($tempoAberturaAntecipada))){

                                $evidencia = $evidencia."\n" . $rowAberturaFinalSemana .' - Unidade em Risco. Abertura da Unidade em horário fora do padrão em relação ao horário de abertura da unidade conforme a seguir';
                                $evidencia = $evidencia . "\n" . 'Data' . "\t" . 'Data Abertura' . "\t" . 'Horário de Atendimento' . "\t" . 'Hora da Abertura' . "\t" . 'Tempo Abertura';
                                foreach ($tempoAberturaAntecipada  as $tempo => $mdaData){

                                    $evidencia = $evidencia . "\n"
                                        . date('d/m/Y', strtotime($mdaData["dataInicioExpediente"]))
                                        . "\t" . $mdaData["InicioExpediente"] . "\t" . $mdaData["HorárioDeAbertura"]
                                        . "\t" . $mdaData["DiferencaTempoDeAbertura"];

                                }
                            }

                            if(isset($tempoAberturaPosExpediente)&&(!empty($tempoAberturaPosExpediente))){
                                $evidencia = $evidencia."\n".'Unidade em Risco. Abertura da unidade em horário fora do padrão em relação ao horário de fechamento da unidade conforme a seguir:';
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
                                $evidencia = $evidencia."\n".'Unidade em Risco. Abertura da unidade em dia de feriado conforme a seguir:';
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
                            $itensdeinspecao->situacao = 'Inspecionado';
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
//                          echo 'line 1400 -> '.$dto->id , $itensdeinspecao;
                            $itensdeinspecao->update();


//                                dd( $naoMonitorado , $aviso , $alarmesFinalSemana, $feriadoporUnidades,  $eventosf , $eventos);



//                                return view('compliance.inspecao.editar',compact
//                                (
//                                    'registro'
//                                    , 'id'
//                                    , 'total'
//                                    , 'acessos_final_semana'
//                                    , 'rowAberturaFinalSemana'
//                                    , 'count_alarmesFinalSemana'
//                                    , 'acessosEmFeriados'
//                                    , 'tempoAbertura'
//                                    , 'tempoAberturaAntecipada'
//                                    , 'tempoAberturaPosExpediente'
//                                    , 'aviso'
//                                    , 'dtmax'
//                                    , 'now'
//                                    ,'dtmenos12meses'
//                                    ,'naoMonitorado'
//                                ));


                        }

//  Final  do teste Alarme Arme/desarme

//                      Inicio  do teste Extravio Responsabilidade Definida
                        if ((($registro->numeroGrupoVerificacao == 205) && ($registro->numeroDoTeste == 2))
                            || (($registro->numeroGrupoVerificacao == 334) && ($registro->numeroDoTeste == 1))
                            || (($registro->numeroGrupoVerificacao == 372) && ($registro->numeroDoTeste == 1))
                            || (($registro->numeroGrupoVerificacao == 286) && ($registro->numeroDoTeste == 2))
                            || (($registro->numeroGrupoVerificacao == 221) && ($registro->numeroDoTeste == 2))
                            || (($registro->numeroGrupoVerificacao == 354) && ($registro->numeroDoTeste == 1))
                            || (($registro->numeroGrupoVerificacao == 231) && ($registro->numeroDoTeste == 1))
                            || (($registro->numeroGrupoVerificacao == 271) && ($registro->numeroDoTeste == 1))) {


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

                                } else {
                                    $resp_definidas = DB::table('resp_definidas')
                                        ->select('mcu', 'unidade', 'data_pagamento', 'objeto', 'nu_sei', 'data', 'situacao', 'valor_da_indenizacao')
                                        ->where('mcu', '=', $registro->mcu)
                                        ->where('data_pagamento', '<=', $dtmenos90dias)
                                        ->where('nu_sei', '=', '')
                                        ->get();
                                }
                            } catch (\Exception $e) {

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
                                $valorFalta = $total;
                                $evidencia = $evidencia . "\n" . 'Número Objeto' . "\t" . 'Número Processo' . "\t" . 'Data Processo' . "\t" . 'Data Atualização' . "\t" . 'Última Atualização' . "\t" . 'Valor';

                                foreach ($resp_definidas as $tabela) {
                                    foreach ($resp_definidas as $tabela) {
//      ########## ATENÇÃO ##########
// 01/04/2020 Abilio esse trecho de código precisa ser testado não havia dados suficiete para implementar o
// teste no desenvolvimento caso houver algum ajuste  aualizar o controller InspeçãoController para esse item.

                                        $evidencia = $evidencia . "\n" . $tabela->objeto . "\t"
                                            . (isset($tabela->nu_sei) && $tabela->nu_sei == '' ? '   ----------  ' : $tabela->nu_sei)
                                            . "\t" . (isset($tabela->data_pagamento) && $tabela->data_pagamento == '' ? '   ----------  '
                                                : date('d/m/Y', strtotime($tabela->data_pagamento)))
                                            . "\t" . (isset($tabela->data) && $tabela->data == '' ? '   ----------  '
                                                : date('d/m/Y', strtotime($tabela->data)))
                                            . "\t" . (isset($tabela->situacao) && $tabela->situacao == '' ? '   ----------  '
                                                : $tabela->situacao)
                                            . "\t" . 'R$' . number_format($tabela->valor_da_indenizacao, 2, ',', '.');
                                    }
                                    $evidencia = $evidencia . "\n" . 'Valor em Falta :' . "\t" . 'R$' . number_format($valorFalta, 2, ',', '.');
//        ####################
                                }
                            }
                            else {
                                    $dtmax = $dtmenos90dias;
                                    $avaliacao = 'Conforme';
                                    $oportunidadeAprimoramento = 'Em análise à planilha de controle de processos de apuração de extravios de objetos indenizados com responsabilidade definida, disponibilizada pela área de Segurança da Superintendência Regional CSEP, que detem informações a partir de 2015 até ' . date('d/m/Y', strtotime($dtmax)) . ', constatou-se a inexistência de processos pendentes de conclusão há mais de 90 dias sob responsabilidade da unidade.';
                                }

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
                                    if($avaliacao == 'Não Conforme') $pontuado = $registro->totalPontos * $fm->fator_multiplicador;
                                } else {
                                    if ($avaliacao == 'Não Conforme') $pontuado = $registro->totalPontos * 1;
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
                                $itensdeinspecao->situacao = 'Inspecionado';
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
//                          dd('line 1400 -> ',$itensdeinspecao);
                                $itensdeinspecao->update();
//
//                                    return view('compliance.inspecao.editar', compact
//                                    (
//                                        'registro'
//                                        , 'id'
//                                        , 'total'
//                                        , 'resp_definidas'
//                                        , 'dtmax'
//                                        , 'dtmin'
//                                        , 'count'
//                                    ));

                            }
//                      Final  do teste Extravio Responsabilidade Definida

//                       Inicio  do teste SLD-02-BDF
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
                                $itemQuantificado = 'Não';
                                $reincidente = 0;
                                $reinc = 'Não';

                                $sl02bdfsMaxdata = SL02_bdf::where('cod_orgao', $registro->sto)->max('dt_movimento');

                                if (!empty($sl02bdfsMaxdata)) {
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

                                    if (!$sl02bdfs30->isEmpty()) {
                                        $acumulados30 = $sl02bdfs30->sum('diferenca'); // soma a coluna valor da coleção de dados
                                        $ocorrencias30 = $sl02bdfs30->count('diferenca');
                                        $evidencia = $evidencia . "\n" . 'Período '
                                            . date('d/m/Y', strtotime($sl02bdfsMaxdata)) . ', até '
                                            . date('d/m/Y', strtotime($dtmenos30dias)) . '.';
                                        $evidencia = $evidencia . "\n" . 'Data' . "\t" . 'Saldo de Numerário' . "\t" . 'Limite de Saldo' . "\t" . 'Diferença';
                                        $row = 1;
                                        foreach ($sl02bdfs30 as $tabela) {

                                            $evidencia = $evidencia . "\n" . date('d/m/Y', strtotime($tabela->dt_movimento))
                                                . "\t" . 'R$' . number_format($tabela->saldo_atual, 2, ',', '.')
                                                . "\t" . 'R$' . number_format($tabela->limite, 2, ',', '.')
                                                . "\t" . 'R$' . number_format($tabela->diferenca, 2, ',', '.');
                                            $row++;
                                        }
                                        $acumulados30 = $acumulados30 / $row;
                                        $evidencia = $evidencia . "\n" . 'Quantidade de ocorrências em 30 dias ' . $ocorrencias30
                                            . '. Quantidade média de ocorrências em 30 dias '
                                            . number_format((($ocorrencias30 / 23) * 100), 2, ',', '.')
                                            . '. Valor médio ultrapassado R$ '
                                            . number_format($acumulados30, 2, ',', '.');

                                    }

                                    $sl02bdfs60 = DB::table('sl02bdfs')
                                        ->select('sl02bdfs.*')
                                        ->where('cod_orgao', '=', $registro->sto)
                                        ->where('dt_movimento', '<', $dtmenos30dias)
                                        ->where('dt_movimento', '>=', $dtmenos60dias)
                                        ->where('diferenca', '>=', 1)
                                        ->orderBy('dt_movimento', 'desc')
                                        ->get();

                                    if (!$sl02bdfs60->isEmpty()) {
                                        $acumulados60 = $sl02bdfs60->sum('diferenca'); // soma a coluna valor da coleção de dados
                                        $ocorrencias60 = $sl02bdfs60->count('diferenca');
                                        $evidencia = $evidencia . "\n" . 'Período '
                                            . date('d/m/Y', strtotime($dtmenos30dias)) . ', até '
                                            . date('d/m/Y', strtotime($dtmenos60dias)) . '.';
                                        $evidencia = $evidencia . "\n" . 'Data' . "\t" . 'Saldo de Numerário' . "\t" . 'Limite de Saldo' . "\t" . 'Diferença';
                                        $row = 1;
                                        foreach ($sl02bdfs60 as $tabela) {

                                            $evidencia = $evidencia . "\n" . date('d/m/Y', strtotime($tabela->dt_movimento))
                                                . "\t" . 'R$' . number_format($tabela->saldo_atual, 2, ',', '.')
                                                . "\t" . 'R$' . number_format($tabela->limite, 2, ',', '.')
                                                . "\t" . 'R$' . number_format($tabela->diferenca, 2, ',', '.');
                                            $row++;
                                        }
                                        $acumulados60 = $acumulados60 / $row;
                                        $evidencia = $evidencia . "\n" . 'Quantidade de ocorrências em 30 dias ' . $ocorrencias60
                                            . '. Quantidade média de ocorrências em 30 dias '
                                            . number_format((($ocorrencias60 / 23) * 100), 2, ',', '.')
                                            . '. Valor médio ultrapassado R$ '
                                            . number_format($acumulados60, 2, ',', '.');

                                    }

                                    $sl02bdfs90 = DB::table('sl02bdfs')
                                        ->select('sl02bdfs.*')
                                        ->where('cod_orgao', '=', $registro->sto)
                                        ->where('dt_movimento', '<', $dtmenos60dias)
                                        ->where('dt_movimento', '>=', $dtmenos90dias)
                                        ->where('diferenca', '>=', 1)
                                        ->orderBy('dt_movimento', 'desc')
                                        ->get();

                                    if (!$sl02bdfs90->isEmpty()) {
                                        $acumulados90 = $sl02bdfs90->sum('diferenca'); // soma a coluna valor da coleção de dados
                                        $ocorrencias90 = $sl02bdfs90->count('diferenca');
                                        $evidencia = $evidencia . "\n" . 'Período '
                                            . date('d/m/Y', strtotime($dtmenos60dias)) . ', até '
                                            . date('d/m/Y', strtotime($dtmenos90dias)) . '.';
                                        $evidencia = $evidencia . "\n" . 'Data' . "\t" . 'Saldo de Numerário' . "\t" . 'Limite de Saldo' . "\t" . 'Diferença';
                                        $row = 1;
                                        foreach ($sl02bdfs90 as $tabela) {

                                            $evidencia = $evidencia . "\n" . date('d/m/Y', strtotime($tabela->dt_movimento))
                                                . "\t" . 'R$' . number_format($tabela->saldo_atual, 2, ',', '.')
                                                . "\t" . 'R$' . number_format($tabela->limite, 2, ',', '.')
                                                . "\t" . 'R$' . number_format($tabela->diferenca, 2, ',', '.');
                                            $row++;
                                        }
                                        $acumulados90 = $acumulados90 / $row;
                                        $evidencia = $evidencia . "\n" . 'Quantidade de ocorrências em 30 dias ' . $ocorrencias90
                                            . '. Quantidade média de ocorrências em 30 dias '
                                            . number_format((($ocorrencias90 / 23) * 100), 2, ',', '.')
                                            . '. Valor médio ultrapassado R$ '
                                            . number_format($acumulados90, 2, ',', '.');

                                    }

                                    if (($acumulados30 >= 1) && ($acumulados60 >= 1) && ($acumulados90 >= 1)) {
                                        $total = ($acumulados30 + $acumulados60 + $acumulados90) / 3;
                                        $ocorrencias = $ocorrencias30 + $ocorrencias60 + $ocorrencias90;
                                    }

                                    if (($acumulados30 >= 1) && ($acumulados60 >= 1) && ($acumulados90 == 0)) {
                                        $total = ($acumulados30 + $acumulados60) / 2;
                                        $ocorrencias = $ocorrencias30 + $ocorrencias60;
                                    }

                                    if (($acumulados30 >= 1) && ($acumulados60 == 0) && ($acumulados90 == 0)) {
                                        $total = $acumulados30;
                                        $ocorrencias = $ocorrencias30;
                                    }

                                    if (($acumulados30 == 0) && ($acumulados60 >= 1) && ($acumulados90 == 0)) {
                                        $total = $acumulados60;
                                        $ocorrencias = $ocorrencias60;
                                    }

                                    if (($acumulados30 == 0) && ($acumulados60 == 0) && ($acumulados90 >= 1)) {
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
                                            . $ocorrencias . ' dias, o que corresponde a uma média de ' . $ocorrencias / 3 . ' ocorrências por mês, considerando o período, conforme detalhado a seguir:';

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
                                        } catch (\Exception $e) {
                                            $reincidente = 0;
                                            $reinc = 'Não';
                                        }
                                        if ($total > 0.00) {
                                            $itemQuantificado = 'Sim';
                                            $evidencia = $evidencia . "\n" . 'Em Risco ' . number_format($total, 2, ',', '.');
                                            $valorFalta = null;
                                            $valorSobra = null;
                                            $valorRisco = $total;
                                        }

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
                                            if($avaliacao == 'Não Conforme') $pontuado = $registro->totalPontos * $fm->fator_multiplicador;
                                        } else {
                                            if ($avaliacao == 'Não Conforme') $pontuado = $registro->totalPontos * 1;
                                        }
                                    } else {
                                        $avaliacao = 'Conforme';
                                        $oportunidadeAprimoramento = 'Em análise ao Relatório "Saldo de Numerário em relação ao Limite
                                         de Saldo", do sistema BDF, referente ao período de ' . date('d/m/Y', strtotime($dtnow)) . ' a '
                                            . date('d/m/Y', strtotime($dtmenos90dias)) . ',
                                            constatou-se que não houve descumprimento do limite de saldo estabelecido para a unidade.';
                                    }
                                } else {
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
                                $itensdeinspecao->situacao = 'Inspecionado';
                                $itensdeinspecao->pontuado = $pontuado;
                                $itensdeinspecao->itemQuantificado = $itemQuantificado;
                                $itensdeinspecao->orientacao = $registro->orientacao;
                                $itensdeinspecao->eventosSistema = 'Item avaliado Remotamente por Websgi em ' . date('d/m/Y', strtotime($dtnow)) . '.';
                                $itensdeinspecao->reincidencia = $reinc;
                                $itensdeinspecao->codVerificacaoAnterior = $codVerificacaoAnterior;
                                $itensdeinspecao->numeroGrupoReincidente = $numeroGrupoReincidente;
                                $itensdeinspecao->numeroItemReincidente = $numeroItemReincidente;
//                                                dd('line 1277 -> ',$itensdeinspecao);
                                $itensdeinspecao->update();
                            }
//                       Final  do teste SLD-02-BDF

//                       Inicio  do teste SMB_BDF
                            if ((($registro->numeroGrupoVerificacao == 230) && ($registro->numeroDoTeste == 6))
                                || (($registro->numeroGrupoVerificacao == 270) && ($registro->numeroDoTeste == 3))) {

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
                                        $evidencia = null;
                                    }
                                } catch (\Exception $e) {
                                    $reincidente = 0;
                                    $reinc = 'Não';
                                    $codVerificacaoAnterior = null;
                                    $numeroGrupoReincidente = null;
                                    $numeroItemReincidente = null;
                                    $evidencia = null;
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
                                                $oportunidadeAprimoramento = 'Em análise ao sistema SDE – Sistema de Depósito Bancário, na opção "Contabilização", Conciliação SMB x BDF – dados “Não Conciliados”, referente ao período de ' . date('d/m/Y', strtotime($dtmenos90dias)) . ' a ' . date('d/m/Y', strtotime($dtnow)) . ', verificou-se a inexistência de divergências.';
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
                                                $itensdeinspecao->consequencias = null;
                                                $itensdeinspecao->situacao = 'Inspecionado';
                                                $itensdeinspecao->pontuado = 0.00;
                                                $itensdeinspecao->itemQuantificado = 'Não';
                                                $itensdeinspecao->orientacao = null;
                                                $itensdeinspecao->eventosSistema = 'Item avaliado remotamente por Websgi em ' . date('d/m/Y', strtotime($dtnow)) . '.';
//                                            dd('line -> 818' ,$itensdeinspecao);
//                                            $itensdeinspecao->update();

                                            }
//                                          Final Testa ultimo registro se tem compensação
//                                          Inicio Testa ultimo registro com compensação
                                            else {

                                                $avaliacao = 'Não Conforme';
                                                $oportunidadeAprimoramento = 'Em análise ao sistema SDE – Sistema de Depósito Bancário, na opção "Contabilização", Conciliação SMB x BDF – dados “Não Conciliados”, referente ao período de ' . date('d/m/Y', strtotime($dtmenos90dias)) . ' a ' . date('d/m/Y', strtotime($dtnow)) . ', constatou-se a existência de divergências entre o valor depositado na conta bancária dos Correios pela Agência e o valor do bloqueto gerado no sistema SARA, no total de R$ ' . number_format($divergencia, 2, ',', '.') . ' , conforme relacionado a seguir:';

                                                $evidencia = $evidencia . "\n" . 'Data' . "\t" . 'Divergência' . "\t" . 'Tipo';
                                                foreach ($smb_bdf_naoconciliados as $smb_bdf_naoconciliado) {
                                                    $evidencia = $evidencia . "\n"
                                                        . date('d/m/Y', strtotime($smb_bdf_naoconciliado->Data))
                                                        . "\t" . 'R$ ' . number_format($smb_bdf_naoconciliado->Divergencia, 2, ',', '.');

                                                    if (($smb_bdf_naoconciliado->BDFDinheiro <> 0) && ($smb_bdf_naoconciliado->BDFCheque <> 0) && ($smb_bdf_naoconciliado->BDFBoleto <> 0)) {
                                                        $evidencia = $evidencia . "\t" . 'Dinheiro/Cheque/Boleto';
                                                    } elseif (($smb_bdf_naoconciliado->BDFDinheiro <> 0) && ($smb_bdf_naoconciliado->BDFBoleto <> 0)) {
                                                        $evidencia = $evidencia . "\t" . 'Dinheiro/Boleto';
                                                    } elseif (($smb_bdf_naoconciliado->BDFDinheiro <> 0) && ($smb_bdf_naoconciliado->BDFCheque <> 0)) {
                                                        $evidencia = $evidencia . "\t" . 'Dinheiro/Cheque';
                                                    } elseif (($smb_bdf_naoconciliado->BDFBoleto <> 0) && ($smb_bdf_naoconciliado->BDFCheque <> 0)) {
                                                        $evidencia = $evidencia . "\t" . 'Boleto/Cheque';
                                                    } elseif ($smb_bdf_naoconciliado->BDFDinheiro <> 0) {
                                                        $evidencia = $evidencia . "\t" . 'Dinheiro';
                                                    } elseif ($smb_bdf_naoconciliado->BDFBoleto <> 0) {
                                                        $evidencia = $evidencia . "\t" . 'Boleto';
                                                    } elseif ($smb_bdf_naoconciliado->BDFCheque <> 0) {
                                                        $evidencia = $evidencia . "\t" . 'Cheque';
                                                    } else {
                                                        $evidencia = $evidencia . "\t" . 'Não identificado';
                                                    }
                                                }

                                                if ($divergencia > 0.00) {
                                                    $total = $divergencia;
                                                    $evidencia = $evidencia . "\n" . 'Em Falta ' . $divergencia;
                                                    $valorFalta = $total;
                                                    $valorSobra = null;
                                                    $valorRisco = null;

                                                } else {
                                                    $total = $divergencia * -1;
                                                    $evidencia = $evidencia . "\n" . 'Em Falta ' . $total;
                                                    $valorSobra = null;
                                                    $valorFalta = $total;
                                                    $valorRisco = null;
                                                }
//                                                dd('line 876',  $smb , $bdf ,$divergencia, $total );

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

//                                                dd('line 821',  $smb , $bdf ,$divergencia, $total );

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
                                                $itensdeinspecao->situacao = 'Inspecionado';
                                                $itensdeinspecao->pontuado = $pontuado;
                                                $itensdeinspecao->itemQuantificado = 'Sim';
                                                $itensdeinspecao->orientacao = $registro->orientacao;
                                                $itensdeinspecao->eventosSistema = 'Item avaliado Remotamente por Websgi em ' . date('d/m/Y', strtotime($dtnow)) . '.';
                                                $itensdeinspecao->reincidencia = $reinc;
                                                $itensdeinspecao->codVerificacaoAnterior = $codVerificacaoAnterior;
                                                $itensdeinspecao->numeroGrupoReincidente = $numeroGrupoReincidente;
                                                $itensdeinspecao->numeroItemReincidente = $numeroItemReincidente;
//                                                dd('line 917 -> ',$itensdeinspecao);
                                                $itensdeinspecao->update();
                                            }
//                                          Final Testa ultimo registro com compensação
                                        }
//                                      Final  se divergencia é um valor diferente de zero

//                                      Inicio  se divergencia é um valor igual zero
                                        if ($divergencia == 0.0) {

                                            $dataanterior = null;
                                            foreach ($smb_bdf_naoconciliados as $smb_bdf_naoconciliado) {
                                                if ($dataanterior !== null) {
                                                    $dataantual = $dataanterior;
                                                    $dataantual->addDays(1);
                                                    $unidade_enderecos = DB::table('unidade_enderecos')
                                                        ->Where([['mcu', '=', $registro->mcu]])
                                                        ->select('unidade_enderecos.*')
                                                        ->first();
                                                    $feriado = DB::table('feriados')
                                                        ->Where([['data_do_feriado', '=', $dataantual]])
                                                        ->Where([['nome_municipio', '=', $unidade_enderecos->cidade]])
                                                        ->Where([['uf', '=', $unidade_enderecos->uf]])
                                                        ->select('feriados.*')
                                                        ->first();

                                                    if ($feriado) {
                                                        $diasemana = $dataanterior;
                                                        $diasemana->addDays(5);
                                                    } else {
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

                                                    if ($periodo->count() > 1) {
                                                        $avaliacao = 'Não Conforme';
                                                        $oportunidadeAprimoramento = 'Em análise ao sistema SDE – Sistema de Depósito Bancário, na opção "Contabilização", Conciliação SMB x BDF – dados “Não Conciliados”,  referente ao período de ' . date('d/m/Y', strtotime($dtmenos90dias)) . ' a ' . date('d/m/Y', strtotime($dtnow)) . ', constatou-se a existência de depositos na conta dos Correios pela Agência com prazo superior D+1. Evento em data anterior à ' . date('d/m/Y', strtotime($dataanterior));
                                                        $total = $smb_bdf_naoconciliado->BDFBoleto;
                                                        $valorRisco = $smb_bdf_naoconciliado->BDFBoleto;
                                                        break;
                                                    }
                                                }
                                                $dataanterior = new Carbon($smb_bdf_naoconciliado->Data);
                                            }
                                            if ($periodo->count() > 1) {
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

                                                $evidencia = $evidencia . "\n" . 'Data' . "\t" . 'Valor do Boleto';
                                                foreach ($smb_bdf_naoconciliados as $smb_bdf_naoconciliado) {
                                                    $evidencia = $evidencia . "\n"
                                                        . date('d/m/Y', strtotime($smb_bdf_naoconciliado->Data))
                                                        . "\t" . 'R$ ' . number_format($smb_bdf_naoconciliado->BDFBoleto, 2, ',', '.');
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
                                                $itensdeinspecao->situacao = 'Inspecionado';
                                                $itensdeinspecao->pontuado = $pontuado;
                                                $itensdeinspecao->itemQuantificado = 'Sim';
                                                $itensdeinspecao->orientacao = $registro->orientacao;
                                                $itensdeinspecao->consequencias = null;
                                                $itensdeinspecao->eventosSistema = 'Item avaliado Remotamente por Websgi em ' . date('d/m/Y', strtotime($dtnow)) . '.';
                                                $itensdeinspecao->reincidencia = $reinc;
                                                $itensdeinspecao->codVerificacaoAnterior = $codVerificacaoAnterior;
                                                $itensdeinspecao->numeroGrupoReincidente = $numeroGrupoReincidente;
                                                $itensdeinspecao->numeroItemReincidente = $numeroItemReincidente;
//                                                dd('line 977 ->  valor em risco ',$itensdeinspecao);
                                                $itensdeinspecao->update();
                                            } else {
                                                $avaliacao = 'Conforme';
                                                $oportunidadeAprimoramento = 'Em análise ao sistema SDE – Sistema de Depósito Bancário, na opção "Contabilização", Conciliação SMB x BDF – dados “Não Conciliados”, referente ao período de ' . date('d/m/Y', strtotime($dtmenos90dias)) . ' a ' . date('d/m/Y', strtotime($dtnow)) . ', verificou-se a inexistência de divergências.';
                                                $dto = DB::table('itensdeinspecoes')
                                                    ->Where([['inspecao_id', '=', $registro->inspecao_id]])
                                                    ->Where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                                                    ->select('itensdeinspecoes.*')
                                                    ->first();
                                                $itensdeinspecao = Itensdeinspecao::find($dto->id);
                                                $itensdeinspecao->avaliacao = $avaliacao;
                                                $itensdeinspecao->oportunidadeAprimoramento = $oportunidadeAprimoramento;
                                                $itensdeinspecao->evidencia = null;
                                                $itensdeinspecao->valorFalta = 0.00;
                                                $itensdeinspecao->situacao = 'Inspecionado';
                                                $itensdeinspecao->pontuado = 0.00;
                                                $itensdeinspecao->itemQuantificado = 'Não';
                                                $itensdeinspecao->orientacao = null;
                                                $itensdeinspecao->eventosSistema = 'Item avaliado remotamente por Websgi em ' . date('d/m/Y', strtotime($dtnow)) . '.';
//                                                dd('line -> 994' ,$itensdeinspecao);
                                                $itensdeinspecao->update();
                                            }
                                        }
//                                      Final  se divergencia é um valor igual zero
                                    }
//                              Final  se há divergencia

                                }
//                              Final  se tem registro de pendências SMB_BDF
//                              Inicio  se Não tem registro de pendências SMB_BDF
                                else {
                                    $avaliacao = 'Conforme';
                                    $oportunidadeAprimoramento = 'Em análise ao sistema SDE – Sistema de Depósito Bancário, na opção "Contabilização", Conciliação SMB x BDF – dados “Não Conciliados”, referente ao período de ' . date('d/m/Y', strtotime($dtmenos90dias)) . ' a ' . date('d/m/Y', strtotime($dtnow)) . ', verificou-se a inexistência de divergências.';

                                    $dto = DB::table('itensdeinspecoes')
                                        ->Where([['inspecao_id', '=', $registro->inspecao_id]])
                                        ->Where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                                        ->select('itensdeinspecoes.*')
                                        ->first();
                                    $itensdeinspecao = Itensdeinspecao::find($dto->id);
                                    $itensdeinspecao->avaliacao = $avaliacao;
                                    $itensdeinspecao->oportunidadeAprimoramento = $oportunidadeAprimoramento;
                                    $itensdeinspecao->evidencia = $evidencia;
//                                    $itensdeinspecao->valorFalta = $valorFalta;
//                                    $itensdeinspecao->valorSobra = $valorSobra;
//                                    $itensdeinspecao->valorRisco = $valorRisco;
                                    $itensdeinspecao->situacao = 'Inspecionado';
                                    $itensdeinspecao->pontuado = 0.00;
                                    $itensdeinspecao->itemQuantificado = 'Não';
                                    $itensdeinspecao->consequencias = null;
                                    $itensdeinspecao->orientacao = null;
                                    $itensdeinspecao->eventosSistema = 'Item avaliado remotamente por Websgi em ' . date('d/m/Y', strtotime($dtnow)) . '.';
//                                    dd('line -> 1027 não tem registro de pendências SMB_BDF' ,$itensdeinspecao);
                                    $itensdeinspecao->update();
                                }
//                              Final  se Não tem registro de pendências SMB_BDF

                            }
//                      Final  do teste SMB_BDF


//                      Inicio do teste PROTER
                            if ((($registro->numeroGrupoVerificacao == 202) && ($registro->numeroDoTeste == 1))
                                || (($registro->numeroGrupoVerificacao == 332) && ($registro->numeroDoTeste == 1))
                                || (($registro->numeroGrupoVerificacao == 213) && ($registro->numeroDoTeste == 1))
                                || (($registro->numeroGrupoVerificacao == 230) && ($registro->numeroDoTeste == 5))
                                || (($registro->numeroGrupoVerificacao == 270) && ($registro->numeroDoTeste == 2))) {
//                                dd($registro);
                                $countproters_peso = 0;
                                $countproters_cep = 0;
                                $reincidencia = DB::table('snci')
                                    ->select('no_inspecao', 'no_grupo', 'no_item', 'dt_fim_inspecao', 'dt_inic_inspecao')
                                    ->where([['descricao_item', 'like', '%PROTER%']])
                                    ->where([['sto', '=', $registro->sto]])
                                    ->orderBy('no_inspecao', 'desc')
                                    ->first();
                                try {
                                    if ($reincidencia->no_inspecao > 1) {
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
                                } catch (\Exception $e) {
                                    $reincidente = 0;
                                    $reinc = 'Não';
                                }
//                          Inicio tem Reincidencia proter
                                if ($reincidente == 1) {
                                    $proters_con = DB::table('proters')
                                        ->select(
                                            'tipo_de_pendencia'
                                            , 'no_do_objeto'
                                            , 'cep_entrega_sro'
                                            , 'data_da_pendencia'
                                        )
                                        ->where([['mcu', '=', $registro->mcu]])
                                        ->where([['tipo_de_pendencia', '=', 'CON']])
                                        ->where([['data_da_pendencia', '>=', $reincidencia_dt_fim_inspecao]])
                                        ->where([['data_da_pendencia', '<=', $dtmenos90dias]])
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
                                        ->where([['data_da_pendencia', '>=', $reincidencia_dt_fim_inspecao]])
                                        ->where([['data_da_pendencia', '<=', $dtmenos90dias]])
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
                                        ->where([['data_da_pendencia', '>=', $reincidencia_dt_fim_inspecao]])
                                        ->where([['data_da_pendencia', '<=', $dtmenos90dias]])
                                        ->get();
//                                    dd('var -> ', $proters_cep,$registro->mcu, $reincidencia_dt_fim_inspecao,  $dtmenos90dias );

                                    if (!$proters_con->isEmpty()) {
                                        $countproters_con = $proters_con->count('no_do_objeto');
                                    } else {
                                        $countproters_con = 0;
                                    }

                                    if (!$proters_peso->isEmpty()) {
                                        $countproters_peso = $proters_peso->count('no_do_objeto');
                                        $total_proters_peso = $proters_peso->sum('diferenca_a_recolher');
                                    } else {
                                        $total_proters_peso = 0.00;
                                    }

                                    if (!$proters_cep->isEmpty()) {
                                        $countproters_cep = $proters_cep->count('no_do_objeto');
                                        $total_proters_cep = $proters_cep->sum('diferenca_a_recolher');
                                    } else {
                                        $total_proters_cep = 0.00;
                                    }

                                    $total = $total_proters_peso + $total_proters_cep;
                                    $countproters = $countproters_con + $countproters_peso + $countproters_cep;


//                                  Inicio se tem pendencia proter com reincidencia
                                    if (($countproters_con >= 1) || ($total > 0.00)) {
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
                                        $oportunidadeAprimoramento = 'Em Análise aos dados do Sistema Proter a partir de Jan/2017. Excetuando os ultimos 90 dias da data dessa inspeção, constatou-se a existência de pendências sem regularização há mais de 90 dias conforme relacionado a seguir:';
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
                                        } elseif (!empty($evidencia1)) {
                                            $evidencia = $evidencia . $evidencia1 . $evidencia3;
                                        } elseif (!empty($evidencia2)) {
                                            $evidencia = $evidencia . $evidencia2 . $evidencia3;
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
                                        $itensdeinspecao->valorFalta = $total;
                                        $itensdeinspecao->situacao = 'Inspecionado';
                                        $itensdeinspecao->pontuado = $pontuado;
                                        $itensdeinspecao->itemQuantificado = 'Sim';
                                        $itensdeinspecao->orientacao = $registro->orientacao;
                                        $itensdeinspecao->eventosSistema = 'Item avaliado Remotamente por Websgi em ' . date('d/m/Y', strtotime($dtnow)) . '.';
                                        $itensdeinspecao->reincidencia = $reinc;
                                        $itensdeinspecao->codVerificacaoAnterior = $codVerificacaoAnterior;
                                        $itensdeinspecao->numeroGrupoReincidente = $numeroGrupoReincidente;
                                        $itensdeinspecao->numeroItemReincidente = $numeroItemReincidente;
                                        $itensdeinspecao->update();
                                    }
//                                  Fim se tem pendencia proter com reincidencia
//                                  Inicio Não tem pendencia proter com reincidencia
                                    else {
                                        $avaliacao = 'Conforme';
                                        $oportunidadeAprimoramento = 'Em análise aos dados do Sistema Proter, do período de Janeiro/2017 a ' . date('d/m/Y', strtotime($dtmenos90dias)) . ', constatou-se que não havia pendências com mais de 90 dias.';
                                        $dto = DB::table('itensdeinspecoes')
                                            ->Where([['inspecao_id', '=', $registro->inspecao_id]])
                                            ->Where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                                            ->select('itensdeinspecoes.*')
                                            ->first();
                                        $itensdeinspecao = Itensdeinspecao::find($dto->id);
                                        $itensdeinspecao->avaliacao = $avaliacao;
                                        $itensdeinspecao->oportunidadeAprimoramento = $oportunidadeAprimoramento;
                                        $itensdeinspecao->evidencia = null;
                                        $itensdeinspecao->valorFalta = 0.00;
                                        $itensdeinspecao->situacao = 'Inspecionado';
                                        $itensdeinspecao->pontuado = 0.00;
                                        $itensdeinspecao->itemQuantificado = 'Não';
                                        $itensdeinspecao->orientacao = null;
                                        $itensdeinspecao->eventosSistema = 'Item avaliado remotamente por Websgi em ' . date('d/m/Y', strtotime($dtnow)) . '.';
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
                                            , 'data_da_pendencia'
                                        )
                                        ->where([['mcu', '=', $registro->mcu]])
                                        ->where([['tipo_de_pendencia', '=', 'CON']])
//                                        ->where([['data_da_pendencia', '>=', $reincidencia_dt_fim_inspecao ]])
                                        ->where([['data_da_pendencia', '<=', $dtmenos90dias]])
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
                                        ->where([['data_da_pendencia', '<=', $dtmenos90dias]])
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
                                        ->where([['data_da_pendencia', '<=', $dtmenos90dias]])
                                        ->get();

                                    if (!$proters_con->isEmpty()) {
                                        $countproters_con = $proters_con->count('no_do_objeto');
                                    } else {
                                        $countproters_con = 0;
                                    }

                                    if (!$proters_peso->isEmpty()) {
                                        $countproters_peso = $proters_peso->count('no_do_objeto');
                                        $total_proters_peso = $proters_peso->sum('diferenca_a_recolher');
                                    } else {
                                        $total_proters_peso = 0.00;
                                    }

                                    if (!$proters_cep->isEmpty()) {
                                        $countproters_cep = $proters_cep->count('no_do_objeto');
                                        $total_proters_cep = $proters_cep->sum('diferenca_a_recolher');
                                    } else {
                                        $total_proters_cep = 0.00;
                                    }

                                    $total = $total_proters_peso + $total_proters_cep;
                                    $countproters = $countproters_con + $countproters_peso + $countproters_cep;
//                                  Inicio se tem pendencia proter sem reincidencia
                                    if ($countproters >= 1) {

                                        if (($countproters_con >= 1) || ($total > 0.00)) {
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
                                            } elseif (!empty($evidencia1)) {
                                                $evidencia = $evidencia . $evidencia1 . $evidencia3;
                                            } elseif (!empty($evidencia2)) {
                                                $evidencia = $evidencia . $evidencia2 . $evidencia3;
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
                                        $itensdeinspecao->valorFalta = $total;
                                        $itensdeinspecao->situacao = 'Inspecionado';
                                        $itensdeinspecao->pontuado = $pontuado;
                                        $itensdeinspecao->itemQuantificado = 'Sim';
                                        $itensdeinspecao->orientacao = $registro->orientacao;
                                        $itensdeinspecao->eventosSistema = 'Item avaliado Remotamente por Websgi em ' . date('d/m/Y', strtotime($dtnow)) . '.';
                                        $itensdeinspecao->reincidencia = $reinc;
                                        $itensdeinspecao->codVerificacaoAnterior = $codVerificacaoAnterior;
                                        $itensdeinspecao->numeroGrupoReincidente = $numeroGrupoReincidente;
                                        $itensdeinspecao->numeroItemReincidente = $numeroItemReincidente;
                                        $itensdeinspecao->update();
                                    }
//                                  Fim se tem pendencia proter sem reincidencia
//                                  Inicio conforme
                                    else {
                                        $avaliacao = 'Conforme';
                                        $oportunidadeAprimoramento = 'Em análise aos dados do Sistema Proter, do período de Janeiro/2017 a ' . date('d/m/Y', strtotime($dtmenos90dias)) . ', constatou-se que não havia pendências com mais de 90 dias.';
                                        $dto = DB::table('itensdeinspecoes')
                                            ->Where([['inspecao_id', '=', $registro->inspecao_id]])
                                            ->Where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                                            ->select('itensdeinspecoes.*')
                                            ->first();
                                        $itensdeinspecao = Itensdeinspecao::find($dto->id);
                                        $itensdeinspecao->avaliacao = $avaliacao;
                                        $itensdeinspecao->oportunidadeAprimoramento = $oportunidadeAprimoramento;
                                        $itensdeinspecao->evidencia = null;
                                        $itensdeinspecao->valorFalta = 0.00;
                                        $itensdeinspecao->situacao = 'Inspecionado';
                                        $itensdeinspecao->pontuado = 0.00;
                                        $itensdeinspecao->itemQuantificado = 'Não';
                                        $itensdeinspecao->orientacao = null;
                                        $itensdeinspecao->eventosSistema = 'Item avaliado remotamente por Websgi em ' . date('d/m/Y', strtotime($dtnow)) . '.';
                                        $itensdeinspecao->update();
                                    }
//                                  Fim Conforme


                                }
//                          Fim se não  reincidencia
//                          dd( '653',$dtmenos90dias , $registro->mcu, $countproters_con, $countproters_peso,  $countproters_cep );

                            }
//                      Final do teste PROTER

//                      Início do teste WebCont
                            if ((($registro->numeroGrupoVerificacao == 230) && ($registro->numeroDoTeste == 4))
                                || (($registro->numeroGrupoVerificacao == 270) && ($registro->numeroDoTeste == 1))) {

                                $reincidencia = DB::table('snci')
                                    ->select('no_inspecao', 'no_grupo', 'no_item', 'dt_fim_inspecao', 'dt_inic_inspecao')
                                    ->where([['descricao_item', 'like', '%3131)?%']])
                                    ->where([['sto', '=', $registro->sto]])
                                    ->orderBy('no_inspecao', 'desc')
                                    ->first();
                                try {
                                    if ($reincidencia->no_inspecao > 1) {
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
                                } catch (\Exception $e) {
                                    $reincidente = 0;
                                }
                                $mescompetencia = DB::table('debitoempregados')
                                    ->select('competencia')
                                    ->where([['debitoempregados.competencia', '>=', 1]])
                                    ->orderBy('competencia', 'desc')
                                    ->first();
                                $competencia = substr($mescompetencia->competencia, 4, 2) . '/' . substr($mescompetencia->competencia, 0, 4);
                                if ($reincidente == 1) {
                                    $debitoempregados = DB::table('debitoempregados')
                                        ->select('data', 'documento', 'historico', 'matricula', 'valor')
                                        ->where([['debitoempregados.data', '<=', $dtmenos90dias]])
                                        ->where([['debitoempregados.data', '>=', $reincidencia_dt_fim_inspecao]])
                                        ->Where([['debitoempregados.sto', '=', $registro->sto]])
                                        ->get();
                                } else {
                                    $debitoempregados = DB::table('debitoempregados')
                                        ->select('data', 'documento', 'historico', 'matricula', 'valor')
                                        ->where([['debitoempregados.data', '<=', $dtmenos90dias]])
                                        ->Where([['debitoempregados.sto', '=', $registro->sto]])
                                        ->get();
                                }

                                if (!$debitoempregados->isEmpty()) {
                                    $count = $debitoempregados->count('matricula');
                                    $total = $debitoempregados->sum('valor'); // soma a coluna valor da coleção de dados
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

                                    if (($count >= 1) && ($total > $quebracaixa)) {
                                        $avaliacao = 'Não Conforme';
                                        $pontuado = $registro->totalPontos * $fm->fator_multiplicador;
                                        $oportunidadeAprimoramento = 'Em Análise aos dados do Sistema WebCont – Composição Analítica da conta 11202.994000, posição de '
                                            . $competencia . ', constatou-se a existência de ' . $count . ' débitos de empregado sem regularização há mais de 90 dias, conforme relacionado a seguir:';
                                        $evidencia = "\n" . 'Data' . "\t" . 'Documento' . "\t" . 'Histórico' . "\t" . 'Matricula' . "\t" . 'Valor';

                                        foreach ($debitoempregados as $debitoempregado) {

                                            $evidencia = $evidencia . "\n" . date('d/m/Y', strtotime($debitoempregado->data))
                                                . "\t" . $debitoempregado->documento
                                                . "\t" . $debitoempregado->historico
                                                . "\t" . $debitoempregado->matricula
                                                . "\t" . ' R$ ' . number_format($debitoempregado->valor, 2, ',', '.');
                                        }

                                        $evidencia = $evidencia . "\n" . 'Total ' . "\t" . 'R$ ' . number_format($total, 2, ',', '.');
                                        $dto = DB::table('itensdeinspecoes')
                                            ->Where([['inspecao_id', '=', $registro->inspecao_id]])
                                            ->Where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                                            ->select('itensdeinspecoes.*')
                                            ->first();

                                        $itensdeinspecao = Itensdeinspecao::find($dto->id);
                                        $itensdeinspecao->avaliacao = $avaliacao;
                                        $itensdeinspecao->oportunidadeAprimoramento = $oportunidadeAprimoramento;
                                        $itensdeinspecao->evidencia = $evidencia;
                                        $itensdeinspecao->valorFalta = $total;
                                        $itensdeinspecao->situacao = 'Inspecionado';
                                        $itensdeinspecao->pontuado = $pontuado;
                                        $itensdeinspecao->itemQuantificado = 'Sim';
                                        $itensdeinspecao->orientacao = $registro->orientacao;
                                        $itensdeinspecao->eventosSistema = 'Item avaliado Remotamente por Websgi em ' . date('d/m/Y', strtotime($dtnow)) . '.';
                                        $itensdeinspecao->reincidencia = $reinc;
                                        $itensdeinspecao->codVerificacaoAnterior = $codVerificacaoAnterior;
                                        $itensdeinspecao->numeroGrupoReincidente = $numeroGrupoReincidente;
                                        $itensdeinspecao->numeroItemReincidente = $numeroItemReincidente;
                                        $itensdeinspecao->update();
                                    }
                                } else {
//                                    dd('nao  temmmmmmm debitos');
                                    //se não houve registro para a unidade o resultado é conforme
                                    $avaliacao = 'Conforme';
                                    $oportunidadeAprimoramento = 'Em Análise aos dados do Sistema WebCont – Composição Analítica da conta 11202.994000, verificada a posição do mês ' . $competencia . ' constatou-se que não havia histórico de pendências de débito de Empregados maior que 90 dias.';
                                    $dto = DB::table('itensdeinspecoes')
                                        ->Where([['inspecao_id', '=', $registro->inspecao_id]])
                                        ->Where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                                        ->select('itensdeinspecoes.*')
                                        ->first();
                                    $itensdeinspecao = Itensdeinspecao::find($dto->id);
                                    $itensdeinspecao->avaliacao = $avaliacao;
                                    $itensdeinspecao->oportunidadeAprimoramento = $oportunidadeAprimoramento;
                                    $itensdeinspecao->evidencia = null;
                                    $itensdeinspecao->valorFalta = 0.00;
                                    $itensdeinspecao->situacao = 'Inspecionado';
                                    $itensdeinspecao->pontuado = 0.00;
                                    $itensdeinspecao->itemQuantificado = 'Não';
                                    $itensdeinspecao->orientacao = null;
                                    $itensdeinspecao->eventosSistema = 'Item avaliado remotamente por Websgi em ' . date('d/m/Y', strtotime($dtnow)) . '.';
                                    $itensdeinspecao->update();
//                                     dd($competencia);
                                }
                            }
                            // fim doteste webCont

                        }

                    }  // Fim do teste para todas superintendencias se superintendencia = 1

                    // inicio do testee para uma superintendencias
                else {

                        $registros = DB::table('itensdeinspecoes')
                            ->join('inspecoes', 'itensdeinspecoes.inspecao_id', '=', 'inspecoes.id')
                            ->join('unidades', 'itensdeinspecoes.unidade_id', '=', 'unidades.id')
                            ->join('testesdeverificacao', 'itensdeinspecoes.testeVerificacao_id', '=', 'testesdeverificacao.id')
                            ->join('gruposdeverificacao', 'itensdeinspecoes.grupoVerificacao_id', '=', 'gruposdeverificacao.id')
                            ->select('itensdeinspecoes.*', 'inspecoes.*', 'unidades.*', 'testesdeverificacao.*', 'gruposdeverificacao.*')
                            ->where([['situacao', '=', 'Em Inspeção']])
                            ->where([['se', '=', $superintendencia]])
                            ->where([['inspecoes.ciclo', '=', $ciclo]])
                            ->where([['itensdeinspecoes.tipoUnidade_id', '=', $tipodeunidade]])
                            ->get();
//                  Inicio processamento da aavaliação
                        foreach ($registros as $registro) {
                            $consequencias = $registro->consequencias;
                            $orientacao = $registro->orientacao;

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

                                $reincidencia = DB::table('snci')
                                    ->select('no_inspecao', 'no_grupo', 'no_item', 'dt_fim_inspecao', 'dt_inic_inspecao')
                                    ->where([['descricao_item', 'like', '%Os procedimentos de embarque e desembarque da carga%']])
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
                                                . ($controle_de_viagen->inicio_viagem == '' ? '   ----------  ' : \Carbon\Carbon::parse($controle_de_viagen->inicio_viagem)->format('d/m/Y')) . "\t"
                                                . ($controle_de_viagen->controle_viagem == '' ? '   ----------  ' : $controle_de_viagen->controle_viagem) . "\t"
                                                . ($controle_de_viagen->tipo_de_operacao == '' ? '   ----------  ' : $controle_de_viagen->tipo_de_operacao) . "\t"
                                                . ($controle_de_viagen->quantidade == '' ? '   ----------  ' : $controle_de_viagen->quantidade) . "\t"
                                                . ($controle_de_viagen->peso == '' ? '   ----------  ' : $controle_de_viagen->peso) . "\t"
                                                . ($controle_de_viagen->unitizador == '' ? '   ----------  ' : $controle_de_viagen->unitizador) . "\t"
                                                . ($controle_de_viagen->descricao_do_servico == '' ? '   ----------  ' : $controle_de_viagen->descricao_do_servico) . "\t"
                                                . ($controle_de_viagen->local_de_destino == '' ? '   ----------  ' : $controle_de_viagen->local_de_destino);
                                        }

                                        $evidencia = $evidencia. "\n" . ' Foram verificada(s) todas programações de viagens no período do dia '
                                            . \Carbon\Carbon::parse($dtini)->format('d/m/Y') . ' até ' . \Carbon\Carbon::parse($dtfim)->format('d/m/Y')
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
                                        $oportunidadeAprimoramento = 'Em análise aos dados do sistema ERP - GESTÃO DE LINHAS DE TRANSPORTE, constatou-se que não havia operações de Embarque/Desembarque em falta ou incompletas.' .' Foram verificada(s) todas programações de viagens no período do dia ' .\Carbon\Carbon::parse($dtini)->format('d/m/Y').' até '.\Carbon\Carbon::parse($dtfim)->format('d/m/Y').'.';

//

                                    }

                                }
                                else  {
                                    $avaliacao = 'Não Conforme';
                                    $oportunidadeAprimoramento = 'Em análise aos dados do sistema ERP - GESTÃO DE LINHAS DE TRANSPORTE, constatou-se o descumprimento dos procedimentos de embarque e desembarque da carga, conforme relatado a seguir:';
                                    $evidencia = $evidencia . "\n" . '- Verificou-se que a unidade não está executando os lançamentos obrigatórios das informações de embarque e desembarque da carga no Sistema ERP. Não há histórico de registro de embarque/desembarque para troca de expedições. - Foram verificadas as programações de viagens no período de ' . \Carbon\Carbon::parse($dtini)->format('d/m/Y') . ' até ' . \Carbon\Carbon::parse($dtfim)->format('d/m/Y') .', sendo que em 100% das viagens não foi encontrado registros de apontamentos de Embarque/Desembarque.';
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
                                $itensdeinspecao->situacao = 'Inspecionado';
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

//                              echo $avaliacao , $dto->id.' - '.$avaliacao.' ,';
//                              if ($avaliacao == 'Não Conforme') dd('line 2495 -> Não Conforme ', $itensdeinspecao);
//                              if ($avaliacao == 'Conforme') dd('line 2496 -> Conforme ', $itensdeinspecao);
//                              dd($registro->sto , $reincidencia);

                                $itensdeinspecao->update();
                            }
// Final Controle de viagem

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
                                $itensdeinspecao->situacao = 'Inspecionado';
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

//                                echo $avaliacao , $dto->id.' - '.$avaliacao.' ,';
//                                if ($avaliacao == 'Não Conforme') dd('line 2495 -> Não Conforme ', $itensdeinspecao);
//                                if ($avaliacao == 'Conforme') dd('line 2496 -> Conforme ', $itensdeinspecao);
//                                dd($registro->sto , $reincidencia);

                                $itensdeinspecao->update();

//                                return view('compliance.inspecao.editar',
//                                    compact(
//                                        'registro'
//                                        , 'id'
//                                        , 'total'
//                                        , 'plplistapendentes'
//                                        ,'count'
//                                        ,'dtfim'
//                                    ));
                            }
// Final teste PLPs Pendentes


//              Início teste Compartilhamento de Senhas
                            if((($registro->numeroGrupoVerificacao==206) && ($registro->numeroDoTeste==2))
                                || (($registro->numeroGrupoVerificacao==335) && ($registro->numeroDoTeste==2))
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
                                //verifica histórico de inspeções

//                                DB::enableQueryLog();
//                                dd( DB::getQueryLog());

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
                                    $dataultimoevento = \Carbon\Carbon::parse($dtmax)->format('d/m/Y');

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
                                            $compartilhaSenha = DB::table('compartilhasenhas')
                                                ->where('codigo', '=', $registro->codigo)
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
                                                    $compartilhaSenha->codigo = $registro->codigo;
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

                                    $compartilhaSenhas = DB::table('compartilhasenhas')
                                        ->where('codigo', '=', $registro->codigo)
                                        ->where('numeroGrupoVerificacao', '=', $registro->numeroGrupoVerificacao)
                                        ->where('numeroDoTeste', '=', $registro->numeroDoTeste)
                                        ->orderBy('data', 'asc')
                                        ->get();

                                    $count = $compartilhaSenhas->count('codigo');
                                    $dtmax = \Carbon\Carbon::parse($eventos->max('data'))->format('d/m/Y');
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
                                $itensdeinspecao->situacao = 'Inspecionado';
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

//                                if ($avaliacao == 'Não Conforme') dd('line 2325 -> Não Conforme ', $itensdeinspecao);
//                                if ($avaliacao == 'Conforme') dd('line 2326 -> Conforme ', $itensdeinspecao);
//                                if ($naoMonitorado <>'') dd('line 2326 -> Conforme ', $itensdeinspecao);
//                                dd($registro->sto , $reincidencia, $eventos, $compartilhaSenhas);

                                $itensdeinspecao->update();


//                                return view('compliance.inspecao.editar',compact
//                                (
//                                    'registro'
//                                    , 'id'
//                                    , 'total'
//                                    , 'compartilhaSenhas'
//                                    , '$count'
//                                    , '$dtmenos12meses'
//                                    , 'dtnow'
//                                    , 'naoMonitorado'
//                                ));

                            }
//             Final teste Compartilhamento de Senhas



//  Inicio  do teste Alarme Arme/desarme

                            if((($registro->numeroGrupoVerificacao == 206 )&&($registro->numeroDoTeste == 1 ))
                                || (( $registro->numeroGrupoVerificacao == 335 ) && ( $registro->numeroDoTeste == 1 ))
                                || (($registro->numeroGrupoVerificacao == 232)&&($registro->numeroDoTeste == 3 ))
                                || (( $registro->numeroGrupoVerificacao == 272 ) && ( $registro->numeroDoTeste == 2 )))
                            {
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

                                //verifica histórico de inspeções
                                $reincidencia = DB::table('snci')
                                    ->select('no_inspecao', 'no_grupo', 'no_item', 'dt_fim_inspecao', 'dt_inic_inspecao')
                                    ->where([['descricao_item', 'like', '%alarme funciona corretamente%']])
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
                                                    $row => ['Acesso' => \Carbon\Carbon::parse($evento->data)->format('d/m/Y'), 'hora' => $evento->hora],
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


//                                            \Session::flash('mensagem',['msg'=> auth()->user()->name.', Base de Dados da Unidade não atualizada.
//                                                                         Atualize os horários de funcionamento.'
//                                                ,'class'=>'red white-text']);
//                                            return redirect()->route('compliance.unidades.editar',$registro->unidade_id);

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
                                        $evidencia = $evidencia  . "\n" . '$tabela->evAbertura'
                                            . "\t" . '$tabela->evDataAbertura' . "\t"
                                            . '$tabela->evHoraAbertura'
                                            . "\t"
                                            . '$tabela->evFechamento'
                                            . "\t"
                                            . '$tabela->evHoraFechamento'
                                            . "\t" . '$tabela->diaSemana'
                                            . "\t" . '$tabela->tempoPermanencia';
                                    }
                                }

                                if(isset($tempoAbertura)&&(!empty($tempoAbertura))){

                                    $evidencia = $evidencia."\n" . $rowAberturaFinalSemana .'Tempo de abertura em Relação ao Horário de Atendimento conforme a seguir:';
                                    $evidencia = $evidencia . "\n" . 'Data' . "\t" . 'Horário Atendimento' . "\t" . 'Horário da Abertura' . "\t" . 'Tempo Abertura';
                                    foreach ($tempoAbertura  as $tempo => $mdaData){
                                        $evidencia = $evidencia . "\n"
                                            . date('d/m/Y', strtotime($mdaData["dataInicioExpediente"]))
                                            . "\t" . $mdaData["InicioExpediente"] . "\t" . $mdaData["HorárioDeAbertura"]
                                            . "\t" . $mdaData["DiferencaTempoDeAbertura"];
                                    }

                                }

                                if(isset($tempoAberturaAntecipada)&&(!empty($tempoAberturaAntecipada))){

                                    $evidencia = $evidencia."\n" . $rowAberturaFinalSemana .' - Unidade em Risco. Abertura da Unidade em horário fora do padrão em relação ao horário de abertura da unidade conforme a seguir';
                                    $evidencia = $evidencia . "\n" . 'Data' . "\t" . 'Data Abertura' . "\t" . 'Horário de Atendimento' . "\t" . 'Hora da Abertura' . "\t" . 'Tempo Abertura';
                                    foreach ($tempoAberturaAntecipada  as $tempo => $mdaData){

                                        $evidencia = $evidencia . "\n"
                                            . date('d/m/Y', strtotime($mdaData["dataInicioExpediente"]))
                                            . "\t" . $mdaData["InicioExpediente"] . "\t" . $mdaData["HorárioDeAbertura"]
                                            . "\t" . $mdaData["DiferencaTempoDeAbertura"];

                                    }
                                }

                                if(isset($tempoAberturaPosExpediente)&&(!empty($tempoAberturaPosExpediente))){
                                    $evidencia = $evidencia."\n".'Unidade em Risco. Abertura da unidade em horário fora do padrão em relação ao horário de fechamento da unidade conforme a seguir:';
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
                                    $evidencia = $evidencia."\n".'Unidade em Risco. Abertura da unidade em dia de feriado conforme a seguir:';
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
                                $itensdeinspecao->situacao = 'Inspecionado';
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
//                          echo 'line 1400 -> '.$dto->id , $itensdeinspecao;
                                $itensdeinspecao->update();


//                                dd( $naoMonitorado , $aviso , $alarmesFinalSemana, $feriadoporUnidades,  $eventosf , $eventos);



//                                return view('compliance.inspecao.editar',compact
//                                (
//                                    'registro'
//                                    , 'id'
//                                    , 'total'
//                                    , 'acessos_final_semana'
//                                    , 'rowAberturaFinalSemana'
//                                    , 'count_alarmesFinalSemana'
//                                    , 'acessosEmFeriados'
//                                    , 'tempoAbertura'
//                                    , 'tempoAberturaAntecipada'
//                                    , 'tempoAberturaPosExpediente'
//                                    , 'aviso'
//                                    , 'dtmax'
//                                    , 'now'
//                                    ,'dtmenos12meses'
//                                    ,'naoMonitorado'
//                                ));


                            }

//  Final  do teste Alarme Arme/desarme

//                      Inicio  do teste Extravio Responsabilidade Definida
                            if ((($registro->numeroGrupoVerificacao == 205) && ($registro->numeroDoTeste == 2))
                                || (($registro->numeroGrupoVerificacao == 334) && ($registro->numeroDoTeste == 1))
                                || (($registro->numeroGrupoVerificacao == 372) && ($registro->numeroDoTeste == 1))
                                || (($registro->numeroGrupoVerificacao == 286) && ($registro->numeroDoTeste == 2))
                                || (($registro->numeroGrupoVerificacao == 221) && ($registro->numeroDoTeste == 2))
                                || (($registro->numeroGrupoVerificacao == 354) && ($registro->numeroDoTeste == 1))
                                || (($registro->numeroGrupoVerificacao == 231) && ($registro->numeroDoTeste == 1))
                                || (($registro->numeroGrupoVerificacao == 271) && ($registro->numeroDoTeste == 1))) {


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

                                    } else {
                                        $resp_definidas = DB::table('resp_definidas')
                                            ->select('mcu', 'unidade', 'data_pagamento', 'objeto', 'nu_sei', 'data', 'situacao', 'valor_da_indenizacao')
                                            ->where('mcu', '=', $registro->mcu)
                                            ->where('data_pagamento', '<=', $dtmenos90dias)
                                            ->where('nu_sei', '=', '')
                                            ->get();
                                    }
                                } catch (\Exception $e) {

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
                                    $valorFalta = $total;
                                    $evidencia = $evidencia . "\n" . 'Número Objeto' . "\t" . 'Número Processo' . "\t" . 'Data Processo' . "\t" . 'Data Atualização' . "\t" . 'Última Atualização' . "\t" . 'Valor';

                                    foreach ($resp_definidas as $tabela) {


//      ########## ATENÇÃO ##########
// 01/04/2020 Abilio esse trecho de código precisa ser testado não havia dados suficiete para implementar o
// teste no desenvolvimento caso houver algum ajuste  aualizar o controller InspeçãoController para esse item.

                                        $evidencia = $evidencia . "\n" . $tabela->objeto . "\t"
                                            . (isset($tabela->nu_sei) && $tabela->nu_sei == '' ? '   ----------  ' : $tabela->nu_sei)
                                            . "\t" . (isset($tabela->data_pagamento) && $tabela->data_pagamento == '' ? '   ----------  '
                                                : date('d/m/Y', strtotime($tabela->data_pagamento)))
                                            . "\t" . (isset($tabela->data) && $tabela->data == '' ? '   ----------  '
                                                : date('d/m/Y', strtotime($tabela->data)))
                                            . "\t" . (isset($tabela->situacao) && $tabela->situacao == '' ? '   ----------  '
                                                : $tabela->situacao)
                                            . "\t" . 'R$' . number_format($tabela->valor_da_indenizacao, 2, ',', '.');
                                    }
                                    $evidencia = $evidencia . "\n" . 'Valor em Falta :' . "\t" . 'R$' . number_format($valorFalta, 2, ',', '.');
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

                                if ($valorFalta > $quebracaixa) {
                                    $fm = DB::table('relevancias')
                                        ->select('fator_multiplicador', 'valor_final', 'valor_inicio')
                                        ->where('valor_inicio', '<=', $total)
                                        ->orderBy('valor_final', 'desc')
                                        ->first();
                                    if($avaliacao == 'Não Conforme') $pontuado = $registro->totalPontos * $fm->fator_multiplicador;
                                } else {
                                    if ($avaliacao == 'Não Conforme') $pontuado = $registro->totalPontos * 1;
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
                                $itensdeinspecao->situacao = 'Inspecionado';
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
//                          dd('line 1400 -> ',$itensdeinspecao);
                                $itensdeinspecao->update();
//
//                                    return view('compliance.inspecao.editar', compact
//                                    (
//                                        'registro'
//                                        , 'id'
//                                        , 'total'
//                                        , 'resp_definidas'
//                                        , 'dtmax'
//                                        , 'dtmin'
//                                        , 'count'
//                                    ));

                            }
//                      Final  do teste Extravio Responsabilidade Definida

//                       Inicio  do teste SLD-02-BDF
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
                                $itemQuantificado = 'Não';
                                $reincidente = 0;
                                $reinc = 'Não';

                                $sl02bdfsMaxdata = SL02_bdf::where('cod_orgao', $registro->sto)->max('dt_movimento');

                                if (!empty($sl02bdfsMaxdata)) {
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

                                    if (!$sl02bdfs30->isEmpty()) {
                                        $acumulados30 = $sl02bdfs30->sum('diferenca'); // soma a coluna valor da coleção de dados
                                        $ocorrencias30 = $sl02bdfs30->count('diferenca');
                                        $evidencia = $evidencia . "\n" . 'Período '
                                            . date('d/m/Y', strtotime($sl02bdfsMaxdata)) . ', até '
                                            . date('d/m/Y', strtotime($dtmenos30dias)) . '.';
                                        $evidencia = $evidencia . "\n" . 'Data' . "\t" . 'Saldo de Numerário' . "\t" . 'Limite de Saldo' . "\t" . 'Diferença';
                                        $row = 1;
                                        foreach ($sl02bdfs30 as $tabela) {

                                            $evidencia = $evidencia . "\n" . date('d/m/Y', strtotime($tabela->dt_movimento))
                                                . "\t" . 'R$' . number_format($tabela->saldo_atual, 2, ',', '.')
                                                . "\t" . 'R$' . number_format($tabela->limite, 2, ',', '.')
                                                . "\t" . 'R$' . number_format($tabela->diferenca, 2, ',', '.');
                                            $row++;
                                        }
                                        $acumulados30 = $acumulados30 / $row;
                                        $evidencia = $evidencia . "\n" . 'Quantidade de ocorrências em 30 dias ' . $ocorrencias30
                                            . '. Quantidade média de ocorrências em 30 dias '
                                            . number_format((($ocorrencias30 / 23) * 100), 2, ',', '.')
                                            . '. Valor médio ultrapassado R$ '
                                            . number_format($acumulados30, 2, ',', '.');

                                    }

                                    $sl02bdfs60 = DB::table('sl02bdfs')
                                        ->select('sl02bdfs.*')
                                        ->where('cod_orgao', '=', $registro->sto)
                                        ->where('dt_movimento', '<', $dtmenos30dias)
                                        ->where('dt_movimento', '>=', $dtmenos60dias)
                                        ->where('diferenca', '>=', 1)
                                        ->orderBy('dt_movimento', 'desc')
                                        ->get();

                                    if (!$sl02bdfs60->isEmpty()) {
                                        $acumulados60 = $sl02bdfs60->sum('diferenca'); // soma a coluna valor da coleção de dados
                                        $ocorrencias60 = $sl02bdfs60->count('diferenca');
                                        $evidencia = $evidencia . "\n" . 'Período '
                                            . date('d/m/Y', strtotime($dtmenos30dias)) . ', até '
                                            . date('d/m/Y', strtotime($dtmenos60dias)) . '.';
                                        $evidencia = $evidencia . "\n" . 'Data' . "\t" . 'Saldo de Numerário' . "\t" . 'Limite de Saldo' . "\t" . 'Diferença';
                                        $row = 1;
                                        foreach ($sl02bdfs60 as $tabela) {

                                            $evidencia = $evidencia . "\n" . date('d/m/Y', strtotime($tabela->dt_movimento))
                                                . "\t" . 'R$' . number_format($tabela->saldo_atual, 2, ',', '.')
                                                . "\t" . 'R$' . number_format($tabela->limite, 2, ',', '.')
                                                . "\t" . 'R$' . number_format($tabela->diferenca, 2, ',', '.');
                                            $row++;
                                        }
                                        $acumulados60 = $acumulados60 / $row;
                                        $evidencia = $evidencia . "\n" . 'Quantidade de ocorrências em 30 dias ' . $ocorrencias60
                                            . '. Quantidade média de ocorrências em 30 dias '
                                            . number_format((($ocorrencias60 / 23) * 100), 2, ',', '.')
                                            . '. Valor médio ultrapassado R$ '
                                            . number_format($acumulados60, 2, ',', '.');

                                    }

                                    $sl02bdfs90 = DB::table('sl02bdfs')
                                        ->select('sl02bdfs.*')
                                        ->where('cod_orgao', '=', $registro->sto)
                                        ->where('dt_movimento', '<', $dtmenos60dias)
                                        ->where('dt_movimento', '>=', $dtmenos90dias)
                                        ->where('diferenca', '>=', 1)
                                        ->orderBy('dt_movimento', 'desc')
                                        ->get();

                                    if (!$sl02bdfs90->isEmpty()) {
                                        $acumulados90 = $sl02bdfs90->sum('diferenca'); // soma a coluna valor da coleção de dados
                                        $ocorrencias90 = $sl02bdfs90->count('diferenca');
                                        $evidencia = $evidencia . "\n" . 'Período '
                                            . date('d/m/Y', strtotime($dtmenos60dias)) . ', até '
                                            . date('d/m/Y', strtotime($dtmenos90dias)) . '.';
                                        $evidencia = $evidencia . "\n" . 'Data' . "\t" . 'Saldo de Numerário' . "\t" . 'Limite de Saldo' . "\t" . 'Diferença';
                                        $row = 1;
                                        foreach ($sl02bdfs90 as $tabela) {

                                            $evidencia = $evidencia . "\n" . date('d/m/Y', strtotime($tabela->dt_movimento))
                                                . "\t" . 'R$' . number_format($tabela->saldo_atual, 2, ',', '.')
                                                . "\t" . 'R$' . number_format($tabela->limite, 2, ',', '.')
                                                . "\t" . 'R$' . number_format($tabela->diferenca, 2, ',', '.');
                                            $row++;
                                        }
                                        $acumulados90 = $acumulados90 / $row;
                                        $evidencia = $evidencia . "\n" . 'Quantidade de ocorrências em 30 dias ' . $ocorrencias90
                                            . '. Quantidade média de ocorrências em 30 dias '
                                            . number_format((($ocorrencias90 / 23) * 100), 2, ',', '.')
                                            . '. Valor médio ultrapassado R$ '
                                            . number_format($acumulados90, 2, ',', '.');

                                    }

                                    if (($acumulados30 >= 1) && ($acumulados60 >= 1) && ($acumulados90 >= 1)) {
                                        $total = ($acumulados30 + $acumulados60 + $acumulados90) / 3;
                                        $ocorrencias = $ocorrencias30 + $ocorrencias60 + $ocorrencias90;
                                    }

                                    if (($acumulados30 >= 1) && ($acumulados60 >= 1) && ($acumulados90 == 0)) {
                                        $total = ($acumulados30 + $acumulados60) / 2;
                                        $ocorrencias = $ocorrencias30 + $ocorrencias60;
                                    }

                                    if (($acumulados30 >= 1) && ($acumulados60 == 0) && ($acumulados90 == 0)) {
                                        $total = $acumulados30;
                                        $ocorrencias = $ocorrencias30;
                                    }

                                    if (($acumulados30 == 0) && ($acumulados60 >= 1) && ($acumulados90 == 0)) {
                                        $total = $acumulados60;
                                        $ocorrencias = $ocorrencias60;
                                    }

                                    if (($acumulados30 == 0) && ($acumulados60 == 0) && ($acumulados90 >= 1)) {
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
                                            . $ocorrencias . ' dias, o que corresponde a uma média de ' . $ocorrencias / 3 . ' ocorrências por mês, considerando o período, conforme detalhado a seguir:';

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
                                        } catch (\Exception $e) {
                                            $reincidente = 0;
                                            $reinc = 'Não';
                                        }
                                        if ($total > 0.00) {
                                            $itemQuantificado = 'Sim';
                                            $evidencia = $evidencia . "\n" . 'Em Risco ' . number_format($total, 2, ',', '.');
                                            $valorFalta = null;
                                            $valorSobra = null;
                                            $valorRisco = $total;
                                        }
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
                                            if($avaliacao == 'Não Conforme') $pontuado = $registro->totalPontos * $fm->fator_multiplicador;
                                        } else {
                                            if ($avaliacao == 'Não Conforme') $pontuado = $registro->totalPontos * 1;
                                        }
                                    } else {
                                        $avaliacao = 'Conforme';
                                        $oportunidadeAprimoramento = 'Em análise ao Relatório "Saldo de Numerário em relação ao Limite
                                         de Saldo", do sistema BDF, referente ao período de ' . date('d/m/Y', strtotime($dtnow)) . ' a '
                                            . date('d/m/Y', strtotime($dtmenos90dias)) . ',
                                            constatou-se que não houve descumprimento do limite de saldo estabelecido para a unidade.';
                                    }
                                } else {
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
                                $itensdeinspecao->situacao = 'Inspecionado';
                                $itensdeinspecao->pontuado = $pontuado;
                                $itensdeinspecao->itemQuantificado = $itemQuantificado;
                                $itensdeinspecao->orientacao = $registro->orientacao;
                                $itensdeinspecao->eventosSistema = 'Item avaliado Remotamente por Websgi em ' . date('d/m/Y', strtotime($dtnow)) . '.';
                                $itensdeinspecao->reincidencia = $reinc;
                                $itensdeinspecao->codVerificacaoAnterior = $codVerificacaoAnterior;
                                $itensdeinspecao->numeroGrupoReincidente = $numeroGrupoReincidente;
                                $itensdeinspecao->numeroItemReincidente = $numeroItemReincidente;
//                                                dd('line 1277 -> ',$itensdeinspecao);
                                $itensdeinspecao->update();
                            }
//                       Final  do teste SLD-02-BDF

//                       Inicio  do teste SMB_BDF
                            if ((($registro->numeroGrupoVerificacao == 230) && ($registro->numeroDoTeste == 6))
                                || (($registro->numeroGrupoVerificacao == 270) && ($registro->numeroDoTeste == 3))) {

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
                                        $evidencia = null;
                                    }
                                } catch (\Exception $e) {
                                    $reincidente = 0;
                                    $reinc = 'Não';
                                    $codVerificacaoAnterior = null;
                                    $numeroGrupoReincidente = null;
                                    $numeroItemReincidente = null;
                                    $evidencia = null;
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
                                                $oportunidadeAprimoramento = 'Em análise ao sistema SDE – Sistema de Depósito Bancário, na opção "Contabilização", Conciliação SMB x BDF – dados “Não Conciliados”, referente ao período de ' . date('d/m/Y', strtotime($dtmenos90dias)) . ' a ' . date('d/m/Y', strtotime($dtnow)) . ', verificou-se a inexistência de divergências.';
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
                                                $itensdeinspecao->consequencias = null;
                                                $itensdeinspecao->situacao = 'Inspecionado';
                                                $itensdeinspecao->pontuado = 0.00;
                                                $itensdeinspecao->itemQuantificado = 'Não';
                                                $itensdeinspecao->orientacao = null;
                                                $itensdeinspecao->eventosSistema = 'Item avaliado remotamente por Websgi em ' . date('d/m/Y', strtotime($dtnow)) . '.';
//                                            dd('line -> 818' ,$itensdeinspecao);
//                                            $itensdeinspecao->update();

                                            }
//                                          Final Testa ultimo registro se tem compensação
//                                          Inicio Testa ultimo registro com compensação
                                            else {

                                                $avaliacao = 'Não Conforme';
                                                $oportunidadeAprimoramento = 'Em análise ao sistema SDE – Sistema de Depósito Bancário, na opção "Contabilização", Conciliação SMB x BDF – dados “Não Conciliados”, referente ao período de ' . date('d/m/Y', strtotime($dtmenos90dias)) . ' a ' . date('d/m/Y', strtotime($dtnow)) . ', constatou-se a existência de divergências entre o valor depositado na conta bancária dos Correios pela Agência e o valor do bloqueto gerado no sistema SARA, no total de R$ ' . number_format($divergencia, 2, ',', '.') . ' , conforme relacionado a seguir:';

                                                $evidencia = $evidencia . "\n" . 'Data' . "\t" . 'Divergência' . "\t" . 'Tipo';
                                                foreach ($smb_bdf_naoconciliados as $smb_bdf_naoconciliado) {
                                                    $evidencia = $evidencia . "\n"
                                                        . date('d/m/Y', strtotime($smb_bdf_naoconciliado->Data))
                                                        . "\t" . 'R$ ' . number_format($smb_bdf_naoconciliado->Divergencia, 2, ',', '.');

                                                    if (($smb_bdf_naoconciliado->BDFDinheiro <> 0) && ($smb_bdf_naoconciliado->BDFCheque <> 0) && ($smb_bdf_naoconciliado->BDFBoleto <> 0)) {
                                                        $evidencia = $evidencia . "\t" . 'Dinheiro/Cheque/Boleto';
                                                    } elseif (($smb_bdf_naoconciliado->BDFDinheiro <> 0) && ($smb_bdf_naoconciliado->BDFBoleto <> 0)) {
                                                        $evidencia = $evidencia . "\t" . 'Dinheiro/Boleto';
                                                    } elseif (($smb_bdf_naoconciliado->BDFDinheiro <> 0) && ($smb_bdf_naoconciliado->BDFCheque <> 0)) {
                                                        $evidencia = $evidencia . "\t" . 'Dinheiro/Cheque';
                                                    } elseif (($smb_bdf_naoconciliado->BDFBoleto <> 0) && ($smb_bdf_naoconciliado->BDFCheque <> 0)) {
                                                        $evidencia = $evidencia . "\t" . 'Boleto/Cheque';
                                                    } elseif ($smb_bdf_naoconciliado->BDFDinheiro <> 0) {
                                                        $evidencia = $evidencia . "\t" . 'Dinheiro';
                                                    } elseif ($smb_bdf_naoconciliado->BDFBoleto <> 0) {
                                                        $evidencia = $evidencia . "\t" . 'Boleto';
                                                    } elseif ($smb_bdf_naoconciliado->BDFCheque <> 0) {
                                                        $evidencia = $evidencia . "\t" . 'Cheque';
                                                    } else {
                                                        $evidencia = $evidencia . "\t" . 'Não identificado';
                                                    }
                                                }

                                                if ($divergencia > 0.00) {
                                                    $total = $divergencia;
                                                    $evidencia = $evidencia . "\n" . 'Em Falta ' . $divergencia;
                                                    $valorFalta = $total;
                                                    $valorSobra = null;
                                                    $valorRisco = null;
//                                                o Dpto disse para pontuar como falta.pare

                                                } else {
                                                    $total = $divergencia * -1;
                                                    $evidencia = $evidencia . "\n" . 'Em Falta ' . $total;
                                                    $valorSobra = null;
                                                    $valorFalta = $total;
                                                    $valorRisco = null;
                                                }
//                                                dd('line 876',  $smb , $bdf ,$divergencia, $total );

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

//                                                dd('line 821',  $smb , $bdf ,$divergencia, $total );

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
                                                $itensdeinspecao->situacao = 'Inspecionado';
                                                $itensdeinspecao->pontuado = $pontuado;
                                                $itensdeinspecao->itemQuantificado = 'Sim';
                                                $itensdeinspecao->orientacao = $registro->orientacao;
                                                $itensdeinspecao->eventosSistema = 'Item avaliado Remotamente por Websgi em ' . date('d/m/Y', strtotime($dtnow)) . '.';
                                                $itensdeinspecao->reincidencia = $reinc;
                                                $itensdeinspecao->codVerificacaoAnterior = $codVerificacaoAnterior;
                                                $itensdeinspecao->numeroGrupoReincidente = $numeroGrupoReincidente;
                                                $itensdeinspecao->numeroItemReincidente = $numeroItemReincidente;
//                                                dd('line 917 -> ',$itensdeinspecao);
                                                $itensdeinspecao->update();
                                            }
//                                          Final Testa ultimo registro com compensação
                                        }
//                                      Final  se divergencia é um valor diferente de zero

//                                      Inicio  se divergencia é um valor igual zero
                                        if ($divergencia == 0.0) {

                                            $dataanterior = null;
                                            foreach ($smb_bdf_naoconciliados as $smb_bdf_naoconciliado) {
                                                if ($dataanterior !== null) {
                                                    $dataantual = $dataanterior;
                                                    $dataantual->addDays(1);
                                                    $unidade_enderecos = DB::table('unidade_enderecos')
                                                        ->Where([['mcu', '=', $registro->mcu]])
                                                        ->select('unidade_enderecos.*')
                                                        ->first();
                                                    $feriado = DB::table('feriados')
                                                        ->Where([['data_do_feriado', '=', $dataantual]])
                                                        ->Where([['nome_municipio', '=', $unidade_enderecos->cidade]])
                                                        ->Where([['uf', '=', $unidade_enderecos->uf]])
                                                        ->select('feriados.*')
                                                        ->first();
                                                    if ($feriado) {
                                                        $diasemana = $dataanterior;
                                                        $diasemana->addDays(5);
                                                    } else {
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

                                                    if ($periodo->count() > 1) {
                                                        $avaliacao = 'Não Conforme';
                                                        $oportunidadeAprimoramento = 'Em análise ao sistema SDE – Sistema de Depósito Bancário, na opção "Contabilização", Conciliação SMB x BDF – dados “Não Conciliados”,  referente ao período de ' . date('d/m/Y', strtotime($dtmenos90dias)) . ' a ' . date('d/m/Y', strtotime($dtnow)) . ', constatou-se a existência de depositos na conta dos Correios pela Agência com prazo superior D+1. Evento em data anterior à ' . date('d/m/Y', strtotime($dataanterior));
                                                        $total = $smb_bdf_naoconciliado->BDFBoleto;
                                                        $valorRisco = $smb_bdf_naoconciliado->BDFBoleto;
                                                        break;
                                                    }
                                                }
                                                $dataanterior = new Carbon($smb_bdf_naoconciliado->Data);
                                            }
                                            if ($periodo->count() > 1) {
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

                                                $evidencia = $evidencia . "\n" . 'Data' . "\t" . 'Valor do Boleto';
                                                foreach ($smb_bdf_naoconciliados as $smb_bdf_naoconciliado) {
                                                    $evidencia = $evidencia . "\n"
                                                        . date('d/m/Y', strtotime($smb_bdf_naoconciliado->Data))
                                                        . "\t" . 'R$ ' . number_format($smb_bdf_naoconciliado->BDFBoleto, 2, ',', '.');
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
                                                $itensdeinspecao->situacao = 'Inspecionado';
                                                $itensdeinspecao->pontuado = $pontuado;
                                                $itensdeinspecao->itemQuantificado = 'Sim';
                                                $itensdeinspecao->orientacao = $registro->orientacao;
                                                $itensdeinspecao->consequencias = null;
                                                $itensdeinspecao->eventosSistema = 'Item avaliado Remotamente por Websgi em ' . date('d/m/Y', strtotime($dtnow)) . '.';
                                                $itensdeinspecao->reincidencia = $reinc;
                                                $itensdeinspecao->codVerificacaoAnterior = $codVerificacaoAnterior;
                                                $itensdeinspecao->numeroGrupoReincidente = $numeroGrupoReincidente;
                                                $itensdeinspecao->numeroItemReincidente = $numeroItemReincidente;
//                                                dd('line 1314 ->  valor em risco ',$itensdeinspecao);
                                                $itensdeinspecao->update();
                                            } else {
                                                $avaliacao = 'Conforme';
                                                $oportunidadeAprimoramento = 'Em análise ao sistema SDE – Sistema de Depósito Bancário, na opção "Contabilização", Conciliação SMB x BDF – dados “Não Conciliados”, referente ao período de ' . date('d/m/Y', strtotime($dtmenos90dias)) . ' a ' . date('d/m/Y', strtotime($dtnow)) . ', verificou-se a inexistência de divergências.';
                                                $dto = DB::table('itensdeinspecoes')
                                                    ->Where([['inspecao_id', '=', $registro->inspecao_id]])
                                                    ->Where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                                                    ->select('itensdeinspecoes.*')
                                                    ->first();
                                                $itensdeinspecao = Itensdeinspecao::find($dto->id);
                                                $itensdeinspecao->avaliacao = $avaliacao;
                                                $itensdeinspecao->oportunidadeAprimoramento = $oportunidadeAprimoramento;
                                                $itensdeinspecao->evidencia = null;
                                                $itensdeinspecao->valorFalta = 0.00;
                                                $itensdeinspecao->situacao = 'Inspecionado';
                                                $itensdeinspecao->pontuado = 0.00;
                                                $itensdeinspecao->itemQuantificado = 'Não';
                                                $itensdeinspecao->orientacao = null;
                                                $itensdeinspecao->eventosSistema = 'Item avaliado remotamente por Websgi em ' . date('d/m/Y', strtotime($dtnow)) . '.';
//                                                dd('line -> 994' ,$itensdeinspecao);
                                                $itensdeinspecao->update();
                                            }
                                        }
//                                      Final  se divergencia é um valor igual zero
                                    }
//                              Final  se há divergencia

                                }
//                              Final  se tem registro de pendências SMB_BDF
//                              Inicio  se Não tem registro de pendências SMB_BDF
                                else {
                                    $avaliacao = 'Conforme';
                                    $oportunidadeAprimoramento = 'Em análise ao sistema SDE – Sistema de Depósito Bancário, na opção "Contabilização", Conciliação SMB x BDF – dados “Não Conciliados”, referente ao período de ' . date('d/m/Y', strtotime($dtmenos90dias)) . ' a ' . date('d/m/Y', strtotime($dtnow)) . ', verificou-se a inexistência de divergências.';

                                    $dto = DB::table('itensdeinspecoes')
                                        ->Where([['inspecao_id', '=', $registro->inspecao_id]])
                                        ->Where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                                        ->select('itensdeinspecoes.*')
                                        ->first();
                                    $itensdeinspecao = Itensdeinspecao::find($dto->id);
                                    $itensdeinspecao->avaliacao = $avaliacao;
                                    $itensdeinspecao->oportunidadeAprimoramento = $oportunidadeAprimoramento;
                                    $itensdeinspecao->evidencia = $evidencia;
//                                    $itensdeinspecao->valorFalta = $valorFalta;
//                                    $itensdeinspecao->valorSobra = $valorSobra;
//                                    $itensdeinspecao->valorRisco = $valorRisco;
                                    $itensdeinspecao->situacao = 'Inspecionado';
                                    $itensdeinspecao->pontuado = 0.00;
                                    $itensdeinspecao->itemQuantificado = 'Não';
                                    $itensdeinspecao->consequencias = null;
                                    $itensdeinspecao->orientacao = null;
                                    $itensdeinspecao->eventosSistema = 'Item avaliado remotamente por Websgi em ' . date('d/m/Y', strtotime($dtnow)) . '.';
//                                    dd('line -> 1027 não tem registro de pendências SMB_BDF' ,$itensdeinspecao);
                                    $itensdeinspecao->update();
                                }
//                              Final  se Não tem registro de pendências SMB_BDF

                            }
//                      Final  do teste SMB_BDF


//                      Inicio do teste PROTER
                            if ((($registro->numeroGrupoVerificacao == 202) && ($registro->numeroDoTeste == 1))
                                || (($registro->numeroGrupoVerificacao == 332) && ($registro->numeroDoTeste == 1))
                                || (($registro->numeroGrupoVerificacao == 213) && ($registro->numeroDoTeste == 1))
                                || (($registro->numeroGrupoVerificacao == 230) && ($registro->numeroDoTeste == 5))
                                || (($registro->numeroGrupoVerificacao == 270) && ($registro->numeroDoTeste == 2))) {
//                                dd($registro);
                                $countproters_peso = 0;
                                $countproters_cep = 0;
                                $reincidencia = DB::table('snci')
                                    ->select('no_inspecao', 'no_grupo', 'no_item', 'dt_fim_inspecao', 'dt_inic_inspecao')
                                    ->where([['descricao_item', 'like', '%PROTER%']])
                                    ->where([['sto', '=', $registro->sto]])
                                    ->orderBy('no_inspecao', 'desc')
                                    ->first();
                                try {
                                    if ($reincidencia->no_inspecao > 1) {
                                        $reincidente = 1;
                                        $reinc = 'Sim';
                                        $codVerificacaoAnterior = $reincidencia->no_inspecao;
                                        $numeroGrupoReincidente = $reincidencia->no_grupo;
                                        $numeroItemReincidente = $reincidencia->no_item;
                                        $reincidencia_dt_fim_inspecao = new Carbon($reincidencia->dt_fim_inspecao);
                                        $reincidencia_dt_inic_inspecao = new Carbon($reincidencia->dt_inic_inspecao);
                                        $reincidencia_dt_fim_inspecao->subMonth(3);
                                        $reincidencia_dt_inic_inspecao->subMonth(3);
                                    } else {
                                        $reincidente = 0;
                                        $reinc = 'Não';
                                        $codVerificacaoAnterior = null;
                                        $numeroGrupoReincidente = null;
                                        $numeroItemReincidente = null;
                                    }
                                } catch (\Exception $e) {
                                    $reincidente = 0;
                                    $reinc = 'Não';
                                    $codVerificacaoAnterior = null;
                                    $numeroGrupoReincidente = null;
                                    $numeroItemReincidente = null;
                                }
//                          Inicio tem Reincidencia proter
                                if ($reincidente == 1) {
                                    $proters_con = DB::table('proters')
                                        ->select(
                                            'tipo_de_pendencia'
                                            , 'no_do_objeto'
                                            , 'cep_entrega_sro'
                                            , 'data_da_pendencia'
                                        )
                                        ->where([['mcu', '=', $registro->mcu]])
                                        ->where([['tipo_de_pendencia', '=', 'CON']])
                                        ->where([['data_da_pendencia', '>=', $reincidencia_dt_fim_inspecao]])
                                        ->where([['data_da_pendencia', '<=', $dtmenos90dias]])
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
                                        ->where([['data_da_pendencia', '>=', $reincidencia_dt_fim_inspecao]])
                                        ->where([['data_da_pendencia', '<=', $dtmenos90dias]])
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
                                        ->where([['data_da_pendencia', '>=', $reincidencia_dt_fim_inspecao]])
                                        ->where([['data_da_pendencia', '<=', $dtmenos90dias]])
                                        ->get();
//                                    dd('var -> ', $proters_cep,$registro->mcu, $reincidencia_dt_fim_inspecao,  $dtmenos90dias );

                                    if (!$proters_con->isEmpty()) {
                                        $countproters_con = $proters_con->count('no_do_objeto');
                                    } else {
                                        $countproters_con = 0;
                                    }

                                    if (!$proters_peso->isEmpty()) {
                                        $countproters_peso = $proters_peso->count('no_do_objeto');
                                        $total_proters_peso = $proters_peso->sum('diferenca_a_recolher');
                                    } else {
                                        $total_proters_peso = 0.00;
                                    }

                                    if (!$proters_cep->isEmpty()) {
                                        $countproters_cep = $proters_cep->count('no_do_objeto');
                                        $total_proters_cep = $proters_cep->sum('diferenca_a_recolher');
                                    } else {
                                        $total_proters_cep = 0.00;
                                    }

                                    $total = $total_proters_peso + $total_proters_cep;
                                    $countproters = $countproters_con + $countproters_peso + $countproters_cep;


//                                  Inicio se tem pendencia proter com reincidencia
                                    if (($countproters_con >= 1) || ($total > 0.00)) {
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
                                        $oportunidadeAprimoramento = 'Em Análise aos dados do Sistema Proter a partir de Jan/2017. Excetuando os ultimos 90 dias da data dessa inspeção, constatou-se a existência de pendências sem regularização há mais de 90 dias conforme relacionado a seguir:';
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
                                        } elseif (!empty($evidencia1)) {
                                            $evidencia = $evidencia . $evidencia1 . $evidencia3;
                                        } elseif (!empty($evidencia2)) {
                                            $evidencia = $evidencia . $evidencia2 . $evidencia3;
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
                                        $itensdeinspecao->valorFalta = $total;
                                        $itensdeinspecao->situacao = 'Inspecionado';
                                        $itensdeinspecao->pontuado = $pontuado;
                                        $itensdeinspecao->itemQuantificado = 'Sim';
                                        $itensdeinspecao->orientacao = $registro->orientacao;
                                        $itensdeinspecao->eventosSistema = 'Item avaliado Remotamente por Websgi em ' . date('d/m/Y', strtotime($dtnow)) . '.';
                                        $itensdeinspecao->reincidencia = $reinc;
                                        $itensdeinspecao->codVerificacaoAnterior = $codVerificacaoAnterior;
                                        $itensdeinspecao->numeroGrupoReincidente = $numeroGrupoReincidente;
                                        $itensdeinspecao->numeroItemReincidente = $numeroItemReincidente;
                                        $itensdeinspecao->update();
                                    }
//                                  Fim se tem pendencia proter com reincidencia
//                                  Inicio Não tem pendencia proter com reincidencia
                                    else {
                                        $avaliacao = 'Conforme';
                                        $oportunidadeAprimoramento = 'Em análise aos dados do Sistema Proter, do período de Janeiro/2017 a ' . date('d/m/Y', strtotime($dtmenos90dias)) . ', constatou-se que não havia pendências com mais de 90 dias.';
                                        $dto = DB::table('itensdeinspecoes')
                                            ->Where([['inspecao_id', '=', $registro->inspecao_id]])
                                            ->Where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                                            ->select('itensdeinspecoes.*')
                                            ->first();
                                        $itensdeinspecao = Itensdeinspecao::find($dto->id);
                                        $itensdeinspecao->avaliacao = $avaliacao;
                                        $itensdeinspecao->oportunidadeAprimoramento = $oportunidadeAprimoramento;
                                        $itensdeinspecao->evidencia = null;
                                        $itensdeinspecao->valorFalta = 0.00;
                                        $itensdeinspecao->situacao = 'Inspecionado';
                                        $itensdeinspecao->pontuado = 0.00;
                                        $itensdeinspecao->itemQuantificado = 'Não';
                                        $itensdeinspecao->orientacao = null;
                                        $itensdeinspecao->eventosSistema = 'Item avaliado remotamente por Websgi em ' . date('d/m/Y', strtotime($dtnow)) . '.';
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
                                            , 'data_da_pendencia'
                                        )
                                        ->where([['mcu', '=', $registro->mcu]])
                                        ->where([['tipo_de_pendencia', '=', 'CON']])
//                                        ->where([['data_da_pendencia', '>=', $reincidencia_dt_fim_inspecao ]])
                                        ->where([['data_da_pendencia', '<=', $dtmenos90dias]])
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
                                        ->where([['data_da_pendencia', '<=', $dtmenos90dias]])
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
                                        ->where([['data_da_pendencia', '<=', $dtmenos90dias]])
                                        ->get();

                                    if (!$proters_con->isEmpty()) {
                                        $countproters_con = $proters_con->count('no_do_objeto');
                                    } else {
                                        $countproters_con = 0;
                                    }

                                    if (!$proters_peso->isEmpty()) {
                                        $countproters_peso = $proters_peso->count('no_do_objeto');
                                        $total_proters_peso = $proters_peso->sum('diferenca_a_recolher');
                                    } else {
                                        $total_proters_peso = 0.00;
                                    }

                                    if (!$proters_cep->isEmpty()) {
                                        $countproters_cep = $proters_cep->count('no_do_objeto');
                                        $total_proters_cep = $proters_cep->sum('diferenca_a_recolher');
                                    } else {
                                        $total_proters_cep = 0.00;
                                    }

                                    $total = $total_proters_peso + $total_proters_cep;
                                    $countproters = $countproters_con + $countproters_peso + $countproters_cep;
//                                  Inicio se tem pendencia proter sem reincidencia
                                    if ($countproters >= 1) {

                                        if (($countproters_con >= 1) || ($total > 0.00)) {
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
                                            } elseif (!empty($evidencia1)) {
                                                $evidencia = $evidencia . $evidencia1 . $evidencia3;
                                            } elseif (!empty($evidencia2)) {
                                                $evidencia = $evidencia . $evidencia2 . $evidencia3;
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
                                        $itensdeinspecao->valorFalta = $total;
                                        $itensdeinspecao->situacao = 'Inspecionado';
                                        $itensdeinspecao->pontuado = $pontuado;
                                        $itensdeinspecao->itemQuantificado = 'Sim';
                                        $itensdeinspecao->orientacao = $registro->orientacao;
                                        $itensdeinspecao->eventosSistema = 'Item avaliado Remotamente por Websgi em ' . date('d/m/Y', strtotime($dtnow)) . '.';
                                        $itensdeinspecao->reincidencia = $reinc;
                                        $itensdeinspecao->codVerificacaoAnterior = $codVerificacaoAnterior;
                                        $itensdeinspecao->numeroGrupoReincidente = $numeroGrupoReincidente;
                                        $itensdeinspecao->numeroItemReincidente = $numeroItemReincidente;
                                        $itensdeinspecao->update();
                                    }
//                                  Fim se tem pendencia proter sem reincidencia
//                                  Inicio conforme
                                    else {
                                        $avaliacao = 'Conforme';
                                        $oportunidadeAprimoramento = 'Em análise aos dados do Sistema Proter, do período de Janeiro/2017 a ' . date('d/m/Y', strtotime($dtmenos90dias)) . ', constatou-se que não havia pendências com mais de 90 dias.';
                                        $dto = DB::table('itensdeinspecoes')
                                            ->Where([['inspecao_id', '=', $registro->inspecao_id]])
                                            ->Where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                                            ->select('itensdeinspecoes.*')
                                            ->first();
                                        $itensdeinspecao = Itensdeinspecao::find($dto->id);
                                        $itensdeinspecao->avaliacao = $avaliacao;
                                        $itensdeinspecao->oportunidadeAprimoramento = $oportunidadeAprimoramento;
                                        $itensdeinspecao->evidencia = null;
                                        $itensdeinspecao->valorFalta = 0.00;
                                        $itensdeinspecao->situacao = 'Inspecionado';
                                        $itensdeinspecao->pontuado = 0.00;
                                        $itensdeinspecao->itemQuantificado = 'Não';
                                        $itensdeinspecao->orientacao = null;
                                        $itensdeinspecao->eventosSistema = 'Item avaliado remotamente por Websgi em ' . date('d/m/Y', strtotime($dtnow)) . '.';
                                        $itensdeinspecao->update();
                                    }
//                                  Fim Conforme


                                }
//                          Fim se não  reincidencia
//                          dd( '653',$dtmenos90dias , $registro->mcu, $countproters_con, $countproters_peso,  $countproters_cep );

                            }
//                      Final do teste PROTER

//                      Início do teste WebCont
                            if ((($registro->numeroGrupoVerificacao == 230) && ($registro->numeroDoTeste == 4))
                                || (($registro->numeroGrupoVerificacao == 270) && ($registro->numeroDoTeste == 1))) {

                                $reincidencia = DB::table('snci')
                                    ->select('no_inspecao', 'no_grupo', 'no_item', 'dt_fim_inspecao', 'dt_inic_inspecao')
                                    ->where([['descricao_item', 'like', '%3131)?%']])
                                    ->where([['sto', '=', $registro->sto]])
                                    ->orderBy('no_inspecao', 'desc')
                                    ->first();
                                try {
                                    if ($reincidencia->no_inspecao > 1) {
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
                                } catch (\Exception $e) {
                                    $reincidente = 0;
                                }
                                $mescompetencia = DB::table('debitoempregados')
                                    ->select('competencia')
                                    ->where([['debitoempregados.competencia', '>=', 1]])
                                    ->orderBy('competencia', 'desc')
                                    ->first();
                                $competencia = substr($mescompetencia->competencia, 4, 2) . '/' . substr($mescompetencia->competencia, 0, 4);
                                if ($reincidente == 1) {
                                    $debitoempregados = DB::table('debitoempregados')
                                        ->select('data', 'documento', 'historico', 'matricula', 'valor')
                                        ->where([['debitoempregados.data', '<=', $dtmenos90dias]])
                                        ->where([['debitoempregados.data', '>=', $reincidencia_dt_fim_inspecao]])
                                        ->Where([['debitoempregados.sto', '=', $registro->sto]])
                                        ->get();
                                } else {
                                    $debitoempregados = DB::table('debitoempregados')
                                        ->select('data', 'documento', 'historico', 'matricula', 'valor')
                                        ->where([['debitoempregados.data', '<=', $dtmenos90dias]])
                                        ->Where([['debitoempregados.sto', '=', $registro->sto]])
                                        ->get();
                                }

                                if (!$debitoempregados->isEmpty()) {
                                    $count = $debitoempregados->count('matricula');
                                    $total = $debitoempregados->sum('valor'); // soma a coluna valor da coleção de dados
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

                                    if (($count >= 1) && ($total > $quebracaixa)) {
                                        $avaliacao = 'Não Conforme';
                                        $pontuado = $registro->totalPontos * $fm->fator_multiplicador;
                                        $oportunidadeAprimoramento = 'Em Análise aos dados do Sistema WebCont – Composição Analítica da conta 11202.994000, posição de '
                                            . $competencia . ', constatou-se a existência de ' . $count . ' débitos de empregado sem regularização há mais de 90 dias, conforme relacionado a seguir:';
                                        $evidencia = "\n" . 'Data' . "\t" . 'Documento' . "\t" . 'Histórico' . "\t" . 'Matricula' . "\t" . 'Valor';

                                        foreach ($debitoempregados as $debitoempregado) {

                                            $evidencia = $evidencia . "\n" . date('d/m/Y', strtotime($debitoempregado->data))
                                                . "\t" . $debitoempregado->documento
                                                . "\t" . $debitoempregado->historico
                                                . "\t" . $debitoempregado->matricula
                                                . "\t" . ' R$ ' . number_format($debitoempregado->valor, 2, ',', '.');
                                        }

                                        $evidencia = $evidencia . "\n" . 'Total ' . "\t" . 'R$ ' . number_format($total, 2, ',', '.');
                                        $dto = DB::table('itensdeinspecoes')
                                            ->Where([['inspecao_id', '=', $registro->inspecao_id]])
                                            ->Where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                                            ->select('itensdeinspecoes.*')
                                            ->first();

                                        $itensdeinspecao = Itensdeinspecao::find($dto->id);
                                        $itensdeinspecao->avaliacao = $avaliacao;
                                        $itensdeinspecao->oportunidadeAprimoramento = $oportunidadeAprimoramento;
                                        $itensdeinspecao->evidencia = $evidencia;
                                        $itensdeinspecao->valorFalta = $total;
                                        $itensdeinspecao->situacao = 'Inspecionado';
                                        $itensdeinspecao->pontuado = $pontuado;
                                        $itensdeinspecao->itemQuantificado = 'Sim';
                                        $itensdeinspecao->orientacao = $registro->orientacao;
                                        $itensdeinspecao->eventosSistema = 'Item avaliado Remotamente por Websgi em ' . date('d/m/Y', strtotime($dtnow)) . '.';
                                        $itensdeinspecao->reincidencia = $reinc;
                                        $itensdeinspecao->codVerificacaoAnterior = $codVerificacaoAnterior;
                                        $itensdeinspecao->numeroGrupoReincidente = $numeroGrupoReincidente;
                                        $itensdeinspecao->numeroItemReincidente = $numeroItemReincidente;
                                        $itensdeinspecao->update();
                                    }
                                } else {
//                                    dd('nao  temmmmmmm debitos');
                                    //se não houve registro para a unidade o resultado é conforme
                                    $avaliacao = 'Conforme';
                                    $oportunidadeAprimoramento = 'Em Análise aos dados do Sistema WebCont – Composição Analítica da conta 11202.994000, verificada a posição do mês ' . $competencia . ' constatou-se que não havia histórico de pendências de débito de Empregados maior que 90 dias.';
                                    $dto = DB::table('itensdeinspecoes')
                                        ->Where([['inspecao_id', '=', $registro->inspecao_id]])
                                        ->Where([['testeVerificacao_id', '=', $registro->testeVerificacao_id]])
                                        ->select('itensdeinspecoes.*')
                                        ->first();
                                    $itensdeinspecao = Itensdeinspecao::find($dto->id);
                                    $itensdeinspecao->avaliacao = $avaliacao;
                                    $itensdeinspecao->oportunidadeAprimoramento = $oportunidadeAprimoramento;
                                    $itensdeinspecao->evidencia = null;
                                    $itensdeinspecao->valorFalta = 0.00;
                                    $itensdeinspecao->situacao = 'Inspecionado';
                                    $itensdeinspecao->pontuado = 0.00;
                                    $itensdeinspecao->itemQuantificado = 'Não';
                                    $itensdeinspecao->orientacao = null;
                                    $itensdeinspecao->eventosSistema = 'Item avaliado remotamente por Websgi em ' . date('d/m/Y', strtotime($dtnow)) . '.';
                                    $itensdeinspecao->update();
//                                     dd($competencia);
                                }
                            } // fim doteste webCont
                        }
                    } // Fim do teste para uma superintendencias
                }
            }
            ini_set('memory_limit', '128M');
        }

//$this->command->info('Avaliação Realizada com sucesso!');

}
