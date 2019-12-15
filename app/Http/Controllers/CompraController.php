<?php
namespace App\Http\Controllers;
use App\Compra;
use App\Tarjeta;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Helpers\JwtAuth;

class CompraController extends Controller
{

    public function store(Request $request)
    {
        $token    =  $request->header('Authorization'); // con esto capturamos la autorización
        if($token!=null){ // aca solo validamos que el token existe pero falta mirar que se avalido
            $jwt = new JwtAuth();
            $validar = $jwt->verificarToken($token);
            if($validar==true){
                if( DB::table('tarjetas')->where('numero_tarjeta',$request->numero_tarjeta)->where('clave',$request->clave)->count() > 0 ){
                    $info_tarjeta   =  DB::table('tarjetas')->where('numero_tarjeta',$request->numero_tarjeta)->where('clave',$request->clave)->get();
                    $saldo_tarjeta  =  $info_tarjeta[0]->saldo;
                    if( $saldo_tarjeta >= $request->total_compra ){


                        $saldo_nuevo   = $saldo_tarjeta - $request->total_compra;
                        if($saldo_nuevo >= 50000){
                             DB::update('update tarjetas set saldo = ? where numero_tarjeta = ?',[$saldo_nuevo,$request->numero_tarjeta]);

                            $compras = new Compra;
                            $compras->numero_factura = rand (10000,999999.);
                            $compras->fecha          = date('Y-m-d');
                            $compras->articulos      = $request->articulos;
                            $compras->total_compra   = $request->total_compra;
                            $compras->id_tarjeta     = $info_tarjeta[0]->id;
                            $compras->save();
                            $response = array(
                            'success' => true,
                            'msg'     => 'La compra se ha registrado correctamente'
                            );
                        }else{
                           $response = array(
                            'success' => false,
                            'msg'     => 'Error, debe mantener un saldo de $50.000 '
                            );
                        }


                        return new JsonResponse($response);
                    }else{
                        $response = array(
                        'success' => false,
                        'msg'     => 'La tarjeta no tiene fondos suficientes para la compra, intente con otra tarjeta asalariado'
                        );
                        return new JsonResponse($response);
                    }
                }else{
                    $response = array(
                    'success' => false,
                    'msg'     => 'El número de tarjeta o la clave son incorrectos'
                    );
                    return new JsonResponse($response);
                }
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

    //func

    // función donde se retorna una sola tarjeta
    public function traerCompra($numero_factura){
        $compra = DB::table('compras')->where('numero_factura',$numero_factura)->get();
        return new JsonResponse($compra);
    }

    // función donde se traen todas las compras
    public function show(Compra $compra, Request $request)
    {
        $token    =  $request->header('Authorization'); // con esto capturamos la autorización
        if($token!=null){ // aca solo validamos que el token existe pero falta mirar que se avalido
            $jwt = new JwtAuth();
            $validar = $jwt->verificarToken($token);
            if($validar==true){
                $compras =  Compra::all();
                return new JsonResponse(array(
                    'success' => true,
                    'mensaje' => $compras
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
