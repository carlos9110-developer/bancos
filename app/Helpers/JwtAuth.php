<?php
namespace App\Helpers;
use Firebase\JWT\JWT;
use Firebase\JWT\JWT\SignatureInvalidException;
use Firebase\JWT\JWT\UnexpectedValueException;
use Firebase\JWT\JWT\DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use App\User;
class JwtAuth{

    private $secret         = "sjshshshahashha";
    private $algoritmoCod   =  "HS256";

    public function  login($user,$pass){
        $usuario = User::where(array(
            'email'   => $user,
            'password'=> hash("SHA256",$pass),
        ))->first();

        if(is_object($usuario)){
            if($usuario->estado=='1'){
                $payload = array(
                    'sub'    => $usuario->id,
                    'nombre' => $usuario->name,
                    'usr'    => $usuario->email,
                    'iat'    => time(),
                    'exp'    => time() + (60 * 60 * 2)
                );
                $jwt = JWT::encode($payload,$this->secret,$this->algoritmoCod);
                $response = array(
                    'success' => true,
                    'token'   => $jwt,
                    'id_user' => $usuario->id,
                    'rol'     => $usuario->rol,
                    'nombre'  => $usuario->name,
                    'telefono'=> $usuario->telefono,
                    'email'   => $usuario->email
                );
            } else {
                $response = array(
                    'success' => false,
                    'msg'     => "Error, el usuario se encuentra desactivado"
                );
            }
        } else {
            $response = array(
                'success' => false,
                'msg'     => "Error, usuario o contraseña incorrectos"
            );
        }
        return $response;
    } // aca temrina el login

    public function verificarToken($token, $decodificados = false){
        $auth       = false;
        $payload    = null;

        try{
            $payload = JWT::decode($token,$this->secret,array($this->algoritmoCod));
            $auth       = true;
        }catch (SignatureInvalidException $ex){ // este case es cuando pasan un token con una firma invalida
            $auth = false;
        }catch (\UnexpectedValueException $ex){
            $auth = false;
        }  catch (\DomainException $ex){
            $auth = false;
        }
        catch (Exception $ex){
            $auth = false;
        }

        if($decodificados == true){
            return $payload;
        } else {
            return $auth;
        }
    }


}
