<?php

namespace App\Http\Controllers;
use App\Facturacion;

class FacturacionController extends Controller
{
    //metodo donde se registra la facturacion de un pedido
    public function facturacion($id_pedido,$valor,$id_cajero)
    {
        Facturacion::create([
            'id_cajero'=>$id_cajero,
            'id_pedido'=>$id_pedido,
            'valor'=>$valor
        ]);
    }
}
