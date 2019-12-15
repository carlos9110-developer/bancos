<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Facturacion extends Model
{
    protected $table = "facturacion";
    protected $fillable = ['id_cajero','id_pedido','valor'];// quiere decir que puede guardar estos valores a grandes valores
    protected $guarded = ['id']; // id
}
