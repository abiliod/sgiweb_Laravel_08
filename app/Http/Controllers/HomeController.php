<?php

namespace App\Http\Controllers;


/**
   * Abilio 29-02-2020 Inclusão de Funcionalidade
   * App\Papel
   * App\Papel_user;
   * App\User;
   * Utilizar as classes para atribuir papel inicial "Cliente/Fornecedor" para o novo usuário
   **/
  use App\Papel;
  use App\Papel_user;

//  use App\Models\Pessoas\Pessoa;
  use Auth;

class HomeController extends Controller {
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct() {

        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

     public function index()
     {
         $activeUser = Auth::user()->activeUser;
         if($activeUser==true)
         {
             $user_id = Auth::user()->id;
             $user = Papel_user::where('user_id', $user_id)->first();
             if (!$user) { //usuario sem papel
                 //verifica se o papel existe
                 $papel = Papel::where('nome', "Cliente/Fornecedor")->first();
                 if ($papel) {
                     // papel existe atribui papel ao usuário
                     Papel_user::firstOrCreate(
                         ['user_id' => $user_id], ['papel_id' => $papel->id]
                     );
                     // direciona usuario para a view padrão do PAPEL
                      return view('home');
                     //return view('admin.pessoas.entradaCPF_CNPJ', compact('registro'));
                 }
             } else {
//                 verifica qual o papel do usuario
                 $papel_id = Papel::where('id', $user->papel_id)->first();
//                  direciona usuario para a view Dashboard padrão do perfil do usuário
                 switch ($papel_id->nome)
                 {
                     case $papel_id->nome:"admin";
                         return view('principal/home'); // criar a view padrão
                         break;
                    case $papel_id->nome:"gestor_ac";
                        return view('principal/home', compact('papel_id')); // criar a view padrão
                        break;
                     case $papel_id->nome:"gestor_cvco";
                         return view('principal/home', compact('papel_id')); // criar a view padrão
                         break;
                     case $papel_id->nome:"gestor_scoi";
                         return view('principal/home', compact('papel_id')); // criar a view padrão
                         break;
                     case $papel_id->nome:"Inspetor";
                         return view('principal/home', compact('papel_id')); // criar a view padrão
                         break;
                     case $papel_id->nome:"Cliente/Fornecedor";
                         return view('principal/home', compact('papel_id')); // direcionar para o carrinho
                         break;
                     default:
                         return view('principal/home', compact('papel_id'));
                 }
             }
         }
         else
         {
             \Session::flash('mensagem',['msg'=>'Usuário Inativo. Contate o administrador'
                 ,'class'=>'red white-text']);
             return  view('principal/welcome');
         }
    }
}
