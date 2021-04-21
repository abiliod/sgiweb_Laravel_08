<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class ApiProtectedRoute extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
     try{
         JWTAuth::parseToken()->authenticate();
         $request->merge(['user' => auth('api')->user()]);
     }
     catch (\Exception $e){
         if( $e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException)
         {
             return response()->json(['status'=>'Token inválido!']);
             }
             else if( $e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException)
             {
               return response()->json(['status'=>'Token Expirado']);
             }
             else
             {
                 return response()->json(['status' => 'Autorização do token não localizada']);
             }
        }
        return $next($request);
    }
}
