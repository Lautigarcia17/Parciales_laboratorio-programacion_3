<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseMW;
require_once "autentificadora.php";

class MW
{
    public static function ValidarParametrosVacios(Request $request, RequestHandler $handler) : ResponseMW
    {
        $datosArray = $request->getParsedBody();
        $contenido = new stdClass();
        $contenido->mensaje = "";
        $status = 409;
        
        if (isset($datosArray["user"])) {
            $obj_json = json_decode($datosArray["user"]);

            if ($obj_json->correo != "" && $obj_json->clave != "") {
                $responseApi = $handler->handle($request);
                $contenido = (json_decode((string)$responseApi->getBody()));
                if ($contenido->mensaje != "null") $status = 200; 
            }
            else 
            {
                if ($obj_json->correo == ""){
                    $contenido->mensaje.= "Falta atributo correo!!";  
                }
                if ($obj_json->clave == ""){
                    $contenido->mensaje.= "Falta atributo clave!!";  
                }
            }
        }
        else {
            $contenido->mensaje = "Falta parametro user";
        }

        $newResponse = new ResponseMW($status);
        $newResponse->getBody()->write(json_encode($contenido));

        return $newResponse->withHeader('Content-Type', 'application/json');
    } 

    public function VerificarUsuarioBD(Request $request, RequestHandler $handler): ResponseMW
    {
        $datosArray = $request->getParsedBody();
        $obj_respuesta = new stdClass();
        $obj_respuesta->mensaje = "El usuario no existe!";
        $status = 403;

        $obj_json = json_decode($datosArray["user"]);
        $usuarioEncontrado = Usuario::TraerUnUsuario($obj_json->correo,$obj_json->clave);
        if ($usuarioEncontrado != null) 
        {
            $responseApi = $handler->handle($request);
            $obj_respuesta->mensaje = json_decode((string)$responseApi->getBody());
            $status = $obj_respuesta->mensaje->status;
        }

        $response = new ResponseMW();
        $response = $response->withStatus($status);
        $response->getBody()->write(json_encode($obj_respuesta));
        return $response->withHeader('Content-Type', 'application/json');
    }

    function ChequearJWT(Request $request, RequestHandler $handler) : ResponseMW
    {

        $obj_respuesta = new stdClass();
        $obj_respuesta->mensaje = "No se cargo el token";
        $status = 403;
        
        if (isset($request->getHeader("token")[0])) 
        {
            $token = $request->getHeader("token")[0];

            $obj_rta=Autentificadora::verificarJWT($token);
            $status = $obj_rta->verificado ? 200 : 403;
            if ($status == 200) {
                $responseApi = $handler->handle($request);
                $obj_respuesta = json_decode((string)$responseApi->getBody());
                if($obj_respuesta->exito == false) $status = 403;
            }
            else {
                $obj_respuesta->mensaje = "Token Invalido!";
            }
        }



        $newResponse = new ResponseMW($status);
        $newResponse->getBody()->write(json_encode($obj_respuesta));

        return $newResponse->withHeader('Content-Type', 'application/json');        
    }

    public static function MostrarTablaSinClave(Request $request, RequestHandler $handler): ResponseMW
    {

        $response = $handler->handle($request);
        $usuarios = json_decode((string) $response->getBody());
        if ($usuarios->dato !=null) 
        {
            $arrayUsuarios = $usuarios->dato;

            foreach ($arrayUsuarios as $usuario) {
                unset($usuario->clave);
            }
            $contenido = MW::GenerarTablaSinClave($arrayUsuarios);
            $status = 200;
        }
        else
        {
            $contenido = json_encode($usuarios);
            $status = 424;
        }

        

        $response = new ResponseMW();
        $response = $response->withStatus($status);
        $response->getBody()->write($contenido);
        return $response;
    }


    private static function GenerarTablaSinClave($listado): string
    {
        $grilla = '
        <html>
        <head>
            <title>Listado de Usuarios</title>
        </head>
        <body>
            <table class="table" border="1">
                <thead>
                    <tr>
                        <th> id            </th>
                        <th> correo            </th>
                        <th> nombre     </th>
                        <th> apellido     </th>
                        <th> foto     </th>
                        <th> perfil     </th>
                    </tr>
                </thead>';

        foreach ($listado as $value)
        {
            $grilla .= "    <tr>
                                <td>".$value->id."</td>
                                <td>".$value->correo."</td>
                                <td>".$value->nombre."</td>
                                <td>".$value->apellido."</td>
                                <td><img src='".$value->foto."' width='50px' height='50px'></td>
                                <td>".$value->perfil."</td>
                            </tr>";
        }

        $grilla .= '    </table>
                    </body>';
    

        return $grilla;
    }

    public static function MostrarTablaPropietario(Request $request, RequestHandler $handler): ResponseMW
    {
        $obj_respuesta = new stdclass();
        $obj_respuesta->exito = false;
        $obj_respuesta->mensaje = "No tiene los permisos suficientes!";
        $status = 403;
        $contenido = json_encode($obj_respuesta);

        $token = $request->getHeader("token")[0];
        $datosToken = Autentificadora::obtenerPayLoad($token);
        if ($datosToken->payload !=null) 
        {
            $perfilToken = $datosToken->payload->usuario->perfil;
            if ($perfilToken == "propietario") 
            {
                $response = $handler->handle($request);
                $usuarios = json_decode((string) $response->getBody());
                if ($usuarios->dato !=null) 
                {
                    $arrayUsuarios = $usuarios->dato;
        
                    $contenido = MW::GenerarTablaPropietario($arrayUsuarios);
                    $status = 200;
                }
                else
                {
                    $contenido = json_encode($usuarios);
                    $status = 424;
                }
            }
        }


        $response = new ResponseMW();
        $response = $response->withStatus($status);
        $response->getBody()->write($contenido);
        return $response;
    }

    private static function GenerarTablaPropietario($listado): string
    {
        $grilla = '
        <html>
        <head>
            <title>Listado de Usuarios</title>
        </head>
        <body>
            <table class="table" border="1">
                <thead>
                    <tr>
                        <th> correo            </th>
                        <th> nombre     </th>
                        <th> apellido     </th>
                    </tr>
                </thead>';

        foreach ($listado as $value)
        {
            $grilla .= "    <tr>
                                <td>".$value->correo."</td>
                                <td>".$value->nombre."</td>
                                <td>".$value->apellido."</td>
                            </tr>";
        }

        $grilla .= '    </table>
                    </body>';
    

        return $grilla;
    }

    public static function MostrarTablaJuguetes(Request $request, RequestHandler $handler): ResponseMW
    {

        $response = $handler->handle($request);
        $juguetes = json_decode((string) $response->getBody());
        if ($juguetes->dato !=null) 
        {
            $arrayJuguetes = $juguetes->dato;
            $arrayJuguetesImpares = array();
            foreach ($arrayJuguetes as $juguete) {
                if ($juguete->id %2 ==1) 
                {
                    array_push($arrayJuguetesImpares,$juguete);
                }
            }
            $contenido = MW::GenerarTablaJuguetes($arrayJuguetesImpares);
            $status = 200;
        }
        else
        {
            $contenido = json_encode($juguetes);
            $status = 424;
        }

        

        $response = new ResponseMW();
        $response = $response->withStatus($status);
        $response->getBody()->write($contenido);
        return $response;
    }


    private static function GenerarTablaJuguetes($listado): string
    {
        $grilla = '
        <html>
        <head>
            <title>Listado de Usuarios</title>
        </head>
        <body>
            <table class="table" border="1">
                <thead>
                    <tr>
                        <th> id            </th>
                        <th> marca            </th>
                        <th> precio     </th>
                        <th> foto     </th>
                    </tr>
                </thead>';

        foreach ($listado as $value)
        {
            $grilla .= "    <tr>
                                <td>".$value->id."</td>
                                <td>".$value->marca."</td>
                                <td>".$value->precio."</td>
                                <td><img src='".$value->path_foto."' width='50px' height='50px'></td>
                            </tr>";
        }

        $grilla .= '    </table>
                    </body>';
    

        return $grilla;
    }

    public static function VerificarCorreo(Request $request, RequestHandler $handler): ResponseMW
    {
        $datosArray = $request->getParsedBody();
        $obj_respuesta = new stdClass();
        $obj_respuesta->mensaje = "El correo ya existe!";
        $status = 403;

        $obj_json = json_decode($datosArray["usuario"]);
        $usuarioEncontrado = Usuario::VerificarUsuarioCorreo($obj_json->correo);
        if ($usuarioEncontrado == 0) 
        {
            $responseApi = $handler->handle($request);
            $obj_respuesta->mensaje = json_decode((string)$responseApi->getBody());
            $status = $obj_respuesta->mensaje->status;
        }

        $response = new ResponseMW();
        $response = $response->withStatus($status);
        $response->getBody()->write(json_encode($obj_respuesta));
        return $response->withHeader('Content-Type', 'application/json');
    }


}


?>