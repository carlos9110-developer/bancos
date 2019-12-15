<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\ComidasBebidas;
use Illuminate\Http\JsonResponse;

class ComidasBebidasController extends Controller
{
    // metodo donde se traen todas las comidas y bebidas de la base de datos
    public function listarComidasBebidas()
    {
        $result = ComidasBebidas::select('id','nombre','precio')->get();
        return new JsonResponse($result);
    }

    // metodo donde se traen todas las comidas  de la base de datos
    public function listarComidas()
    {
        $result = ComidasBebidas::select('id','nombre','precio')->where('tipo','1')->get();
        return new JsonResponse($result);
    }

    // metodo donde se traen todas las  bebidas de la base de datos
    public function listarBebidas()
    {
        $result = ComidasBebidas::select('id','nombre','precio')->where('tipo','2')->get();
        return new JsonResponse($result);
    }

    // metodo para registrar las comidas y bebidas de la base de datos
    public function registroComidasBebidas(Request $request)
    {
        if(!is_null($request->nombre) && !is_null($request->precio) && !is_null($request->tipo) )
        {
            DB::beginTransaction();
            try {
                ComidasBebidas::create([
                    'nombre'=>$request->nombre,
                    'precio'=>$request->precio,
                    'tipo'=>$request->tipo
                ]);
                DB::commit();
                $success = true;
            } catch (\Exception $e) {
                $success = false;
                DB::rollback();
            }
            if($success){
                $response = array("success"=>true,"msg"=>"Registro realizado exitosamente");
            } else{
                $response = array("success"=>false,"msg"=>"Error, no fue posible realizar el registro, se encontró un producto con el mismo nombrep");
            }
        } else {
            $response = array("success"=>false,"msg"=>"Error, no se envio la información requerida para realizar el registro");
        }
        return new JsonResponse($response);
    }

    // metodo para obtener la información de un determinado producto
    public function informacionProducto(Request $request)
    {
        if (!is_null($request->id_producto)){
            $result = ComidasBebidas::select('id','nombre','precio','tipo')->where('id',$request->id_producto)->first();
            return new JsonResponse($result);
        } else{
            $response = array("success"=>false,"msg"=>"Error, no se envio la información requerida para consultar la información del producto");
            return new JsonResponse($response);
        }
    }
}
