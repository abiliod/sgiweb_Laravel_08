<?php

namespace App\Http\Controllers\Correios;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Correios\Unidade;
use App\Models\Correios\TipoDeUnidade;
use App\Models\Correios\Inspecao;
use App\Models\Correios\Itensdeinspecao;
use App\Http\Requests\Compliance\salvarInspecao;
use App\Models\Correios\SequenceInspecao;

class UnidadesController extends Controller
{

    public function salvarInspecao(salvarInspecao $request)
    {
        $dados = $request->all();
        $verifica = DB::table('inspecoes')
            ->select('inspecoes.*')
            ->Where([['unidade_id', '=',  $dados['unidade_id']]])
            ->Where([['tipoVerificacao', '=',  $dados['tipoVerificacao']]])
            ->Where([['ciclo', '=', $dados['ciclo']]])
        ->first();
        if ($verifica === null) {

            if($dados['inspetorcoordenador']  == $dados['inspetorcolaborador'] )
            {
                \Session::flash('mensagem',['msg'=>'Inspetores devem ser diferentes !'
                    ,'class'=>'red white-text']);
                return redirect()->back();
            }

            $unidade = Unidade::find($dados['unidade_id']); // unidade inspecionada

            //gerar numeração a inspeção gerada manualmente começa com o número 1 até 1999
            $sequence_inspcaos = DB::table('sequence_inspcaos')
                ->select('sequence_inspcaos.*')
                ->Where([['se', '=', $unidade->se]])
                ->Where([['ciclo', '=', $dados['ciclo']]])
                ->Where([['sequence', '<', 2000]])
                ->orderBy('sequence','desc')
                ->first();


            if(!empty($sequence_inspcaos))
            {
                $sequence = $sequence_inspcaos->sequence;
                $sequence ++;
                $sequenceInspecao = SequenceInspecao::find($sequence_inspcaos->id);
                $sequenceInspecao->se      = $unidade->se;
                $sequenceInspecao->ciclo =  $dados['ciclo'];
                $sequenceInspecao->sequence      = $sequence;
                $sequenceInspecao->update();
            }
            else
            {
                $sequence=1;
                $sequenceInspecao = new SequenceInspecao;
                $sequenceInspecao->se      = $unidade->se;
                $sequenceInspecao->ciclo =  $dados['ciclo'];
                $sequenceInspecao->sequence      = $sequence;
                $sequenceInspecao->save();
            }

            $sequence = str_pad(  $sequenceInspecao->sequence , 4, '0', STR_PAD_LEFT);
            $codigo = $unidade->se.$sequence.$dados['ciclo'];

            if( $dados ['tipoVerificacao'] != 'previa')
            {
                $inspecao = new Inspecao;
                $inspecao->ciclo      = $dados ['ciclo'];
                $inspecao->descricao =  $unidade->descricao;
                $inspecao->datainiPreInspeção      = $dados ['datainiPreInspeção'];
                $inspecao->codigo      = $codigo;
                $inspecao->unidade_id      = $dados ['unidade_id'];
                $inspecao->tipoUnidade_id      = $dados ['tipoUnidade_id'];
                $inspecao->tipoVerificacao      = $dados ['tipoVerificacao'];
                $inspecao->status      = $dados ['status'];
                $inspecao->inspetorcoordenador      = $dados ['inspetorcoordenador'];
                $inspecao->inspetorcolaborador      = $dados ['inspetorcolaborador'];
                $inspecao->numHrsPreInsp      = $dados ['numHrsPreInsp'];
                $inspecao->numHrsDesloc      = $dados ['numHrsDesloc'];
                $inspecao->numHrsInsp      = $dados ['numHrsInsp'];
                $inspecao->unidade_id      = $dados['unidade_id'];
                $inspecao->save();

                $parametros = DB::table('tiposdeunidade')
                    ->join('gruposdeverificacao', 'tiposdeunidade.id',  '=',   'tipoUnidade_id')
                    ->join('testesdeverificacao', 'grupoVerificacao_id', '=', 'gruposdeverificacao.id')
                    ->where([
                        ['gruposdeverificacao.tipoUnidade_id', '=',  $inspecao->tipoUnidade_id  ] //" tipoUnidade_id " => " 1 "
                    ])
                    ->where([
                        ['gruposdeverificacao.tipoVerificacao', '=', $inspecao->tipoVerificacao  ] //" tipoVerificacao " => " Remoto "
                    ])
                    ->where([
                        ['gruposdeverificacao.ciclo', '=', $inspecao->ciclo  ] // REGRA o Caderno é por ciclo
                    ])
                ->get();
                foreach($parametros as $parametro)
                {
                    $registro = new Itensdeinspecao;
                    $registro->inspecao_id =  $inspecao->id ; //veriricação relacionada

                    //$parametro é um objeto, não uma matriz, então deve acessá-lo da seguinte forma:
                    $registro->unidade_id =  $dados['unidade_id']; //unidade verificada
                    $registro->tipoUnidade_id =  $dados['tipoUnidade_id']; //Tipo de unidade
                    $registro->grupoVerificacao_id =  $parametro->grupoVerificacao_id;//grupo de verificação
                    $registro->testeVerificacao_id =  $parametro->id;// $registro->id teste de verificação
                    $registro->oportunidadeAprimoramento = $parametro->roteiroConforme;
                    $registro->consequencias =   $parametro->consequencias;
                    $registro->norma  =   $parametro->norma;
                    $registro->save();
                }

                \Session::flash('mensagem',['msg'=>'Inspeção Gerada com sucesso !'
                    ,'class'=>'green white-text']);
                return redirect()->route('compliance.unidades');
            }
            else
            {
                \Session::flash('mensagem',['msg'=>'Tipo de Inspeção não executada por essa rotina !'
                    ,'class'=>'red white-text']);
                return redirect()->back();
            }
        }
        else
        {
            \Session::flash('mensagem',['msg'=>'Já existe uma inspeção nessa modalidade para essa unidade neste ciclo !'
                ,'class'=>'green white-text']);
            return redirect()->route('compliance.unidades');
        }

    }

    public function gerarInspecao($id)
    {
        $registro = Unidade::find($id);

        $tiposautorizado = DB::table('tiposdeunidade')
            ->where([
                ['tiposdeunidade.id', '=', $registro->tipoUnidade_id],
            ])
            ->first();
        if ($tiposautorizado->inspecionar=='Sim')
        {
            $tiposDeUnidade = DB::table('tiposdeunidade')
                ->where([
                    ['tiposdeunidade.id', '=', $registro->tipoUnidade_id],
                ])
                ->get();

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
                    //->orderBy('users.se', 'asc')
                   // ->orderBy('users.name', 'asc')
                    ->first();


                switch ($papel_user->papel_id)
                {
                    case 1:
                    case 2:
                        {
                            //  dd('pare');
                            $inspetores = DB::table('papel_user')
                                ->join('users', 'users.id',  '=',   'user_id')
                                ->select('users.*','papel_user.*')
                                //->Where([['se', '=', $businessUnitUser->se]])
                                ->Where([['papel_id', '=', 6]])
                              //  ->orderBy('users.name', 'asc')
                                ->orderBy('users.se', 'asc')

                                ->get();
                        }
                        break;
                    case 4:
                        {
                           $inspetores = DB::table('papel_user')
                                ->join('users', 'users.id',  '=',   'user_id')
                                ->select('users.*','papel_user.*')
                                ->Where([['se', '=', $businessUnitUser->se]])
                                ->Where([['papel_id', '=', 6]])
                                ->orderBy('users.name', 'asc')
                           ->get();
                        }
                        break;
                    case 5:
                        {
                            \Session::flash('mensagem',['msg'=>'Perfil não autorizado.'
                                ,'class'=>'red white-text']);
                           // return redirect()->route('login');
                        }
                        break;
                    case 3:
                    case 6:
                        {
                          //  dd('pare');
                            $inspetores = DB::table('papel_user')
                                ->join('users', 'users.id',  '=',   'user_id')
                                ->select('users.*','papel_user.*')
                                ->Where([['se', '=', $businessUnitUser->se]])
                                ->Where([['papel_id', '=', 6]])
                                ->orderBy('users.name', 'asc')
                             //   ->orderBy('users.se', 'asc')
                                ->get();
                        }
                        break;
                }


                return view('compliance.unidades.gerarInspecao',compact('registro','tiposDeUnidade','inspetores'));
            }
            else
            {
                return redirect()->route('login');
            }

        }
        else
        {
            \Session::flash('mensagem',['msg'=>'Esta unidade ainda não está liberada para Inspeção:  '.$registro->descricao.'  !'
                ,'class'=>'red white-text']);
            return redirect()->route('compliance.unidades');
        }


    }

    public function atualizar (Request $request, $id)
    {
        $registro = Unidade::find($id);
        $dados = $request->all();
        $registro->se =  $dados['se'];
        $registro->tipoUnidade_id =  $dados['tipoUnidade_id'];
        $registro->descricao =  $dados['descricao'];
        $registro->an8 =  $dados['an8'];
        $registro->mcu =  $dados['mcu'];
        $registro->sto =  $dados['sto'];
        $registro->status_unidadeDesc =  $dados['status_unidadeDesc'];
        $registro->inicio_expediente =  $dados['inicio_expediente'];
        $registro->inicio_atendimento =  $dados['inicio_atendimento'];
        $registro->final_atendimento =  $dados['final_atendimento'];
        $registro->final_expediente =  $dados['final_expediente'];
        $registro->inicio_intervalo_refeicao =  $dados['inicio_intervalo_refeicao'];
        $registro->final_intervalo_refeicao =  $dados['final_intervalo_refeicao'];
        $registro->trabalha_sabado =  $dados['trabalha_sabado'];
        if( $registro->trabalha_sabado=="Não"){
            $dados['inicio_expediente_sabado']=NULL;
            $dados['final_expediente_sabado']=NULL;
            $dados['horario_lim_post_final_semana']=NULL;
        }
        $registro->inicio_expediente_sabado =  $dados['inicio_expediente_sabado'];
        $registro->final_expediente_sabado =  $dados['final_expediente_sabado'];
        $registro->trabalha_domingo =  $dados['trabalha_domingo'];
        if( $registro->trabalha_sabado=="Não"){
            $dados['inicio_expediente_domingo']=NULL;
            $dados['final_expediente_domingo']=NULL;
            $dados['horario_lim_post_final_semana']=NULL;
        }
        $registro->inicio_expediente_domingo =  $dados['inicio_expediente_domingo'];
        $registro->final_expediente_domingo =  $dados['final_expediente_domingo'];
        $registro->tem_plantao =  $dados['tem_plantao'];
        if( $registro->tem_plantao=="Não"){
            $dados['inicio_plantao_sabado']=NULL;
            $dados['final_plantao_sabado']=NULL;
            $dados['inicio_plantao_domingo']=NULL;
            $dados['final_plantao_domingo']=NULL;
            $dados['horario_lim_post_final_semana']=NULL;
        }
        $registro->inicio_plantao_sabado =  $dados['inicio_plantao_sabado'];
        $registro->final_plantao_sabado =  $dados['final_plantao_sabado'];
        $registro->inicio_plantao_domingo =  $dados['inicio_plantao_domingo'];
        $registro->final_plantao_domingo =  $dados['final_plantao_domingo'];
        $registro->tem_distribuicao =  $dados['tem_distribuicao'];
        if( $registro->tem_distribuicao=="Tem distribuição"){
            $dados['inicio_distribuicao']=NULL;
            $dados['final_distribuicao']=NULL;
        }
        $registro->inicio_distribuicao =  $dados['inicio_distribuicao'];
        $registro->final_distribuicao =  $dados['final_distribuicao'];

        if(($registro->tipoUnidade_id >= 13) && ($registro->tipoUnidade_id !=31) ){
            $dados['horario_lim_post_na_semana']=NULL;
            $dados['horario_lim_post_final_semana']=NULL;
        }
        $registro->horario_lim_post_na_semana =  $dados['horario_lim_post_na_semana'];
        $registro->horario_lim_post_final_semana =  $dados['horario_lim_post_final_semana'];
        $registro->telefone =  $dados['telefone'];
        $registro->email =  $dados['email'];
        $registro->update();

        \Session::flash('mensagem',['msg'=>'Registro da Unidade:  '.$registro->descricao.' foi atualizado com sucesso !'
        ,'class'=>'green white-text']);
        return redirect()->route('compliance.unidades');
    }

    public function edit($id)
    {
        $status_unidadeDesc = DB::table('unidades')
        ->select('status_unidadeDesc')
        ->groupBy('status_unidadeDesc')
        ->get();
        $tiposDeUnidade = TipoDeUnidade::all();
        $registro = Unidade::find($id);
        //dd('pare');
        return view('compliance.unidades.editar',compact('registro','tiposDeUnidade','status_unidadeDesc'));
    }

    public function search (Request $request)
    {
        $status = 'Criado e instalado';
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
                      //  dd('1    ->',$businessUnitUser , $papel_user);

                        $registros = DB::table('unidades')
                            ->join('tiposdeunidade', 'unidades.tipoUnidade_id', '=', 'tiposdeunidade.id')
                            ->select(
                                'unidades.*', 'tiposdeunidade.inspecionar', 'tiposdeunidade.tipoInspecao'
                            )
                            ->where([['tiposdeunidade.inspecionar', '=',  'Sim']])
                            ->where([['unidades.status_unidadeDesc', '=',  $status]])
                            ->where([
                                ['descricao', 'LIKE', '%' . $request->all()['search'] .'%' ],
                                ['status_unidadeDesc', '=', $status],
                                ['tiposdeunidade.inspecionar', '=',  'Sim']
                            ])
                            ->orWhere([
                                ['mcu', 'LIKE', '%' . $request->all()['search'] .'%' ],
                                ['status_unidadeDesc', '=', $status],
                                ['tiposdeunidade.inspecionar', '=',  'Sim']

                            ])
                            ->orWhere([
                                ['sto', 'LIKE', '%' . $request->all()['search'] .'%' ],
                                ['status_unidadeDesc', '=', $status],
                                ['tiposdeunidade.inspecionar', '=',  'Sim']
                            ])
                            ->orWhere([
                                ['telefone', 'LIKE', '%' . $request->all()['search'] .'%' ],
                                ['status_unidadeDesc', '=', $status],
                                ['tiposdeunidade.inspecionar', '=',  'Sim']
                            ])
                            ->orderBy('seDescricao', 'desc')
                            ->orderBy('tipoOrgaoDesc', 'asc')
                            ->orderBy('descricao', 'asc')
                            ->paginate(10);

                    }
                    break;
                case 3:
                    {
                        $registros = DB::table('unidades')
                            ->join('tiposdeunidade', 'unidades.tipoUnidade_id', '=', 'tiposdeunidade.id')
                            ->select(
                                'unidades.*', 'tiposdeunidade.inspecionar', 'tiposdeunidade.tipoInspecao'
                            )
                            ->where([['tiposdeunidade.inspecionar', '=',  'Sim']])
                            ->where([['unidades.status_unidadeDesc', '=',  $status]])
                            ->Where([['se', '=', $mcu_subordinacaoAdm->se]])
                            ->where([
                                ['descricao', 'LIKE', '%' . $request->all()['search'] .'%' ],
                                ['status_unidadeDesc', '=', $status],
                                ['tiposdeunidade.inspecionar', '=',  'Sim'],
                                ['tiposdeunidade.tipoInspecao', '!=',  'Monitorada']
                            ])
                            ->orWhere([
                                ['mcu', 'LIKE', '%' . $request->all()['search'] .'%' ],
                                ['status_unidadeDesc', '=', $status],
                                ['tiposdeunidade.inspecionar', '=',  'Sim'],
                                ['tiposdeunidade.tipoInspecao', '!=',  'Monitorada']

                            ])
                            ->orWhere([
                                ['sto', 'LIKE', '%' . $request->all()['search'] .'%' ],
                                ['status_unidadeDesc', '=', $status],
                                ['tiposdeunidade.inspecionar', '=',  'Sim'],
                                ['tiposdeunidade.tipoInspecao', '!=',  'Monitorada']
                            ])
                            ->orWhere([
                                ['telefone', 'LIKE', '%' . $request->all()['search'] .'%' ],
                                ['status_unidadeDesc', '=', $status],
                                ['tiposdeunidade.inspecionar', '=',  'Sim'],
                                ['tiposdeunidade.tipoInspecao', '!=',  'Monitorada']
                            ])
                            ->orderBy('seDescricao', 'desc')
                            ->orderBy('tipoOrgaoDesc', 'asc')
                            ->orderBy('descricao', 'asc')
                            ->paginate(10);

                    }
                    break;
                case 4:
                    {
                   $registros = DB::table('unidades')
                   ->join('tiposdeunidade', 'unidades.tipoUnidade_id', '=', 'tiposdeunidade.id')
                   ->select(
                       'unidades.*', 'tiposdeunidade.inspecionar', 'tiposdeunidade.tipoInspecao'
                   )
                   ->where([['tiposdeunidade.inspecionar', '=',  'Sim']])
                   ->where([['unidades.status_unidadeDesc', '=',  $status]])
                   ->Where([['se', '=', $businessUnitUser->se]])
                   ->where([
                       ['descricao', 'LIKE', '%' . $request->all()['search'] .'%' ],
                       ['status_unidadeDesc', '=', $status],
                       ['tiposdeunidade.inspecionar', '=',  'Sim'],
                       ['tiposdeunidade.tipoInspecao', '!=',  'Monitorada']
                   ])
                   ->orWhere([
                       ['mcu', 'LIKE', '%' . $request->all()['search'] .'%' ],
                       ['status_unidadeDesc', '=', $status],
                       ['tiposdeunidade.inspecionar', '=',  'Sim'],
                       ['tiposdeunidade.tipoInspecao', '!=',  'Monitorada']

                   ])
                   ->orWhere([
                       ['sto', 'LIKE', '%' . $request->all()['search'] .'%' ],
                       ['status_unidadeDesc', '=', $status],
                       ['tiposdeunidade.inspecionar', '=',  'Sim'],
                       ['tiposdeunidade.tipoInspecao', '!=',  'Monitorada']
                   ])
                   ->orWhere([
                       ['telefone', 'LIKE', '%' . $request->all()['search'] .'%' ],
                       ['status_unidadeDesc', '=', $status],
                       ['tiposdeunidade.inspecionar', '=',  'Sim'],
                       ['tiposdeunidade.tipoInspecao', '!=',  'Monitorada']
                   ])
                   ->orderBy('seDescricao', 'desc')
                   ->orderBy('tipoOrgaoDesc', 'asc')
                   ->orderBy('descricao', 'asc')
                   ->paginate(10);

                      //  dd($request->all()['search'] ,   $businessUnitUser->mcu_subordinacaoAdm, $registros);
                    }
                    break;
                case 5:
                    {
                        $registros = DB::table('unidades')
                            ->join('tiposdeunidade', 'unidades.tipoUnidade_id', '=', 'tiposdeunidade.id')
                            ->select(
                                'unidades.*', 'tiposdeunidade.inspecionar', 'tiposdeunidade.tipoInspecao'
                            )
                            ->where([['tiposdeunidade.inspecionar', '=',  'Sim']])
                            ->where([['unidades.status_unidadeDesc', '=',  $status]])
                            ->Where([['se', '=', $businessUnitUser->mcu]])
                            ->Where([['tiposdeunidade.tipoInspecao', '!=',  'Monitorada']])

                            ->orderBy('seDescricao', 'desc')
                            ->orderBy('tipoOrgaoDesc', 'asc')
                            ->orderBy('descricao', 'asc')
                            ->paginate(10);

                    }
                    break;
                case 6:
                    {
                        //dd('caso 2');
                        $registros = DB::table('unidades')
                            ->join('tiposdeunidade', 'unidades.tipoUnidade_id', '=', 'tiposdeunidade.id')
                            ->select(
                                'unidades.*', 'tiposdeunidade.inspecionar', 'tiposdeunidade.tipoInspecao'
                            )
                            ->where([['tiposdeunidade.inspecionar', '=',  'Sim']])
                            ->where([['unidades.status_unidadeDesc', '=',  $status]])
                            ->Where([['se', '=', $businessUnitUser->se]])
                            ->where([
                                ['descricao', 'LIKE', '%' . $request->all()['search'] .'%' ],
                                ['status_unidadeDesc', '=', $status],
                                ['tiposdeunidade.inspecionar', '=',  'Sim'],
                                ['tiposdeunidade.tipoInspecao', '!=',  'Monitorada']

                            ])
                            ->orWhere([
                                ['mcu', 'LIKE', '%' . $request->all()['search'] .'%' ],
                                ['status_unidadeDesc', '=', $status],
                                ['tiposdeunidade.inspecionar', '=',  'Sim'],
                                ['tiposdeunidade.tipoInspecao', '!=',  'Monitorada']

                            ])
                            ->orWhere([
                                ['sto', 'LIKE', '%' . $request->all()['search'] .'%' ],
                                ['status_unidadeDesc', '=', $status],
                                ['tiposdeunidade.inspecionar', '=',  'Sim'],
                                ['tiposdeunidade.tipoInspecao', '!=',  'Monitorada']
                            ])
                            ->orWhere([
                                ['telefone', 'LIKE', '%' . $request->all()['search'] .'%' ],
                                ['status_unidadeDesc', '=', $status],
                                ['tiposdeunidade.inspecionar', '=',  'Sim'],
                                ['tiposdeunidade.tipoInspecao', '!=',  'Monitorada']
                            ])
                            ->orderBy('seDescricao', 'desc')
                            ->orderBy('tipoOrgaoDesc', 'asc')
                            ->orderBy('descricao', 'asc')
                            ->paginate(10);
                    }
                    break;
            }

           // return view('compliance.unidades.index',compact('registros'));
        }





        return view('compliance.unidades.index',compact('registros'));
    }

    public function index()
    {
        $status = 'Criado e instalado';
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
                         $registros = DB::table('unidades')
                             ->join('tiposdeunidade', 'unidades.tipoUnidade_id', '=', 'tiposdeunidade.id')
                             ->select(
                                 'unidades.*', 'tiposdeunidade.inspecionar', 'tiposdeunidade.tipoInspecao'
                             )
                             ->where([['tiposdeunidade.inspecionar', '=',  'Sim']])
                             ->where([['unidades.status_unidadeDesc', '=',  $status]])
                             ->orderBy('seDescricao', 'desc')
                             ->orderBy('tipoOrgaoDesc', 'asc')
                             ->orderBy('descricao', 'asc')
                             ->paginate(10);
                    }
                break;
                case 3:
                    {
                        $registros = DB::table('unidades')
                            ->join('tiposdeunidade', 'unidades.tipoUnidade_id', '=', 'tiposdeunidade.id')
                            ->select(
                                'unidades.*', 'tiposdeunidade.inspecionar', 'tiposdeunidade.tipoInspecao'
                            )
                            ->where([['tiposdeunidade.inspecionar', '=',  'Sim']])
                            ->where([['tiposdeunidade.tipoInspecao', '!=',  'Monitorada']])
                            ->where([['unidades.status_unidadeDesc', '=',  $status]])
                            ->Where([['se', '=', $businessUnitUser->se]])
                            ->orderBy('seDescricao', 'desc')
                            ->orderBy('tipoOrgaoDesc', 'asc')
                            ->orderBy('descricao', 'asc')
                            ->paginate(10);

                    }
                break;
                case 4:
                    {

                        $registros = DB::table('unidades')
                            ->join('tiposdeunidade', 'unidades.tipoUnidade_id', '=', 'tiposdeunidade.id')
                            ->select(
                                'unidades.*', 'tiposdeunidade.inspecionar', 'tiposdeunidade.tipoInspecao'
                            )
                            ->where([['tiposdeunidade.inspecionar', '=',  'Sim']])
                            ->where([['tiposdeunidade.tipoInspecao', '!=',  'Monitorada']])
                            ->where([['unidades.status_unidadeDesc', '=',  $status]])
                            ->Where([['se', '=', $businessUnitUser->se]])
                            ->orderBy('seDescricao', 'desc')
                            ->orderBy('tipoOrgaoDesc', 'asc')
                            ->orderBy('descricao', 'asc')
                            ->paginate(10);
                       // ->get(1);

                       // dd($businessUnitUser, $registros);

                    }
                break;
                case 5:
                    {
                      $registros = DB::table('unidades')
                          ->join('tiposdeunidade', 'unidades.tipoUnidade_id', '=', 'tiposdeunidade.id')
                          ->select(
                              'unidades.*', 'tiposdeunidade.inspecionar', 'tiposdeunidade.tipoInspecao'
                          )
                          ->where([['tiposdeunidade.inspecionar', '=',  'Sim']])
                          ->where([['tiposdeunidade.tipoInspecao', '!=',  'Monitorada']])
                          ->where([['unidades.status_unidadeDesc', '=',  $status]])
                          ->Where([['se', '=', $businessUnitUser->mcu]])
                          ->orderBy('seDescricao', 'desc')
                          ->orderBy('tipoOrgaoDesc', 'asc')
                          ->orderBy('descricao', 'asc')
                          ->paginate(10);

                    }
                break;
                case 6:
                    {
                      //      dd('caso 6');
                            $registros = DB::table('unidades')
                                ->join('tiposdeunidade', 'unidades.tipoUnidade_id', '=', 'tiposdeunidade.id')
                                ->select(
                                    'unidades.*', 'tiposdeunidade.inspecionar', 'tiposdeunidade.tipoInspecao'
                                )
                                ->where([['tiposdeunidade.inspecionar', '=',  'Sim']])
                                ->where([['tiposdeunidade.tipoInspecao', '!=',  'Monitorada']])
                                ->where([['unidades.status_unidadeDesc', '=',  $status]])
                                ->Where([['se', '=', $businessUnitUser->se]])
                                ->orderBy('seDescricao', 'desc')
                                ->orderBy('tipoOrgaoDesc', 'asc')
                                ->orderBy('descricao', 'asc')
                                ->paginate(10);


                        }
                break;
            }

           // dd($registros);
            return view('compliance.unidades.index',compact('registros'));
        }

        else
        {
            \Session::flash('mensagem',['msg'=>'Não foi possivel localizar a Unidade do Usuário atualize o Cadastro desse usuário.'
                ,'class'=>'red white-text']);
            return redirect()->route('home');
        }
    }
}
