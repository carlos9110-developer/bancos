<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaccionesTable extends Migration
{
    public function up()
    {
        Schema::create('transacciones', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tipo','10');// 1 si es consignación , 2 si es retiro
            $table->date('fecha');
            $table->string('hora','40');
            $table->integer('monto');
            $table->string('descripcion','250');
            $table->foreign('id_cajero')->references('id')->on('users')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedInteger('id_cajero');
            $table->foreign('id_cuenta')->references('id')->on('cuentas')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedInteger('id_cuenta');
            $table->charset   = 'utf8mb4';
            $table->collation = 'utf8mb4_spanish_ci';
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transacciones');
    }
}
