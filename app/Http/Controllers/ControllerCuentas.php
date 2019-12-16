<?php

namespace App\Http\Controllers;

use App\Cuentas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class ControllerCuentas extends Controller
{
    // metodo para traer todas las cuentas
    public function traerCuentas(Request $request)
    {
        $result = Cuentas::join('clientes','clientes.id','=','cuentas.id_cliente')
                ->select('cuentas.numero_cuenta','cuentas.saldo','cuentas.clave','cuentas.id','cuentas.estado','clientes.cedula','clientes.nombres','clientes.apellidos')
                ->get();
        return datatables()->of($result)->toJson();
    }

    // metodo para traer todas las cuentas
    public function traerCuentasCliente(Request $request)
    {
        $result = Cuentas::join('clientes','clientes.id','=','cuentas.id_cliente')
                ->select('cuentas.numero_cuenta','cuentas.saldo','cuentas.clave','cuentas.id','cuentas.estado','clientes.cedula','clientes.nombres','clientes.apellidos')
                ->where('cuentas.id_cliente',$request->id_cliente)
                ->get();
        return datatables()->of($result)->toJson();
    }

    // metodo para registrar una determinada cuenta
    public function registroCuenta(Request $request)
    {
        DB::beginTransaction();
        try {
            $claveCuenta = substr($request->cedula, -4);
            $this->registrarCuenta($claveCuenta,$request->id_cliente);
            DB::commit();
            $response = array("success"=>true,"msg"=>"Cuenta registrada exitosamente");
        } catch (\Exception $e) {
            DB::rollback();
            $response = array("success"=>false,"msg"=>"Error, se presento un problema en el servidor al realizar la acción");
        }
        return new JsonResponse($response);
    }

    //metodo para registrar las cuentas
    public function registrarCuenta($clave,$id_cliente)
    {
        $objCuentas = new Cuentas();
        $objCuentas->numero_cuenta = $this->numeroCuenta();
        $objCuentas->saldo         = 0;
        $objCuentas->clave         = $clave;
        $objCuentas->id_cliente    = $id_cliente;
        $objCuentas->save();
    }

    //metodo para retornar el número de cuenta
    private function numeroCuenta()
    {
        $verificacionCuenta = true;
        while($verificacionCuenta)
        {
            $numeroCuenta =  mt_rand(100000,999999);
            $result       =  Cuentas::where('numero_cuenta',$numeroCuenta)->first();
            if (!is_object($result)) {
                $verificacionCuenta = false;
            }
        }
        return $numeroCuenta;
    }

    // metodo para actualizar la clave de las cuentas, de un cliente cuaando se ha modificado su cedula
    public function actualizarClaveCuentas($clave,$id_cliente)
    {
        Cuentas::where("id_cliente",$id_cliente)->update(["clave"=>$clave]);
    }

    // metodo donde se desactiva una cuenta
    public function desactivarCuenta(Request $request)
    {
        DB::beginTransaction();
        try {
            Cuentas::where("id",$request->id)->update(["estado"=>'0']);
            DB::commit();
            $response = array("success"=>true,"msg"=>"Cuenta desactivada exitosamente");
        } catch (\Exception $e) {
            DB::rollback();
            $response = array("success"=>false,"msg"=>"Error, se presento un problema en el servidor al realizar la acción");
        }
        return new JsonResponse($response);
    }

    // metodo donde se desactiva una cuenta
    public function activarCuenta(Request $request)
    {
        if($this->consultarValorCuenta($request->id)){
            DB::beginTransaction();
            try {
                $this->guardarActivarCuenta($request->id);
                DB::commit();
                $response = array("success"=>true,"msg"=>"Cuenta activada exitosamente");
            } catch (\Exception $e) {
                DB::rollback();
                $response = array("success"=>false,"msg"=>"Error, se presento un problema en el servidor al realizar la acción");
            }
        } else {
            $response = array("success"=>false,"msg"=>"Error, la cuenta debe tener al menos $100000 de saldo para activarse");
        }
        return new JsonResponse($response);
    }

    // metodo para activar una determinada cuenta
    private function guardarActivarCuenta($idCuenta)
    {
        Cuentas::where("id",$idCuenta)->update(["estado"=>'1']);
    }

    // metodo para consultar el valor de una cuenta
    private function consultarValorCuenta($idCuenta)
    {
        $result =   Cuentas::where("id",$idCuenta)->select("saldo")->first();
        if($result->saldo >= 100000){
            return true;
        } else {
            return false;
        }
    }

    // metodo donde se suma valor a una cuenta
    public function sumarCuenta($idCuenta,$monto)
    {
        $saldoActual = $this->saldoActual($idCuenta);
        $nuevoSaldo  = $saldoActual + $monto;
        $result = Cuentas::where("id",$idCuenta)->update(["saldo"=>$nuevoSaldo]);
        if($result){
            if($this->consultarValorCuenta($idCuenta)){
                $this->guardarActivarCuenta($idCuenta);
            }
        }
    }

    // metodo donde se retorna el saldo actual
    private function saldoActual($idCuenta)
    {
        $result = Cuentas::where("id",$idCuenta)->select("saldo")->first();
        return $result->saldo;
    }

    // metodo para corregir el saldo cuando se edita una transacción
    public function corregirSaldo($idCuenta,$monto,$valorConsignacion,$guia)
    {
        $saldoActual = $this->saldoActual($idCuenta);
        // si se va corregir una consignación
        if($guia==1)
        {
            $nuevoSaldo  = $saldoActual - $valorConsignacion;
            $result = Cuentas::where("id",$idCuenta)->update(["saldo"=>$nuevoSaldo]);
            if($result){
                $nuevoSaldo = $nuevoSaldo + $monto;
                $result = Cuentas::where("id",$idCuenta)->update(["saldo"=>$nuevoSaldo]);
                if($result){
                    if($this->consultarValorCuenta($idCuenta)){
                        $this->guardarActivarCuenta($idCuenta);
                    }
                }
            }
        }
        else
        {
            $nuevoSaldo  = $saldoActual + $valorConsignacion;
            $result = Cuentas::where("id",$idCuenta)->update(["saldo"=>$nuevoSaldo]);
            if($result){
                $nuevoSaldo = $nuevoSaldo - $monto;
                $result = Cuentas::where("id",$idCuenta)->update(["saldo"=>$nuevoSaldo]);
            }
        }
    }

    // metodo donde se registran los retiros
    public function restarCuenta($idCuenta,$monto)
    {
        $saldoActual = $this->saldoActual($idCuenta);
        $nuevoSaldo  = $saldoActual - $monto;
        $result = Cuentas::where("id",$idCuenta)->update(["saldo"=>$nuevoSaldo]);
    }


}
