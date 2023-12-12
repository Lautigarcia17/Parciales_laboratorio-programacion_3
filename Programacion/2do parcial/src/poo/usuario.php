<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once "accesoDatos.php";
require_once "autentificadora.php";


class Usuario 
{
    public int $id;
    public string $correo;
    public string $clave;
    public string $nombre;
    public string $apellido;
    public string $perfil;
    public string $foto;

	public function TraerTodos(Request $request, Response $response, array $args): Response 
	{
		$obj_respuesta = new stdClass();
        $obj_respuesta->exito = false;
        $obj_respuesta->mensaje = "No se encontraron usuarios!";
        $obj_respuesta->dato = null;
        $status = 424;

		$usuarios = Usuario::traerTodoLosUsuarios();
		
		if (count($usuarios))
		{
			$obj_respuesta->exito = true;
            $obj_respuesta->mensaje = "Usuarios encontrados!";
            // $obj_respuesta->dato = json_encode($usuarios);
            $obj_respuesta->dato = $usuarios;
            $status = 200;
		}

		$newResponse = $response->withStatus($status);
		$newResponse->getBody()->write(json_encode($obj_respuesta));

		return $newResponse->withHeader('Content-Type', 'application/json');	
	}

	function VerificarUsuario(Request $request, Response $response, array $args) : Response
    {
        $datosArray = $request->getParsedBody();
		$obj_respuesta = new stdclass();
        $obj_respuesta->exito = false;
        $obj_respuesta->mensaje = "No se encontro el usuario!";
		$obj_respuesta->jwt = null;
        $obj_respuesta->status = 403;

        $obj_json = json_decode($datosArray["user"]);

		if (isset($obj_json)) 
		{
			$usuarioEncontrado = Usuario::TraerUnUsuario($obj_json->correo,$obj_json->clave);
			if ($usuarioEncontrado != null) 
			{
				unset($usuarioEncontrado->clave);
				$obj_respuesta->jwt = Autentificadora::crearJWT($usuarioEncontrado);
				$obj_respuesta->exito = true;
				$obj_respuesta->mensaje = "Se encontro el usuario!";
				$obj_respuesta->status  = 200;
			}
		}

		$newResponse = $response->withStatus($obj_respuesta->status);
		$newResponse->getBody()->write(json_encode($obj_respuesta));
        return $newResponse->withHeader('Content-Type', 'application/json');;
    }

    public static function TraerUnUsuario($correo,$clave)
    {
        $objetoAcceso = AccesoDatos::dameUnObjetoAcceso("usuarios");
        $consulta = $objetoAcceso->retornarConsulta("SELECT * FROM `usuarios` WHERE clave = :clave AND correo = :correo ");
        
        $consulta->bindParam(':correo',$correo,PDO::PARAM_STR,20);
        $consulta->bindParam(':clave',$clave,PDO::PARAM_STR,20);

        $consulta->execute();
    
        return $consulta->fetchObject('Usuario');
    }

	public function ChequearJWT(Request $request, Response $response, array $args): Response
    {
        $contenidoAPI = "";
        $obj_respuesta = new stdClass();

        $obj_respuesta->exito = false;
        $status = 403;

		$token = $request->getHeader("token")[0];
        if (isset($token)) {
            $obj = Autentificadora::verificarJWT($token);

            if ($obj->verificado) {
                $obj_respuesta->exito = true;
                $status = 200;
            }
        }

        $contenidoAPI = json_encode($obj_respuesta);

        $response = $response->withStatus($status);
        $response->getBody()->write($contenidoAPI);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function VerificarUsuarioCorreo($correo)
    {
        $objetoAcceso = AccesoDatos::dameUnObjetoAcceso("usuarios");
        $consulta = $objetoAcceso->retornarConsulta("SELECT * FROM `usuarios` WHERE correo = :correo ");
        
        $consulta->bindParam(':correo',$correo,PDO::PARAM_STR,20);

        $consulta->execute();
        return $consulta->rowCount();;
    }

	public static function traerTodoLosUsuarios()
	{
		$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta = $objetoAccesoDato->retornarConsulta("SELECT * FROM usuarios");
		$consulta->execute();			
		return $consulta->fetchAll(PDO::FETCH_CLASS, "Usuario");		
	}

    public function AgregarUsuario(Request $request, Response $response, array $args): Response 
	{
        $parametros = $request->getParsedBody();
		$obj_respuesta = new stdclass();
        $obj_respuesta->exito = false;
        $obj_respuesta->mensaje = "No se mando el usuario y/o la foto";
        $obj_respuesta->status = 418;


		if(isset($parametros["usuario"]) && count($request->getUploadedFiles()))
		{
			$obj = json_decode($parametros["usuario"]);
			$miUsuario = new Usuario();
			$miUsuario->correo = $obj->correo;
			$miUsuario->clave =  $obj->clave;
			$miUsuario->nombre =  $obj->nombre;
			$miUsuario->apellido =  $obj->apellido;
			$miUsuario->perfil =  $obj->perfil;
	
		
			//Agregar foto archivo		
			$archivos = $request->getUploadedFiles();
			$destino = "./src/fotos/";
			
			$nombreAnterior = $archivos['foto']->getClientFilename();
			$extension = explode(".", $nombreAnterior);
			$extension = array_reverse($extension);
			
			$foto = $destino . $miUsuario->correo . "." . $extension[0];
			$archivos['foto']->moveTo("." . $foto); // "agrego un . para no guardarlo en el $foto y que se guarde en el path_foto, no respetando el ejemplo dado"
			$miUsuario->foto =  $foto;	
			//

			if ($miUsuario->InsertarUsuario()) 
			{
				$obj_respuesta->exito = true;
				$obj_respuesta->mensaje = "Usuario Agregado";
				$obj_respuesta->status = 200;
			}
			else {
				$obj_respuesta->mensaje = "No se pudo agregar el usuario";
			}
		}

        $newResponse = $response->withStatus($obj_respuesta->status);
        $newResponse->getBody()->write(json_encode($obj_respuesta));

		return $newResponse->withHeader('Content-Type', 'application/json');
    }

    public function InsertarUsuario()
	{
		$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta = $objetoAccesoDato->retornarConsulta("INSERT into usuarios (correo,clave,nombre,apellido,foto,perfil)values(:correo,:clave,:nombre,:apellido,:foto,:perfil)");
		$consulta->bindValue(':correo',$this->correo, PDO::PARAM_STR);
		$consulta->bindValue(':clave',$this->clave, PDO::PARAM_STR);
		$consulta->bindValue(':nombre',$this->nombre, PDO::PARAM_STR);
		$consulta->bindValue(':apellido',$this->apellido, PDO::PARAM_STR);
		$consulta->bindValue(':foto', $this->foto, PDO::PARAM_STR);
		$consulta->bindValue(':perfil', $this->perfil, PDO::PARAM_STR);
		$consulta->execute();		
		return $objetoAccesoDato->retornarUltimoIdInsertado();
	}
}