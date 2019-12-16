<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuentasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cuentas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('numero_cuenta','6')->unique();
            $table->integer('saldo');
            $table->string('clave','4');
            $table->char('estado','1')->default('0');// uno si esta activado y dos desactivado
            $table->foreign('id_cliente')->references('id')->on('clientes')->onDelete('restrict')->onUpdate('restrict');
            $table->unsignedInteger('id_cliente');
            $table->charset   = 'utf8mb4';
            $table->collation = 'utf8mb4_spanish_ci';
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cuentas');
    }
}
