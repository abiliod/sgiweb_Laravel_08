<?php

namespace App\Http\Controllers\Correios;

use App\Http\Controllers\Controller;
//use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Correios\Inspecao;
use App\Models\Correios\Itensdeinspecao;
use App\Models\Correios\ModelsDto\LancamentosSRO;
use Carbon\Carbon;
use Carbon\CarbonPeriod;


class InspecaoController extends Controller {

    public function exportsro($codigo) {
        $exportLancamentosSROs  =  DB::table('lancamentossro')
            ->select('objeto')
            ->where('codigo', '=', $codigo)
            ->where('numeroGrupoVerificacao', '=', 277)
            ->where('numeroDoTeste', '=', 3)
            ->where('estado', '=',  'Pendente')
            ->get();

        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>';
        $xml .= '<rootelement>';
        foreach ($exportLancamentosSROs as $exportLancamentosSRO)
        {
            $xml .= "\n\t".'<Dados>';
            $xml .= "\n\t\t".'<Objeto>'.$exportLancamentosSRO->objeto.'</Objeto>';
            $xml .= "\n\t".'</Dados>';
        }
        $xml .= "\n".'</rootelement>';
        $diretorio = "xml/compliance/inspecao/";
        $arquivo = $codigo.'_AmostraNaoEntreguePrimeiraTentativa.xml';
        $arquivo = preg_replace('/\\s\\s+/', ' ', $arquivo);
        $fp = fopen($diretorio.$arquivo, 'w+');
        fwrite($fp, $xml);
        fclose($fp);
        return response()->download($diretorio.$arquivo);
    }

    public function deletefiles($img) {
        $pos = strpos($img, '&');
        $id = substr($img,$pos+1);
        $inspecao = DB::table('itensdeinspecoes')
        ->join('inspecoes', 'itensdeinspecoes.inspecao_id', '=', 'inspecoes.id')
        ->where([['itensdeinspecoes.id', '=', $id ]])
        ->first();
        $diretorio = "img/compliance/inspecao/".$inspecao->codigo."/".$id."/";
        $imagempath = substr($img,0,$pos);
        $imagempath= str_replace('-', '/', $imagempath);

        $pos = strpos( $imagempath, $diretorio );
        if ($pos === false) {
            \Session::flash('mensagem',['msg'=>'Imagem não foi Excluida do Item.'
            ,'class'=>'red white-text']);
        } else {
            unlink($imagempath);
            \Session::flash('mensagem',['msg'=>'A Imagem foi Excluida do Item.'
            ,'class'=>'green white-text']);
        }
        return redirect()-> route('compliance.inspecao.editar',$id);
    }

    public function update(Request $request, $id)
    {
        $now = Carbon::now();
        $now->format('d-m-Y H:i:s');

        $inspecao = DB::table('itensdeinspecoes')
        ->join('inspecoes', 'itensdeinspecoes.inspecao_id', '=', 'inspecoes.id')
        ->where([['itensdeinspecoes.id', '=', $id ]])
        ->first();
        $diretorio = "img/compliance/inspecao/".$inspecao->codigo."/".$id.'/';  //id da inspeção e id do item inspecionado
        $dados = $request->all();
        $registro = Itensdeinspecao::find($id);
        if($dados['situacao'] =="cancel")
        {
           return redirect()-> route('compliance.inspecao',$registro->inspecao_id);
        }

        $pontuadoInspetor = 0;
//        if(($dados['situacao']=="Em Inspeção")||($dados['situacao']=="Concluido"))
        if($dados['situacao']=="Em Inspeção"){
            $registro->eventosSistema =
                "\nInspecionado por: ".Auth::user()->name." em ".$now
                ."\nSituação: ".trim($dados['situacao'])
                ."\nAvaliação: ".trim($dados['avaliacao'])
                ."\nOportunidade de Aprimoramento: ".trim($dados['oportunidadeAprimoramento'])
                ."\n"
                ."\n #######################    Registro Anterior   #########################"
                ."\n"
                .$registro->eventosSistema;
            $registro->situacao =  'Inspecionado';
        }
        if($dados['situacao']=="Inspecionado"){
            $registro->eventosSistema =
                "\nCorroborado por: ".Auth::user()->name." em ".$now
                ."\nSituação: ".trim($dados['situacao'])
                ."\nAvaliação: ".trim($dados['avaliacao'])
                ."\nOportunidade de Aprimoramento: ".trim($dados['oportunidadeAprimoramento'])
                ."\n"
                ."\n #######################    Registro Anterior   #########################"
                ."\n"
                .$registro->eventosSistema;
            $registro->situacao =  'Corroborado';
        }



        if($dados['avaliacao'] =="Conforme") {
            $registro->avaliacao = trim($dados['avaliacao']);
            $registro->oportunidadeAprimoramento  = trim($dados['oportunidadeAprimoramento']);
            $registro->norma = trim($dados['norma']);
            $registro->itemQuantificado ='Não';
            $registro->reincidencia ='Não';
            $registro->evidencia =null;
            $registro->consequencias = null;
            $registro->valorFalta ='0.00';
            $registro->valorSobra ='0.00';
            $registro->valorRisco ='0.00';
            $registro->codVerificacaoAnterior = null;
            $registro->numeroGrupoReincidente = null;
            $registro->numeroItemReincidente = null;
            $registro->orientacao = null;
            $registro->pontuadoInspetor = $pontuadoInspetor;


        }
        elseif($dados['avaliacao'] =="Não Conforme") {
            $valorFalta = $dados['valorFalta'];
            $valorSobra = $dados['valorSobra'];
            $valorRisco = $dados['valorRisco'];

            $quebra = DB::table('relevancias')
                ->select('valor_final')
                ->where('fator_multiplicador', '=', 1)
                ->first();
            $quebracaixa = $quebra->valor_final * 0.1;

            $testesdeverificacao = DB::table('testesdeverificacao')
                ->select('totalPontos')
                ->where('id', '=', $registro->testeVerificacao_id)
            ->first();


            if( $valorFalta > $quebracaixa){
                $fm = DB::table('relevancias')
                    ->select('fator_multiplicador', 'valor_final', 'valor_inicio')
                    ->where('valor_inicio', '<=', $valorFalta)
                    ->orderBy('valor_final', 'desc')
                    ->first();
                $pontuadoInspetor = $testesdeverificacao->totalPontos * $fm->fator_multiplicador;
            }
            else{
                $pontuadoInspetor = $testesdeverificacao->totalPontos * 1;
            }

            $registro->avaliacao = trim($dados['avaliacao']);
            $registro->oportunidadeAprimoramento  = trim($dados['oportunidadeAprimoramento']);
            $registro->norma = trim($dados['norma']);
            $registro->evidencia = trim($dados['evidencia']);
            $registro->reincidencia = $dados['reincidencia'];
            $registro->orientacao = trim($dados['orientacao']);
            $registro->consequencias =  trim($dados['consequencias']);
            $registro->itemQuantificado =  trim($dados['itemQuantificado']);
            $registro->itemQuantificado ='Não';
            $registro->valorFalta = $valorFalta;
            $registro->valorSobra = $valorSobra;
            $registro->valorRisco = $valorRisco;
            $registro->pontuadoInspetor = $pontuadoInspetor;

            if(isset($dados['itemQuantificado'])){
               if($dados['itemQuantificado'] =="Sim"){
                    $registro->itemQuantificado  = $dados['itemQuantificado'];
                    $registro->valorFalta = $valorFalta;
                    $registro->valorSobra = $valorSobra;
                    $registro->valorRisco = $valorRisco;
                    if(($dados['valorFalta']=="") && ($dados['valorSobra']=="") &&($dados['valorRisco']=="")) {
                        \Session::flash('mensagem',['msg'=>'Informe ao menos um valor quantificado ausente.'
                        ,'class'=>'red white-text']);
                        return back()->withInput();
                    }
               }
            }


          if($request->hasfile('imagem')) {
                request()->validate([
                    'imagem' => 'required',
                    'imagem.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
                ]);

                if ($image = $request->file('imagem')) {
                    foreach ($image as $files) {
                        $destinationPath = $diretorio; // upload path
                        $profileImage = date('YmdHis') . "." . $files->getClientOriginalExtension();
                        $files->move($destinationPath, $profileImage);
                        $imagens[]['image'] = "$profileImage";
                    }
                }
                $registro->diretorio=$diretorio;
                $registro->imagem = $registro->imagem . json_encode($imagens);
          }

          if(($dados['situacao']=="Inspecionado")
            ||($dados['situacao']=="Corroborado")) {
              $registro->eventosSistema =
                  $dados['situacao']." por: ".Auth::user()->name." em ".$now
                  ."\nSituação ".trim($dados['situacao'])
                  ."\nAvaliação: ".trim($dados['avaliacao'])
                  ."\nOportunidade de Aprimoramento: ".trim($dados['oportunidadeAprimoramento'])
                  ."\nEvidencias: ".trim($dados['evidencia'])
                  ."\nNorma: ".trim($dados['norma'])
                  ."\nConsequencias: ".trim($dados['consequencias'])
                  ."\nValor Falta: ".trim($dados['valorFalta'])
                  ."\nValor Sobra: ".trim($dados['valorSobra'])
                  ."\nValor Risco: ".trim($dados['valorRisco'])
                  ."\nÉ Reincidência: ".trim($dados['reincidencia'])
                  ."\nCódigo da Verificação Anterior: ".trim($dados['codVerificacaoAnterior'])
                  ."\nGrupo Reincidente: ".trim($dados['numeroGrupoReincidente'])
                  ."\nItemReincidente:  ".trim($dados['numeroItemReincidente'])
                  ."\nOrientações: ".trim($dados['orientacao'])
                  ."\n"
                  ."\n #######################    Registro Anterior   #########################"
                  ."\n"
                  .$registro->eventosSistema;
          }

        }

        else if($dados['avaliacao'] =="Não Verificado") {

            $registro->avaliacao = trim($dados['avaliacao']);
            $registro->oportunidadeAprimoramento  = trim($dados['oportunidadeAprimoramento']);
            $registro->consequencias = null;
            $registro->evidencia =null;
            if(isset($dados['norma'])) $registro->norma  = trim($dados['norma']);
            $registro->itemQuantificado ='Não';
            $registro->valorFalta ='0.00';
            $registro->valorSobra ='0.00';
            $registro->valorRisco ='0.00';
            $registro->reincidencia ='Não';
            $registro->codVerificacaoAnterior = null;
            $registro->numeroGrupoReincidente = null;
            $registro->numeroItemReincidente = null;
            $registro->orientacao = null;
            $registro->pontuadoInspetor = $pontuadoInspetor;
        }

        else {  ////Não Executa Tarefa

            $registro->avaliacao = trim($dados['avaliacao']);
            $registro->oportunidadeAprimoramento  = trim($dados['oportunidadeAprimoramento']);
            $registro->consequencias = null;
            $registro->evidencia = null;
            if(isset($dados['norma'])) $registro->norma  = trim($dados['norma']);
            $registro->itemQuantificado ='Não';
            $registro->valorFalta ='0.00';
            $registro->valorSobra ='0.00';
            $registro->valorRisco ='0.00';
            $registro->reincidencia ='Não';
            $registro->pontuadoInspetor = $pontuadoInspetor;
            $registro->codVerificacaoAnterior =null;
            $registro->numeroGrupoReincidente =null;
            $registro->numeroItemReincidente =null;
            $registro->orientacao = null;
        }

//        dd('Corroborando o item parou linha 294 ',$registro);

        $registro->update();

        \Session::flash('mensagem',['msg'=>'Registro inspecionado com sucesso!'
        ,'class'=>'green white-text']);

        if($dados['situacao'] =="Em Inspeção")
        {
           return redirect()-> route('compliance.inspecao.editar',$registro->id);
        }

        return redirect()-> route('compliance.inspecao',$inspecao->inspecao_id);
    }

    public function editsro(Request $request, $id) {
        $dados = $request->all();
         //dd($dados);
        $registro = LancamentosSRO::find($id);

        if(( $request['numeroGrupoVerificacao']==277) && ($request['numeroDoTeste']==2))
        {
            $registro->estado = $dados['estado'];
            $registro->falhaDetectada = $dados['falhaDetectada'];
        }
        elseif (( $dados['numeroGrupoVerificacao']==277) && ($dados['numeroDoTeste']==3))
        {
            $registro->estado = $dados['estado'];
            $registro->falhaDetectada = $dados['falhaDetectada'];
            $registro->enderecoPostagem = $dados['enderecoPostagem'];
            $registro->localBaixa1tentativa = $dados['localBaixa1tentativa'];
        }

        $registro->update();

        $res  =  DB::table('lancamentossro')
            ->where('codigo', '=', $registro->codigo)
            ->where('numeroGrupoVerificacao', '=', $registro->numeroGrupoVerificacao)
            ->where('numeroDoTeste', '=', $registro->numeroDoTeste)
            //->where('estado', '=',  'Pendente')
            ->get();

        $pend = 0;
        $aval = 0;
        foreach ($res as $register) {
            if ($register->estado == 'Pendente') {
                $pend++;
            } else {
                $aval++;
            }
        }
        $mostra = $pend + $aval;

        //dd('pendencia '.$pend, 'mostra'.$mostra, 'avaliados'. $aval);

        $id =  $dados['item'];

        if ($aval < $mostra)
        {
            //return redirect()-> route('compliance.inspecao.editar',$id);
            // dd('pendencia '.$pend,'amostra '. $mostra);
            $res = DB::table('lancamentossro')
                ->where('codigo', '=', $registro->codigo)
                ->where('numeroGrupoVerificacao', '=', $registro->numeroGrupoVerificacao)
                ->where('numeroDoTeste', '=', $registro->numeroDoTeste)
                ->where('estado', '=', 'Pendente')
                ->get();

            $registro = DB::table('itensdeinspecoes')
                ->join('inspecoes', 'itensdeinspecoes.inspecao_id', '=', 'inspecoes.id')
                ->join('unidades', 'itensdeinspecoes.unidade_id', '=', 'unidades.id')
                ->join('testesdeverificacao', 'itensdeinspecoes.testeVerificacao_id', '=', 'testesdeverificacao.id')
                ->join('gruposdeverificacao', 'itensdeinspecoes.grupoVerificacao_id', '=', 'gruposdeverificacao.id')
                ->select('itensdeinspecoes.*','inspecoes.*','unidades.*','testesdeverificacao.*','gruposdeverificacao.*')
                ->where([['itensdeinspecoes.id', '=', $id ]])
                ->first();
           //  dd('avaliados '.$aval,'amostra '. $mostra);
            return view('compliance.inspecao.index_sro', compact
            (
                'registro'
                , 'id'
                , 'res'
            ));
        }
        if ($aval == $mostra)
        {
            return redirect()-> route('compliance.inspecao.editar',$id);
        }
    }

    public function edit($id) {
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
        $total=0.00;
        $count =0;

        $registro = DB::table('itensdeinspecoes')
            ->join('inspecoes', 'itensdeinspecoes.inspecao_id', '=', 'inspecoes.id')
            ->join('unidades', 'itensdeinspecoes.unidade_id', '=', 'unidades.id')
            ->join('testesdeverificacao', 'itensdeinspecoes.testeVerificacao_id', '=', 'testesdeverificacao.id')
            ->join('gruposdeverificacao', 'itensdeinspecoes.grupoVerificacao_id', '=', 'gruposdeverificacao.id')
            ->select('itensdeinspecoes.*','inspecoes.*','unidades.*','testesdeverificacao.*','gruposdeverificacao.*')
            ->where([['itensdeinspecoes.id', '=', $id ]])
            ->first();


// Início teste CFTV - NAO INSPEÇÃO AUTOMÁTICA
        if((($registro->numeroGrupoVerificacao==206) && ($registro->numeroDoTeste==3))
            || (($registro->numeroGrupoVerificacao==335) && ($registro->numeroDoTeste==3))
            || (($registro->numeroGrupoVerificacao==232) && ($registro->numeroDoTeste==6))
            || (($registro->numeroGrupoVerificacao==272) && ($registro->numeroDoTeste==4)))  {

            $cftvs = DB::table('cftvs')
                ->select( 'cftvs.*' )
                ->where([['mcu', '=',  $registro->mcu  ]])
            ->get();


            return view('compliance.inspecao.editar',compact
            (
                'registro'
                , 'id'
                , 'total'
                , 'cftvs'

            ));
        }
// Final teste CFTV


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
            $lancamentossros =  DB::table('lancamentossro')
                ->where('codigo', '=', $registro->codigo)
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
//                                    ->where('descricao_do_evento', '=', 'DESTINATARIO AUSENTE')
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
//                    $periodo = CarbonPeriod::create($dtini, $dtfim);
//                    $dias = $periodo->count() - 1;

                    return view('compliance.inspecao.editar', compact
                    (
                        'registro'
                        , 'id'
                        , 'total'
                        , 'count' // x
                        , 'dtini' // x
                        , 'dtfim' //
                        , 'media'
                        , 'random'
                        , 'amostra'
                        //      , 'item'
                        , 'res'
                        , 'qtd_falhas'
                        , 'percentagem_falhas'
                    ));
                }
                if ($pend <= $mostra) // existem objetos pendentes de avaliação
                {
                    $res = DB::table('lancamentossro')
                        ->where('codigo', '=', $registro->codigo)
                        ->where('numeroGrupoVerificacao', '=', $registro->numeroGrupoVerificacao)
                        ->where('numeroDoTeste', '=', $registro->numeroDoTeste)
                        ->where('estado', '=', 'Pendente')
                        ->get();
                    return view('compliance.inspecao.index_sro', compact
                    (
                        'registro'
                        , 'id'
                        , 'res'
                    ));
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
                if(! $micro_strategys->isEmpty()) {
                    $count = $micro_strategys->count('codigo_do_objeto');
                    $dtini = $micro_strategys->min('data_do_evento');
                    $dtfim = $micro_strategys->max('data_do_evento');
                    $periodo = CarbonPeriod::create($dtini, $dtfim);
                    $dias = $periodo->count() - 1;
                    // $media = intval($count / $dias);
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
                    $res = DB::table('lancamentossro')
                        ->where('codigo', '=', $registro->codigo)
                        ->where('numeroGrupoVerificacao', '=', $registro->numeroGrupoVerificacao)
                        ->where('numeroDoTeste', '=', $registro->numeroDoTeste)
                        ->where('estado', '=', 'Pendente')
                        ->get();
                    return view('compliance.inspecao.index_sro', compact
                    (
                        'registro'
                        , 'id'
                        , 'res'
                    ));
                }
                else // Nãp tem objetos na consulta
                {
                    return view('compliance.inspecao.editar', compact
                    (
                        'registro'
                        , 'id'
                        , 'total' //xx
                        , 'count' //xx
                        , 'dtini'
                        , 'dtfim'
                        , 'media'
                        , 'random'
                        , 'amostra'
                        //  , 'item' //xx
                        , 'res'
                        , 'qtd_falhas'
                        , 'percentagem_falhas'
                    ));
                }
            }
        }
        return view('compliance.inspecao.editar',compact('registro','id','total'));
    }

    public function search (Request $request ) {
        if(($request->all()['gruposdeverificacao']==NULL) && ($request->all()['situacao']==NULL) && ($request->all()['search']==NULL))
        {
            \Session::flash('mensagem',['msg'=>'Para Filtrar ao menos uma opção é necessária.'
            ,'class'=>'red white-text']);
            return redirect()->back();
        }

        if(($request->all()['gruposdeverificacao']!==NULL) && ($request->all()['situacao']!==NULL) && ($request->all()['search']!=NULL)) {
            $registros = DB::table('itensdeinspecoes')
                ->join('inspecoes', 'itensdeinspecoes.inspecao_id', '=', 'inspecoes.id')
                ->join('gruposdeverificacao', 'itensdeinspecoes.grupoVerificacao_id', '=', 'gruposdeverificacao.id')
                ->join('testesdeverificacao', 'itensdeinspecoes.testeVerificacao_id', '=', 'testesdeverificacao.id')
                ->select('itensdeinspecoes.*'
                    ,'gruposdeverificacao.numeroGrupoVerificacao'
                    ,'gruposdeverificacao.nomegrupo'
                    ,'testesdeverificacao.numeroDoTeste'
                    ,'testesdeverificacao.teste'
                )
                ->where([['inspecao_id', '=', $request->all()['id']]])
                ->Where([['itensdeinspecoes.grupoVerificacao_id', '=', $request->all()['gruposdeverificacao']]])
                ->orWhere([['itensdeinspecoes.situacao', '=', $request->all()['situacao']]])
                ->orwhere([['testesdeverificacao.teste', 'LIKE', '%' . $request->all()['search'] .'%' ]])
                ->orderBy('itensdeinspecoes.testeVerificacao_id' , 'asc')
                ->groupBy('itensdeinspecoes.testeVerificacao_id')
            ->paginate(10);
        }


        if(($request->all()['gruposdeverificacao']!==NULL) && ($request->all()['situacao']==NULL) && ($request->all()['search']==NULL)) {
            $registros = DB::table('itensdeinspecoes')
                ->join('inspecoes', 'itensdeinspecoes.inspecao_id', '=', 'inspecoes.id')
                ->join('gruposdeverificacao', 'itensdeinspecoes.grupoVerificacao_id', '=', 'gruposdeverificacao.id')
                ->join('testesdeverificacao', 'itensdeinspecoes.testeVerificacao_id', '=', 'testesdeverificacao.id')
                ->select('itensdeinspecoes.*'
                        ,'gruposdeverificacao.numeroGrupoVerificacao'
                        ,'gruposdeverificacao.nomegrupo'
                        ,'testesdeverificacao.numeroDoTeste'
                        ,'testesdeverificacao.teste'
                )
                ->where([['inspecao_id', '=', $request->all()['id']]])
                ->Where([['itensdeinspecoes.grupoVerificacao_id', '=', $request->all()['gruposdeverificacao']]])
                ->orderBy('itensdeinspecoes.testeVerificacao_id' , 'asc')
                ->groupBy('itensdeinspecoes.testeVerificacao_id')
            ->paginate(10);
            \Session::flash('mensagem',['msg'=>'Filtro Aplicado! Grupo de Verificação'
                ,'class'=>'orange white-text']);
        }

        if(($request->all()['gruposdeverificacao']==NULL) && ($request->all()['situacao']==NULL) && ($request->all()['search']!==NULL)){
            $registros = DB::table('itensdeinspecoes')
                ->join('inspecoes', 'itensdeinspecoes.inspecao_id', '=', 'inspecoes.id')
                ->join('gruposdeverificacao', 'itensdeinspecoes.grupoVerificacao_id', '=', 'gruposdeverificacao.id')
                ->join('testesdeverificacao', 'itensdeinspecoes.testeVerificacao_id', '=', 'testesdeverificacao.id')
                ->select('itensdeinspecoes.*'
                    ,'gruposdeverificacao.numeroGrupoVerificacao'
                    ,'gruposdeverificacao.nomegrupo'
                    ,'testesdeverificacao.numeroDoTeste'
                    ,'testesdeverificacao.teste'
                )
                ->where([['inspecao_id', '=', $request->all()['id']]])
                ->where([['testesdeverificacao.teste', 'LIKE', '%' . $request->all()['search'] .'%' ]])
                ->orderBy('itensdeinspecoes.testeVerificacao_id' , 'asc')
                ->groupBy('itensdeinspecoes.testeVerificacao_id')
                ->paginate(10);
            \Session::flash('mensagem',['msg'=>'Filtro Aplicado! Descrição'
                ,'class'=>'orange white-text']);

        }

        if(($request->all()['gruposdeverificacao']==NULL) && ($request->all()['situacao']!==NULL) && ($request->all()['search']==NULL)) {

            $registros = DB::table('itensdeinspecoes')
            ->join('inspecoes', 'itensdeinspecoes.inspecao_id', '=', 'inspecoes.id')
            ->join('gruposdeverificacao', 'itensdeinspecoes.grupoVerificacao_id', '=', 'gruposdeverificacao.id')
            ->join('testesdeverificacao', 'itensdeinspecoes.testeVerificacao_id', '=', 'testesdeverificacao.id')
            ->select('itensdeinspecoes.*'
                    ,'gruposdeverificacao.numeroGrupoVerificacao'
                    ,'gruposdeverificacao.nomegrupo'
                    ,'testesdeverificacao.numeroDoTeste'
                    ,'testesdeverificacao.teste'
            )
            ->where([['inspecao_id', '=', $request->all()['id']]])
                ->Where([['itensdeinspecoes.situacao', '=', $request->all()['situacao']]])
                ->orderBy('itensdeinspecoes.testeVerificacao_id' , 'asc')
                ->groupBy('itensdeinspecoes.testeVerificacao_id')
           ->paginate(10);

          $count = $registros->count('situacao');
            if($count == 0) {
                \Session::flash('mensagem',['msg'=>'Filtro Aplicado! Situação. Não há registros nessa situação o sistema considerou todos.'
                    ,'class'=>'red white-text']);
                $registros = DB::table('itensdeinspecoes')
                    ->join('inspecoes', 'itensdeinspecoes.inspecao_id', '=', 'inspecoes.id')
                    ->join('gruposdeverificacao', 'itensdeinspecoes.grupoVerificacao_id', '=', 'gruposdeverificacao.id')
                    ->join('testesdeverificacao', 'itensdeinspecoes.testeVerificacao_id', '=', 'testesdeverificacao.id')
                    ->select('itensdeinspecoes.*'
                        ,'gruposdeverificacao.numeroGrupoVerificacao'
                        ,'gruposdeverificacao.nomegrupo'
                        ,'testesdeverificacao.numeroDoTeste'
                        ,'testesdeverificacao.teste'
                    )
                    ->where([['inspecao_id', '=', $request->all()['id']]])
                    ->orderBy('itensdeinspecoes.testeVerificacao_id' , 'asc')
                    ->groupBy('itensdeinspecoes.testeVerificacao_id')
                    ->paginate(10);
            }
            else{
                \Session::flash('mensagem',['msg'=>'Filtro Aplicado! Situação'
                    ,'class'=>'orange white-text']);
            }
        }

        $inspecao = Inspecao::find($request->all()['id']);
        $gruposdeverificacao = DB::table('gruposdeverificacao')
            ->select('gruposdeverificacao.*')
            ->where([['tipoUnidade_id', '=', $inspecao['tipoUnidade_id']]])
            ->where([['tipoVerificacao', '=', $inspecao['tipoVerificacao']]])
        ->get();

        $dado = DB::table('itensdeinspecoes')
            ->join('inspecoes', 'itensdeinspecoes.inspecao_id', '=', 'inspecoes.id')
            ->join('unidades', 'itensdeinspecoes.unidade_id', '=', 'unidades.id')
            ->join('testesdeverificacao', 'itensdeinspecoes.testeVerificacao_id', '=', 'testesdeverificacao.id')
            ->join('gruposdeverificacao', 'itensdeinspecoes.grupoVerificacao_id', '=', 'gruposdeverificacao.id')
            ->select('itensdeinspecoes.*','inspecoes.*','unidades.*','testesdeverificacao.*','gruposdeverificacao.*')
            ->where([['inspecoes.id', '=', $request->all()['id'] ]])
        ->first();
        return view('compliance.inspecao.index',compact('inspecao','registros','gruposdeverificacao','dado'));
    }

    public function index($id)  {
        $inspecao = Inspecao::find($id);
        $countEmInsp = null;
        $countInspecionado = null;
        $countCorroborado = null;

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
            ->where([['situacao', '=', 'Concluido' ]])
            ->orderBy('itensdeinspecoes.testeVerificacao_id' , 'asc')
        ->paginate(10);

        $contasituacao = $registros->count('situacao');
        $countEmInsp = $registros->count('situacao');

        if($contasituacao == 0) {
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
                ->Where([['situacao', '=', 'Inspecionado' ]])
                ->orderBy('itensdeinspecoes.testeVerificacao_id' , 'asc')
            ->paginate(10);
            $contasituacao = $registros->count('situacao');
            $countInspecionado = $registros->count('situacao');
        }

        if($contasituacao == 0) {
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
                ->Where([['situacao', '!=', 'Corroborado' ]])
                ->orderBy('itensdeinspecoes.testeVerificacao_id' , 'asc')
            ->paginate(10);
            $contasituacao = $registros->count('situacao');
            $countCorroborado = $registros->count('situacao');
        }
        if($contasituacao == 0) {
            $totalitensnaoconforme=0;
            $totalpontosnaoconforme=0;
            $itensdeinspecao = DB::table('itensdeinspecoes')
                ->where([['inspecao_id', '=', $inspecao->id]])
                ->where([['situacao', '=', 'Inspecionado']])
                ->orderBy('testeVerificacao_id' , 'asc')
            ->get();
           if (!empty($itensdeinspecao)) $totalPontos =  $itensdeinspecao->sum('pontuadoInspetor');
            $totalitensavaliados =  $itensdeinspecao->count('id');

//                      Inicio itens das inspeções  == Registro
            foreach ($itensdeinspecao as $itemInspecao) {
                if ($itemInspecao->avaliacao == 'Não Conforme') {
                    $testesdeverificacao = DB::table('testesdeverificacao')
                        ->select('testesdeverificacao.*')
                        ->Where([['id', '=', $itemInspecao->testeVerificacao_id]])
                        ->first();
                    $totalitensnaoconforme++;
                    $totalpontosnaoconforme = $totalpontosnaoconforme + $itemInspecao->pontuadoInspetor;
                    $valor_ref_itens_inspecionados = $valor_ref_itens_inspecionados + $testesdeverificacao->maximodepontos;
                }
            }
//                      Fim itens da inspeção  == Registro

#################   CALCULA TNC
            if ($valor_ref_itens_inspecionados == 0) {
                $tnc = 0;
            }
            else{
                $tnc = ( $totalpontosnaoconforme / $valor_ref_itens_inspecionados)*100;
            }
#################   CALCULA TNC

#################   CLASSIFICA A  INSPEÇÃO
            if($tnc > 50){
                $classificacao = 'Controle ineficaz';
            }
            elseif ($tnc > 20 ){
                $classificacao = 'Controle pouco eficaz';
            }
            elseif ($tnc > 10 ){
                $classificacao = 'Controle de eficácia mediana';
            }
            elseif ($tnc > 5 ){
                $classificacao = 'Controle eficaz';
            }
            elseif ($tnc <= 5 ){
                $classificacao = 'Controle plenamente eficaz';
            }
            $status = 'Corroborado';
#################   CLASSIFICA A  INSPEÇÃO
            $inspecoes = Inspecao::find( $id );
            $inspecoes->totalpontosInspetor = $totalpontosnaoconforme;
            $inspecoes->valor_ref_itens_inspecionados = $valor_ref_itens_inspecionados;
            $inspecoes->totalitensavaliados = $totalitensavaliados;
            $inspecoes->totalitensnaoconforme = $totalitensnaoconforme;
            $inspecoes->totalpontosnaoconforme = $totalpontosnaoconforme;
            $inspecoes->pontuacaoFinal = $totalpontosnaoconforme;
            $inspecoes->tnc =  $tnc;
            $inspecoes->classificacao =  $classificacao;
            $inspecoes->status = $status;


            dd('linha 943' , $inspecoes) ;
            $inspecoes->save();
            return redirect()-> route('compliance.verificacoes');
        }

        $gruposdeverificacao = DB::table('gruposdeverificacao')
            ->select('gruposdeverificacao.*')
            ->where([['tipoUnidade_id', '=', $inspecao['tipoUnidade_id']]])
            ->where([['tipoVerificacao', '=', $inspecao['tipoVerificacao']]])
        ->get();

        $dado = DB::table('itensdeinspecoes')
            ->join('inspecoes', 'itensdeinspecoes.inspecao_id', '=', 'inspecoes.id')
            ->join('unidades', 'itensdeinspecoes.unidade_id', '=', 'unidades.id')
            ->join('testesdeverificacao', 'itensdeinspecoes.testeVerificacao_id', '=', 'testesdeverificacao.id')
            ->join('gruposdeverificacao', 'itensdeinspecoes.grupoVerificacao_id', '=', 'gruposdeverificacao.id')
            ->select('itensdeinspecoes.*','inspecoes.*','unidades.*','testesdeverificacao.*','gruposdeverificacao.*')
            ->where([['inspecoes.id', '=', $id ]])
            ->first();
  //      dd($registros);
        return view('compliance.inspecao.index',compact('inspecao','registros'
            ,'gruposdeverificacao','dado','countEmInsp', 'countInspecionado','countCorroborado'));
    }

    public function corroborar($id) {
        $now = Carbon::now();
        $now->format('d-m-Y H:i:s');
        $inspecao = Inspecao::find($id);

        $registros = DB::table('itensdeinspecoes')
            ->select('itensdeinspecoes.*')
            ->where([['inspecao_id', '=', $id]])
            ->where([['situacao', '=', 'Em Inspeção' ]])
        ->get();
        $count = $registros->count('situacao');

         if($count === 0){

             $totalitensnaoconforme=0;
             $totalpontosnaoconforme=0;
             $totalitensavaliados=0;

            foreach ($registros as $registro) {

                if ($registro->avaliacao == 'Não Conforme') {
                    $testesdeverificacao = DB::table('testesdeverificacao')
                        ->select('testesdeverificacao.*')
                        ->Where([['id', '=', $registro->testeVerificacao_id]])
                        ->first();
                    $totalitensnaoconforme++;
                    $totalpontosnaoconforme = $totalpontosnaoconforme + $registro->pontuadoInspetor;
                    $valor_ref_itens_inspecionados = $valor_ref_itens_inspecionados + $testesdeverificacao->maximodepontos;
                }
                $totalitensavaliados++;

                $dado = Itensdeinspecao::find($registro->id);
                $dado->eventosSistema =
                    "Corroborado por: ".Auth::user()->name." em ".$now
                    ."\n"
                    .$registro->eventosSistema;
                $dado->situacao = 'Corroborado' ;
                $dado->save();
            }

#################   CALCULA TNC
             if ($valor_ref_itens_inspecionados == 0) {
                 $tnc = 0;
             }
             else{
                 $tnc = ( $totalpontosnaoconforme / $valor_ref_itens_inspecionados)*100;
             }
#################   CALCULA TNC

#################   CLASSIFICA A  INSPEÇÃO
             if($tnc > 50){
                 $classificacao = 'Controle ineficaz';
             }
             elseif ($tnc > 20 ){
                 $classificacao = 'Controle pouco eficaz';
             }
             elseif ($tnc > 10 ){
                 $classificacao = 'Controle de eficácia mediana';
             }
             elseif ($tnc > 5 ){
                 $classificacao = 'Controle eficaz';
             }
             elseif ($tnc <= 5 ){
                 $classificacao = 'Controle plenamente eficaz';
             }
             $status = 'Corroborado';
#################   CLASSIFICA A  INSPEÇÃO
             $inspecao->totalpontosInspetor = $totalpontosnaoconforme;
             $inspecao->valor_ref_itens_inspecionados = $valor_ref_itens_inspecionados;
             $inspecao->totalitensavaliados = $totalitensavaliados;
             $inspecao->totalitensnaoconforme = $totalitensnaoconforme;
             $inspecao->totalpontosnaoconforme = $totalpontosnaoconforme;
             $inspecao->pontuacaoFinal = $totalpontosnaoconforme;
             $inspecao->tnc =  $tnc;
             $inspecao->classificacao =  $classificacao;
             $inspecao->status = $status;
             $inspecao->eventoInspecao = $inspecao->eventoInspecao . "\r\n".'Inspeção corroborada, concluida por '.Auth::user()->name." em ".\Carbon\Carbon::parse($now)->format( 'd/m/Y' );
//dd('linha 1043' , $inspecao) ;
             $inspecao->save();
             \Session::flash('mensagem',['msg'=>'Inspeção Corroborada. Concluida por: ' .Auth::user()->name." em ".\Carbon\Carbon::parse($now)->format( 'd/m/Y' ),'class'=>'blue white-text']);
             return redirect()-> route('compliance.verificacoes');
        }
         else{
             \Session::flash('mensagem',['msg'=>'Inspeção com pendência de avaliação de testes','class'=>'red white-text']);
             return redirect()-> back();
         }

    }
}
