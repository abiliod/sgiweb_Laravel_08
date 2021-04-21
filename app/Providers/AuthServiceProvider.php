<?php

namespace App\Providers;

use Illuminate\Contracts\Auth\Access\Gate as GateContract;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

use Illuminate\Support\Facades\Gate;

use Illuminate\Support\Facades\DB as Schema;

use App\Permissao;

class AuthServiceProvider extends ServiceProvider {
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [ 'App\Model' => 'App\Policies\ModelPolicy', ];

    public function getPermissoes() {
        return Permissao::with('papeis')->get();
    }

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(GateContract $gate){

    /**
    * Register any authentication / authorization services.
    *
    *
    */
        foreach ($this->getPermissoes() as $permissao) {
            $gate->define($permissao->nome,
                function($user) use($permissao){
                    return $user->existePapel($permissao->papeis) || $user->existeAdmin();
                }
            );
        }
   }
}
