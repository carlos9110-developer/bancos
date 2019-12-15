<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ComidasBebidas extends Model
{
    protected $table = "comidas_bebidas";
    protected $fillable = ['nombre','precio','tipo'];// quiere decir que puede guardar estos valores a grandes valores
    protected $guarded = ['id']; // id
}
