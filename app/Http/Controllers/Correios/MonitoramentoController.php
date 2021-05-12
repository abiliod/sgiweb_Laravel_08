<?php

namespace App\Http\Controllers\Correios;

use App\Http\Controllers\Controller;
use App\Jobs\AvaliaInspecao;
use App\Jobs\GeraInspecao;
use App\Jobs\JobConclui_Inspecao;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\PhpOffice\PhpSpreadsheet\Shared\Date;
use Auth;


class MonitoramentoController extends Controller {

    public function avaliacao(Request $request) {
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

        $codVerificacaoAnterior = null;
        $numeroGrupoReincidente = null;
        $numeroItemReincidente = null;
        $validator = Validator::make($request->all(), [
            'superintendencia' => 'required'
            , 'tipodeunidade' => 'required'
        ]);
        if (!$validator->passes()) {
            \Session::flash('mensagem', ['msg' => 'Parâmetros insuficiente para o agendamento do Job.'
                , 'class' => 'red white-text']);
            return redirect()->back();
        }
        else {
            $superintendencias = $request->all(['superintendencia']);
            $tipodeunidade = $request->all(['tipodeunidade']);
            $ciclo = $request->all(['ciclo']);

//#########################################################################################################
//            para ativar a fila no console
//            php artisan queue:work --queue=avaliaInspecao

            $job = (new AvaliaInspecao($superintendencias, $tipodeunidade , $ciclo))
                ->onQueue('avaliaInspecao')->delay($dtnow->addMinutes(1));
            dispatch($job);

            \Session::flash('mensagem', ['msg' => 'Job AvaliaInspecao aguardando processamento.'
                , 'class' => 'blue white-text']);
            return redirect()->back();

//   O valor de 134217728 bytes é equivalente a 128M

//   Erro de Memória.Isso ocorre porque no arquivo php.ini o parâmetro memory_limit está configurado para 128M. E para consertar o problema é só editar o arquivo e alterar para 256M.
//#########################################################################################################

        }
    }

    public function avaliar() {
        $status = 'Criado e instalado';
        try {
            $businessUnitUser = DB::table('unidades')
                ->Where([['mcu', '=', auth()->user()->businessUnit]])
                ->select('unidades.*')
                ->first();
        }catch ( \Exception $e){
            return redirect()->route('login');
        }

        if (!empty($businessUnitUser)) {
            $papel_user = DB::table('papel_user')
                ->Where([['user_id', '=', auth()->user()->id]])
                ->Where([['papel_id', '>=', 1]])
                ->select('papel_id')
                ->first();
            switch ($papel_user->papel_id) {
                case 1:
                case 2:
                    {
                        $registros = DB::table('unidades')
                            ->select(
                                'id', 'se', 'seDescricao'
                            )
                            ->where([['se', '>', 1]])
                            ->where([['status_unidadeDesc', '=', $status]])
                            ->groupBy('se')
                            ->orderBy('seDescricao', 'asc')
                        ->get();

                    }
                    break;
            }

            $tiposDeUnidade = DB::table('tiposdeunidade')
                ->where([
                    ['inspecionar', '=', 'sim'],
                    ['tipoInspecao', '=', 'Monitorada'],
                ])
            ->get();

            return view('compliance.monitoramento.avaliar', compact('registros', 'tiposDeUnidade'));
        } else {
            \Session::flash('mensagem', ['msg' => 'Não foi possivel exibir os itens você provavelmente não é administrador.'
                , 'class' => 'red white-text']);
            return redirect()->route('home');
        }
    }


    public function create(Request $request) {

        $validator = Validator::make($request->all(), [
             'data' => 'required'
            , 'superintendencia' => 'required'
            , 'tipodeunidade' => 'required'
            , 'ciclo' => 'required'
        ]);

        if (!$validator->passes()) {
            \Session::flash('mensagem', ['msg' => 'Parâmetros insuficiente para o agendamento do Job.'
                , 'class' => 'red white-text']);
            return redirect()->back();
        }
        else {
            $dados = $request->all();
            $superintendencias = $request->all(['superintendencia']);
            $ciclo = $dados['ciclo'];
            $dataAgenda = $this->transformDate($dados['data']);
            $status = 'Criado e instalado';
            $tipoInspecao = DB::table('tiposdeunidade')
                ->where([
                    ['tipoInspecao', '=', 'Monitorada'],
                ])
                ->where([
                    ['id', '=', $dados['tipodeunidade']],
                ])
            ->get();


            if ($tipoInspecao->isEmpty()) {
                \Session::flash('mensagem', ['msg' => 'Tipo de Inspeção não Previsto!'
                    , 'class' => 'red white-text']);
                return redirect()->back();
            }
            else
            {


                // php artisan queue:work --queue=geraInspecao
                ini_set('memory_limit', '512M');
                ini_set('max_input_time', 350);
                ini_set('max_execution_time', 350);

  //                dd($superintendencias, $status , $ciclo, $dataAgenda);

                $job = (new GeraInspecao($superintendencias, $status , $ciclo, $dataAgenda))
                    ->onQueue('geraInspecao')->delay($dataAgenda->addMinutes(1));
                dispatch($job);

                \Session::flash('mensagem', ['msg' => 'Job aguardando processamento.'
                    , 'class' => 'blue white-text']);
                ini_set('memory_limit', '128M');
                ini_set('max_input_time', 60);
                ini_set('max_execution_time', 60);
                return redirect()->back();
            }
        }

    }

    public function criar() {
        $status = 'Criado e instalado';
        try {
            $businessUnitUser = DB::table('unidades')
                ->Where([['mcu', '=', auth()->user()->businessUnit]])
                ->select('unidades.*')
                ->first();
        }catch ( \Exception $e){
        return redirect()->route('login');
    }


        if (!empty($businessUnitUser)) {
            $papel_user = DB::table('papel_user')
                ->Where([['user_id', '=', auth()->user()->id]])
                ->Where([['papel_id', '>=', 1]])
                ->select('papel_id')
                ->first();
            switch ($papel_user->papel_id) {
                case 1:
                case 2:
                    {
                        $registros = DB::table('unidades')
                            ->select(
                                'id', 'se', 'seDescricao'
                            )
                            ->where([['se', '>', 1]])
                            ->where([['status_unidadeDesc', '=', $status]])
                            ->groupBy('se')
                            ->orderBy('seDescricao', 'asc')
                        ->get();

                    }
                    break;
            }

            $tiposDeUnidade = DB::table('tiposdeunidade')
                ->where([
                    ['inspecionar', '=', 'sim'],
                    ['tipoInspecao', '=', 'Monitorada'],
                ])
            ->get();

            return view('compliance.monitoramento.criar', compact('registros', 'tiposDeUnidade'));
        } else {
            \Session::flash('mensagem', ['msg' => 'Não foi possivel exibir os itens você provavelmente não é administrador.'
                , 'class' => 'red white-text']);
            return redirect()->route('home');
        }
    }



    public function gerar_xml(Request $request) {
        $dtnow = new Carbon();
        $validator = Validator::make($request->all(), [
            'superintendencia' => 'required'
            , 'tipodeunidade' => 'required'
        ]);
        if (!$validator->passes()) {
            \Session::flash('mensagem', ['msg' => 'Parâmetros insuficiente para o agendamento do Job.'
                , 'class' => 'red white-text']);
            return redirect()->back();
        } else {
            $superintendencias = $request->all(['superintendencia']);
            $tipodeunidade = $request->all(['tipodeunidade']);
            $ciclo = $request->all(['ciclo']);

//#########################################################################################################
//            para ativar a fila no console
//            php artisan queue:work --queue= geraXmlInspecao


            $job = (new JobXml_Inspecao ($superintendencias, $tipodeunidade, $ciclo))
                ->onQueue('geraXmlInspecao')->delay($dtnow->addMinutes(1));
            dispatch($job);

            \Session::flash('mensagem', ['msg' => 'Job Xml Inspecao aguardando processamento.'
                , 'class' => 'blue white-text']);
            return redirect()->back();

//   O valor de 134217728 bytes é equivalente a 128M

//   Erro de Memória.Isso ocorre porque no arquivo php.ini o parâmetro memory_limit está configurado para 128M. E para consertar o problema é só editar o arquivo e alterar para 256M.
//#########################################################################################################

//
//
//            ini_set('memory_limit', '512M');
//            foreach ($superintendencias as $res) { // request é Array de indice para Superintendências
//                foreach ($res as $superintendencia) { // percorre o Array de objeto Superintendências
//
//                    // testa se o primeiro parâmetro é para todas superintendecia onde SE == 1
//                    // Inicio do teste para todas superintendencias
//                    if ($superintendencia === 1) {
//
//                        // se verdadeiro se SE == 1 seleciona todas superintendência cujo a SE > 1
//
//                    }  // Fim do teste para todas superintendencias se superintendencia = 1
//
//                    // inicio do testee para uma superintendencias
//                    else {
//
//                        $inspecoes = DB::table('itensdeinspecoes')
//                            ->join('inspecoes', 'itensdeinspecoes.inspecao_id', '=', 'inspecoes.id')
//                            ->join('unidades', 'itensdeinspecoes.unidade_id', '=', 'unidades.id')
//                            ->join('testesdeverificacao', 'itensdeinspecoes.testeVerificacao_id', '=', 'testesdeverificacao.id')
//                            ->join('gruposdeverificacao', 'itensdeinspecoes.grupoVerificacao_id', '=', 'gruposdeverificacao.id')
//                            ->select('itensdeinspecoes.*', 'inspecoes.*', 'unidades.*', 'testesdeverificacao.*', 'gruposdeverificacao.*')
//                            ->where([['status', '=', 'Corroborado']]) // após os testes descomentar
//                            ->where([['se', '=', $superintendencia]])
//                            ->where([['inspecoes.ciclo', '=', $ciclo]])
//                            ->where([['itensdeinspecoes.tipoUnidade_id', '=', $tipodeunidade]])
//                            ->get();
//                        ini_set('memory_limit', '512M');
//
//                        // Início Gerar  XML
//
//                        foreach ($inspecoes as $resp) {
//                            $inspecao = Inspecao::find($resp->inspecao_id);
//                            $situacao = 'AN';
//                            $dtEncerramento = date('Y-m-d', strtotime($dtnow));
//                            $vazio = ' ';
//                            $totalPontosNaInspeção = null;
////                      $diretorio = "xml/compliance/inspecao/";
//                            $diretorio = "D:/webpages/sgiweb-Laravel_7_4/public/xml/compliance/inspecao/";
//
//                            $businessUnit = DB::table('unidades')
//                                ->Where([['id', '=',  $inspecao->unidade_id]])
//                                ->select('unidades.*')
//                                ->first();
//
//                            $registros = DB::table('itensdeinspecoes')
//                                ->join('inspecoes', 'itensdeinspecoes.inspecao_id', '=', 'inspecoes.id')
//                                ->join('gruposdeverificacao', 'itensdeinspecoes.grupoVerificacao_id', '=', 'gruposdeverificacao.id')
//                                ->join('testesdeverificacao', 'itensdeinspecoes.testeVerificacao_id', '=', 'testesdeverificacao.id')
//                                ->select('inspecoes.*'
//                                    , 'itensdeinspecoes.*'
//                                    ,'gruposdeverificacao.numeroGrupoVerificacao'
//                                    ,'gruposdeverificacao.nomegrupo'
//                                    ,'testesdeverificacao.numeroDoTeste'
//                                    ,'testesdeverificacao.teste'
//                                )
//                                ->where([['inspecao_id', '=', $inspecao->id]])
//                                ->orderBy('itensdeinspecoes.testeVerificacao_id' , 'asc')
//                                ->get();
//
////                            $totalPontosNaInspecao =  $registros->sum('pontuado');
//// Gera  XML
/*                            $xml = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>';*/
//                            $xml .= '<rootelement>';
//                            foreach ($registros as $registro) {
//                                switch ($registro->avaliacao) {
//                                    case "Conforme":
//                                        $avaliacao='C';
//                                        $comentario = $registro->oportunidadeAprimoramento .$registro->norma;
//                                        break;
//                                    case "Não Conforme":
//                                        $avaliacao='N';
//                                        $comentario = $registro->oportunidadeAprimoramento ."\n".$registro->evidencia ."\n".$registro->norma."\n".$registro->consequencias;
//                                        break;
//                                    case "Não Verificado":
//                                        $avaliacao='V';
//                                        $comentario = $registro->oportunidadeAprimoramento ."\n".$registro->norma;
//                                        break;
//                                    case "Não Executa Tarefa":
//                                        $avaliacao='E';
//                                        $comentario = $registro->oportunidadeAprimoramento ."\n".$registro->norma;
//                                        break;
//                                }
//
////                        testa se o registro foi avaliado, se não foi avaliado  a avaliação é nula para o registro
//                                if ( $registro->eventosSistema == null) $avaliacao = null;
//
//                                if ( $registro->itemQuantificado == 'Sim') {
//                                    $quantificado = 'Quantificado';
//                                    $falta = number_format($registro->valorFalta, 2, ',', '.');
//                                    $sobra =number_format($registro->valorSobra, 2, ',', '.');
//                                    $risco =number_format($registro->valorRisco, 2, ',', '.');
//                                }
//                                else {
//                                    $quantificado = 'Não Quantificado';
//                                    $falta = 0;
//                                    $sobra = 0;
//                                    $risco = 0;
//                                }
//
//                                if ( $registro->reincidencia == 'Sim') {
//                                    $codVerificacaoAnterior =  $registro->codVerificacaoAnterior;
//                                    $numeroGrupoReincidente =  $registro->numeroGrupoReincidente;
//                                    $numeroItemReincidente =  $registro->numeroItemReincidente;
//                                }
//                                else {
//                                    $codVerificacaoAnterior = ' ';
//                                    $numeroGrupoReincidente =  ' ';
//                                    $numeroItemReincidente =   ' ';
//
//                                }
//
//                                if ( ( $inspecao->tipoVerificacao=='Remoto') || ( $inspecao->tipoVerificacao=='Monitorada') ) {
//                                    $modalidade=1;
//                                }
//                                else {
//                                    $modalidade=0;
//                                }
//
//                                $xml .= "\n\t".'<Dados>';
//                                $xml .= "\n\t\t".'<RIP_Unidade>'.$inspecao->unidade->sto.'</RIP_Unidade>';
//                                $xml .= "\n\t\t".'<RIP_NumInspecao>'.$inspecao->codigo.'</RIP_NumInspecao>';
//                                $xml .= "\n\t\t".'<RIP_NumGrupo>'.$registro->numeroGrupoVerificacao.'</RIP_NumGrupo>';
//                                $xml .= "\n\t\t".'<RIP_NumItem>'.$registro->numeroDoTeste.'</RIP_NumItem>';
//                                $xml .= "\n\t\t".'<RIP_CodDiretoria>'.$inspecao->unidade->se.'</RIP_CodDiretoria>';
//
//                                $xml .= "\n\t\t".'<RIP_CodReop>'.$businessUnit->mcu_subordinacaoAdm.'</RIP_CodReop>';
//                                //isset($inspecao->reop_cod) ? $inspecao->reop_cod : ' '
//                                $xml .= "\n\t\t".'<RIP_Ano>'.$inspecao->ciclo.'</RIP_Ano>';
//                                $xml .= "\n\t\t".'<RIP_Resposta>'.$avaliacao.'</RIP_Resposta>';
//                                $xml .= "\n\t\t".'<RIP_Comentario>'."\r\n\t\t\t".$comentario."\r\n\t\t".'</RIP_Comentario>';
//                                $xml .= "\n\t\t".'<RIP_Caractvlr>'.$quantificado.'</RIP_Caractvlr>';
//                                $xml .= "\n\t\t".'<RIP_Falta>'.$falta.'</RIP_Falta>';
//                                $xml .= "\n\t\t".'<RIP_Sobra>'.$sobra.'</RIP_Sobra>';
//                                $xml .= "\n\t\t".'<RIP_EmRisco>'.$risco.'</RIP_EmRisco>';
//                                $xml .= "\n\t\t".'<RIP_DtUltAtu>'.\Carbon\Carbon::parse($inspecao->updated_at)->format( 'd/m/Y' ).'</RIP_DtUltAtu>';
//
////                            $xml .= "\n\t\t".'<RIP_UserName>'.auth()->user()->document.'</RIP_UserName>';
//                                $xml .= "\n\t\t".'<RIP_UserName>'.$vazio.'</RIP_UserName>';
//                                $xml .= "\n\t\t".'<RIP_Recomendacoes>'.$registro->orientacao.' </RIP_Recomendacoes>';
//                                $xml .= "\n\t\t".'<RIP_ReincInspecao>'.$codVerificacaoAnterior.'</RIP_ReincInspecao>';
//                                $xml .= "\n\t\t".'<RIP_ReincGrupo>'.$numeroGrupoReincidente.'</RIP_ReincGrupo>';
//                                $xml .= "\n\t\t".'<RIP_ReincItem>'.$numeroItemReincidente.'</RIP_ReincItem>';
//
////                        ################  INICIO verificar quais os campos corretos#############
////                        Tabelade itens deinspeção do SNCI ( RIP_Pontuado)
////                        Tabela de inspeções do SNCI (INP_TotalPontuado)
//
//                                $xml .= "\n\t\t".'<RIP_Pontuado>'.$registro->pontuado.'</RIP_Pontuado>';
//                                $xml .= "\n\t\t".'<RIP_PontuadoInspetor>'.$registro->pontuadoInspetor.'</RIP_PontuadoInspetor>';
//                                $xml .= "\n\t\t".'<INP_TotalPontos>'.$registro->totalPontos.'</INP_TotalPontos>';
//                                $xml .= "\n\t\t".'<INP_TotalpontosInspetor>'.$registro->totalpontosInspetor.'</INP_TotalpontosInspetor>';
//                                $xml .= "\n\t\t".'<INP_PontuacaoFinal>'.$registro->pontuacaoFinal.'</INP_PontuacaoFinal>';
//                                $xml .= "\n\t\t".'<INP_Valor_ref_itens_inspecionados>'.$registro->valor_ref_itens_inspecionados.'</INP_Valor_ref_itens_inspecionados>';
//                                $xml .= "\n\t\t".'<INP_Totalpontosnaoconforme>'.$registro->totalpontosnaoconforme.'</INP_Totalpontosnaoconforme>';
//                                $xml .= "\n\t\t".'<INP_Tnc>'.$registro->tnc.'</INP_Tnc>';
//                                $xml .= "\n\t\t".'<INP_Totalitensavaliados>'.$registro->totalitensavaliados.'</INP_Totalitensavaliados>';
//                                $xml .= "\n\t\t".'<INP_Totalitensnaoconforme>'.$registro->totalitensnaoconforme.'</INP_Totalitensnaoconforme>';
//                                $xml .= "\n\t\t".'<INP_Classificacao>'.$registro->classificacao.'</INP_Classificacao>';
//
////                        ################ FIM  verificar quais os campos corretos no Sistema SNCI se não existir pedir para criar#############
//
//                                $xml .= "\n\t\t".'<INP_HrsPreInspecao>'.$registro->NumHrsPreInsp.'</INP_HrsPreInspecao>';
//                                $xml .= "\n\t\t".'<INP_DtInicDeslocamento>'.Carbon::parse($registro->datainiPreInspeção)->format( 'd/m/Y' ).'</INP_DtInicDeslocamento>';
//                                $xml .= "\n\t\t".'<INP_DtFimDeslocamento>'.Carbon::parse($inspecao->updated_at)->format( 'd/m/Y' ).'</INP_DtFimDeslocamento>';
//                                $xml .= "\n\t\t".'<INP_HrsDeslocamento>'.$registro->NumHrsDesloc.'</INP_HrsDeslocamento>';
//                                $xml .= "\n\t\t".'<INP_DtInicInspecao>'.Carbon::parse($registro->datainiPreInspeção)->format( 'd/m/Y' ).'</INP_DtInicInspecao>';
//                                $xml .= "\n\t\t".'<INP_DtFimInspecao>'.Carbon::parse($inspecao->updated_at)->format( 'd/m/Y' ).'</INP_DtFimInspecao>';
//                                $xml .= "\n\t\t".'<INP_HrsInspecao>'.$registro->NumHrsInsp.'</INP_HrsInspecao>';
//                                $xml .= "\n\t\t".'<INP_Situacao>'.$situacao.'</INP_Situacao>';
//
////                        ################  Checar se a formatação do campo  INP_DtEncerramento está correta #############
//                                $xml .= "\n\t\t".'<INP_DtEncerramento>'.$dtEncerramento.'</INP_DtEncerramento>';
//                                $xml .= "\n\t\t".'<INP_Coordenador>'.$inspecao->inspetorcoordenador.'</INP_Coordenador>';
//                                $xml .= "\n\t\t".'<INP_Responsavel>'.$businessUnit->documentRespUnidade.' - '.$businessUnit->nomeResponsavelUnidade.'</INP_Responsavel>';
//                                $xml .= "\n\t\t".'<INP_Motivo>'.$vazio.'</INP_Motivo>';
//                                $xml .= "\n\t\t".'<INP_Introducao>'.$vazio.'</INP_Introducao>';
//                                $xml .= "\n\t\t".'<INP_Conclusao>'.$vazio.'</INP_Conclusao>';
//                                $xml .= "\n\t\t".'<INP_Modalidade>'.$modalidade.'</INP_Modalidade>';
//                                $xml .= "\n\t\t".'<IPT_MatricInspetor>'.$inspecao->inspetorcoordenador.$inspecao->inspetorcolaborador.'</IPT_MatricInspetor>';
//                                $xml .= "\n\t\t".'<IPT_NumHrsPreInsp>'.$inspecao->NumHrsPreInsp.' '.$inspecao->NumHrsPreInsp.'</IPT_NumHrsPreInsp>';
//                                $xml .= "\n\t\t".'<IPT_NumHrsDesloc>'.$inspecao->NumHrsDesloc.' '.$inspecao->NumHrsDesloc.'</IPT_NumHrsDesloc>';
//                                $xml .= "\n\t\t".'<IPT_NumHrsInsp>'.$inspecao->NumHrsInsp.' '.$inspecao->NumHrsInsp.'</IPT_NumHrsInsp>';
//                                $xml .= "\n\t".'</Dados>';
//                            }
//                            $xml .= "\n".'</rootelement>';
//                            $arquivo = $inspecao->codigo.'_'.$inspecao->inspetorcoordenador.'.xml';
//                            // colocar o status correto  para manifestação da unidade
//                            $inspecao->status = 'Em Manifestação';
//                            $inspecao->eventoInspecao = $inspecao->eventoInspecao . "\r\n".'Em Análise/Manifestação Xml gerado por SgiWeb '." em ".\Carbon\Carbon::parse(Carbon::now())->format( 'd/m/Y' );
//                            $inspecao->xml = $diretorio.$arquivo;
////                          Atualiza dados da Inspeção
//                            $inspecao->save();
////                          Move  o arquivo XML para a pasta de destino
//                            $arquivo = preg_replace('/\\s\\s+/', ' ', $arquivo);
//                            $fp = fopen($diretorio.$arquivo, 'w+');
//                            fwrite($fp, $xml);
//                            fclose($fp);
//
////                          FINALIZA o arquivo XML
//                        }
//
//                        //   final da geração do arqquivo XML
//                        ini_set('memory_limit', '128M');
//
//                    } // Fim  para gerar xml por superintendencias
//                }
//            }
//            ini_set('memory_limit', '128M');
//
        }
    }

    public function xml() {
        $status = 'Criado e instalado';
        try {
            $businessUnitUser = DB::table('unidades')
                ->Where([['mcu', '=', auth()->user()->businessUnit]])
                ->select('unidades.*')
                ->first();
        }catch ( \Exception $e){
            return redirect()->route('login');
        }

        if (!empty($businessUnitUser)) {
            $papel_user = DB::table('papel_user')
                ->Where([['user_id', '=', auth()->user()->id]])
                ->Where([['papel_id', '>=', 1]])
                ->select('papel_id')
                ->first();
            switch ($papel_user->papel_id) {
                case 1:
                case 2:
                    {
                        $registros = DB::table('unidades')
                            ->select(
                                'id', 'se', 'seDescricao'
                            )
                            ->where([['se', '>', 1]])
                            ->where([['status_unidadeDesc', '=', $status]])
                            ->groupBy('se')
                            ->orderBy('seDescricao', 'asc')
                            ->get();

                    }
                    break;
            }

            $tiposDeUnidade = DB::table('tiposdeunidade')
                ->where([
                    ['inspecionar', '=', 'sim'],
                    ['tipoInspecao', '=', 'Monitorada'],
                ])
                ->get();

//            dd( $tiposDeUnidade);

            return view('compliance.monitoramento.gerar_xml', compact('registros', 'tiposDeUnidade'));
        } else {
            \Session::flash('mensagem', ['msg' => 'Não foi possivel exibir os itens você provavelmente não é administrador.'
                , 'class' => 'red white-text']);
            return redirect()->route('home');
        }
    }

    public function concluir_insp(Request $request) {

        $dtnow = new Carbon();
        $validator = Validator::make($request->all(), [
            'superintendencia' => 'required'
            , 'tipodeunidade' => 'required'
        ]);
        if (!$validator->passes()) {
            \Session::flash('mensagem', ['msg' => 'Parâmetros insuficiente para o agendamento do Job.'
                , 'class' => 'red white-text']);
            return redirect()->back();
        } else {
            $superintendencias = $request->all(['superintendencia']);
            $tipodeunidade = $request->all(['tipodeunidade']);
            $ciclo = $request->all(['ciclo']);

//#########################################################################################################
//            para ativar a fila no console

//            php artisan queue:work --queue= concluirInspecao

            $job = (new JobConclui_Inspecao ($superintendencias, $tipodeunidade, $ciclo))
                ->onQueue('concluirInspecao')->delay($dtnow->addMinutes(1));
            dispatch($job);

            \Session::flash('mensagem', ['msg' => 'Job Job Conclui_Inspecao  aguardando processamento.'
                , 'class' => 'blue white-text']);
            return redirect()->back();

//   O valor de 134217728 bytes é equivalente a 128M

//   Erro de Memória.Isso ocorre porque no arquivo php.ini o parâmetro memory_limit está configurado para 128M. E para consertar o problema é só editar o arquivo e alterar para 256M.
//#########################################################################################################

            ini_set('memory_limit', '512M');
            ini_set('max_input_time', 350);
            ini_set('max_execution_time', 350);


//            foreach ($superintendencias as $res) { // request é Array de indice para Superintendências
//                foreach ($res as $superintendencia) { // percorre o Array de objeto Superintendências
//                    // testa se o primeiro parâmetro é para todas superintendecia onde SE == 1
//                    // Inicio do teste para todas superintendencias
//                    if ($superintendencia == 1) {
//                        // se verdadeiro se SE == 1 seleciona todas superintendência cujo a SE > 1
////                      Início Todas Inspeções da Superintendencia
//                        $inspecoes = DB::table( 'unidades' )
//                            ->join('inspecoes', 'unidades.id', '=', 'inspecoes.unidade_id')
//                            ->select('inspecoes.*'   )
//                            ->where([['unidades.se', '=', $superintendencia ]])
//                            ->where([['inspecoes.ciclo', '=', $ciclo ]])
//                            ->where([['inspecoes.status', '=', 'Em Inspeção' ]])
//                            ->where([['unidades.tipoUnidade_id', '=', $tipodeunidade ]])
//                            ->get();
////                      Início Todas Inspeções da Superintendencia
//                        foreach ($inspecoes as $inspecao){
////                          inicio percorrida das inspeções
//                            $totalPontos = 0;
//                            $totalitensavaliados = 0;
//                            $valor_ref_itens_inspecionados = 0;
//                            $totalpontosnaoconforme =0;
//                            $totalitensnaoconforme = 0;
//                            $classificacao = null;
//                            $pontuacaoFinal=0;
//
//                            $itensdeinspecao = DB::table('itensdeinspecoes')
//                                ->where([['inspecao_id', '=', $inspecao->id]])
//                                ->where([['situacao', '=', 'Inspecionado']])
//                                ->orderBy('testeVerificacao_id' , 'asc')
//                                ->get();
//
////                            dd($itensdeinspecao);
//
//                            if (!empty($itensdeinspecao)) $totalPontos =  $itensdeinspecao->sum('pontuado');
//
////                      Inicio itens das inspeções  == Registro
//                            foreach ($itensdeinspecao as $registro){
//                                $consequencias = $registro->consequencias;
//                                $eventosSistema = "Corroborado remotamente por Websgi em ".date('d/m/Y H:m:s', strtotime($dtnow))
//                                    ."\n"
//                                    .$registro->eventosSistema;
//                                $situacao = null;
//
//                                if ($registro->avaliacao == 'Não Conforme') {
//                                    $testesdeverificacao = DB::table('testesdeverificacao')
//                                        ->select('testesdeverificacao.*')
//                                        ->Where([['id', '=', $registro->testeVerificacao_id]])
//                                        ->first();
//                                    $totalitensnaoconforme++;
//                                    $totalpontosnaoconforme = $totalpontosnaoconforme + $registro->pontuado;
//                                    $valor_ref_itens_inspecionados = $valor_ref_itens_inspecionados + $testesdeverificacao->maximodepontos;
//                                    $situacao = 'Não Respondido';
//
//                                }
//                                else{
//                                    $consequencias = null;
//                                    $situacao = 'Concluido' ;
//                                }
//
////                           GRAVA ITEM DA INSPEÇÃO
//                                DB::table('itensdeinspecoes')
//                                    ->where([['id', '=', $registro->id]])
//                                    ->where([['inspecao_id', '=', $inspecao->id]])
//                                    ->update([
//                                        'situacao' => $situacao,
//                                        'eventosSistema' => $eventosSistema,
//                                        'consequencias' => $consequencias
//                                    ]);
////                           GRAVA ITEM DA INSPEÇÃO
//
//                            }
////                      Fim itens da inspeção  == Registro
//
//#################   CALCULA TNC
//                            if ($valor_ref_itens_inspecionados == 0) {
//                                $tnc = 0;
//                            }
//                            else{
//                                $tnc = ( $totalpontosnaoconforme / $valor_ref_itens_inspecionados)*100;
//                            }
//#################   CALCULA TNC
//
//#################   CLASSIFICA A  INSPEÇÃO
//                            if($tnc > 50){
//                                $classificacao = 'Controle ineficaz';
//                                $status = 'Em Inspeção';
//                            }
//                            elseif ($tnc > 20 ){
//                                $classificacao = 'Controle pouco eficaz';
//                                $status = 'Em Inspeção';
//
//                            }
//                            elseif ($tnc > 10 ){
//                                $classificacao = 'Controle de eficácia mediana';
//                                $status = 'Corroborado';
//                                $pontuacaoFinal= $totalPontos;
//                            }
//                            elseif ($tnc > 5 ){
//                                $classificacao = 'Controle eficaz';
//                                $status = 'Corroborado';
//                                $pontuacaoFinal = $totalPontos;
//                            }
//                            elseif ($tnc <= 5 ){
//                                $classificacao = 'Controle plenamente eficaz';
//                                $status = 'Corroborado';
//                                $pontuacaoFinal = $totalPontos;
//                            }
//#################   CLASSIFICA A  INSPEÇÃO
//
//############  GRAVA A INSPEÇÃO
//                            DB::table('inspecoes')
//                                ->where([['id', '=', $inspecao->id]])
//                                ->update([
//                                    'totalPontos' => $totalPontos,
//                                    'totalitensavaliados' => $totalitensavaliados,
//                                    'totalitensnaoconforme' => $totalitensnaoconforme,
//                                    'totalpontosnaoconforme' => $totalpontosnaoconforme,
//                                    'valor_ref_itens_inspecionados' => $valor_ref_itens_inspecionados,
//                                    'tnc' => $tnc,
//                                    'status' => $status,
//                                    'pontuacaoFinal' =>  $pontuacaoFinal,
//                                    'classificacao' => $classificacao
//                                ]);
//
//############ STATUS PARA CONTINUAÇÃO DE AVALIAÇÃO PRESENCIAL
//                            if  ($tnc > 20){
//                                DB::table('itensdeinspecoes')
//                                    ->where([['inspecao_id', '=', $inspecao->id]])
//                                    ->where([['avaliacao', '=', 'Não Conforme']])
//                                    ->update([
//                                        'situacao' =>  'Em Inspeção'
//                                    ]);
//                            }
//############ STATUS PARA CONTINUAÇÃO DE AVALIAÇÃO PRESENCIAL
//
//############ STATUS PARA ENVIO DA INSPEÇÃO PARA O SNCI
//                            if  (($tnc < 20) && ($totalitensnaoconforme>1)){
//                                DB::table('itensdeinspecoes')
//                                    ->where([['inspecao_id', '=', $inspecao->id]])
//                                    ->where([['avaliacao', '=', 'Não Conforme']])
//                                    ->update([
//                                        'situacao' =>  'Não Respondido'
//                                    ]);
//                            }
//############ STATUS PARA ENVIO DA INSPEÇÃO PARA O SNCI
//                        }
////                      Fim percorrida das inspeções
//                    }
//
//                // Fim do teste para todas superintendencias se superintendencia = 1
//
//                    // inicio do testee para uma superintendencias
//                    else {
//
////                      Início Todas Inspeções da Superintendencia
//                        $inspecoes = DB::table( 'unidades' )
//                            ->join('inspecoes', 'unidades.id', '=', 'inspecoes.unidade_id')
//                            ->select('inspecoes.*'   )
//                            ->where([['unidades.se', '=', $superintendencia ]])
//                            ->where([['inspecoes.ciclo', '=', $ciclo ]])
//                            ->where([['inspecoes.status', '=', 'Em Inspeção' ]])
//                            ->where([['unidades.tipoUnidade_id', '=', $tipodeunidade ]])
//                        ->get();
////                      Início Todas Inspeções da Superintendencia
//                        foreach ($inspecoes as $inspecao){
////                          inicio percorrida das inspeções
//                            $totalPontos = 0;
//                            $totalitensavaliados = 0;
//                            $valor_ref_itens_inspecionados = 0;
//                            $totalpontosnaoconforme =0;
//                            $totalitensnaoconforme = 0;
//                            $classificacao = null;
//                            $pontuacaoFinal=0;
//
//                            $itensdeinspecao = DB::table('itensdeinspecoes')
//                                ->where([['inspecao_id', '=', $inspecao->id]])
//                                ->where([['situacao', '=', 'Inspecionado']])
//                                ->orderBy('testeVerificacao_id' , 'asc')
//                            ->get();
//
////                            dd($itensdeinspecao);
//
//                            if (!empty($itensdeinspecao)) $totalPontos =  $itensdeinspecao->sum('pontuado');
//
////                      Inicio itens das inspeções  == Registro
//                            foreach ($itensdeinspecao as $registro){
//                                $consequencias = $registro->consequencias;
//                                $eventosSistema = "Corroborado remotamente por Websgi em ".date('d/m/Y H:m:s', strtotime($dtnow))
//                                    ."\n"
//                                    .$registro->eventosSistema;
//                                $situacao = null;
//
//                                if ($registro->avaliacao == 'Não Conforme') {
//                                    $testesdeverificacao = DB::table('testesdeverificacao')
//                                        ->select('testesdeverificacao.*')
//                                        ->Where([['id', '=', $registro->testeVerificacao_id]])
//                                        ->first();
//                                    $totalitensnaoconforme++;
//                                    $totalpontosnaoconforme = $totalpontosnaoconforme + $registro->pontuado;
//                                    $valor_ref_itens_inspecionados = $valor_ref_itens_inspecionados + $testesdeverificacao->maximodepontos;
//                                    $situacao = 'Não Respondido';
//
//                                }
//                                else{
//                                    $consequencias = null;
//                                    $situacao = 'Concluido' ;
//                                }
//
////                           GRAVA ITEM DA INSPEÇÃO
//                                DB::table('itensdeinspecoes')
//                                    ->where([['id', '=', $registro->id]])
//                                    ->where([['inspecao_id', '=', $inspecao->id]])
//                                    ->update([
//                                        'situacao' => $situacao,
//                                        'eventosSistema' => $eventosSistema,
//                                        'consequencias' => $consequencias
//                                    ]);
////                           GRAVA ITEM DA INSPEÇÃO
//
//                            }
////                      Fim itens da inspeção  == Registro
//
//#################   CALCULA TNC
//                            if ($valor_ref_itens_inspecionados == 0) {
//                                $tnc = 0;
//                            }
//                            else{
//                                $tnc = ( $totalpontosnaoconforme / $valor_ref_itens_inspecionados)*100;
//                            }
//#################   CALCULA TNC
//
//#################   CLASSIFICA A  INSPEÇÃO
//                            if($tnc > 50){
//                                $classificacao = 'Controle ineficaz';
//                                $status = 'Em Inspeção';
//                            }
//                            elseif ($tnc > 20 ){
//                                $classificacao = 'Controle pouco eficaz';
//                                $status = 'Em Inspeção';
//
//                            }
//                            elseif ($tnc > 10 ){
//                                $classificacao = 'Controle de eficácia mediana';
//                                $status = 'Corroborado';
//                                $pontuacaoFinal= $totalPontos;
//                            }
//                            elseif ($tnc > 5 ){
//                                $classificacao = 'Controle eficaz';
//                                $status = 'Corroborado';
//                                $pontuacaoFinal = $totalPontos;
//                            }
//                            elseif ($tnc <= 5 ){
//                                $classificacao = 'Controle plenamente eficaz';
//                                $status = 'Corroborado';
//                                $pontuacaoFinal = $totalPontos;
//                            }
//#################   CLASSIFICA A  INSPEÇÃO
//
//############  GRAVA A INSPEÇÃO
//                            DB::table('inspecoes')
//                                ->where([['id', '=', $inspecao->id]])
//                                ->update([
//                                    'totalPontos' => $totalPontos,
//                                    'totalitensavaliados' => $totalitensavaliados,
//                                    'totalitensnaoconforme' => $totalitensnaoconforme,
//                                    'totalpontosnaoconforme' => $totalpontosnaoconforme,
//                                    'valor_ref_itens_inspecionados' => $valor_ref_itens_inspecionados,
//                                    'tnc' => $tnc,
//                                    'status' => $status,
//                                    'pontuacaoFinal' =>  $pontuacaoFinal,
//                                    'classificacao' => $classificacao
//                                ]);
//
//############ STATUS PARA CONTINUAÇÃO DE AVALIAÇÃO PRESENCIAL
//                            if  ($tnc > 20){
//                                DB::table('itensdeinspecoes')
//                                    ->where([['inspecao_id', '=', $inspecao->id]])
//                                    ->where([['avaliacao', '=', 'Não Conforme']])
//                                    ->update([
//                                        'situacao' =>  'Em Inspeção'
//                                    ]);
//                            }
//############ STATUS PARA CONTINUAÇÃO DE AVALIAÇÃO PRESENCIAL
//
//############ STATUS PARA ENVIO DA INSPEÇÃO PARA O SNCI
//                            if  (($tnc < 20) && ($totalitensnaoconforme>1)){
//                                DB::table('itensdeinspecoes')
//                                    ->where([['inspecao_id', '=', $inspecao->id]])
//                                    ->where([['avaliacao', '=', 'Não Conforme']])
//                                    ->update([
//                                        'situacao' =>  'Não Respondido'
//                                    ]);
//                            }
//############ STATUS PARA ENVIO DA INSPEÇÃO PARA O SNCI
//                            }
////                      Fim percorrida das inspeções
//                        }
////                      Fim Todas Inspeções da Superintendencia
//
//                    }
//
//                }

            }
    }

    public function concluir() {
        $status = 'Criado e instalado';
        try {
            $businessUnitUser = DB::table('unidades')
                ->Where([['mcu', '=', auth()->user()->businessUnit]])
                ->select('unidades.*')
                ->first();
        }catch ( \Exception $e){
            return redirect()->route('login');
        }

        if (!empty($businessUnitUser)) {
            $papel_user = DB::table('papel_user')
                ->Where([['user_id', '=', auth()->user()->id]])
                ->Where([['papel_id', '>=', 1]])
                ->select('papel_id')
                ->first();
            switch ($papel_user->papel_id) {
                case 1:
                case 2:
                    {
                        $registros = DB::table('unidades')
                            ->select(
                                'id', 'se', 'seDescricao'
                            )
                            ->where([['se', '>', 1]])
                            ->where([['status_unidadeDesc', '=', $status]])
                            ->groupBy('se')
                            ->orderBy('seDescricao', 'asc')
                            ->get();

                    }
                    break;
            }

            $tiposDeUnidade = DB::table('tiposdeunidade')
                ->where([
                    ['inspecionar', '=', 'sim'],
                    ['tipoInspecao', '=', 'Monitorada'],
                ])
                ->get();

//            dd( 'conclindo 1657 '. $tiposDeUnidade);

            return view('compliance.monitoramento.concluir_insp', compact('registros', 'tiposDeUnidade'));
        } else {
            \Session::flash('mensagem', ['msg' => 'Não foi possivel exibir os itens você provavelmente não é administrador.'
                , 'class' => 'red white-text']);
            return redirect()->route('home');
        }
    }

    public function show(){
        return view('compliance.monitoramento.show');  //
    }
    public function transformDate($value, $format = 'Y-m-d') {
        try {
            return Carbon::instance(
            \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
        }
        catch (\Exception $e) {
            return Carbon::createFromFormat($format, $value);
        }
    }
}
