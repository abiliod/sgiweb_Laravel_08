<?php

namespace App\Http\Controllers\Correios;

use App\Http\Controllers\Controller;
use App\Models\Correios\GrupoDeVerificacao;
use Illuminate\Http\Request;

//use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

use App\Models\Correios\TesteDeVerificacao;
//use App\Models\Correios\GrupoDeVerificacao;
use App\Models\Correios\TipoDeUnidade;
use App\Http\Requests\Compliance\SalvarTesteDeVerificacao;

class RelatoController extends Controller
{

    public function destroy($id) {

//        $testeDeVerificacao = DB::table('testesDeVerificacao')
 //          ->select('numeroDoTeste','teste')
  //         ->where([
   //             ['grupoVerificacao_id', '=', $id]
    //    ])
     //   ->first();

 //       if($testeDeVerificacao){
 //           \Session::flash('mensagem',['msg'=>'Registro Não pode ser excluido, pois está  Relacionado ao item de Verificação Número. '.$testeDeVerificacao->numeroDoTeste.''
 //           ,'class'=>'red white-text']);
 //   		return redirect()->route('compliance.grupoVerificacao');
 //       }else{
  //      }


      //      $registro = TesteDeVerificacao::find($id);
        //     $registro->delete();
             \Session::flash('mensagem',['msg'=>'Registro Nâo deletado! Funcionalidade não disponibilizada  por motivos  de segurança.'
             ,'class'=>'red white-text']);
             return redirect()->route('compliance.relatos');


    }

    public function salvar(SalvarTesteDeVerificacao $request)
    {
        TesteDeVerificacao::create($request->all());
        \Session::flash('mensagem',['msg'=>'Teste de Inspecao Criado com sucesso !'
                  ,'class'=>'green white-text']);
        return redirect()->route('compliance.relatos');
    }

    public function adicionar()
    {
        $gruposdeverificacao = DB::table('gruposdeverificacao')
        ->join('tiposdeunidade', 'tiposdeunidade.id',  '=',   'gruposdeverificacao.tipoUnidade_id')
        ->select('gruposdeverificacao.id','ciclo','tipoVerificacao','sigla','tipodescricao','numeroGrupoVerificacao','nomegrupo')
        -> orderBy ('ciclo', 'ASC')
        -> orderBy ('tipoUnidade_id', 'ASC')
        -> orderBy ('numeroGrupoVerificacao', 'ASC')
        ->get();
        return view('compliance.relatos.adicionar',compact('gruposdeverificacao'));
    }

    public function atualizar (Request $request, $id)
    {
        $registro =  TesteDeVerificacao::find($id);
        $dados = $request->all();
        $registro->grupoVerificacao_id =  $dados['grupoVerificacao_id'];
        $registro->numeroDoTeste =  $dados['numeroDoTeste'];
        $registro->inspecaoObrigatoria =  $dados['inspecaoObrigatoria'];
        $registro->teste =  $dados['teste'];
        $registro->ajuda =  $dados['ajuda'];
        $registro->amostra =  $dados['amostra'];
        $registro->norma =  $dados['norma'];
        $registro->sappp =  $dados['sappp'];
        $registro->tabela_CFP =  $dados['tabela_CFP'];
        $registro->impactoFinanceiro =  $dados['impactoFinanceiro'];
        $registro->riscoFinanceiro =  $dados['riscoFinanceiro'];
        $registro->descumprimentoLeisContratos =  $dados['descumprimentoLeisContratos'];
        $registro->descumprimentoNormaInterna =  $dados['descumprimentoNormaInterna'];
        $registro->riscoSegurancaIntegridade =  $dados['riscoSegurancaIntegridade'];
        $registro->riscoImgInstitucional =  $dados['riscoImgInstitucional'];
        $registro->totalPontos = $dados['totalPontos'];
        $registro->consequencias = $dados['consequencias'];
        $registro->roteiroConforme = $dados['roteiroConforme'];
        $registro->roteiroNaoConforme = $dados['roteiroNaoConforme'];
        $registro->roteiroNaoVerificado = $dados['roteiroNaoVerificado'];
        $registro->itemanosanteriores = $dados['itemanosanteriores'];
        $registro->orientacao = $dados['orientacao'];
        $registro->preVerificar = $dados['preVerificar'];
        $registro->update();

        \Session::flash('mensagem',['msg'=>'Teste de Verificação atualizado com sucesso !'
        ,'class'=>'green white-text']);

        $grupoVerificacao = GrupoDeVerificacao::find($registro->grupoVerificacao_id);
        $registros = DB::table('tiposdeunidade')
            ->join('gruposdeverificacao', 'tiposdeunidade.id',  '=',   'tipoUnidade_id')
            ->join('testesdeverificacao', 'grupoVerificacao_id', '=', 'gruposdeverificacao.id')
            ->where([
                ['gruposdeverificacao.id', '=', $grupoVerificacao->id ]
            ])
            ->paginate(100);
        $gruposdeverificacao = DB::table('gruposdeverificacao')
            ->select('nomegrupo')
            ->groupBy('nomegrupo')
            ->get();
        $tiposDeUnidade = DB::table('tiposdeunidade')
            ->join('gruposdeverificacao',
                'tiposdeunidade.id',
                '=',
                'gruposdeverificacao.tipoUnidade_id')
            ->select('tipoUnidade_id','sigla','tipodescricao')
            ->groupBy('tipodescricao')
            ->get();
        $dados = TipoDeUnidade::find($grupoVerificacao->tipoUnidade_id);
        return view('compliance.relatos.index',compact('registros', 'tiposDeUnidade','gruposdeverificacao','dados'));
    }

    public function edit($id)
    {
        $registro = TesteDeVerificacao::find($id);
        $gruposdeverificacao = DB::table('gruposdeverificacao')
        ->join('tiposdeunidade', 'tiposdeunidade.id',  '=',   'gruposdeverificacao.tipoUnidade_id')
        ->select('gruposdeverificacao.id','ciclo','tipoVerificacao','sigla','tipodescricao','numeroGrupoVerificacao','nomegrupo')
        -> orderBy ('ciclo', 'ASC')
        -> orderBy ('tipoUnidade_id', 'ASC')
        -> orderBy ('numeroGrupoVerificacao', 'ASC')
        ->get();

        $grupoVerificacao = GrupoDeVerificacao::find($registro->grupoVerificacao_id);
        $tipodeUnidade = $grupoVerificacao->tipoUnidade_id;
        return view('compliance.relatos.editar',compact('registro','tipodeUnidade','gruposdeverificacao'));
    }

    public function search (Request $request) {
        $dados = $request->all();

        if ($dados['nomegrupo'] == null) $dados['nomegrupo'] ="";

        if (($dados['tipoUnidade_id'] >= "1")&&($dados['tipoVerificacao'] == null)){
            \Session::flash('mensagem',['msg'=>'Tipo de Verificação é Requerido !'
                  ,'class'=>'red white-text']);
            return redirect()->back();
        }

        if (($dados['tipoUnidade_id'] == "0")&&($dados['tipoVerificacao'] == null)){
            \Session::flash('mensagem',['msg'=>'Tipo de Unidade e Tipo de Verificação é Requerido !'
                  ,'class'=>'red white-text']);
            return redirect()->back();
        }else if($dados['tipoUnidade_id'] >= "1"){
                        //dd("elseif");
                        $registros = DB::table('tiposdeunidade')
                        ->join('gruposdeverificacao', 'tiposdeunidade.id',  '=',   'tipoUnidade_id')
                        ->join('testesdeverificacao', 'grupoVerificacao_id', '=', 'gruposdeverificacao.id')
                        ->where([
                                ['gruposdeverificacao.tipoUnidade_id', '=', $dados['tipoUnidade_id'] ]
                                ])
                        ->where([
                                ['gruposdeverificacao.tipoVerificacao', '=', $dados['tipoVerificacao'] ]
                                ])
                        ->where([
                                ['gruposdeverificacao.nomegrupo', 'like','%' . $dados['nomegrupo'] .'%' ]
                                ])
                        ->paginate(100);
                }else{
            //   dd('else');
                $registros = DB::table('tiposdeunidade')
                ->join('gruposdeverificacao', 'tiposdeunidade.id',  '=',   'tipoUnidade_id')
                ->join('testesdeverificacao', 'grupoVerificacao_id', '=', 'gruposdeverificacao.id')
                ->where([
                        ['gruposdeverificacao.tipoVerificacao', '=', $dados['tipoVerificacao'] ]
                        ])
                ->where([
                        ['gruposdeverificacao.nomegrupo', 'like','%' . $dados['nomegrupo'] .'%' ]
                        ])
                ->paginate(100);
        }
        $gruposdeverificacao = DB::table('gruposdeverificacao')
            ->select('nomegrupo')
            ->groupBy('nomegrupo')
            ->orderBy('nomegrupo')
            ->get();

        $tiposDeUnidade = DB::table('tiposdeunidade')
            ->join('gruposdeverificacao',
                'tiposdeunidade.id',
                '=',
                'gruposdeverificacao.tipoUnidade_id')
            ->select('tipoUnidade_id','sigla','tipodescricao')
            ->groupBy('tipodescricao')
            ->get();

        $dados = TipoDeUnidade::find($request['tipoUnidade_id']);



        return view('compliance.relatos.index',compact('registros', 'tiposDeUnidade','gruposdeverificacao','dados'));
    }

    public function index() {

        $registros = DB::table('tiposdeunidade')
            ->join('gruposdeverificacao', 'tiposdeunidade.id',  '=',   'tipoUnidade_id')
            ->join('testesdeverificacao', 'grupoVerificacao_id', '=', 'gruposdeverificacao.id')
            ->paginate(15);

        $gruposdeverificacao = DB::table('gruposdeverificacao')
            ->select('nomegrupo')
            ->orderBy('nomegrupo')
            ->groupBy('nomegrupo')
            ->get();

        $dados = TipoDeUnidade::find(1);
        $dados->id= 0;
        $dados->sigla='Selecione um ';
        $dados->tipodescricao='Tipo de Unidade';
        $dados->nomegrupo='Selecione um Grupo de Unidade';


        $tiposDeUnidade = DB::table('tiposdeunidade')
            ->join('gruposdeverificacao',
                'tiposdeunidade.id',
                '=',
                'gruposdeverificacao.tipoUnidade_id')
            ->select('tipoUnidade_id','sigla','tipodescricao')
            ->groupBy('tipodescricao')
            ->get();

        return view('compliance.relatos.index',compact('registros', 'tiposDeUnidade','gruposdeverificacao','dados'));
    }
}
