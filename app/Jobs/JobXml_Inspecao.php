<?php

namespace App\Jobs;

use App\Models\Correios\Inspecao;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;


class JobXml_Inspecao implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        $dtnow = new Carbon();
        $superintendencias = $this->superintendencias;
        $tipodeunidade = $this->tipodeunidade;
        $ciclo = $this->ciclo;
        ini_set('memory_limit', '512M');
        foreach ($superintendencias as $res) { // request é Array de indice para Superintendências
            foreach ($res as $superintendencia) { // percorre o Array de objeto Superintendências

                // testa se o primeiro parâmetro é para todas superintendecia onde SE == 1
                // Inicio do teste para todas superintendencias
                if ($superintendencia == 1) {
                    // se verdadeiro se SE == 1 seleciona todas superintendência cujo a SE > 1

                    $inspecoes = DB::table('itensdeinspecoes')
                        ->join('inspecoes', 'itensdeinspecoes.inspecao_id', '=', 'inspecoes.id')
                        ->join('unidades', 'itensdeinspecoes.unidade_id', '=', 'unidades.id')
                        ->join('testesdeverificacao', 'itensdeinspecoes.testeVerificacao_id', '=', 'testesdeverificacao.id')
                        ->join('gruposdeverificacao', 'itensdeinspecoes.grupoVerificacao_id', '=', 'gruposdeverificacao.id')
                        ->select('itensdeinspecoes.*', 'inspecoes.*', 'unidades.*', 'testesdeverificacao.*', 'gruposdeverificacao.*')
                        ->where([['situacao', '=', 'Em Inspeção']])
                        ->where([['se', '=', 1]])
                        ->where([['inspecoes.ciclo', '=', $ciclo]])
                        ->where([['itensdeinspecoes.tipoUnidade_id', '=', $tipodeunidade]])
//                            ->where([['sto', '=', 16301561 ]]) // ACREUNA
                        //->limit(100)
                        ->get();
                    ini_set('memory_limit', '512M');

                    // Início Gerar  XML

                    foreach ($inspecoes as $resp) {
                        $inspecao = Inspecao::find($resp->inspecao_id);
                        $situacao = 'AN';
                        $dtEncerramento = date('Y-m-d', strtotime($dtnow));
//                            dd($dtEncerramento);
//                            $dtEncerramento = '00:00:00';
                        $vazio = ' ';
                        $totalPontosNaInspeção = null;
//                        $diretorio = "xml/compliance/inspecao/";
                        $diretorio = "D:/webpages/sgiweb-Laravel_7_4/public/xml/compliance/inspecao/";

//                        D:webpages\sgiweb-Laravel_7_4\public\xml\compliance\inspecao

                        $businessUnit = DB::table('unidades')
                            ->Where([['id', '=',  $inspecao->unidade_id]])
                            ->select('unidades.*')
                            ->first();

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
                            ->where([['inspecao_id', '=', $inspecao->id]])
                            ->orderBy('itensdeinspecoes.testeVerificacao_id' , 'asc')
                            ->get();

                        $row=0;
                        $totalPontosNaInspecao =  $registros->sum('pontuado');
// Gera  XML
                        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>';
                        $xml .= '<rootelement>';
                        foreach ($registros as $registro) {
                            switch ($registro->avaliacao) {
                                case "Conforme":
                                    $avaliacao='C';
                                    $comentario = $registro->oportunidadeAprimoramento .$registro->norma;
                                    break;
                                case "Não Conforme":
                                    $avaliacao='N';
                                    $comentario = $registro->oportunidadeAprimoramento ."\n".$registro->evidencia ."\n".$registro->norma."\n".$registro->consequencias;
                                    break;
                                case "Não Verificado":
                                    $avaliacao='V';
                                    $comentario = $registro->oportunidadeAprimoramento ."\n".$registro->norma;
                                    break;
                                case "Não Executa Tarefa":
                                    $avaliacao='E';
                                    $comentario = $registro->oportunidadeAprimoramento ."\n".$registro->norma;
                                    break;
                            }

//                        testa se o registro foi avaliado, se não foi avaliado  a avaliação é nula para o registro
                            if ( $registro->eventosSistema == null) $avaliacao = null;

                            if ( $registro->itemQuantificado == 'Sim') {
                                $quantificado = 'Quantificado';
                                $falta = number_format($registro->valorFalta, 2, ',', '.');
                                $sobra =number_format($registro->valorSobra, 2, ',', '.');
                                $risco =number_format($registro->valorRisco, 2, ',', '.');
                            }
                            else {
                                $quantificado = 'Não Quantificado';
                                $falta = 0;
                                $sobra = 0;
                                $risco = 0;
                            }

                            if ( $registro->reincidencia == 'Sim') {
                                $codVerificacaoAnterior =  $registro->codVerificacaoAnterior;
                                $numeroGrupoReincidente =  $registro->numeroGrupoReincidente;
                                $numeroItemReincidente =  $registro->numeroItemReincidente;
                            }
                            else {
                                $codVerificacaoAnterior = ' ';
                                $numeroGrupoReincidente =  ' ';
                                $numeroItemReincidente =   ' ';

                            }

                            if (  $inspecao->tipoVerificacao=='Remoto') {
                                $modalidade=1;
                            }
                            else {
                                $modalidade=0;
                            }

                            $xml .= "\n\t".'<Dados>';
                            $xml .= "\n\t\t".'<RIP_Unidade>'.$inspecao->unidade->sto.'</RIP_Unidade>';
                            $xml .= "\n\t\t".'<RIP_NumInspecao>'.$inspecao->codigo.'</RIP_NumInspecao>';
                            $xml .= "\n\t\t".'<RIP_NumGrupo>'.$registro->numeroGrupoVerificacao.'</RIP_NumGrupo>';
                            $xml .= "\n\t\t".'<RIP_NumItem>'.$registro->numeroDoTeste.'</RIP_NumItem>';
                            $xml .= "\n\t\t".'<RIP_CodDiretoria>'.$inspecao->unidade->se.'</RIP_CodDiretoria>';

                            $xml .= "\n\t\t".'<RIP_CodReop>'.$businessUnit->mcu_subordinacaoAdm.'</RIP_CodReop>';
                            //isset($inspecao->reop_cod) ? $inspecao->reop_cod : ' '
                            $xml .= "\n\t\t".'<RIP_Ano>'.$inspecao->ciclo.'</RIP_Ano>';
                            $xml .= "\n\t\t".'<RIP_Resposta>'.$avaliacao.'</RIP_Resposta>';
                            $xml .= "\n\t\t".'<RIP_Comentario>'."\r\n\t\t\t".$comentario."\r\n\t\t".'</RIP_Comentario>';
                            $xml .= "\n\t\t".'<RIP_Caractvlr>'.$quantificado.'</RIP_Caractvlr>';
                            $xml .= "\n\t\t".'<RIP_Falta>'.$falta.'</RIP_Falta>';
                            $xml .= "\n\t\t".'<RIP_Sobra>'.$sobra.'</RIP_Sobra>';
                            $xml .= "\n\t\t".'<RIP_EmRisco>'.$risco.'</RIP_EmRisco>';
                            $xml .= "\n\t\t".'<RIP_DtUltAtu>'.\Carbon\Carbon::parse($inspecao->updated_at)->format( 'd/m/Y' ).'</RIP_DtUltAtu>';

//                            $xml .= "\n\t\t".'<RIP_UserName>'.auth()->user()->document.'</RIP_UserName>';
                            $xml .= "\n\t\t".'<RIP_UserName>'.$vazio.'</RIP_UserName>';

                            $xml .= "\n\t\t".'<RIP_Recomendacoes>'.$registro->orientacao.' </RIP_Recomendacoes>';
                            $xml .= "\n\t\t".'<RIP_ReincInspecao>'.$codVerificacaoAnterior.'</RIP_ReincInspecao>';
                            $xml .= "\n\t\t".'<RIP_ReincGrupo>'.$numeroGrupoReincidente.'</RIP_ReincGrupo>';
                            $xml .= "\n\t\t".'<RIP_ReincItem>'.$numeroItemReincidente.'</RIP_ReincItem>';

//                        ################  INICIO verificar quais os campos corretos#############
//                        Tabelade itens deinspeção do SNCI ( RIP_Pontuado)
//                        Tabela de inspeções do SNCI (INP_TotalPontuado)
                            $xml .= "\n\t\t".'<RIP_Pontuado>'.$registro->pontuado.'</RIP_Pontuado>';
                            $xml .= "\n\t\t".'<INP_TotalPontuado>'.$totalPontosNaInspecao.'</INP_TotalPontuado>';
//                        ################ FIM  verificar quais os campos corretos#############

                            $xml .= "\n\t\t".'<INP_HrsPreInspecao>'.$registro->NumHrsPreInsp.'</INP_HrsPreInspecao>';
                            $xml .= "\n\t\t".'<INP_DtInicDeslocamento>'.\Carbon\Carbon::parse($registro->datainiPreInspeção)->format( 'd/m/Y' ).'</INP_DtInicDeslocamento>';
                            $xml .= "\n\t\t".'<INP_DtFimDeslocamento>'.\Carbon\Carbon::parse($inspecao->updated_at)->format( 'd/m/Y' ).'</INP_DtFimDeslocamento>';
                            $xml .= "\n\t\t".'<INP_HrsDeslocamento>'.$registro->NumHrsDesloc.'</INP_HrsDeslocamento>';
                            $xml .= "\n\t\t".'<INP_DtInicInspecao>'.\Carbon\Carbon::parse($registro->datainiPreInspeção)->format( 'd/m/Y' ).'</INP_DtInicInspecao>';
                            $xml .= "\n\t\t".'<INP_DtFimInspecao>'.\Carbon\Carbon::parse($inspecao->updated_at)->format( 'd/m/Y' ).'</INP_DtFimInspecao>';
                            $xml .= "\n\t\t".'<INP_HrsInspecao>'.$registro->NumHrsInsp.'</INP_HrsInspecao>';
                            $xml .= "\n\t\t".'<INP_Situacao>'.$situacao.'</INP_Situacao>';

//                        ################  Checar se a formatação do campo  INP_DtEncerramento está correta #############
                            $xml .= "\n\t\t".'<INP_DtEncerramento>'.$dtEncerramento.'</INP_DtEncerramento>';
                            $xml .= "\n\t\t".'<INP_Coordenador>'.$inspecao->inspetorcoordenador.'</INP_Coordenador>';
                            $xml .= "\n\t\t".'<INP_Responsavel>'.$businessUnit->documentRespUnidade.' - '.$businessUnit->nomeResponsavelUnidade.'</INP_Responsavel>';
                            $xml .= "\n\t\t".'<INP_Motivo>'.$vazio.'</INP_Motivo>';
                            $xml .= "\n\t\t".'<INP_Introducao>'.$vazio.'</INP_Introducao>';
                            $xml .= "\n\t\t".'<INP_Conclusao>'.$vazio.'</INP_Conclusao>';
                            $xml .= "\n\t\t".'<INP_Modalidade>'.$modalidade.'</INP_Modalidade>';
                            $xml .= "\n\t\t".'<IPT_MatricInspetor>'.$inspecao->inspetorcoordenador.$inspecao->inspetorcolaborador.'</IPT_MatricInspetor>';
                            $xml .= "\n\t\t".'<IPT_NumHrsPreInsp>'.$inspecao->NumHrsPreInsp.' '.$inspecao->NumHrsPreInsp.'</IPT_NumHrsPreInsp>';
                            $xml .= "\n\t\t".'<IPT_NumHrsDesloc>'.$inspecao->NumHrsDesloc.' '.$inspecao->NumHrsDesloc.'</IPT_NumHrsDesloc>';
                            $xml .= "\n\t\t".'<IPT_NumHrsInsp>'.$inspecao->NumHrsInsp.' '.$inspecao->NumHrsInsp.'</IPT_NumHrsInsp>';
                            $xml .= "\n\t".'</Dados>';
                        }
                        $xml .= "\n".'</rootelement>';
                        $arquivo = $inspecao->codigo.'_'.$inspecao->inspetorcoordenador.'.xml';
                        $inspecao->status = 'Em Análise';
                        $inspecao->eventoInspecao = $inspecao->eventoInspecao . "\r\n".'Em Análise Xml gerado por SgiWeb' ." em ".\Carbon\Carbon::parse(Carbon::now())->format( 'd/m/Y' );
                        $inspecao->xml = $diretorio.$arquivo;
//                          Atualiza dados da Inspeção
                        $inspecao->save();
//                          Move  o arquivo XML para a pasta de destino
                        $arquivo = preg_replace('/\\s\\s+/', ' ', $arquivo);
                        $fp = fopen($diretorio.$arquivo, 'w+');
                        fwrite($fp, $xml);
                        fclose($fp);
                        $row ++;
                        if ($row ==5) dd('parou - '.$row.' registros');
//                          FINALIZA o arquivo XML
                    }

                    //   final da geração do arqquivo XML
                    ini_set('memory_limit', '128M');



                }  // Fim do teste para todas superintendencias se superintendencia = 1

                // inicio do testee para uma superintendencias
                else {
                    $totalPontosNaInspecao = 0;
                    $inspecoes = DB::table('itensdeinspecoes')
                        ->join('inspecoes', 'itensdeinspecoes.inspecao_id', '=', 'inspecoes.id')
                        ->join('unidades', 'itensdeinspecoes.unidade_id', '=', 'unidades.id')
                        ->join('testesdeverificacao', 'itensdeinspecoes.testeVerificacao_id', '=', 'testesdeverificacao.id')
                        ->join('gruposdeverificacao', 'itensdeinspecoes.grupoVerificacao_id', '=', 'gruposdeverificacao.id')
                        ->select('itensdeinspecoes.*', 'inspecoes.*', 'unidades.*', 'testesdeverificacao.*', 'gruposdeverificacao.*')
                        ->where([['status', '=', 'Em Inspeção']])  // após os testes comentar
//                          ->where([['status', '=', 'Inspecionado']]) // após os testes descomentar
                        ->where([['se', '=', $superintendencia]])
                        ->where([['inspecoes.ciclo', '=', $ciclo]])
                        ->where([['itensdeinspecoes.tipoUnidade_id', '=', $tipodeunidade]])
//                            ->where([['sto', '=', 16300882 ]]) // Anicuns
//                            ->where([['sto', '=', 16303407 ]]) // alto horizonte
//                            ->where([['sto', '=', 16300947 ]])   // Britania  16300947
//                            ->where([['mcu', '=', 6684 ]]) //ac anicuns
                        //->where([['itensdeinspecoes.testeVerificacao_id', '=', 3678 ]])  //3678  é smb_bdf
//                            ->limit(100)
                        ->get();
                    ini_set('memory_limit', '512M');

                    // Início Gerar  XML

                    foreach ($inspecoes as $resp) {
                        $inspecao = Inspecao::find($resp->inspecao_id);
                        $situacao = 'AN';
                        $dtEncerramento = date('Y-m-d', strtotime($dtnow));
//                            dd($dtEncerramento);
//                            $dtEncerramento = '00:00:00';
                        $vazio = ' ';
                        $totalPontosNaInspeção = null;
//                      $diretorio = "xml/compliance/inspecao/";
                        $diretorio = "D:/webpages/sgiweb-Laravel_7_4/public/xml/compliance/inspecao/";

                        $businessUnit = DB::table('unidades')
                            ->Where([['id', '=',  $inspecao->unidade_id]])
                            ->select('unidades.*')
                            ->first();

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
                            ->where([['inspecao_id', '=', $inspecao->id]])
                            ->orderBy('itensdeinspecoes.testeVerificacao_id' , 'asc')
                            ->get();

                        $row=0;
                        $totalPontosNaInspecao =  $registros->sum('pontuado');
// Gera  XML
                        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>';
                        $xml .= '<rootelement>';
                        foreach ($registros as $registro) {
                            switch ($registro->avaliacao) {
                                case "Conforme":
                                    $avaliacao='C';
                                    $comentario = $registro->oportunidadeAprimoramento .$registro->norma;
                                    break;
                                case "Não Conforme":
                                    $avaliacao='N';
                                    $comentario = $registro->oportunidadeAprimoramento ."\n".$registro->evidencia ."\n".$registro->norma."\n".$registro->consequencias;
                                    break;
                                case "Não Verificado":
                                    $avaliacao='V';
                                    $comentario = $registro->oportunidadeAprimoramento ."\n".$registro->norma;
                                    break;
                                case "Não Executa Tarefa":
                                    $avaliacao='E';
                                    $comentario = $registro->oportunidadeAprimoramento ."\n".$registro->norma;
                                    break;
                            }

//                        testa se o registro foi avaliado, se não foi avaliado  a avaliação é nula para o registro
                            if ( $registro->eventosSistema == null) $avaliacao = null;

                            if ( $registro->itemQuantificado == 'Sim') {
                                $quantificado = 'Quantificado';
                                $falta = number_format($registro->valorFalta, 2, ',', '.');
                                $sobra =number_format($registro->valorSobra, 2, ',', '.');
                                $risco =number_format($registro->valorRisco, 2, ',', '.');
                            }
                            else {
                                $quantificado = 'Não Quantificado';
                                $falta = 0;
                                $sobra = 0;
                                $risco = 0;
                            }

                            if ( $registro->reincidencia == 'Sim') {
                                $codVerificacaoAnterior =  $registro->codVerificacaoAnterior;
                                $numeroGrupoReincidente =  $registro->numeroGrupoReincidente;
                                $numeroItemReincidente =  $registro->numeroItemReincidente;
                            }
                            else {
                                $codVerificacaoAnterior = ' ';
                                $numeroGrupoReincidente =  ' ';
                                $numeroItemReincidente =   ' ';

                            }

                            if (  $inspecao->tipoVerificacao=='Remoto') {
                                $modalidade=1;
                            }
                            else {
                                $modalidade=0;
                            }

                            $xml .= "\n\t".'<Dados>';
                            $xml .= "\n\t\t".'<RIP_Unidade>'.$inspecao->unidade->sto.'</RIP_Unidade>';
                            $xml .= "\n\t\t".'<RIP_NumInspecao>'.$inspecao->codigo.'</RIP_NumInspecao>';
                            $xml .= "\n\t\t".'<RIP_NumGrupo>'.$registro->numeroGrupoVerificacao.'</RIP_NumGrupo>';
                            $xml .= "\n\t\t".'<RIP_NumItem>'.$registro->numeroDoTeste.'</RIP_NumItem>';
                            $xml .= "\n\t\t".'<RIP_CodDiretoria>'.$inspecao->unidade->se.'</RIP_CodDiretoria>';

                            $xml .= "\n\t\t".'<RIP_CodReop>'.$businessUnit->mcu_subordinacaoAdm.'</RIP_CodReop>';
                            //isset($inspecao->reop_cod) ? $inspecao->reop_cod : ' '
                            $xml .= "\n\t\t".'<RIP_Ano>'.$inspecao->ciclo.'</RIP_Ano>';
                            $xml .= "\n\t\t".'<RIP_Resposta>'.$avaliacao.'</RIP_Resposta>';
                            $xml .= "\n\t\t".'<RIP_Comentario>'."\r\n\t\t\t".$comentario."\r\n\t\t".'</RIP_Comentario>';
                            $xml .= "\n\t\t".'<RIP_Caractvlr>'.$quantificado.'</RIP_Caractvlr>';
                            $xml .= "\n\t\t".'<RIP_Falta>'.$falta.'</RIP_Falta>';
                            $xml .= "\n\t\t".'<RIP_Sobra>'.$sobra.'</RIP_Sobra>';
                            $xml .= "\n\t\t".'<RIP_EmRisco>'.$risco.'</RIP_EmRisco>';
                            $xml .= "\n\t\t".'<RIP_DtUltAtu>'.\Carbon\Carbon::parse($inspecao->updated_at)->format( 'd/m/Y' ).'</RIP_DtUltAtu>';


//                            $xml .= "\n\t\t".'<RIP_UserName>'.auth()->user()->document.'</RIP_UserName>';
                            $xml .= "\n\t\t".'<RIP_UserName>'.$vazio.'</RIP_UserName>';


                            $xml .= "\n\t\t".'<RIP_Recomendacoes>'.$registro->orientacao.' </RIP_Recomendacoes>';
                            $xml .= "\n\t\t".'<RIP_ReincInspecao>'.$codVerificacaoAnterior.'</RIP_ReincInspecao>';
                            $xml .= "\n\t\t".'<RIP_ReincGrupo>'.$numeroGrupoReincidente.'</RIP_ReincGrupo>';
                            $xml .= "\n\t\t".'<RIP_ReincItem>'.$numeroItemReincidente.'</RIP_ReincItem>';

//                        ################  INICIO verificar quais os campos corretos#############
//                        Tabelade itens deinspeção do SNCI ( RIP_Pontuado)
//                        Tabela de inspeções do SNCI (INP_TotalPontuado)
                            $xml .= "\n\t\t".'<RIP_Pontuado>'.$registro->pontuado.'</RIP_Pontuado>';
                            $xml .= "\n\t\t".'<INP_TotalPontuado>'.$totalPontosNaInspecao.'</INP_TotalPontuado>';
//                        ################ FIM  verificar quais os campos corretos#############

                            $xml .= "\n\t\t".'<INP_HrsPreInspecao>'.$registro->NumHrsPreInsp.'</INP_HrsPreInspecao>';
                            $xml .= "\n\t\t".'<INP_DtInicDeslocamento>'.\Carbon\Carbon::parse($registro->datainiPreInspeção)->format( 'd/m/Y' ).'</INP_DtInicDeslocamento>';
                            $xml .= "\n\t\t".'<INP_DtFimDeslocamento>'.\Carbon\Carbon::parse($inspecao->updated_at)->format( 'd/m/Y' ).'</INP_DtFimDeslocamento>';
                            $xml .= "\n\t\t".'<INP_HrsDeslocamento>'.$registro->NumHrsDesloc.'</INP_HrsDeslocamento>';
                            $xml .= "\n\t\t".'<INP_DtInicInspecao>'.\Carbon\Carbon::parse($registro->datainiPreInspeção)->format( 'd/m/Y' ).'</INP_DtInicInspecao>';
                            $xml .= "\n\t\t".'<INP_DtFimInspecao>'.\Carbon\Carbon::parse($inspecao->updated_at)->format( 'd/m/Y' ).'</INP_DtFimInspecao>';
                            $xml .= "\n\t\t".'<INP_HrsInspecao>'.$registro->NumHrsInsp.'</INP_HrsInspecao>';
                            $xml .= "\n\t\t".'<INP_Situacao>'.$situacao.'</INP_Situacao>';

//                        ################  Checar se a formatação do campo  INP_DtEncerramento está correta #############
                            $xml .= "\n\t\t".'<INP_DtEncerramento>'.$dtEncerramento.'</INP_DtEncerramento>';
                            $xml .= "\n\t\t".'<INP_Coordenador>'.$inspecao->inspetorcoordenador.'</INP_Coordenador>';
                            $xml .= "\n\t\t".'<INP_Responsavel>'.$businessUnit->documentRespUnidade.' - '.$businessUnit->nomeResponsavelUnidade.'</INP_Responsavel>';
                            $xml .= "\n\t\t".'<INP_Motivo>'.$vazio.'</INP_Motivo>';
                            $xml .= "\n\t\t".'<INP_Introducao>'.$vazio.'</INP_Introducao>';
                            $xml .= "\n\t\t".'<INP_Conclusao>'.$vazio.'</INP_Conclusao>';
                            $xml .= "\n\t\t".'<INP_Modalidade>'.$modalidade.'</INP_Modalidade>';
                            $xml .= "\n\t\t".'<IPT_MatricInspetor>'.$inspecao->inspetorcoordenador.$inspecao->inspetorcolaborador.'</IPT_MatricInspetor>';
                            $xml .= "\n\t\t".'<IPT_NumHrsPreInsp>'.$inspecao->NumHrsPreInsp.' '.$inspecao->NumHrsPreInsp.'</IPT_NumHrsPreInsp>';
                            $xml .= "\n\t\t".'<IPT_NumHrsDesloc>'.$inspecao->NumHrsDesloc.' '.$inspecao->NumHrsDesloc.'</IPT_NumHrsDesloc>';
                            $xml .= "\n\t\t".'<IPT_NumHrsInsp>'.$inspecao->NumHrsInsp.' '.$inspecao->NumHrsInsp.'</IPT_NumHrsInsp>';
                            $xml .= "\n\t".'</Dados>';
                        }
                        $xml .= "\n".'</rootelement>';
                        $arquivo = $inspecao->codigo.'_'.$inspecao->inspetorcoordenador.'.xml';
                        $inspecao->status = 'Em Análise';
                        $inspecao->eventoInspecao = $inspecao->eventoInspecao . "\r\n".'Em Análise Xml gerado por SgiWeb '." em ".\Carbon\Carbon::parse(Carbon::now())->format( 'd/m/Y' );
                        $inspecao->xml = $diretorio.$arquivo;
//                          Atualiza dados da Inspeção
                        $inspecao->save();
//                          Move  o arquivo XML para a pasta de destino
                        $arquivo = preg_replace('/\\s\\s+/', ' ', $arquivo);
                        $fp = fopen($diretorio.$arquivo, 'w+');
                        fwrite($fp, $xml);
                        fclose($fp);
                        $row ++;
                        if ($row ==5) dd('parou - '.$row.' registros');
//                          FINALIZA o arquivo XML
                    }

                    //   final da geração do arqquivo XML
                    ini_set('memory_limit', '128M');

                } // Fim  para gerar xml por superintendencias
            }
        }
        ini_set('memory_limit', '128M');
    }
}
