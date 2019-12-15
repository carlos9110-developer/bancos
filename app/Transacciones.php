<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transacciones extends Model
{
    protected $table    = "transacciones";
    protected $fillable = [];// con esto quito todas las restricciones
    protected $guarded  = ['id']; // id
}
