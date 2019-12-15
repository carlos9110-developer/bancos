<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
class PedidosPreparados extends Model
{
    protected $table = "pedidos_preparados";
    protected $fillable = ['id_pedido','id_cocinero'];// quiere decir que puede guardar estos valores a grandes valores
    protected $guarded = ['id']; // id
}
