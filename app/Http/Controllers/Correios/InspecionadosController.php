<?php

namespace App\Http\Controllers\Correios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Correios\Inspecao;
use App\Models\Correios\Itensdeinspecao;
use Auth;
use Carbon\Carbon;
use PDF;

class InspecionadosController extends Controller {

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function createXML($id)
    {
        $inspecao = Inspecao::find($id);
        $situacao = 'AN';
        $dtEncerramento = '00:00:00';
        $businessUnit = DB::table('unidades')
            ->Where([['id', '=',  $inspecao->unidade_id]])
            ->select('unidades.*')
            ->first();
        $vazio = ' ';

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
            ->orderBy('itensdeinspecoes.testeVerificacao_id' , 'asc')
            ->get();

        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>';
        $xml .= '<rootelement>';
        foreach ($registros as $registro)
        {
            switch ($registro->avaliacao)
            {
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
            if ( $registro->itemQuantificado == 'Sim')
            {
                $quantificado = 'Quantificado';
                $falta = number_format($registro->valorFalta, 2, ',', '.');
                $sobra =number_format($registro->valorSobra, 2, ',', '.');
                $risco =number_format($registro->valorRisco, 2, ',', '.');

            }else
            {
                $quantificado = 'Não Quantificado';
                $falta = 0;
                $sobra = 0;
                $risco = 0;
            }

            if ( $registro->reincidencia == 'Sim')
            {
                $codVerificacaoAnterior =  $registro->codVerificacaoAnterior;
                $numeroGrupoReincidente =  $registro->numeroGrupoReincidente;
                $numeroItemReincidente =  $registro->numeroItemReincidente;
            }else
            {
                $codVerificacaoAnterior = ' ';
                $numeroGrupoReincidente =  ' ';
                $numeroItemReincidente =   ' ';

            }



            if (  $inspecao->tipoVerificacao=='Remoto'){
                $modalidade=1;
            }
            else{
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
            $xml .= "\n\t\t".'<RIP_UserName>'.auth()->user()->document.'</RIP_UserName>';
            $xml .= "\n\t\t".'<RIP_Recomendacoes>'.$registro->orientacao.' </RIP_Recomendacoes>';
            $xml .= "\n\t\t".'<RIP_ReincInspecao>'.$codVerificacaoAnterior.'</RIP_ReincInspecao>';
            $xml .= "\n\t\t".'<RIP_ReincGrupo>'.$numeroGrupoReincidente.'</RIP_ReincGrupo>';
            $xml .= "\n\t\t".'<RIP_ReincItem>'.$numeroItemReincidente.'</RIP_ReincItem>';
            $xml .= "\n\t\t".'<INP_HrsPreInspecao>'.$registro->NumHrsPreInsp.'</INP_HrsPreInspecao>';
            $xml .= "\n\t\t".'<INP_DtInicDeslocamento>'.\Carbon\Carbon::parse($registro->datainiPreInspeção)->format( 'd/m/Y' ).'</INP_DtInicDeslocamento>';
            $xml .= "\n\t\t".'<INP_DtFimDeslocamento>'.\Carbon\Carbon::parse($inspecao->updated_at)->format( 'd/m/Y' ).'</INP_DtFimDeslocamento>';
            $xml .= "\n\t\t".'<INP_HrsDeslocamento>'.$registro->NumHrsDesloc.'</INP_HrsDeslocamento>';
            $xml .= "\n\t\t".'<INP_DtInicInspecao>'.\Carbon\Carbon::parse($registro->datainiPreInspeção)->format( 'd/m/Y' ).'</INP_DtInicInspecao>';
            $xml .= "\n\t\t".'<INP_DtFimInspecao>'.\Carbon\Carbon::parse($inspecao->updated_at)->format( 'd/m/Y' ).'</INP_DtFimInspecao>';
            $xml .= "\n\t\t".'<INP_HrsInspecao>'.$registro->NumHrsInsp.'</INP_HrsInspecao>';
            $xml .= "\n\t\t".'<INP_Situacao>'.$situacao.'</INP_Situacao>';
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

        $diretorio = "xml/compliance/inspecao/";
        $arquivo = $inspecao->codigo.'_'.$inspecao->inspetorcoordenador.'.xml';
        $inspecao->status = 'Em Análise';
        $inspecao->eventoInspecao = $inspecao->eventoInspecao . "\r\n".'Em Análise Xml gerado por '.Auth::user()->name." em ".\Carbon\Carbon::parse(Carbon::now())->format( 'd/m/Y' );
        $inspecao->xml = $diretorio.$arquivo;
        $inspecao->save();

        $arquivo = preg_replace('/\\s\\s+/', ' ', $arquivo);
        $fp = fopen($diretorio.$arquivo, 'w+');
        fwrite($fp, $xml);
        fclose($fp);
        return response()->download($diretorio.$arquivo);
//
    }

    public function recusar ($id) {

        $now = Carbon::now();
        $now->format('d-m-Y H:i:s');

        $inspecao = Inspecao::find($id);

        $registros = DB::table('itensdeinspecoes')
            ->select('itensdeinspecoes.*'
            )
            ->where([['inspecao_id', '=', $id]])
            ->where([['situacao', '=', 'Corroborado' ]])
        ->get();

        $count = $registros->count('situacao');
       // dd('Situação - > '. $count);
        if($count >= 1){
            foreach ($registros as $registro){
                $dado = Itensdeinspecao::find($registro->id);
      //        dd($registro);
                $dado->eventosSistema =
                     "Devolvido por: ".Auth::user()->name." em ".$now
                    ."\n\t\t"
                    .$registro->eventosSistema;
                $dado->situacao = 'Inspecionado' ;
              //  dd($dado);
                $dado->save();
            }
            $inspecao->status = 'Em Inspeção';
            $inspecao->save();
        }
       \Session::flash('mensagem',['msg'=>'A Inspeção código '. $inspecao->codigo.' Foi Devolvida!'
            ,'class'=>'blue white-text']);
        return redirect()-> route('compliance.inspecionados');


    }

    public function createPDF($id) {
        // retreive all records from db

        $time='120';
      //  $size='1024M';

       // ini_set('upload_max_filesize', $size);
       // ini_set('post_max_size', $size);

        ini_set('max_input_time', $time);
        ini_set('max_execution_time', $time);


        $inspecao = Inspecao::find($id);

       //var_dump($inspecao );
       // dd($inspecao );

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
        ->orderBy('itensdeinspecoes.testeVerificacao_id' , 'asc')
        ->get();
        //var_dump($registros);
       // dd();
        // share data to view
        //view()->share('papelTrabalho', $inspecao, $registros);

        $pdf = PDF::loadView('compliance.inspecionados.pdfPapelTrabalho',compact('inspecao','registros'));
        // download PDF file with download method
        return $pdf->download('papelTrabalho_'.$inspecao->codigo.'-'.trim($inspecao->descricao).'.pdf');
     //   return $pdf->setPaper('a4')->stream('Papel_Trabalho'),$pdf->download('papelTrabalho.pdf');
    }


    public function papelTrabalho($id)  {

        $inspecao = Inspecao::find($id);

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
        ->orderBy('itensdeinspecoes.testeVerificacao_id' , 'asc')
        ->get();

        $count = $registros->count('situacao');
        return view('compliance.inspecionados.papelTrabalho',compact('inspecao','registros'));
    }

    public function search (Request $request)  {
        // Pendencia gerar querys de uniao para adequar ao perfil 18/12/2020  -Abilio
        if($request->all()['ciclo']==NULL)
        {
            \Session::flash('mensagem',['msg'=>'Ciclo é requerido para esse Filtro!'
            ,'class'=>'red white-text']);
        }
        else
        {
            \Session::flash('mensagem',['msg'=>'Filtro Aplicado!'
            ,'class'=>'orange white-text']);
        }

        $dados = $request->all();

        $tiposDeUnidade = DB::table('tiposdeunidade')
            ->join('gruposdeverificacao',
                'tiposdeunidade.id',
                '=',
                'gruposdeverificacao.tipoUnidade_id')
            ->select('tipoUnidade_id','sigla','tipodescricao')
            ->groupBy('tipodescricao')
            ->get();

        if ($request->all()['codigo'] >1)
        {
            $registros = DB::table('inspecoes')
                ->where([['codigo', 'LIKE', '%' . $request->all()['codigo'] .'%' ]])
                ->paginate(15);
            return view('compliance.inspecionados.index',compact('registros', 'tiposDeUnidade'));
        }

        if (!empty($request->all()['search']))
        {
            $registros = DB::table('inspecoes')
                ->Where([['ciclo', '=', $dados['ciclo']]])
                ->where([['descricao', 'LIKE', '%' . $request->all()['search'] .'%' ]])
                ->paginate(15);
            return view('compliance.inspecionados.index',compact('registros', 'tiposdeunidade'));
        }

        if ( (!empty($request->all()['tipoUnidade_id'])) && (!empty($request->all()['tipoVerificacao']))  && (!empty($request->all()['inspetor']))  )
        {
            $registros = DB::table('inspecoes')
                ->Where([['ciclo', '=', $dados['ciclo']]])
                ->Where([['tipoUnidade_id', '=', $dados['tipoUnidade_id']]])
                ->Where([['tipoVerificacao', '=', $dados['tipoVerificacao']]])
                ->Where([['inspetorcoordenador', '=', $dados['inspetor']]])
                ->Where([['inspetorcolaborador', '=', $dados['inspetor']]])
            ->paginate(15);
            return view('compliance.inspecionados.index',compact('registros', 'tiposdeunidade'));
        }

        if ((!empty($request->all()['tipoVerificacao']))  && (!empty($request->all()['inspetor']))  )
        {
            $registros = DB::table('inspecoes')
                ->Where([['ciclo', '=', $dados['ciclo']]])
                //->Where([['tipoUnidade_id', '=', $dados['tipoUnidade_id']]])
                ->Where([['tipoVerificacao', '=', $dados['tipoVerificacao']]])
                ->Where([['inspetorcoordenador', '=', $dados['inspetor']]])
                ->Where([['inspetorcolaborador', '=', $dados['inspetor']]])
            ->paginate(15);
            return view('compliance.inspecionados.index',compact('registros', 'tiposdeunidade'));
        }

        if ((!empty($request->all()['tipoUnidade_id']))  && (!empty($request->all()['inspetor']))  )
        {
            $registros = DB::table('inspecoes')
                ->Where([['ciclo', '=', $dados['ciclo']]])
                ->Where([['tipoUnidade_id', '=', $dados['tipoUnidade_id']]])
               // ->Where([['tipoVerificacao', '=', $dados['tipoVerificacao']]])
                ->Where([['inspetorcoordenador', '=', $dados['inspetor']]])
                ->Where([['inspetorcolaborador', '=', $dados['inspetor']]])
            ->paginate(15);
            return view('compliance.inspecionados.index',compact('registros', 'tiposdeunidade'));
        }
        if (!empty($request->all()['inspetor']))
        {
            $registros = DB::table('inspecoes')
                ->Where([['ciclo', '=', $dados['ciclo']]])
                ->Where([['inspetorcoordenador', '=', $dados['inspetor']]])
                ->Where([['inspetorcolaborador', '=', $dados['inspetor']]])
            ->paginate(15);
            return view('compliance.inspecionados.index',compact('registros', 'tiposdeunidade'));
        }

        if (!empty($request->all()['tipoUnidade_id']))
        {
            $registros = DB::table('inspecoes')
                ->Where([['ciclo', '=', $dados['ciclo']]])
                ->Where([['tipoUnidade_id', '=', $dados['tipoUnidade_id']]])
                // ->Where([['tipoVerificacao', '=', $dados['tipoVerificacao']]])
               // ->Where([['inspetorcoordenador', '=', $dados['inspetor']]])
              //  ->Where([['inspetorcolaborador', '=', $dados['inspetor']]])
            ->paginate(15);
            return view('compliance.inspecionados.index',compact('registros', 'tiposdeunidade'));
        }
        if (!empty($request->all()['tipoVerificacao']))
        {
            $registros = DB::table('inspecoes')
                ->Where([['ciclo', '=', $dados['ciclo']]])
               // ->Where([['tipoUnidade_id', '=', $dados['tipoUnidade_id']]])
                 ->Where([['tipoVerificacao', '=', $dados['tipoVerificacao']]])
                // ->Where([['inspetorcoordenador', '=', $dados['inspetor']]])
                //  ->Where([['inspetorcolaborador', '=', $dados['inspetor']]])
            ->paginate(15);
            return view('compliance.inspecionados.index',compact('registros', 'tiposdeunidade'));
        }
        $registros = DB::table('inspecoes')
            ->Where([['ciclo', '=', $dados['ciclo']]])
        ->paginate(15);
        // Pendencia gerar querys de uniao para adequar ao perfil 18/12/2020  -Abilio

        return view('compliance.inspecionados.index',compact('registros', 'tiposdeunidade'));
    }

    public function index()
    {

        $businessUnitUser = DB::table('unidades')
            ->Where([['mcu', '=', auth()->user()->businessUnit]])
            ->select('unidades.*')
            ->first();
        if(!empty( $businessUnitUser ))
        {
            $tiposDeUnidade = DB::table('tiposdeunidade')
                ->join('gruposdeverificacao',
                    'tiposdeunidade.id',
                    '=',
                    'gruposdeverificacao.tipoUnidade_id')
                ->select('tipoUnidade_id','sigla','tipodescricao')
                ->groupBy('tipodescricao')
                ->get();



            $papel_user = DB::table('papel_user')
                ->Where([['user_id', '=', auth()->user()->id]])
                ->Where([['papel_id', '>=', 1]])
                ->select('papel_id')
                ->first();
            switch ($papel_user->papel_id)
            {
                case 1:
                case 2:
                    {
                        $inspetores = DB::table('papel_user')
                            ->join('users', 'users.id',  '=',   'user_id')
                            ->select('users.*','papel_user.*')
                            // ->Where([['se', '=', $businessUnitUser->se]])
                            // ->Where([['user_id', '=', auth()->user()->id]])
                            ->Where([['papel_id', '=', 6]])
                            ->get();
                        $registros = DB::table('unidades')
                            ->join('inspecoes', 'unidades.id',  '=',   'unidade_id')
                            ->select('inspecoes.*','unidades.se','unidades.seDescricao')
                            ->where([['status', '=', 'Em Inspeção']])
                            ->orderBy('codigo' , 'asc')
                            ->paginate(10);
                    }
                    break;
                case 3:
                    {
                        $inspetores = DB::table('papel_user')
                            ->join('users', 'users.id',  '=',   'user_id')
                            ->select('users.*','papel_user.*')
                            ->Where([['se', '=', $businessUnitUser->se]])
                            //  ->Where([['user_id', '=', auth()->user()->id]])
                            ->Where([['papel_id', '=', 6]])
                            ->get();
                        $registros = DB::table('unidades')
                            ->join('inspecoes', 'unidades.id',  '=',   'unidade_id')
                            ->select('inspecoes.*','unidades.se','unidades.seDescricao')
                            ->where([['status', '=', 'Em Inspeção']])
                            ->Where([['unidades.se', '=', $businessUnitUser->se]])
                            ->orderBy('codigo' , 'asc')
                            ->paginate(10);
                        \Session::flash('mensagem',['msg'=>'Listando Inspeções da '.$businessUnitUser->seDescricao
                            ,'class'=>'orange white-text']);
                    }
                    break;
                case 4:
                    {
                        $inspetores = DB::table('papel_user')
                            ->join('users', 'users.id',  '=',   'user_id')
                            ->select('users.*','papel_user.*')
                            ->Where([['se', '=', $businessUnitUser->se]])
                            //  ->Where([['user_id', '=', auth()->user()->id]])
                            ->Where([['papel_id', '=', 6]])
                            ->get();
                        $registros = DB::table('unidades')
                            ->join('inspecoes', 'unidades.id',  '=',   'unidade_id')
                            ->select('inspecoes.*','unidades.se','unidades.seDescricao')
                            ->where([['status', '=', 'Inspecionado']])
                            ->Where([['unidades.se', '=', $businessUnitUser->se]])
                            ->orderBy('codigo' , 'asc')
                            ->paginate(10);
                        \Session::flash('mensagem',['msg'=>'Listando Inspeções da '.$businessUnitUser->seDescricao
                            ,'class'=>'orange white-text']);
                    }
                    break;
                case 5:
                    {
                        \Session::flash('mensagem',['msg'=>'Não autorizado.'
                            ,'class'=>'red white-text']);
                    }
                    break;
                case 6:
                    {
                        $inspetores = DB::table('papel_user')
                            ->join('users', 'users.id',  '=',   'user_id')
                            ->select('users.*','papel_user.*')
                            ->Where([['se', '=', $businessUnitUser->se]])
                            ->Where([['user_id', '=', auth()->user()->id]])
                            ->Where([['papel_id', '=', 6]])
                            ->get();

                        $first = DB::table('unidades')
                            ->join('inspecoes', 'unidades.id',  '=',   'unidade_id')
                            ->select('inspecoes.*','unidades.se','unidades.seDescricao')
                            ->where([['status', '=', 'Inspecionado']])
                            ->where([['inspetorcoordenador', '=', auth()->user()->document]]);
                        $registros = DB::table('unidades')
                            ->join('inspecoes', 'unidades.id',  '=',   'unidade_id')
                            ->select('inspecoes.*','unidades.se','unidades.seDescricao')
                            ->where([['status', '=', 'Inspecionado']])
                            ->Where([['inspetorcolaborador', '=', auth()->user()->document]])
                            ->union($first)
                            ->orderBy('codigo' , 'asc')
                        ->paginate(10);

                    }
                    break;
                default:  return redirect()->route('home');
            }
//            return view('compliance.verificacoes.index',compact('registros','tiposDeUnidade', 'inspetores'));
            return view('compliance.inspecionados.index',compact('registros','tiposDeUnidade', 'inspetores'));
        }
        else
        {
            \Session::flash('mensagem',['msg'=> auth()->user()->name.', você não está associado a uma Unidade. Contate um administrador.'
                ,'class'=>'red white-text']);
            return redirect()->route('home');
        }
    }
}
