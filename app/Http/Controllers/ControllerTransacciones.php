<?php
namespace App\Http\Controllers;

use App\Transacciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class ControllerTransacciones extends Controller
{
    //metodo que llama los metodos para guardar una consiganación
    public function consignacion(Request $request)
    {
        // se utiliza transacciones por que se tienen que realizar acciones en varias tablas
        DB::beginTransaction();
        try {
            $this->guardarConsignacion($request);
            DB::commit();
            $response = array("success"=>true,"msg"=>"Consignación registrada con exito");
        } catch (\Exception $e) {
            DB::rollback();
            $response = array("success"=>false,"msg"=>"Error, no fue posible registrar la consignación intentelo de nuevo");
        }
        return new JsonResponse($response);
    }

    // metodo para guardar una consignación
    private function guardarConsignacion($request)
    {
        $objCarbon = new Carbon();
        $objTrans               = new Transacciones();
        $objTrans->tipo         = "1";
        $objTrans->fecha        = $objCarbon->format('Y-m-d');
        $objTrans->hora         = $objCarbon->format('H:i:s');
        $objTrans->monto        = $request->monto;
        $objTrans->descripcion  = "Consignación de cuenta No ".$request->cuenta;
        $objTrans->id_cajero    = $request->id_cajero;
        $objTrans->id_cuenta    = $request->id_cuenta;
        $result = $objTrans->save();

        if($result){
            $objCuenta = new ControllerCuentas();
            $objCuenta->sumarCuenta($request->id_cuenta,$request->monto);
        }
    }


    // metodo donde se llaman los metodos para editar una consiganación
    public function editarConsignacion(Request $request)
    {
        DB::beginTransaction();
        try {
            $this->guardarEditarConsignacion($request);
            DB::commit();
            $response = array("success"=>true,"msg"=>"Registro consignación editado con exito");
        } catch (\Exception $e) {
            DB::rollback();
            $response = array("success"=>false,"msg"=>"Error, no fue posible editar la consignación intentelo de nuevo");
        }
        return new JsonResponse($response);
    }

    // metodo donde se guarda la edición de una consignación
    private function guardarEditarConsignacion($request)
    {
        $valorActual = $this->valorActualTransaccion($request->id);
        $objCarbon = new Carbon();
        $objTrans               = Transacciones::find($request->id);
        $objTrans->fecha        = $objCarbon->format('Y-m-d');
        $objTrans->hora         = $objCarbon->format('H:i:s');
        $objTrans->monto        = $request->monto;
        $objTrans->id_cajero    = $request->id_cajero;
        $result = $objTrans->save();
        if($result){
            $objCuenta = new ControllerCuentas();
            $objCuenta->corregirSaldo($request->id_cuenta,$request->monto,$valorActual,1);
        }
    }

    // metodo donde se consulta el valor actual de la transacción que se va editar
    private function valorActualTransaccion($id)
    {
        $result = Transacciones::where("id",$id)->select("monto")->first();
        return $result->monto;
    }

    //metodo que llama los metodos para guardar un retiro
    public function retiro(Request $request)
    {
        // se utiliza transacciones por que se tienen que realizar acciones en varias tablas
        DB::beginTransaction();
        try {
            $this->guardarRetiro($request);
            DB::commit();
            $response = array("success"=>true,"msg"=>"Retiro registrado con exito");
        } catch (\Exception $e) {
            DB::rollback();
            $response = array("success"=>false,"msg"=>"Error, no fue posible registrar el retiro intentelo de nuevo");
        }
        return new JsonResponse($response);
    }

    // metodo para guardar un retiro
    private function guardarRetiro($request)
    {
        $objCarbon = new Carbon();
        $objTrans               = new Transacciones();
        $objTrans->tipo         = "1";
        $objTrans->fecha        = $objCarbon->format('Y-m-d');
        $objTrans->hora         = $objCarbon->format('H:i:s');
        $objTrans->monto        = $request->monto;
        $objTrans->descripcion  = "Retiro de cuenta No ".$request->cuenta;
        $objTrans->id_cajero    = $request->id_cajero;
        $objTrans->id_cuenta    = $request->id_cuenta;
        $result = $objTrans->save();

        if($result){
            $objCuenta = new ControllerCuentas();
            $objCuenta->restarCuenta($request->id_cuenta,$request->monto);
        }
    }

    // metodo donde se llaman los metodos para editar una consignación
    public function editarRetiro(Request $request)
    {
        DB::beginTransaction();
        try {
            $this->guardarEditarRetiro($request);
            DB::commit();
            $response = array("success"=>true,"msg"=>"Registro retiro editado con exito");
        } catch (\Exception $e) {
            DB::rollback();
            $response = array("success"=>false,"msg"=>"Error, no fue posible editar el retiro intentelo de nuevo");
        }
        return new JsonResponse($response);
    }

    // metodo donde se guarda la edición de una consignación
    private function guardarEditarRetiro($request)
    {
        $valorActual = $this->valorActualTransaccion($request->id);
        $objCarbon = new Carbon();
        $objTrans               = Transacciones::find($request->id);
        $objTrans->fecha        = $objCarbon->format('Y-m-d');
        $objTrans->hora         = $objCarbon->format('H:i:s');
        $objTrans->monto        = $request->monto;
        $objTrans->id_cajero    = $request->id_cajero;
        $result = $objTrans->save();
        if($result){
            $objCuenta = new ControllerCuentas();
            $objCuenta->corregirSaldo($request->id_cuenta,$request->monto,$valorActual,2);
        }
    }


}
