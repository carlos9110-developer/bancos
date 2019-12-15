<?php

namespace App\Http\Middleware;
use Closure;
use App\Helpers\JwtAuth;
use Illuminate\Http\JsonResponse;

class VerificacionToken
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


        if($this->verificarToken($request)){
            return $next($request);
        }
        $response = array('success' => false,'msg' => 'Error, no tiene permisos para utilizar nuestra api');
        return new JsonResponse($response,401);

    }

    private function verificarToken($request)
    {
        $token    =  $request->header('Authorization'); // con esto capturamos la autorización
        if($token!=null)
        {
            $jwt = new JwtAuth();
            $validar = $jwt->verificarToken($token);
            if($validar==true)
                return true;
            return false;
        }else
        {
            return false;
        }
    }

}
