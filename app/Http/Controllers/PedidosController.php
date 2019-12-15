<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Pedido;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
/**
 * Estados Pedidos
 * 1 solicitado
 * 2 preparado
 * 3 servido
 * 4 facturado
 * 5 cancelado
 */
class PedidosController extends Controller
{
    // función donde se registran los pedidos
    public function insertarPedidos(Request $request)
    {
        if(!is_null($request->mesa) && !is_null($request->piso) && !is_null($request->id_mesero)
         && !is_null($request->valor) && !is_null($request->detallePedido) )
        {
            DB::beginTransaction();
            try {
                $id_pedido = Pedido::create([
                    'mesa'=>$request->mesa,
                    'piso'=>$request->piso,
                    'id_mesero'=>$request->id_mesero,
                    'estado'=>"1",
                    'valor'=>$request->valor
                ]);
                foreach ($request->detallePedido as $key => $value) {
                    DB::insert('INSERT INTO detalle_pedido (id_pedido,id_comidas_bebidas,cantidad,valor)
                    VALUES (:id_pedido,:id_comidas_bebidas,:cantidad,:valor)',
                    ['id_pedido'=>$id_pedido->id,'id_comidas_bebidas'=>$value['id_comidas_bebidas'],'cantidad'=>$value['cantidad'],'valor'=>$value['valor']]);
                }
                DB::commit();
                $success = true;
            } catch (\Exception $e) {
                $success = false;
                DB::rollback();
            }
            if($success){
                $response = array("success"=>true,"msg"=>"Pedido registrado correctamente");
            } else{
                $response = array("success"=>false,"msg"=>"Error, no fue posible registrar el pedido, intentelo de nuevo");
            }
        } else {
            $response = array("success"=>false,"msg"=>"Error, no se envio la información requerida para registrar el pedido");
        }
        return new JsonResponse($response);
    }

    // función donde se pasa el pedido de solicitado a preparado
    public function pedidoPreparado(Request $request)
    {
        if(!is_null($request->id_pedido) && !is_null($request->id_cocinero) )
        {
            $pedidosPreparados = new PedidosPreparadosController();
            DB::beginTransaction();
            try {
                Pedido::where('id', $request->id_pedido)->update(['estado' => '2']);
                $pedidosPreparados->registrarPreparacionPedido($request);
                DB::commit();
                $success = true;
            } catch (\Exception $e) {
                $success = false;
                DB::rollback();
            }
            if($success){
                $response = array("success"=>true,"msg"=>"El pedido paso a estado preparado exitosamente");
            } else{
                $response = array("success"=>false,"msg"=>"Error, no fue posible actualizar el estado del pedido, intentelo de nuevo");
            }
        } else{
            $response = array("success"=>false,"msg"=>"Error, no se envio la información requerida para actualizar el estado del pedido");
        }
        return new JsonResponse($response);
    }

    // función donde se pasa un pedido a estado servido
    public function pedidoServido(Request $request)
    {
        if(!is_null($request->id_pedido) )
        {
            $result            = Pedido::where('id', $request->id_pedido)->update(['estado' => '3']);
            if($result==1){
                $response = array("success"=>true,"msg"=>"El pedido paso a estado servido exitosamente");
            } else{
                $response = array("success"=>false,"msg"=>"Error, no fue posible actualizar el estado del pedido, intentelo de nuevo");
            }
        } else{
            $response = array("success"=>false,"msg"=>"Error, no se envio la información requerida para actualizar el estado del pedido");
        }
        return new JsonResponse($response);
    }

    // función para cancelar un determinado pedido
    public function cancelarPedido(Request $request)
    {
        if(!is_null($request->id_pedido) )
        {
            $result            = Pedido::where('id', $request->id_pedido)->where('estado','1')->update(['estado' => '5']);
            if($result==1){
                $response = array("success"=>true,"msg"=>"El pedido se cancelo exitosamente");
            } else{
                $response = array("success"=>false,"msg"=>"Error, no fue posible cancelar el pedido, verifique que se encuentre en estado solicitado");
            }
        } else{
            $response = array("success"=>false,"msg"=>"Error, no se envio la información requerida para cancelar el pedido");
        }
        return new JsonResponse($response);
    }

    // función donde se a facturado un detemrinado pedido
    public function facturacionPedido(Request $request)
    {
        if(!is_null($request->id_pedido) && !is_null($request->id_cajero) )
        {
            $result            = Pedido::where('id',$request->id_pedido)->select('valor')->first();
            if(is_object($result)){
                $facturacion = new FacturacionController();
                DB::beginTransaction();
                try {
                    Pedido::where('id',$request->id_pedido)->update(['estado' => '4']);
                    $facturacion->facturacion($request->id_pedido,$result->valor,$request->id_cajero);
                    DB::commit();
                    $success = true;
                } catch (\Exception $e) {
                    $success = false;
                    DB::rollback();
                }
                if($success){
                    $response = array("success"=>true,"msg"=>"Facturación pedido registrada con exito");
                } else{
                    $response = array("success"=>false,"msg"=>"Error, no fue posible registrar la facturación del pedido");
                }
            } else{
                $response = array("success"=>false,"msg"=>"Error, no se encontro ningún pedido con el id enviado");
            }
        } else{
            $response = array("success"=>false,"msg"=>"Error, no se envio la información requerida para realizar la facturación");
        }
        return new JsonResponse($response);
    }

    //listar todos los pedidos que estan en estado solicitado que lo puede usar el usuario tipo administrador
    public function listarPedidosSolicitados()
    {
        $result = DB::table('pedidos')
            ->join('users','users.id','=','pedidos.id_mesero')
            ->select('pedidos.id','pedidos.mesa','pedidos.piso','pedidos.valor','users.name AS mesero')
            ->where('pedidos.estado','1')
            ->get();
        return new JsonResponse($result);
    }

    //listar los pedidos en estado solicitado de un determinado mesero, es usado por el administrados y el mesero
    public function listarPedidosSolicitadosMesero(Request $request)
    {
        if(!is_null($request->id_mesero)){
            $result = DB::table('pedidos')
            ->join('users','users.id','=','pedidos.id_mesero')
            ->select('pedidos.id','pedidos.mesa','pedidos.piso','pedidos.valor')
            ->where('pedidos.estado','1')->where('pedidos.id_mesero',$request->id_mesero)
            ->get();
            return new JsonResponse($result);
        } else{
            $response = array("success"=>false,"msg"=>"Error, no se envio la información requerida listar los pedidos solicitados");
            return new JsonResponse($response);
        }

    }

    //listar todos los pedidos que estan en estado preparado, es usado por el administrador
    public function listarPedidosPreparados()
    {
        $result = DB::table('pedidos')
            ->join('users','users.id','=','pedidos.id_mesero')
            ->select('pedidos.id','pedidos.mesa','pedidos.piso','pedidos.valor','users.name AS mesero')
            ->where('pedidos.estado','2')
            ->get();
        return new JsonResponse($result);
    }

    //listar los pedidos en estado solicitado de un determinado mesero, es usado por el administrador y el mesero
    public function listarPedidosPreparadosMesero(Request $request)
    {
        if(!is_null($request->id_mesero)){
            $result = DB::table('pedidos')
            ->join('users','users.id','=','pedidos.id_mesero')
            ->select('pedidos.id','pedidos.mesa','pedidos.piso','pedidos.valor','users.name AS mesero')
            ->where('pedidos.estado','2')->where('pedidos.id_mesero',$request->id_mesero)
            ->get();
            return new JsonResponse($result);
        } else{
            $response = array("success"=>false,"msg"=>"Error, no se envio la información requerida listar los pedidos preparados");
            return new JsonResponse($response);
        }
    }

    //listar todos los pedidos que estan en estado preparado, es usado por el administrador
    public function listarPedidosServido()
    {
        $result = DB::table('pedidos')
            ->join('users','users.id','=','pedidos.id_mesero')
            ->select('pedidos.id','pedidos.mesa','pedidos.piso','pedidos.valor','users.name AS mesero')
            ->where('pedidos.estado','3')
            ->get();
        return new JsonResponse($result);
    }

    //listar los pedidos en estado solicitado de un determinado mesero, es usado por el administrador y el mesero
    public function listarPedidosServidoMesero(Request $request)
    {
        if(!is_null($request->id_mesero)){
            $result = DB::table('pedidos')
            ->join('users','users.id','=','pedidos.id_mesero')
            ->select('pedidos.id','pedidos.mesa','pedidos.piso','pedidos.valor','users.name AS mesero')
            ->where('pedidos.estado','3')->where('pedidos.id_mesero',$request->id_mesero)
            ->get();
            return new JsonResponse($result);
        } else{
            $response = array("success"=>false,"msg"=>"Error, no se envio la información requerida listar los pedidos servidos");
            return new JsonResponse($response);
        }
    }

    //listar todos los pedidos que estan en estado preparado, es usado por el administrador
    public function listarPedidosFacturado()
    {
        $result = DB::table('pedidos')
        ->join('users','users.id','=','pedidos.id_mesero')
        ->select('pedidos.id','pedidos.mesa','pedidos.piso','pedidos.valor','users.name AS mesero')
        ->where('pedidos.estado','4')
        ->get();
        return new JsonResponse($result);
    }

    //listar los pedidos en estado solicitado de un determinado mesero, es usado por el administrador
    public function listarPedidosFacturadoMesero(Request $request)
    {
        if(!is_null($request->id_mesero)){
            $result = DB::table('pedidos')
            ->join('users','users.id','=','pedidos.id_mesero')
            ->select('pedidos.id','pedidos.mesa','pedidos.piso','pedidos.valor','users.name AS mesero')
            ->where('pedidos.estado','4')->where('pedidos.id_mesero',$request->id_mesero)
            ->get();
            return new JsonResponse($result);
        } else{
            $response = array("success"=>false,"msg"=>"Error, no se envio la información requerida listar los pedidos servidos");
            return new JsonResponse($response);
        }
    }

    // función donde se lista el detalle de un detemrinado pedido
    function listarDetallePedido(Request $request)
    {
        if(!is_null($request->id_pedido)){
            $result = DB::table('detalle_pedido')
            ->join('comidas_bebidas','comidas_bebidas.id','=','detalle_pedido.id_comidas_bebidas')
            ->select('comidas_bebidas.nombre as producto','comidas_bebidas.precio')
            ->where('detalle_pedido.id_pedido',$request->id_pedido)
            ->get();
            return new JsonResponse($result);
        } else{
            $response = array("success"=>false,"msg"=>"Error, no se envio la información requerida listar el detalle del pedido");
            return new JsonResponse($response);
        }
    }


}
