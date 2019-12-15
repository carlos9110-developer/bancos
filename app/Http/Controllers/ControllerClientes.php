<?php

namespace App\Http\Controllers;

use App\Clientes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class ControllerClientes extends Controller
{
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


}
