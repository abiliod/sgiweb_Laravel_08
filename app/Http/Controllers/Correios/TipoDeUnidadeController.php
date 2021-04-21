<?php

namespace App\Http\Controllers\Correios;

use App\Http\Controllers\Controller;
use App\Models\Correios\TipoDeUnidade;
use App\Models\Correios\Unidade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TipoDeUnidadeController extends Controller
{


    public function salvar(Request $request )
    {
        dd('aki salva');
    }
    public function adicionar()
    {
        return view('compliance.tipounidades.adicionar');
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $registro = TipoDeUnidade::find($id);
        $dados = $request->all();
        $registro->codigo = $dados['codigo'];
        $registro->sigla = $dados['sigla'];
        $registro->tipodescricao = $dados['descricao'];
        $registro->inspecionar = $dados['inspecionar'];
        $registro->tipoInspecao = $dados['tipoInspecao'];

    //    dd($registro);
        $registro->update();

        \Session::flash('mensagem',['msg'=>'O Tipo de Unidade:  '.$registro->descricao.' foi atualizado com sucesso !'
            ,'class'=>'green white-text']);
        return redirect()->route('compliance.tipounidades');
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $registro = TipoDeUnidade::find($id) ;
        return view('compliance.tipounidades.editar',compact('registro'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function search (Request $request )
    {
        if($request->all()['search']==NULL)
        {
        $registros = DB::table('tiposdeunidade')
            ->where([
                ['tipoInspecao', '=',$request->all()['tipoInspecao']  ]
            ])
            ->orderBy('tipodescricao' , 'asc')
            ->paginate(10);
        }
        else
        {
            $registros = DB::table('tiposdeunidade')
                ->where([
                    ['tipoInspecao', '=',$request->all()['tipoInspecao']  ]
                ])
                ->where('codigo', 'like', '%' . trim($request->all()['search']) . '%')
                ->Orwhere('sigla', 'like', '%' . trim($request->all()['search']) . '%')
                ->Orwhere('tipodescricao', 'like', '%' . trim($request->all()['search']) . '%')
                ->Orwhere('inspecionar', 'like', '%' . trim($request->all()['search']) . '%')
                ->orderBy('tipodescricao' , 'asc')
                ->paginate(10);
        }
        return view('compliance.tipounidades.index',compact('registros'));
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $registros = DB::table('tiposdeunidade')
            ->orderBy('tipodescricao' , 'asc')
            ->paginate(10);
        return view('compliance.tipounidades.index',compact('registros'));
    }
}
