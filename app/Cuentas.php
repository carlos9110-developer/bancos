<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cuentas extends Model
{
    protected $table    = "cuentas";
    protected $fillable = [];// con esto quito todas las restricciones
    protected $guarded  = ['id']; // id
}
