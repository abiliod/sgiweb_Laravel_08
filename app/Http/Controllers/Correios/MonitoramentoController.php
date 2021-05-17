<?php

namespace App\Http\Controllers\Correios;

use App\Http\Controllers\Controller;
use App\Jobs\AvaliaInspecao;
use App\Jobs\GeraInspecao;
use App\Jobs\JobConclui_Inspecao;
use App\Jobs\JobXml_Inspecao;
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
        }
        else {
            $superintendencias = $request->all(['superintendencia']);
            $tipodeunidade = $request->all(['tipodeunidade']);
            $ciclo = $request->all(['ciclo']);

//#########################################################################################################
//            para ativar a fila no console
//            php artisan queue:work --queue= concluirInspecao

            $job = (new JobConclui_Inspecao ($superintendencias, $tipodeunidade, $ciclo))
                ->onQueue('concluirInspecao')->delay($dtnow->addMinutes(1));
            dispatch($job);

            \Session::flash('mensagem', ['msg' => 'Job Conclui Inspecao  aguardando processamento.'
                , 'class' => 'blue white-text']);
            return redirect()->back();

//   O valor de 134217728 bytes é equivalente a 128M
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
//                            ->where([['inspecoes.tipoVerificacao', '=', 'Monitorada' ]])
//                            ->where([['unidades.tipoUnidade_id', '=', $tipodeunidade ]])
//                            ->get();
////                      Início Todas Inspeções da Superintendencia
//                        foreach ($inspecoes as $inspecao){
//
//
////                          inicio percorrida das inspeções
//                            $totalPontos = 0;
//                            $totalitensavaliados = 0;
//                            $valor_ref_itens_inspecionados = 0;
//                            $totalpontosnaoconforme =0;
//                            $totalitensnaoconforme = 0;
//                            $classificacao = null;
//                            $pontuacaoFinal=0;
//
//
//                            $itensdeinspecao = DB::table('itensdeinspecoes')
//                                ->where([['inspecao_id', '=', $inspecao->id]])
//                                ->where([['situacao', '=', 'Inspecionado']])
//                                ->orderBy('testeVerificacao_id' , 'asc')
//                                ->get();
//
//                            if (!empty($itensdeinspecao)) $totalPontos =  $itensdeinspecao->sum('pontuado');
//                            $totalitensavaliados =  $itensdeinspecao->count('eventosSistema');
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
//
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
//                            elseif(($tnc > 0 ) && ($tnc <= 5 )){
//                                $classificacao = 'Controle plenamente eficaz';
//                                $status = 'Corroborado';
//                                $pontuacaoFinal = $totalPontos;
//                            }
//                            elseif ($tnc == 0 ){
//                                $classificacao = 'Controle plenamente eficaz';
//                                $status = 'Concluida' ;
//                                $pontuacaoFinal = $totalPontos;
//                            }
//
//#################   CLASSIFICA A  INSPEÇÃO
//
////                            dd($inspecao,
////                                ' tottal de $totalPontos ->'.   $totalPontos,
////                                ' tottal de $totalitensavaliados ->'. $totalitensavaliados,
////                                ' tottal de $valor_ref_itens_inspecionados ->'.$valor_ref_itens_inspecionados,
////                                ' tottal de $totalpontosnaoconforme ->'.$totalpontosnaoconforme ,
////                                ' tottal de $totalitensnaoconforme ->'.$totalitensnaoconforme,
////                                ' tottal de $classificacao ->'.$classificacao ,
////                                ' tottal de $pontuacaoFinal ->'.$pontuacaoFinal,
////                                ' tottal de $tnc ->'.$tnc,
////                                ' tottal de $status ->'.$status
////                            );
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
//                                    'inspetorcoordenador' =>  null,
//                                    'inspetorcolaborador' =>  null,
//                                    'classificacao' => $classificacao
//                                ]);
//
//############ STATUS PARA CONTINUAÇÃO DE AVALIAÇÃO PRESENCIAL
//                            if  ($tnc > 20){
//                                DB::table('itensdeinspecoes')
//                                    ->where([['inspecao_id', '=', $inspecao->id]])
////                                    ->where([['avaliacao', '=', 'Não Conforme']])
//                                    ->update([
//                                        'situacao' =>  'Em Inspeção'
//                                    ]);
//                            }
//############ STATUS PARA CONTINUAÇÃO DE AVALIAÇÃO PRESENCIAL
//
//############ STATUS PARA ENVIO DA INSPEÇÃO PARA O SNCI
//                            if  (($tnc < 20) && ($totalitensnaoconforme >=1)){
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
//                    // Fim do teste para todas superintendencias se superintendencia = 1
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
//                            ->where([['inspecoes.tipoVerificacao', '=', 'Monitorada' ]])
//                            ->where([['unidades.tipoUnidade_id', '=', $tipodeunidade ]])
//                            ->get();
////                      Início Todas Inspeções da Superintendencia
//                        foreach ($inspecoes as $inspecao){
//
//
////                          inicio percorrida das inspeções
//                            $totalPontos = 0;
//                            $totalitensavaliados = 0;
//                            $valor_ref_itens_inspecionados = 0;
//                            $totalpontosnaoconforme =0;
//                            $totalitensnaoconforme = 0;
//                            $classificacao = null;
//                            $pontuacaoFinal=0;
//
//
//                            $itensdeinspecao = DB::table('itensdeinspecoes')
//                                ->where([['inspecao_id', '=', $inspecao->id]])
//                                ->where([['situacao', '=', 'Inspecionado']])
//                                ->orderBy('testeVerificacao_id' , 'asc')
//                                ->get();
//
//                            if (!empty($itensdeinspecao)) $totalPontos =  $itensdeinspecao->sum('pontuado');
//                            $totalitensavaliados =  $itensdeinspecao->count('eventosSistema');
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
//
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
//                            elseif(($tnc > 0 ) && ($tnc <= 5 )){
//                                $classificacao = 'Controle plenamente eficaz';
//                                $status = 'Corroborado';
//                                $pontuacaoFinal = $totalPontos;
//                            }
//                            elseif ($tnc == 0 ){
//                                $classificacao = 'Controle plenamente eficaz';
//                                $status = 'Concluida' ;
//                                $pontuacaoFinal = $totalPontos;
//                            }
//
//#################   CLASSIFICA A  INSPEÇÃO
//
////                            dd($inspecao,
////                                ' tottal de $totalPontos ->'.   $totalPontos,
////                                ' tottal de $totalitensavaliados ->'. $totalitensavaliados,
////                                ' tottal de $valor_ref_itens_inspecionados ->'.$valor_ref_itens_inspecionados,
////                                ' tottal de $totalpontosnaoconforme ->'.$totalpontosnaoconforme ,
////                                ' tottal de $totalitensnaoconforme ->'.$totalitensnaoconforme,
////                                ' tottal de $classificacao ->'.$classificacao ,
////                                ' tottal de $pontuacaoFinal ->'.$pontuacaoFinal,
////                                ' tottal de $tnc ->'.$tnc,
////                                ' tottal de $status ->'.$status
////                            );
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
//                                    'inspetorcoordenador' =>  null,
//                                    'inspetorcolaborador' =>  null,
//                                    'classificacao' => $classificacao
//                                ]);
//
//############ STATUS PARA CONTINUAÇÃO DE AVALIAÇÃO PRESENCIAL
//                            if  ($tnc > 20){
//                                DB::table('itensdeinspecoes')
//                                    ->where([['inspecao_id', '=', $inspecao->id]])
////                                    ->where([['avaliacao', '=', 'Não Conforme']])
//                                    ->update([
//                                        'situacao' =>  'Em Inspeção'
//                                    ]);
//                            }
//############ STATUS PARA CONTINUAÇÃO DE AVALIAÇÃO PRESENCIAL
//
//############ STATUS PARA ENVIO DA INSPEÇÃO PARA O SNCI
//                            if  (($tnc < 20) && ($totalitensnaoconforme >=1)){
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
////                      Fim Todas Inspeções da Superintendencia
//
//                }
//
//            }
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
