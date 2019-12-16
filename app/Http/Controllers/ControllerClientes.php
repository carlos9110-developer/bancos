<?php

namespace App\Http\Controllers;

use App\Clientes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class ControllerClientes extends Controller
{
    // metodo que retorna los clientes
    public function traerClientes(Request $request)
    {
        $result = Clientes::select('id','cedula','nombres','apellidos','direccion','telefono','email')->get();
        return datatables()->of($result)->toJson();
    }

    // metodo para registrar un nuevo cliente
    public function registrarCliente(Request $request)
    {
        // se utiliza transacciones por que se tienen que realizar acciones en varias tablas
        DB::beginTransaction();
        try {
            $this->guardarCliente($request);
            DB::commit();
            $response = array("success"=>true,"msg"=>"Cliente registrado exitosamente, recuerde realizar la activación de la cuenta");
        } catch (\Exception $e) {
            DB::rollback();
            $response = array("success"=>false,"msg"=>"Error, no fue posible realizar el registro, verifique que la cédula no este registrada");
        }
        return new JsonResponse($response);
    }

    // metodo donde se realiza el registro de un cliente
    private function guardarCliente($request)
    {
        $objClientes = new Clientes();
        $objClientes->cedula    = $request->cedula;
        $objClientes->nombres   = $request->nombres;
        $objClientes->apellidos = $request->apellidos;
        $objClientes->direccion = $request->direccion;
        $objClientes->telefono  = $request->telefono;
        $objClientes->email     = $request->email;
        $result = $objClientes->save();
        if($result){
            $claveCuenta = substr($request->cedula, -4);
            $this->registrarCuenta($claveCuenta,$objClientes->id);
        }
    }

    // metodo donde se llama al controlador de cuentas, para realizar el registro de una nueva cuenta
    private function registrarCuenta($clave,$id_cliente)
    {
        $objCuentas = new ControllerCuentas();
        $objCuentas->registrarCuenta($clave,$id_cliente);
    }

    // metodo donde se retorna la información de un cliente
    public function informacionCliente(Request $request)
    {
        $infoUsuario = Clientes::where("id",$request->id)->first();
        return new JsonResponse($infoUsuario);
    }

    // metodo donde se actualiza la información de un cliente
    public function actualizarCliente(Request $request)
    {
        DB::beginTransaction();
        try {
            $this->actualizacionCliente($request);
            DB::commit();
            $response = array("success"=>true,"msg"=>"Información cliente actualizada con exito");
        } catch (\Exception $e) {
            DB::rollback();
            $response = array("success"=>false,"msg"=>"Error, no fue posible actualizar la información, verifique que la cédula no este registrada");
        }
        return new JsonResponse($response);
    }

    // guardar actualización información cliente
    private function actualizacionCliente($request)
    {
        $objClientes = Clientes::find($request->id);
        $objClientes->cedula    = $request->cedula;
        $objClientes->nombres   = $request->nombres;
        $objClientes->apellidos = $request->apellidos;
        $objClientes->direccion = $request->direccion;
        $objClientes->telefono  = $request->telefono;
        $objClientes->email     = $request->email;
        $result = $objClientes->save();
        if($result){
            $claveCuenta = substr($request->cedula, -4);
            $this->actualizarClaveCuentas($claveCuenta,$objClientes->id);
        }
    }

    // función que llama al controlador de cuentas para actualizar la clave de la cuenta
    private function actualizarClaveCuentas($clave,$id_cliente)
    {
        $objCuentas = new ControllerCuentas();
        $objCuentas->actualizarClaveCuentas($clave,$id_cliente);
    }

}
