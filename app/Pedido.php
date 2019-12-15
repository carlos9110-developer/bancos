<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
/**
 * Estados Pedidos
 * 1 solicitado
 * 2 preparado
 * 3 servido
 * 4 facturado
 * 5 cancelado
 */
class Pedido extends Model
{
    protected $table = "pedidos";
    protected $fillable = ['mesa','piso','id_mesero','estado','valor'];// quiere decir que puede guardar estos valores a grandes valores
    protected $guarded = ['id']; // id

}
