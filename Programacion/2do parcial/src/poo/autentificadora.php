<?php
use Firebase\JWT\JWT;

class Autentificadora
{
    private static string $secret_key = 'Garcia.Lautaro';
    private static array $encrypt = ['HS256'];
    
    public static function crearJWT(mixed $data, int $exp = (60*2)) : string
    {
        $time = time();

        $token = array(
        	'iat'=>$time,
            'exp' => $time + $exp,
            'usuario' => $data,
            'alumno' => "Lautaro Nahuel Garcia",
            'dni_alumno' => 45040166
        );

        return JWT::encode($token, self::$secret_key);
    }
    
    public static function verificarJWT(string $token) : stdClass
    {
        $datos = new stdClass();
        $datos->verificado = FALSE;
        $datos->mensaje = "";

        try 
        {
            if( ! isset($token))
            {
                $datos->mensaje = "Token vacío!!!";
            }
            else
            {          
                $decode = JWT::decode(
                    $token,
                    self::$secret_key,
                    self::$encrypt
                );


                $datos->verificado = TRUE;
                $datos->mensaje = "Token OK!!!";
                 
            }          
        } 
        catch (Exception $e) 
        {
            $datos->mensaje = "Token inválido!!! - " . $e->getMessage();
        }
    
        return $datos;
    }
    
    public static function obtenerPayLoad(string $token) : object
    {
        $datos = new stdClass();
        $datos->exito = FALSE;
        $datos->payload = NULL;
        $datos->mensaje = "Exitoso";

        try {

            $datos->payload = JWT::decode(
                                            $token,
                                            self::$secret_key,
                                            self::$encrypt
                                        );
            $datos->exito = TRUE;

        } catch (Exception $e) { 

            $datos->mensaje = $e->getMessage();
        }

        return $datos;
    }


}