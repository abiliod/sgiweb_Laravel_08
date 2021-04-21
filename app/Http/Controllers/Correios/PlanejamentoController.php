<?php

namespace App\Http\Controllers\Correios;

use App\Http\Controllers\Controller;
use App\Models\Correios\Inspecao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PlanejamentoController extends Controller
{
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

//        dd($papel_user->papel_id);

        switch ($papel_user->papel_id) {
            case 1:
            case 2:
                {
//                  lista todos inspetores das Superintendências.
                    $inspetores = DB::table('papel_user')
                        ->join('users', 'users.id',  '=',   'user_id')
                        ->select('users.*','papel_user.*')
                        ->Where([['papel_id', '=', 6]])
                    ->get();
//                  1- todos filtros
                    if(
                        (!empty($request->all()['tipoUnidade_id']))
                        && (!empty($request->all()['tipoVerificacao']))
                        && (!empty($request->all()['inspetor']))
                        && (!empty($request->all()['status']))
                        && (!empty($request->all()['search']))
                        && (!empty($request->all()['codigo']))

                    ){
                        $registros = DB::table('unidades')
                            ->join('inspecoes', 'unidades.id',  '=',   'unidade_id')
                            ->select('inspecoes.*','unidades.se','unidades.seDescricao')
    //                        ->Where([['unidades.se', '=', $businessUnitUser->se]])
                            ->where([['ciclo', '=', $dados['ciclo']]])
                            ->Where([['tipoUnidade_id', '=', $dados['tipoUnidade_id']]])
                            ->Where([['tipoVerificacao', '=', $dados['tipoVerificacao']]])
                            ->Where([['status', '=', $dados['status']]])
                            ->Where([['inspetorcoordenador', '=', $dados['inspetor']]])
                            ->Where([['inspetorcolaborador', '=', $dados['inspetor']]])
                            ->where([['inspecoes.descricao', 'LIKE', '%' . $request->all()['search'] .'%' ]])
                            ->where([['codigo', 'LIKE', '%' . $request->all()['codigo'] .'%' ]])
                        ->paginate(10);

                        \Session::flash('mensagem',['msg'=>'Filtro Aplicado, [Todos].'
                            ,'class'=>'orange white-text']);
                    }

    //              2-  filtros [Tipo de Unidade, Tipo de Inspeção, Status e Unidade]
                    if(
                        (!empty($request->all()['tipoUnidade_id']))
                        && (!empty($request->all()['tipoVerificacao']))
    //                        && (!empty($request->all()['inspetor']))
                            && (!empty($request->all()['status']))
                        && (!empty($request->all()['search']))
    //                        && (!empty($request->all()['codigo']))

                    ){
                        $registros = DB::table('unidades')
                            ->join('inspecoes', 'unidades.id',  '=',   'unidade_id')
                            ->select('inspecoes.*','unidades.se','unidades.seDescricao')
    //                        ->Where([['unidades.se', '=', $businessUnitUser->se]])
                            ->where([['ciclo', '=', $dados['ciclo']]])
                            ->Where([['tipoUnidade_id', '=', $dados['tipoUnidade_id']]])
                            ->Where([['tipoVerificacao', '=', $dados['tipoVerificacao']]])
                             ->Where([['status', '=', $dados['status']]])
    //                         ->Where([['inspetorcoordenador', '=', $dados['inspetor']]])
    //                         ->Where([['inspetorcolaborador', '=', $dados['inspetor']]])
    //                         ->where([['inspecoes.descricao', 'LIKE', '%' . $request->all()['search'] .'%' ]])
    //                        ->where([['codigo', 'LIKE', '%' . $request->all()['codigo'] .'%' ]])
                            ->paginate(10);
                        \Session::flash('mensagem',['msg'=>'Filtro Aplicado, [Tipo de Unidade, Tipo de Inspeção, Status e Unidade].'
                            ,'class'=>'orange white-text']);

                        }

    //                3-  filtros [Tipo de Unidade, Tipo de Inspeção, Inspetores envolvidos]
                    if(
                        (!empty($request->all()['tipoUnidade_id']))
                        && (!empty($request->all()['tipoVerificacao']))
                        && (!empty($request->all()['inspetor']))
    //                        && (!empty($request->all()['status']))
    //                        && (!empty($request->all()['search']))
    //                        && (!empty($request->all()['codigo']))

                    ){
                        $registros = DB::table('unidades')
                            ->join('inspecoes', 'unidades.id',  '=',   'unidade_id')
                            ->select('inspecoes.*','unidades.se','unidades.seDescricao')
    //                        ->Where([['unidades.se', '=', $businessUnitUser->se]])
                            ->where([['ciclo', '=', $dados['ciclo']]])
                            ->Where([['tipoUnidade_id', '=', $dados['tipoUnidade_id']]])
                            ->Where([['tipoVerificacao', '=', $dados['tipoVerificacao']]])
    //                         ->Where([['status', '=', $dados['status']]])
                            ->Where([['inspetorcoordenador', '=', $dados['inspetor']]])
                            ->Where([['inspetorcolaborador', '=', $dados['inspetor']]])
    //                         ->where([['inspecoes.descricao', 'LIKE', '%' . $request->all()['search'] .'%' ]])
    //                         ->where([['codigo', 'LIKE', '%' . $request->all()['codigo'] .'%' ]])
                            ->paginate(10);
                        \Session::flash('mensagem',['msg'=>'Filtro Aplicado, [Tipo de Unidade, Tipo de Inspeção, Inspetores envolvidos].'
                            ,'class'=>'orange white-text']);

                        }

    //                4-  filtros [ciclo  inspetor]
                    if(
    //                    (!empty($request->all()['tipoUnidade_id']))
    //                    && (!empty($request->all()['tipoVerificacao']))
                         (!empty($request->all()['inspetor']))
    //                    && (!empty($request->all()['status']))
    //                    && (!empty($request->all()['search']))
    //                    && (!empty($request->all()['codigo']))

                    ){
                    $registros = DB::table('unidades')
                        ->join('inspecoes', 'unidades.id',  '=',   'unidade_id')
                        ->select('inspecoes.*','unidades.se','unidades.seDescricao')
    //                       ->Where([['unidades.se', '=', $businessUnitUser->se]])
                        ->where([['ciclo', '=', $dados['ciclo']]])
    //                      ->Where([['tipoUnidade_id', '=', $dados['tipoUnidade_id']]])
    //                      ->Where([['tipoVerificacao', '=', $dados['tipoVerificacao']]])
    //                     ->Where([['status', '=', $dados['status']]])
                        ->Where([['inspetorcoordenador', '=', $dados['inspetor']]])
                        ->Where([['inspetorcolaborador', '=', $dados['inspetor']]])
    //                     ->where([['inspecoes.descricao', 'LIKE', '%' . $request->all()['search'] .'%' ]])
    //                     ->where([['codigo', 'LIKE', '%' . $request->all()['codigo'] .'%' ]])
                        ->paginate(10);
                    \Session::flash('mensagem',['msg'=>'Filtro Aplicado, [Inspetores envolvidos].'
                        ,'class'=>'orange white-text']);

                    }

    //                4-  filtros [ciclo  Unidades]
                    if(
                     //   (!empty($request->all()['tipoUnidade_id']))
                    //    && (!empty($request->all()['tipoVerificacao']))
                     //   && (!empty($request->all()['inspetor']))
                    //    && (!empty($request->all()['status']))
                         (!empty($request->all()['search']))
                    //    && (!empty($request->all()['codigo']))

                    ){
                    $registros = DB::table('unidades')
                        ->join('inspecoes', 'unidades.id',  '=',   'unidade_id')
                        ->select('inspecoes.*','unidades.se','unidades.seDescricao')
                        //                            ->Where([['unidades.se', '=', $businessUnitUser->se]])
                        ->where([['ciclo', '=', $dados['ciclo']]])
                        //  ->Where([['tipoUnidade_id', '=', $dados['tipoUnidade_id']]])
                        //  ->Where([['tipoVerificacao', '=', $dados['tipoVerificacao']]])
                        // ->Where([['status', '=', $dados['status']]])
                        //  ->Where([['inspetorcoordenador', '=', $dados['inspetor']]])
                        //  ->Where([['inspetorcolaborador', '=', $dados['inspetor']]])
                        ->where([['inspecoes.descricao', 'LIKE', '%' . $request->all()['search'] .'%' ]])
                        // ->where([['codigo', 'LIKE', '%' . $request->all()['codigo'] .'%' ]])
                        ->paginate(10);
                    \Session::flash('mensagem',['msg'=>'Filtro Aplicado, [Unidades].'
                        ,'class'=>'orange white-text']);

                    }

    //                5-  filtros [ciclo  codigo]
                    if(
    //                    (!empty($request->all()['tipoUnidade_id']))
    //                    && (!empty($request->all()['tipoVerificacao']))
    //                    && (!empty($request->all()['inspetor']))
    //                    && (!empty($request->all()['status']))
    //                    && (!empty($request->all()['search']))
                         (!empty($request->all()['codigo']))

                    ){
                    $registros = DB::table('unidades')
                        ->join('inspecoes', 'unidades.id',  '=',   'unidade_id')
                        ->select('inspecoes.*','unidades.se','unidades.seDescricao')
    //                    ->Where([['unidades.se', '=', $businessUnitUser->se]])
                        ->where([['ciclo', '=', $dados['ciclo']]])
    //                      ->Where([['tipoUnidade_id', '=', $dados['tipoUnidade_id']]])
    //                      ->Where([['tipoVerificacao', '=', $dados['tipoVerificacao']]])
    //                      ->Where([['status', '=', $dados['status']]])
    //                      ->Where([['inspetorcoordenador', '=', $dados['inspetor']]])
    //                      ->Where([['inspetorcolaborador', '=', $dados['inspetor']]])
    //                      ->where([['inspecoes.descricao', 'LIKE', '%' . $request->all()['search'] .'%' ]])
                        ->where([['codigo', 'LIKE', '%' . $request->all()['codigo'] .'%' ]])
                        ->paginate(10);
                    \Session::flash('mensagem',['msg'=>'Filtro Aplicado, [Código].'
                        ,'class'=>'orange white-text']);

                    }

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
        $tiposDeUnidade = DB::table('tiposdeunidade')
            ->where([
                ['inspecionar', '=', 'sim'],
                ['tipoInspecao', '=', 'Monitorada'],
            ])
            ->get();
        return view('compliance.planejamento.index',compact('inspetores','registros', 'tiposDeUnidade'));

        //return view('compliance.verificacoes.index',compact('registros', 'tiposDeUnidade'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $businessUnitUser = DB::table('unidades')
            ->Where([['mcu', '=', auth()->user()->businessUnit]])
            ->select('unidades.*')
            ->first();
        if(!empty( $businessUnitUser ))
        {
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
                            ->Where([['se', '=', $businessUnitUser->se]])
                            ->Where([['papel_id', '=', 6]])
                            ->get();
                        $registros = DB::table('unidades')
                            ->join('inspecoes', 'unidades.id',  '=',   'unidade_id')
                            ->select('inspecoes.*','unidades.se','unidades.seDescricao')
                            ->where([['status', '=',  'Em Inspeção']])
                            ->where([['se', '=', $businessUnitUser->se]])
                            ->where([['inspetorcoordenador', '=', null ]])
                            ->orderBy('codigo' , 'asc')
                            ->paginate(15);
        //                dd($inspetores, $businessUnitUser, $registros);
//
//                        +"tipoVerificacao": "Monitorada"
//                    +"status": "Em Inspeção"
//                    +"inspetorcoordenador": null
//                    +"inspetorcolaborador": null
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

            $tiposDeUnidade = DB::table('tiposdeunidade')
                ->where([
                    ['inspecionar', '=', 'sim'],
                    ['tipoInspecao', '=', 'Monitorada'],
                ])
            ->get();

            return view('compliance.planejamento.index',compact('inspetores','registros', 'tiposDeUnidade'));
        }
        else
        {
            \Session::flash('mensagem',['msg'=>'Não foi possivel exibir os itens você provavelmente não é administrador.'
                ,'class'=>'red white-text']);
            return redirect()->route('home');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $registro = Inspecao::find($id);

        $tiposDeUnidade = DB::table('tiposdeunidade')
            ->where([
                ['inspecionar', '=', 'sim'],
                ['tipoInspecao', '=', 'Monitorada'],
            ])
        ->get();

        $businessUnitUser = DB::table('unidades')
            ->Where([['mcu', '=', auth()->user()->businessUnit]])
            ->select('unidades.*')
        ->first();
        $inspetores = DB::table('papel_user')
            ->join('users', 'users.id',  '=',   'user_id')
            ->select('users.*','papel_user.*')
            ->Where([['se', '=', $businessUnitUser->se]])
            ->Where([['papel_id', '=', 6]])
        ->get();
      //  dd($registro,$tiposDeUnidade,$inspetores);
        return view('compliance.planejamento.edit',compact('registro','tiposDeUnidade','inspetores'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
             'inspetorcoordenador' => 'required'
            , 'inspetorcolaborador' => 'required'
            , 'numHrsPreInsp' => 'required'
            , 'numHrsDesloc' => 'required'
            , 'numHrsInsp' => 'required'
        ]);
        if($validator->passes())
        {
            $dados = $request->all();

            if($dados['inspetorcoordenador']  == $dados['inspetorcolaborador'] )
            {
                \Session::flash('mensagem',['msg'=>'Inspetores devem ser diferentes !'
                    ,'class'=>'red white-text']);
                return redirect()->back();
            }

            $registro = Inspecao::find($id);
            $registro->inspetorcoordenador = $dados ['inspetorcoordenador'];
            $registro->inspetorcolaborador = $dados ['inspetorcolaborador'];
            $registro->numHrsPreInsp       = $dados ['numHrsPreInsp'];
            $registro->numHrsDesloc        = $dados ['numHrsDesloc'];
            $registro->numHrsInsp          = $dados ['numHrsInsp'];
            $registro->update();

            \Session::flash('mensagem',['msg'=>'Inspeção Atualizada com sucesso !'
                ,'class'=>'green white-text']);

            return redirect()->route('compliance.planejamento');
        }
        else
        {
            if ( (empty($dados ['inspetorcoordenador'])) ||
                (empty($dados ['inspetorcolaborador'])))
            {
                \Session::flash('mensagem',['msg'=>'Erro o Arquivo. Não foi atualizado vincule Inspetor.'
                    ,'class'=>'red white-text']);
            }
            return back();
        }
    }
}
