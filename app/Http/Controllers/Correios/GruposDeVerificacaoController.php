<?php

namespace App\Http\Controllers\Correios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use Illuminate\Support\Facades\DB;
use App\Models\Correios\TesteDeVerificacao;
use App\Models\Correios\GrupoDeVerificacao;
use App\Models\Correios\TipoDeUnidade;
use App\Http\Requests\Compliance\SalvarGruposDeVerificacao;

class GruposDeVerificacaoController extends Controller
{

    public function destroy($id) {

        $testeDeVerificacao = DB::table('testesDeVerificacao')
           ->select('numeroDoTeste','teste')
           ->where([
                ['grupoVerificacao_id', '=', $id]
        ])
        ->first();

        if($testeDeVerificacao){
            \Session::flash('mensagem',['msg'=>'Registro Não pode ser excluido, pois está  Relacionado ao item de Verificação Número. '.$testeDeVerificacao->numeroDoTeste.''
            ,'class'=>'red white-text']);
    		return redirect()->route('compliance.grupoVerificacao');
        }else{
            $registro = GrupoDeVerificacao::find($id);
             $registro->delete();
             \Session::flash('mensagem',['msg'=>'Registro deletado com sucesso!'
             ,'class'=>'green white-text']);
             return redirect()->route('compliance.grupoVerificacao');
        }

    }


    public function salvar(SalvarGruposDeVerificacao $request)
    //public function salvar(Request $request)

    {
        GrupoDeVerificacao::create($request->all());
        \Session::flash('mensagem',['msg'=>'Grupo de Verificação Criado com sucesso !'
                  ,'class'=>'green white-text']);
        return redirect()->route('compliance.grupoVerificacao');
    }

    public function adicionar()
    {

        $tiposDeUnidade = DB::table('tiposdeunidade')
            ->join('gruposdeverificacao',
                'tiposdeunidade.id',
                '=',
                'gruposdeverificacao.tipoUnidade_id')
            ->select('tipoUnidade_id','sigla','tipodescricao')
            ->groupBy('tipodescricao')
            ->get();

        return view('compliance.grupoVerificacao.adicionar', compact( 'tiposDeUnidade'));
    }


    public function atualizar (Request $request, $id)
    {
        $registro = GrupoDeVerificacao::find($id);
        $dados = $request->all();
        $registro->ciclo =  $dados['ciclo'];
        $registro->tipoUnidade_id =  $dados['tipoUnidade_id'];
        $registro->numeroGrupoVerificacao =  $dados['numeroGrupoVerificacao'];
        $registro->nomegrupo =  $dados['nomegrupo'];
        //dd($registro);
        $registro->update();
        \Session::flash('mensagem',['msg'=>'Grupo de Verificação atualizado com sucesso !'
                  ,'class'=>'green white-text']);
        return redirect()->route('compliance.grupoVerificacao');
    }

    public function edit($id)
    {
        $registro = GrupoDeVerificacao::find($id);
        $tiposDeUnidade = TipoDeUnidade::all();
        return view('compliance.grupoVerificacao.editar',compact('registro', 'tiposDeUnidade'));
    }

    public function search (Request $request)
    {
        $dados = $request->all();
       // dd($dados);
        if ($dados['tipoUnidade_id'] == 0){
            \Session::flash('mensagem',['msg'=>'Tipo de Unidade é Requerido !'
                  ,'class'=>'red white-text']);
        return redirect()->back();
        }

        if (($dados['tipoUnidade_id'] >= 1)&&($dados['tipoVerificacao']!==0 )){
            if (($dados['nomegrupo'] == "0")||($dados['nomegrupo'] == "Selecione um Grupo de Unidade") ) { $dados['nomegrupo'] = ""; }

            $registros = DB::table('tiposdeunidade')
            ->join('gruposdeverificacao', 'tiposdeunidade.id',  '=',   'tipoUnidade_id')
                ->where([
                        ['gruposdeverificacao.tipoUnidade_id', '=', $dados['tipoUnidade_id']]
                ])
                ->where([
                        ['gruposdeverificacao.tipoVerificacao', '=', $dados['tipoVerificacao']]
                ])
                ->where([
                    ['gruposdeverificacao.ciclo', '=', $dados['ciclo']]
                ])

                ->where([
                        ['gruposdeverificacao.nomegrupo', 'like','%' . $dados['nomegrupo'] .'%']
                ])
                ->paginate(15);

        }else if ($dados['tipoVerificacao']!==0 ) {
            $registros = DB::table('tiposdeunidade')
            ->join('gruposdeverificacao', 'tiposdeunidade.id',  '=',   'tipoUnidade_id')
            ->where([
                ['gruposdeverificacao.nomegrupo', '=', $dados['nomegrupo']]
            ])
            ->where([
                ['gruposdeverificacao.ciclo', '=', $dados['ciclo']]
            ])

            ->paginate(15);
        }

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
           // ->groupBy('tipoUnidade_id')
      //  ->get();

        return view('compliance.grupoVerificacao.index',compact('registros', 'tiposDeUnidade','gruposdeverificacao','dados'));
    }

    public function index()
    {
        $registros = DB::table('tiposdeunidade')
        ->join('gruposdeverificacao', 'tiposdeunidade.id',  '=',   'tipoUnidade_id')
        ->paginate(15);


        $gruposdeverificacao   = DB::table('gruposdeverificacao')
        ->select('nomegrupo')
        ->groupBy('nomegrupo')
        ->get();

        //injetando indice zero para alimentar o select com a primeira opção
        $dados = TipoDeUnidade::find(1);
        $dados->id= 0;
        $dados->sigla='Selecione um ';
        $dados->descricao='Tipo de Unidade';

        $tiposDeUnidade = DB::table('tiposdeunidade')
            ->join('gruposdeverificacao',
                'tiposdeunidade.id',
                '=',
                'gruposdeverificacao.tipoUnidade_id')
            ->select('tipoUnidade_id','sigla','tipodescricao')
            ->groupBy('tipodescricao')
            ->get();
        return view('compliance.grupoVerificacao.index',compact('registros', 'tiposDeUnidade','gruposdeverificacao','dados'));
    }
}
