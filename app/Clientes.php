<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Clientes extends Model
{
    protected $table    = "clientes";
    protected $fillable = [];// con esto quito todas las restricciones
    protected $guarded  = ['id']; // id
}
