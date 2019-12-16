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
    Route::get('infoCliente','ControllerClientes@informacionCliente');
    Route::put('actualizarCliente','ControllerClientes@actualizarCliente');
    Route::put('desactivarCuenta','ControllerCuentas@desactivarCuenta');
    Route::put('activarCuenta','ControllerCuentas@activarCuenta');
    Route::post('consignacion','ControllerTransacciones@consignacion');
    Route::put('editarConsignacion','ControllerTransacciones@editarConsignacion');
    Route::post('retiro','ControllerTransacciones@retiro');
    Route::put('editarRetiro','ControllerTransacciones@editarRetiro');
    Route::get('traerClientes','ControllerClientes@traerClientes');
    Route::get('traerCuentas','ControllerCuentas@traerCuentas');
    Route::get('traerCuentasCliente','ControllerCuentas@traerCuentasCliente');
});
 // ruta para registrar las tarjetas


