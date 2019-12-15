<?php
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login','UserController@login');
/* con esto se protejen todas las rutas de nuestra aplicación */
Route::group(['middleware' => 'verificacionToken'], function()
{
    Route::post('registroCliente','ControllerClientes@registrarCliente');
});
 // ruta para registrar las tarjetas


