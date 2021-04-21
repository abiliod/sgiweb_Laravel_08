<?php
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use app\Papel;
use App\Permissao;

class PapelController extends Controller{

    public function index(){
    	if(!auth()->user()->can('papel_listar')){
            return redirect()->route('home');
        }

        $registros = Papel::all();
    	return view('admin.papel.index',compact('registros'));
    }
    public function adicionar()
    {
    	if(!auth()->user()->can('papel_adicionar')){
            return redirect()->route('home');
        }
        return view('admin.papel.adicionar');
    }
    public function salvar(Request $request)
    {
    	if(!auth()->user()->can('papel_adicionar')){
            return redirect()->route('home');
        }
        Papel::create($request->all());
    	return redirect()->route('admin.papel');
    }
    public function editar($id)
    {
    	if(!auth()->user()->can('papel_editar')){
            return redirect()->route('home');
        }
        if(Papel::find($id)->nome == "admin"){
    		return redirect()->route('admin.papel');
    	}
    	$registro = Papel::find($id);
    	return view('admin.papel.editar',compact('registro'));
    }
    public function atualizar(Request $request,$id){
    	if(!auth()->user()->can('papel_editar')){
            return redirect()->route('home');
        }
        if(Papel::find($id)->nome == "admin"){
    		return redirect()->route('admin.papel');
    	}
    	Papel::find($id)->update($request->all());
    	return redirect()->route('admin.papel');
    }
    public function deletar($id)
    {
    	if(!auth()->user()->can('papel_deletar')){
            return redirect()->route('home');
        }
        if(Papel::find($id)->nome == "admin"){
    		return redirect()->route('admin.papel');
    	}
    	Papel::find($id)->delete();
    	return redirect()->route('admin.papel');
    }

    public function permissao($id)
    {
        if(!auth()->user()->can('papel_editar')){
            return redirect()->route('home');
        }
        $papel = Papel::find($id);

        // 10/12/20 aqui tem um bug  a corrigir testar selecionar apenas permissões não concedidas ainda ao papel

        $permissao = Permissao::all();

        return view('admin.papel.permissao',compact('papel','permissao'));
    }
    public function salvarPermissao(Request $request, $id)
    {
        if(!auth()->user()->can('papel_editar')){
            return redirect()->route('home');
        }

        // 10/12/20 aqui tem um bug  a corrigir testar se não existe para não dar erro de violação de acesso sql

        $papel = Papel::find($id);
        $permissao = Permissao::find($request['permissao_id']);

        // dd('aqui tem bug');
        $papel->adicionarPermissao($permissao);
        return redirect()->back();
    }

    public function removerPermissao($id,$id_permissao) //debugar  elemento não esta sendo utilizado  18/12/2020- abilio
    {
        if(!auth()->user()->can('papel_editar')){
            return redirect()->route('home');
        }
        $papel = Papel::find($id);
        $permissao = Permissao::find($id_permissao);
        $papel->removerPermissao($permissao);

        return redirect()->back();
    }
}
