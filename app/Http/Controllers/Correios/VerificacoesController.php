<?php

namespace App\Http\Controllers\Correios;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use App\Models\Correios\Inspecao;


class VerificacoesController extends Controller
{
    public function destroy($id) {
        $registro = Inspecao::find($id);

        if($registro->status == "Em Inspeção")
        {
            $registro->delete();
            \Session::flash('mensagem',['msg'=>'Registro deletado com sucesso!'
            ,'class'=>'green white-text']);
        }
        else
        {
            \Session::flash('mensagem',['msg'=>'Registro Com Status {{ $registro->status }} Não pode ser deletado!'
            ,'class'=>'red white-text']);
        }

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

                        $first = DB::table('unidades')
                            ->join('inspecoes', 'unidades.id',  '=',   'unidade_id')
                            ->select('inspecoes.*','unidades.se','unidades.seDescricao')
                            ->where([['status', '=', 'Em Inspeção']])
                            ->where([['inspetorcoordenador', '=', auth()->user()->document]]);
                        $registros = DB::table('unidades')
                            ->join('inspecoes', 'unidades.id',  '=',   'unidade_id')
                            ->select('inspecoes.*','unidades.se','unidades.seDescricao')
                            ->where([['status', '=', 'Em Inspeção']])
                            ->Where([['inspetorcolaborador', '=', auth()->user()->document]])
                            ->union($first)
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

                        $first = DB::table('unidades')
                            ->join('inspecoes', 'unidades.id',  '=',   'unidade_id')
                            ->select('inspecoes.*','unidades.se','unidades.seDescricao')
                            ->where([['status', '=', 'Em Inspeção']]);
                        // ->where([['inspetorcoordenador', '=', auth()->user()->document]]);

                        $registros = DB::table('unidades')
                            ->join('inspecoes', 'unidades.id',  '=',   'unidade_id')
                            ->select('inspecoes.*','unidades.se','unidades.seDescricao')
                            ->where([['status', '=', 'Em Inspeção']])
                            //  ->Where([['inspetorcolaborador', '=', auth()->user()->document]])
                            ->union($first)
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
                            ->where([['status', '=', 'Em Inspeção']])
                            ->where([['inspetorcoordenador', '=', auth()->user()->document]]);
                        $registros = DB::table('unidades')
                            ->join('inspecoes', 'unidades.id',  '=',   'unidade_id')
                            ->select('inspecoes.*','unidades.se','unidades.seDescricao')
                            ->where([['status', '=', 'Em Inspeção']])
                            ->Where([['inspetorcolaborador', '=', auth()->user()->document]])
                            ->union($first)
                            ->orderBy('codigo' , 'asc')
                            ->paginate(10);
                    }
                    break;
                default:  return redirect()->route('home');
            }

        }
        return view('compliance.verificacoes.index',compact('registros','tiposDeUnidade', 'inspetores'));
    }

    public function search( Request $request)
    {
        $dados = $request->all();
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
        $businessUnitUser = DB::table('unidades')
            ->Where([['mcu', '=', auth()->user()->businessUnit]])
            ->select('unidades.*')
            ->first();
        switch ($papel_user->papel_id)
        {
            case 1:
            case 2:
                {
                    $inspetores = DB::table('papel_user')
                        ->join('users', 'users.id',  '=',   'user_id')
                        ->select('users.*','papel_user.*')
                     //   ->Where([['se', '=', $businessUnitUser->se]])
                      //  ->Where([['user_id', '=', auth()->user()->id]])
                        ->Where([['papel_id', '=', 6]])
                        ->get();

                    $registros = DB::table('unidades')
                        ->join('inspecoes', 'unidades.id',  '=',   'unidade_id')
                        ->select('inspecoes.*','unidades.se','unidades.seDescricao')
                        ->where([['status', '=', 'Em Inspeção']])
                        ->orderBy('codigo' , 'asc')
                        ->paginate(10);

                    if( (!empty($request->all()['tipoUnidade_id'])) && (!empty($request->all()['tipoVerificacao'])) && (!empty($request->all()['inspetor'])) )
                    {
                        $registros = DB::table('unidades')
                            ->join('inspecoes', 'unidades.id',  '=',   'unidade_id')
                            ->select('inspecoes.*','unidades.se','unidades.seDescricao')
                            ->where([['ciclo', '=', $dados['ciclo']]])
                            ->Where([['status', '=', $dados['status']]])
                            ->Where([['tipoUnidade_id', '=', $dados['tipoUnidade_id']]])
                            ->Where([['tipoVerificacao', '=', $dados['tipoVerificacao']]])
//                            ->Where([['unidades.se', '=', $businessUnitUser->se]])
                            ->Where([['inspetorcoordenador', '=', $dados['inspetor']]])
                            ->Where([['inspetorcolaborador', '=', $dados['inspetor']]])

                            ->paginate(10);
                        \Session::flash('mensagem',['msg'=>'Filtro Aplicado, [Tipo de Unidade, Tipo de Verificação e Inspetor Envolvido].'
                            ,'class'=>'orange white-text']);
                    }
                    if( (!empty($request->all()['tipoUnidade_id'])) && (!empty($request->all()['tipoVerificacao']))  )
                    {
                        $registros = DB::table('unidades')
                            ->join('inspecoes', 'unidades.id',  '=',   'unidade_id')
                            ->select('inspecoes.*','unidades.se','unidades.seDescricao')
                            ->where([['ciclo', '=', $dados['ciclo']]])
                            ->Where([['status', '=', $dados['status']]])
                            ->Where([['tipoUnidade_id', '=', $dados['tipoUnidade_id']]])
                            ->Where([['tipoVerificacao', '=', $dados['tipoVerificacao']]])
//                            ->Where([['unidades.se', '=', $businessUnitUser->se]])
                            ->paginate(10);
                        \Session::flash('mensagem',['msg'=>'Filtro Aplicado, [Tipo de Unidade e Tipo de Verificação ].'
                            ,'class'=>'orange white-text']);
                    }
                    if(!empty($request->all()['tipoUnidade_id']))
                    {
                        $registros = DB::table('unidades')
                            ->join('inspecoes', 'unidades.id',  '=',   'unidade_id')
                            ->select('inspecoes.*','unidades.se','unidades.seDescricao')
                            ->where([['ciclo', '=', $dados['ciclo']]])
                            ->Where([['status', '=', $dados['status']]])
                            ->Where([['tipoUnidade_id', '=', $dados['tipoUnidade_id']]])
//                            ->Where([['unidades.se', '=', $businessUnitUser->se]])
                            ->paginate(10);
                        \Session::flash('mensagem',['msg'=>'Filtro Aplicado, [Tipo de Unidade].'
                            ,'class'=>'orange white-text']);
                    }
                    if(!empty($request->all()['tipoVerificacao']))
                    {
                        $registros = DB::table('unidades')
                            ->join('inspecoes', 'unidades.id',  '=',   'unidade_id')
                            ->select('inspecoes.*','unidades.se','unidades.seDescricao')
                            ->where([['ciclo', '=', $dados['ciclo']]])
                            ->Where([['status', '=', $dados['status']]])
                            ->Where([['tipoVerificacao', '=', $dados['tipoVerificacao']]])
//                            ->Where([['unidades.se', '=', $businessUnitUser->se]])
                            ->paginate(10);
                        \Session::flash('mensagem',['msg'=>'Filtro Aplicado, [Tipo de Inspeção].'
                            ,'class'=>'orange white-text']);
                    }
                    if(!empty($request->all()['search'])){
                        $registros = DB::table('unidades')
                            ->join('inspecoes', 'unidades.id',  '=',   'unidade_id')
                            ->select('inspecoes.*','unidades.se','unidades.seDescricao')
                            ->where([['ciclo', '=', $dados['ciclo']]])
                            ->Where([['status', '=', $dados['status']]])
                            ->where([['inspecoes.descricao', 'LIKE', '%' . $request->all()['search'] .'%' ]])
//                            ->Where([['unidades.se', '=', $businessUnitUser->se]])
                            ->paginate(10);
                        \Session::flash('mensagem',['msg'=>'Filtro Aplicado, [Por Unidade].'
                            ,'class'=>'orange white-text']);
                    }
                    if(!empty($request->all()['inspetor'])){
                        $registros = DB::table('unidades')
                            ->join('inspecoes', 'unidades.id',  '=',   'unidade_id')
                            ->select('inspecoes.*','unidades.se','unidades.seDescricao')
                            ->where([['ciclo', '=', $dados['ciclo']]])
                            ->Where([['status', '=', $dados['status']]])
//                            ->Where([['unidades.se', '=', $businessUnitUser->se]])
                            ->Where([['inspetorcoordenador', '=', $dados['inspetor']]])
                            ->Where([['inspetorcolaborador', '=', $dados['inspetor']]])

                            ->paginate(10);
                    }
                    if(!empty($request->all()['codigo']))
                    {
                        $registros = DB::table('unidades')
                            ->join('inspecoes', 'unidades.id',  '=',   'unidade_id')
                            ->select('inspecoes.*','unidades.se','unidades.seDescricao')
                            ->where([['ciclo', '=', $dados['ciclo']]])
                            ->Where([['status', '=', $dados['status']]])
                            ->where([['codigo', 'LIKE', '%' . $request->all()['codigo'] .'%' ]])
//                            ->Where([['unidades.se', '=', $businessUnitUser->se]])
                            ->paginate(10);
                        \Session::flash('mensagem',['msg'=>'Filtro Aplicado, [Por Número da Inspeção].'
                            ,'class'=>'orange white-text']);
                    }

                    \Session::flash('mensagem',['msg'=>'Listando Inspeções em andamento.'
                        ,'class'=>'orange white-text']);
                }
                break;
            case 3:
                {
                    $inspetores = DB::table('papel_user')
                        ->join('users', 'users.id',  '=',   'user_id')
                        ->select('users.*','papel_user.*')
                        ->Where([['se', '=', $businessUnitUser->se]])
                       // ->Where([['user_id', '=', auth()->user()->id]])
                        ->Where([['papel_id', '=', 6]])
                        ->get();
                    if( (!empty($request->all()['tipoUnidade_id'])) && (!empty($request->all()['tipoVerificacao'])) && (!empty($request->all()['inspetor'])) )
                    {
                        $registros = DB::table('unidades')
                            ->join('inspecoes', 'unidades.id',  '=',   'unidade_id')
                            ->select('inspecoes.*','unidades.se','unidades.seDescricao')
                            ->where([['ciclo', '=', $dados['ciclo']]])
                            ->Where([['status', '=', $dados['status']]])
                            ->Where([['tipoUnidade_id', '=', $dados['tipoUnidade_id']]])
                            ->Where([['tipoVerificacao', '=', $dados['tipoVerificacao']]])
                            ->Where([['unidades.se', '=', $businessUnitUser->se]])
                            ->Where([['inspetorcoordenador', '=', $dados['inspetor']]])
                            ->Where([['inspetorcolaborador', '=', $dados['inspetor']]])

                            ->paginate(10);
                        \Session::flash('mensagem',['msg'=>'Filtro Aplicado, [Tipo de Unidade, Tipo de Verificação e Inspetor Envolvido].'
                            ,'class'=>'orange white-text']);
                    }
                    if( (!empty($request->all()['tipoUnidade_id'])) && (!empty($request->all()['tipoVerificacao']))  )
                    {
                        $registros = DB::table('unidades')
                            ->join('inspecoes', 'unidades.id',  '=',   'unidade_id')
                            ->select('inspecoes.*','unidades.se','unidades.seDescricao')
                            ->where([['ciclo', '=', $dados['ciclo']]])
                            ->Where([['status', '=', $dados['status']]])
                            ->Where([['tipoUnidade_id', '=', $dados['tipoUnidade_id']]])
                            ->Where([['tipoVerificacao', '=', $dados['tipoVerificacao']]])
                            ->Where([['unidades.se', '=', $businessUnitUser->se]])
                            ->paginate(10);
                        \Session::flash('mensagem',['msg'=>'Filtro Aplicado, [Tipo de Unidade e Tipo de Verificação ].'
                            ,'class'=>'orange white-text']);
                    }
                    if(!empty($request->all()['tipoUnidade_id']))
                    {
                        $registros = DB::table('unidades')
                            ->join('inspecoes', 'unidades.id',  '=',   'unidade_id')
                            ->select('inspecoes.*','unidades.se','unidades.seDescricao')
                            ->where([['ciclo', '=', $dados['ciclo']]])
                            ->Where([['status', '=', $dados['status']]])
                            ->Where([['tipoUnidade_id', '=', $dados['tipoUnidade_id']]])
                            ->Where([['unidades.se', '=', $businessUnitUser->se]])
                            ->paginate(10);
                        \Session::flash('mensagem',['msg'=>'Filtro Aplicado, [Tipo de Unidade].'
                            ,'class'=>'orange white-text']);
                    }
                    if(!empty($request->all()['tipoVerificacao']))
                    {
                        $registros = DB::table('unidades')
                            ->join('inspecoes', 'unidades.id',  '=',   'unidade_id')
                            ->select('inspecoes.*','unidades.se','unidades.seDescricao')
                            ->where([['ciclo', '=', $dados['ciclo']]])
                            ->Where([['status', '=', $dados['status']]])
                            ->Where([['tipoVerificacao', '=', $dados['tipoVerificacao']]])
                            ->Where([['unidades.se', '=', $businessUnitUser->se]])
                            ->paginate(10);
                        \Session::flash('mensagem',['msg'=>'Filtro Aplicado, [Tipo de Inspeção].'
                            ,'class'=>'orange white-text']);
                    }
                    if(!empty($request->all()['search'])){
                        $registros = DB::table('unidades')
                            ->join('inspecoes', 'unidades.id',  '=',   'unidade_id')
                            ->select('inspecoes.*','unidades.se','unidades.seDescricao')
                            ->where([['ciclo', '=', $dados['ciclo']]])
                            ->Where([['status', '=', $dados['status']]])
                            ->where([['inspecoes.descricao', 'LIKE', '%' . $request->all()['search'] .'%' ]])
                            ->Where([['unidades.se', '=', $businessUnitUser->se]])
                            ->paginate(10);
                        \Session::flash('mensagem',['msg'=>'Filtro Aplicado, [Por Unidade].'
                            ,'class'=>'orange white-text']);
                    }
                    if(!empty($request->all()['inspetor'])){
                        $registros = DB::table('unidades')
                            ->join('inspecoes', 'unidades.id',  '=',   'unidade_id')
                            ->select('inspecoes.*','unidades.se','unidades.seDescricao')
                            ->where([['ciclo', '=', $dados['ciclo']]])
                            ->Where([['status', '=', $dados['status']]])
                            ->Where([['unidades.se', '=', $businessUnitUser->se]])
                            ->Where([['inspetorcoordenador', '=', $dados['inspetor']]])
                            ->Where([['inspetorcolaborador', '=', $dados['inspetor']]])

                            ->paginate(10);
                    }
                    if(!empty($request->all()['codigo']))
                    {
                        $registros = DB::table('unidades')
                            ->join('inspecoes', 'unidades.id',  '=',   'unidade_id')
                            ->select('inspecoes.*','unidades.se','unidades.seDescricao')
                            ->where([['ciclo', '=', $dados['ciclo']]])
                            ->Where([['status', '=', $dados['status']]])
                            ->where([['codigo', 'LIKE', '%' . $request->all()['codigo'] .'%' ]])
                            ->Where([['unidades.se', '=', $businessUnitUser->se]])
                            ->paginate(10);
                        \Session::flash('mensagem',['msg'=>'Filtro Aplicado, [Por Número da Inspeção].'
                            ,'class'=>'orange white-text']);
                    }

                    \Session::flash('mensagem',['msg'=>'Listando Inspeções da '.$businessUnitUser->seDescricao
                        ,'class'=>'orange white-text']);
                }
                break;
            case 4:
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
                    if( (!empty($request->all()['tipoUnidade_id'])) && (!empty($request->all()['tipoVerificacao'])) && (!empty($request->all()['inspetor'])) )
                    {
                        $registros = DB::table('unidades')
                            ->join('inspecoes', 'unidades.id',  '=',   'unidade_id')
                            ->select('inspecoes.*','unidades.se','unidades.seDescricao')
                            ->where([['ciclo', '=', $dados['ciclo']]])
                            ->Where([['status', '=', $dados['status']]])
                            ->Where([['tipoUnidade_id', '=', $dados['tipoUnidade_id']]])
                            ->Where([['tipoVerificacao', '=', $dados['tipoVerificacao']]])
//                                ->Where([['inspetorcoordenador', '=', $dados['inspetor']]])
//                                ->Where([['inspetorcolaborador', '=', $dados['inspetor']]])
                            ->where([['inspetorcoordenador', '=', auth()->user()->document]])
                            ->orWhere([['inspetorcolaborador', '=', auth()->user()->document]])
                            ->paginate(10);
                        \Session::flash('mensagem',['msg'=>'Filtro Aplicado, [Tipo de Unidade, Tipo de Verificação e Inspetor Envolvido].'
                            ,'class'=>'orange white-text']);
                    }
                    if( (!empty($request->all()['tipoUnidade_id'])) && (!empty($request->all()['tipoVerificacao']))  )
                    {
                        $registros = DB::table('unidades')
                            ->join('inspecoes', 'unidades.id',  '=',   'unidade_id')
                            ->select('inspecoes.*','unidades.se','unidades.seDescricao')
                            ->where([['ciclo', '=', $dados['ciclo']]])
                            ->Where([['status', '=', $dados['status']]])
                            ->Where([['tipoUnidade_id', '=', $dados['tipoUnidade_id']]])
                            ->Where([['tipoVerificacao', '=', $dados['tipoVerificacao']]])
                            ->where([['inspetorcoordenador', '=', auth()->user()->document]])
                            ->orWhere([['inspetorcolaborador', '=', auth()->user()->document]])
                            ->paginate(10);
                        \Session::flash('mensagem',['msg'=>'Filtro Aplicado, [Tipo de Unidade e Tipo de Verificação ].'
                            ,'class'=>'orange white-text']);
                    }
                    if(!empty($request->all()['tipoUnidade_id']))
                    {
                        $registros = DB::table('unidades')
                            ->join('inspecoes', 'unidades.id',  '=',   'unidade_id')
                            ->select('inspecoes.*','unidades.se','unidades.seDescricao')
                            ->where([['ciclo', '=', $dados['ciclo']]])
                            ->Where([['status', '=', $dados['status']]])
                            ->Where([['tipoUnidade_id', '=', $dados['tipoUnidade_id']]])
                            ->paginate(10);
                        \Session::flash('mensagem',['msg'=>'Filtro Aplicado, [Tipo de Unidade].'
                            ,'class'=>'orange white-text']);
                    }
                    if(!empty($request->all()['tipoVerificacao']))
                    {
                        $registros = DB::table('unidades')
                            ->join('inspecoes', 'unidades.id',  '=',   'unidade_id')
                            ->select('inspecoes.*','unidades.se','unidades.seDescricao')
                            ->where([['ciclo', '=', $dados['ciclo']]])
                            ->Where([['status', '=', $dados['status']]])
                            ->Where([['tipoVerificacao', '=', $dados['tipoVerificacao']]])
                            ->where([['inspetorcoordenador', '=', auth()->user()->document]])
                            ->orWhere([['inspetorcolaborador', '=', auth()->user()->document]])
                            ->paginate(10);
                        \Session::flash('mensagem',['msg'=>'Filtro Aplicado, [Tipo de Inspeção].'
                            ,'class'=>'orange white-text']);
                    }

                    if(!empty($request->all()['search'])){
                        $registros = DB::table('unidades')
                            ->join('inspecoes', 'unidades.id',  '=',   'unidade_id')
                            ->select('inspecoes.*','unidades.se','unidades.seDescricao')
                            ->where([['ciclo', '=', $dados['ciclo']]])
                            ->Where([['status', '=', $dados['status']]])
                            ->where([['inspecoes.descricao', 'LIKE', '%' . $request->all()['search'] .'%' ]])
                            ->where([['inspetorcoordenador', '=', auth()->user()->document]])
                            ->orWhere([['inspetorcolaborador', '=', auth()->user()->document]])
                            ->paginate(10);
                        \Session::flash('mensagem',['msg'=>'Filtro Aplicado, [Por Unidade].'
                            ,'class'=>'orange white-text']);
                    }
                    if(!empty($request->all()['inspetor'])){
                        $registros = DB::table('unidades')
                            ->join('inspecoes', 'unidades.id',  '=',   'unidade_id')
                            ->select('inspecoes.*','unidades.se','unidades.seDescricao')
                            ->where([['ciclo', '=', $dados['ciclo']]])
                            ->Where([['status', '=', $dados['status']]])
//                                ->Where([['inspetorcoordenador', '=', $dados['inspetor']]])
//                                ->Where([['inspetorcolaborador', '=', $dados['inspetor']]])
                            ->where([['inspetorcoordenador', '=', auth()->user()->document]])
                            ->orWhere([['inspetorcolaborador', '=', auth()->user()->document]])
                            ->paginate(10);
                    }
                    if(!empty($request->all()['codigo']))
                    {
                        $registros = DB::table('unidades')
                            ->join('inspecoes', 'unidades.id',  '=',   'unidade_id')
                            ->select('inspecoes.*','unidades.se','unidades.seDescricao')
                            ->where([['ciclo', '=', $dados['ciclo']]])
                            ->Where([['status', '=', $dados['status']]])
                            ->where([['codigo', 'LIKE', '%' . $request->all()['codigo'] .'%' ]])
                            ->where([['inspetorcoordenador', '=', auth()->user()->document]])
                            ->orWhere([['inspetorcolaborador', '=', auth()->user()->document]])
                            ->paginate(10);
                    }

                }
                break;
            default:  return redirect()->route('home');
        }
        return view('compliance.verificacoes.index',compact('registros','tiposDeUnidade', 'inspetores'));
        //return view('compliance.verificacoes.index',compact('registros', 'tiposDeUnidade'));
    }

    public function index()
    {
        try
        {
           $businessUnitUser = DB::table('unidades')
                ->Where([['mcu', '=', auth()->user()->businessUnit]])
                ->select('unidades.*')
                ->first();
        } catch (Exception $e)  {

//            \Session::flash('mensagem',['msg'=>'Seção expirada '.$e
//                ,'class'=>'red white-text']);
            return redirect()->route('login');
        }

//        $businessUnitUser = DB::table('unidades')
//            ->Where([['mcu', '=', auth()->user()->businessUnit]])
//            ->select('unidades.*')
//            ->first();
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
                       // dd('user 2 ',$registros);
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

                        $first = DB::table('unidades')
                            ->join('inspecoes', 'unidades.id',  '=',   'unidade_id')
                            ->select('inspecoes.*','unidades.se','unidades.seDescricao')
                            ->where([['status', '=', 'Em Inspeção']])
                            ->where([['inspetorcoordenador', '=', auth()->user()->document]]);
                        $registros = DB::table('unidades')
                            ->join('inspecoes', 'unidades.id',  '=',   'unidade_id')
                            ->select('inspecoes.*','unidades.se','unidades.seDescricao')
                            ->where([['status', '=', 'Em Inspeção']])
                            ->Where([['inspetorcolaborador', '=', auth()->user()->document]])
                            ->union($first)
                            ->orderBy('codigo' , 'asc')
                            ->paginate(10);
                        //dd('user 3 ',$registros);
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

                        $first = DB::table('unidades')
                            ->join('inspecoes', 'unidades.id',  '=',   'unidade_id')
                            ->select('inspecoes.*','unidades.se','unidades.seDescricao')
                            ->where([['status', '=', 'Em Inspeção']]);
                           // ->where([['inspetorcoordenador', '=', auth()->user()->document]]);

                        $registros = DB::table('unidades')
                            ->join('inspecoes', 'unidades.id',  '=',   'unidade_id')
                            ->select('inspecoes.*','unidades.se','unidades.seDescricao')
                            ->where([['status', '=', 'Em Inspeção']])
                          //  ->Where([['inspetorcolaborador', '=', auth()->user()->document]])
                            ->union($first)
                            ->orderBy('codigo' , 'asc')
                            ->paginate(10);
                       // dd('user 4 ',$registros);
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
                            ->where([['status', '=', 'Em Inspeção']])
                            ->where([['inspetorcoordenador', '=', auth()->user()->document]]);
                        $registros = DB::table('unidades')
                            ->join('inspecoes', 'unidades.id',  '=',   'unidade_id')
                            ->select('inspecoes.*','unidades.se','unidades.seDescricao')
                            ->where([['status', '=', 'Em Inspeção']])
                            ->Where([['inspetorcolaborador', '=', auth()->user()->document]])
                            ->union($first)
                            ->orderBy('codigo' , 'asc')
                            ->paginate(10);
                        //dd('user 6 ',$registros);
                    }
                    break;
                default:  return redirect()->route('home');
            }
//dd($registros);
            return view('compliance.verificacoes.index',compact('registros','tiposDeUnidade', 'inspetores'));
        }
        else
        {
            \Session::flash('mensagem',['msg'=> auth()->user()->name.', você não está associado a uma Unidade. Contate um administrador.'
                ,'class'=>'red white-text']);
            return redirect()->route('home');
        }
    }
}
