<?php

namespace App\Http\Controllers;

use App\Cuentas;
use Illuminate\Http\Request;

class ControllerCuentas extends Controller
{



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
}
