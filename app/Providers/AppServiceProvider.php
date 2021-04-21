<?php

namespace App\Providers;

use Illuminate\Support\Carbon;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        Carbon::setLocale('pt_BR');
        Schema :: defaultstringLength (191);

      //  DB::connection()->setQueryGrammar(new \App\Database\Query\Grammars\MySqlGrammar);

        /**
         * Para ser armazenada adequadamente essa precisão, adicione o atributo precision (6 para a parte decimal de 6 dígitos) nas migrações, por exemplo, a migração padrão dos usuários se tornaria para armazenar a precisão de microssegundos:
         * Schema::create('users', function (Blueprint $table) {
         *        $table->timestamp('email_verified_at', 6)->nullable();
         *         $table->timestamps(6);
         * });
         */


        //
        //use Illuminate\Database\Eloquent\Builder;
        //Builder::macro('whereLike', function(string $attribute, string $searchTerm) {
          // return $this->orWhere($attribute, 'LIKE', "%{$searchTerm}%");
        //});
        //https://medium.com/@freekmurze/searching-models-using-a-where-like-query-in-laravel-243f09fdf2cc

        //Builder::macro('whereLike', function ($attributes, string $searchTerm) {
      //      $this->where(function (Builder $query) use ($attributes, $searchTerm) {
      //          foreach (array_wrap($attributes) as $attribute) {
     //               $query->orWhere($attribute, 'LIKE', "%{$searchTerm}%");
     //           }
     //       });

     //       return $this;
     //   });
    }
}
