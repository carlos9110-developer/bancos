<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Helpers\JwtAuth;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
 /**
  * ROl 1 = mesero
  * Rol 2 = cocinero
  * Rol 3 = cajero
  * Rol 4 = administrador
  */
class UserController extends Controller
{
    public function login(Request $request)
    {
        $email=$request->email;
        $password=$request->password;
        if(!is_null($email) && !is_null($password) )
        {
            $jwt = new JwtAuth();
            return new JsonResponse($jwt->login($email,$password));
        }
    }

    // función donde se realiza el registro de un determinado usuario
    public function registroUsuario(Request $request)
    {
        if(!is_null($request->nombre) && !is_null($request->email) && !is_null($request->telefono)
         && !is_null($request->rol) && !is_null($request->password) )
        {
            DB::beginTransaction();
            try {
                User::create([
                    'name'=>$request->nombre,
                    'email'=>$request->email,
                    'telefono'=>$request->telefono,
                    'rol'=>$request->rol,
                    'password'=>hash("SHA256",$request->password)
                ]);
                DB::commit();
                $success = true;
            } catch (\Exception $e) {
                $success = false;
                DB::rollback();
            }
            if($success){
                $response = array("success"=>true,"msg"=>"Usuario registrado exitosamente");
            } else{
                $response = array("success"=>false,"msg"=>"Error, no fue posible registrar el usuario, se encontró un registro con el mismo correo");
            }
        } else {
            $response = array("success"=>false,"msg"=>"Error, no se envio la información requerida para registrar el usuario");
        }
        return new JsonResponse($response);
    }

    // metodo donde se actualiza la información de un usuario
    public function editarUsuario(Request $request)
    {
        if(!is_null($request->nombre) && !is_null($request->email) && !is_null($request->telefono)
         && !is_null($request->rol) && !is_null($request->password) && !is_null($request->id_usuario) )
        {
            $objUser  = User::where('id',$request->id_usuario)->first();
            if(is_object($objUser)){
                DB::beginTransaction();
                try {
                    $objUser->update(['name'=>$request->nombre,'email'=>$request->email,'telefono'=>$request->telefono,'rol'=>$request->rol,'password'=>hash("SHA256",$request->password)]);
                    DB::commit();
                    $success = true;
                } catch (\Exception $e) {
                    $success = false;
                    DB::rollback();
                }
                if($success){
                    $response = array("success"=>true,"msg"=>"Información usuario actualizada con exito");
                } else{
                    $response = array("success"=>false,"msg"=>"Error, no fue posible actualizar la información del usuario, se encontró un registro con el mismo correo");
                }
            } else{
                $response = array("success"=>false,"msg"=>"Error, no se encontro ningún usuario con el id enviado");
            }
        } else{
            $response = array("success"=>false,"msg"=>"Error, no se envio la información requerida para actualizar la información del usuario");
        }
        return new JsonResponse($response);
    }

    // metodo donde se actualiza la información de un usuario
    public function desactivarUsuario(Request $request)
    {
        if(!is_null($request->id_usuario))
        {
            $objUser  = User::where('id',$request->id_usuario)->first();
            if(is_object($objUser)){
                DB::beginTransaction();
                try {
                    $objUser->update(['estado'=>'0']);
                    DB::commit();
                    $success = true;
                } catch (\Exception $e) {
                    $success = false;
                    DB::rollback();
                }
                if($success){
                    $response = array("success"=>true,"msg"=>"Usuario desactivado exitosamente");
                } else{
                    $response = array("success"=>false,"msg"=>"Error, no fue posible desactivar el usuario");
                }
            } else{
                $response = array("success"=>false,"msg"=>"Error, no se encontro ningún usuario con el id enviado");
            }
        } else{
            $response = array("success"=>false,"msg"=>"Error, no se envio la información requerida para desactivar el usuario");
        }
        return new JsonResponse($response);
    }

    // metodo donde se actualiza la información de un usuario
    public function activarUsuario(Request $request)
    {
        if(!is_null($request->id_usuario))
        {
            $objUser  = User::where('id',$request->id_usuario)->first();
            if(is_object($objUser)){
                DB::beginTransaction();
                try {
                    $objUser->update(['estado'=>'1']);
                    DB::commit();
                    $success = true;
                } catch (\Exception $e) {
                    $success = false;
                    DB::rollback();
                }
                if($success){
                    $response = array("success"=>true,"msg"=>"Usuario activado exitosamente");
                } else{
                    $response = array("success"=>false,"msg"=>"Error, no fue posible activar el usuario");
                }
            } else{
                $response = array("success"=>false,"msg"=>"Error, no se encontro ningún usuario con el id enviado");
            }
        } else{
            $response = array("success"=>false,"msg"=>"Error, no se envio la información requerida para activar el usuario");
        }
        return new JsonResponse($response);
    }

    // metodó donde se listan todos los usuarios con estado cocinero(2) de la base de datos
    public function listarCocineros()
    {
        $result = User::select('id','name','email','telefono','estado')->where('rol','2')->get();
        return new JsonResponse($result);
    }

    // metodó donde se listan todos los usuarios con estado mesero(1) de la base de datos
    public function listarMeseros()
    {
        $result = User::select('id','name','email','telefono','estado')->where('rol','1')->get();
        return new JsonResponse($result);
    }

    // metodó donde se listan todos los usuarios con estado cajero(3) de la base de datos
    public function listarCajeros()
    {
        $result = User::select('id','name','email','telefono','estado')->where('rol','3')->get();
        return new JsonResponse($result);
    }

    // metodo para obtener la información de un determinado usuario
    public function informacionUsuario(Request $request)
    {
        if (!is_null($request->id_usuario)){
            $result = User::select('id','name','email','telefono','estado','rol')->where('id',$request->id_usuario)->first();
            return new JsonResponse($result);
        } else{
            $response = array("success"=>false,"msg"=>"Error, no se envio la información requerida para consultar la información del usuario");
            return new JsonResponse($response);
        }
    }

}
