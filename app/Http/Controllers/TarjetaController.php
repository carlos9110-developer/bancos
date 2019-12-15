<?php
namespace App\Http\Controllers;
use App\Tarjeta;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Helpers\JwtAuth;
class TarjetaController extends Controller
{

    
    public function store(Request $request){
        $token    =  $request->header('Authorization'); // con esto capturamos la autorización
        if($token!=null){ // aca solo validamos que el token existe pero falta mirar que se avalido
            $jwt = new JwtAuth();
            $validar = $jwt->verificarToken($token);
            if($validar==true){
                $tarjeta = new Tarjeta;
                $tarjeta->numero_tarjeta = $request->numero_tarjeta;
                $tarjeta->titular        = $request->titular;
                $tarjeta->saldo          = $request->saldo;
                $tarjeta->clave          = $request->clave;
                $tarjeta->save();
                $response = array(
                    'success' => true,
                    'msg'     => 'Tarjeta Registrada Correctamente'
                );
                return new JsonResponse($response);
            } else {
                return new JsonResponse(array(
                    'success' => false,
                    'mensaje' => 'El token es incorrecto, ud sera denunciado ante el ministerio de delitos informaticos :('
                ),401);
            }
        } else {
            return new JsonResponse(array(
                'success' => false,
                'mensaje' => 'Hace falta el token para poder acceder a la petición, ud sera denunciado ante el ministerio de delitos informaticos :('
            ),401); // el codigo 401 es cuando no hay autorización
        }

        
    }

    // función donde se retorna una sola tarjeta
    public function consultarSaldo(Request $request){
        $token    =  $request->header('Authorization'); // con esto capturamos la autorización
        if($token!=null){ // aca solo validamos que el token existe pero falta mirar que se avalido
            $jwt = new JwtAuth();
            $validar = $jwt->verificarToken($token);
            if($validar==true){
                if( $this->validarNumeroClave($request->numero_tarjeta,$request->clave)==1  ){
                    $response = array('success' => true,'msg' => 'El saldo de la tarjeta es $'.$this->traerSaldoTarjeta($request->numero_tarjeta));
                }else{
                    $response = array('success' => false,'msg' => 'Error, verifique el número de la tarjeta y la clave por favor');
                }
                return new JsonResponse($response);
                //return new JsonResponse($tarjetas);
            } else {
                return new JsonResponse(array(
                    'success' => false,
                    'mensaje' => 'El token es incorrecto, ud sera denunciado ante el ministerio de delitos informaticos :('
                ),401);
            }
        } else {
            return new JsonResponse(array(
                'success' => false,
                'mensaje' => 'Hace falta el token para poder acceder a la petición, ud sera denunciado ante el ministerio de delitos informaticos :('
            ),401); // el codigo 401 es cuando no hay autorización
        }

        
    }

    // validar clave y número de tarjeta
    public function validarNumeroClave($numero_tarjeta,$clave){
        if( DB::table('tarjetas')->where('numero_tarjeta',$numero_tarjeta)->where('clave',$clave)->count() > 0 ){
            return 1;
        }else{
            return 0;
        }
    }

    // función donde se trae el saldo de una determinada tarjeta
    public function traerSaldoTarjeta($numero_tarjeta){
        $saldoTarjeta = DB::table('tarjetas')->select(['saldo'])->where('numero_tarjeta',$numero_tarjeta)->get();
        $saldoTarjeta = $saldoTarjeta[0]->saldo;
        return $saldoTarjeta;
    }

    public function show(Tarjeta $tarjeta, Request $request){
        $token    =  $request->header('Authorization'); // con esto capturamos la autorización
        if($token!=null){ // aca solo validamos que el token existe pero falta mirar que se avalido
            $jwt = new JwtAuth();
            $validar = $jwt->verificarToken($token);
            if($validar==true){
                $tarjetas =  Tarjeta::all();
                return new JsonResponse(array(
                    'success' => true,
                    'mensaje' => $tarjetas
                )); 
                //return new JsonResponse($tarjetas);
            } else {
                return new JsonResponse(array(
                    'success' => false,
                    'mensaje' => 'El token es incorrecto, ud sera denunciado ante el ministerio de delitos informaticos :('
                ),401);
            }
        } else {
            return new JsonResponse(array(
                'success' => false,
                'mensaje' => 'Hace falta el token para poder acceder a la petición, ud sera denunciado ante el ministerio de delitos informaticos :('
            ),401); // el codigo 401 es cuando no hay autorización
        }  
    }


}
