<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\PedidosPreparados;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PedidosPreparadosController extends Controller
{
    //función donde se registra la prepariación de un pedido
    public function registrarPreparacionPedido(Request $request)
    {
        PedidosPreparados::create([
            'id_pedido'=>$request->id_pedido,
            'id_cocinero'=>$request->id_cocinero
        ]);
    }

    // función donde se trae la información de los pedidos preparados por un determinado cocinero
    public function listarPlatosPreparadosCocinero(Request $request)
    {
        if(!is_null($request->id_cocinero)){
            $result = DB::table('pedidos_preparados')
            ->join('users','users.id','=','pedidos_preparados.id_cocinero')
            ->join('pedidos','pedidos.id','=','pedidos_preparados.id_pedido')
            ->join('detalle_pedido','detalle_pedido.id_pedido','=','pedidos.id')
            ->join('comidas_bebidas','comidas_bebidas.id','=','detalle_pedido.id_comidas_bebidas')
            ->select('pedidos.id as id_pedido','pedidos.mesa','pedidos.piso','users.name AS cocinero','comidas_bebidas.nombre as plato')
            ->where('pedidos_preparados.id_cocinero',$request->id_cocinero)->where('comidas_bebidas.tipo','1')
            ->get();
            return new JsonResponse($result);
        } else{
            $response = array("success"=>false,"msg"=>"Error, no se envio la información requerida listar los pedidos preparados por el cocinero");
            return new JsonResponse($response);
        }
    }
}
