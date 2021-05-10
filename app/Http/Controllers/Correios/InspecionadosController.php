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
