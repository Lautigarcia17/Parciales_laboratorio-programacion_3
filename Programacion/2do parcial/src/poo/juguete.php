<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once "accesoDatos.php";

class Juguete 
{
	public int $id;
 	public string $marca;
  	public string $precio;
  	public string $path_foto;

//*********************************************************************************************//
/* IMPLEMENTO LAS FUNCIONES PARA SLIM */
//*********************************************************************************************//

	public function TraerTodos(Request $request, Response $response, array $args): Response 
	{
		$obj_respuesta = new stdClass();
        $obj_respuesta->exito = false;
        $obj_respuesta->mensaje = "No se encontraron juguetes!";
        $obj_respuesta->dato = null;
        $status = 424;

		$juguetes = Juguete::TraerTodosLosJuguetes();
		
		if (count($juguetes))
		{
			$obj_respuesta->exito = true;
            $obj_respuesta->mensaje = "Juguetes encontrados!";
            // $obj_respuesta->dato = json_encode($juguetes);
            $obj_respuesta->dato = $juguetes;
            $status = 200;
		}

		$newResponse = $response->withStatus($status);
		$newResponse->getBody()->write(json_encode($obj_respuesta));

		return $newResponse->withHeader('Content-Type', 'application/json');	
	}

	// public function TraerUno(Request $request, Response $response, array $args): Response 
	// {
    //  	$id = $args['id'];
    // 	$elJuguete = Juguete::TraerUnJuguete($id);

	// 	if ($elJuguete == false){
	// 		$objStd = new stdClass();
	// 		$objStd->mensaje = "Juguete no encontrado";
	// 		$objStd->exito = false;
	// 		$elJuguete = $objStd;
	// 	}  

	// 	$newResponse = $response->withStatus(200, "OK");
	// 	$newResponse->getBody()->write(json_encode($elJuguete));	

	// 	return $newResponse->withHeader('Content-Type', 'application/json');
	// }
	
	public function AgregarJuguete(Request $request, Response $response, array $args): Response 
	{
        $parametros = $request->getParsedBody();
		$obj_respuesta = new stdclass();
        $obj_respuesta->exito = false;
        $obj_respuesta->mensaje = "No se mando el juguete_json y/o la foto";
        $status = 418;


		if(isset($parametros["juguete_json"]) && count($request->getUploadedFiles()))
		{
			$obj = json_decode($parametros["juguete_json"]);
			$miJuguete = new Juguete();
			$miJuguete->marca = $obj->marca;
			$miJuguete->precio =  $obj->precio;	
	
		
			//Agregar foto archivo		
			$archivos = $request->getUploadedFiles();
			$destino = "./src/fotos/";
			
			$nombreAnterior = $archivos['foto']->getClientFilename();
			$extension = explode(".", $nombreAnterior);
			$extension = array_reverse($extension);
			
			$foto = $destino . $miJuguete->marca . "." . $extension[0];
			$archivos['foto']->moveTo("." . $foto); // "agrego un . para no guardarlo en el $foto y que se guarde en el path_foto, no respetando el ejemplo dado"
			$miJuguete->path_foto =  $foto;	
			//

			if ($miJuguete->InsertarJuguete()) 
			{
				$obj_respuesta->exito = true;
				$obj_respuesta->mensaje = "Juguete Agregado";
				$status = 200;
			}
			else {
				$obj_respuesta->mensaje = "No se pudo agregar el juguete";
			}
			
			
		}

        $newResponse = $response->withStatus($status);
        $newResponse->getBody()->write(json_encode($obj_respuesta));

		return $newResponse->withHeader('Content-Type', 'application/json');
    }
	
	public function ModificarJuguete(Request $request, Response $response, array $args): Response
	{ 
		$obj_respuesta = new stdclass();
        $obj_respuesta->exito = false;
        $obj_respuesta->mensaje = "No se enviaron los datos del juguete y/o foto";
        $status = 418;
		
		$parametros = $request->getParsedBody();

		if (isset($parametros["juguete"]) && count($request->getUploadedFiles())) 
		{
			$obj = json_decode($parametros["juguete"]);

			$juguete_modif = new Juguete();
			$juguete_modif->id = $obj->id_juguete;
			$juguete_modif->marca = $obj->marca;
			$juguete_modif->precio = $obj->precio;

			//Agregar foto archivo
		
				$archivos = $request->getUploadedFiles();
				$destino = "./src/fotos/";
				
				$nombreAnterior = $archivos['foto']->getClientFilename();
				$extension = explode(".", $nombreAnterior);
				$extension = array_reverse($extension);
				
				$foto = $destino . $juguete_modif->marca . "_modificacion." . $extension[0];
				$archivos['foto']->moveTo("." . $foto);
				$juguete_modif->path_foto =  $foto;	
			
			//


				$cantidadModificados = $juguete_modif->ModificarUno();
				if ($cantidadModificados > 0) 
				{
					$obj_respuesta->mensaje = "Juguete  modificado !!";
					$obj_respuesta->exito = true;
					$status = 200;
				}
				else 
				{
					$obj_respuesta->mensaje = "Juguete no modificado !!";
					unlink("." . $juguete_modif->path_foto);  // borro la antigua foto
				}
		}
		

		$newResponse = $response->withStatus($status);
		$newResponse->getBody()->write(json_encode($obj_respuesta));

		return $newResponse->withHeader('Content-Type', 'application/json');		
	}
	
	public function BorrarJuguete(Request $request, Response $response, array $args): Response 
	{		 
		$obj_respuesta = new stdClass();
		$obj_respuesta->exito = false;
        $obj_respuesta->mensaje = "No se pudo borrar el juguete";
        $status = 418;
		
		$id = $args['id_juguete'];

		if ($id >= 0) 
		{
			$Juguete = new Juguete();
			$Juguete->id = $id;
			
			$cantidadDeBorrados = $Juguete->BorrarUno();
			
			if($cantidadDeBorrados>0)
			{
				$obj_respuesta->mensaje = "...algo borró!!!";
				$obj_respuesta->exito = true;
				$status = 200;
			}
			else
			{
				$obj_respuesta->mensaje = "...no borró nada!!!";
			}
		}
		

		$newResponse = $response->withStatus($status);
		$newResponse->getBody()->write(json_encode($obj_respuesta));	

		return $newResponse->withHeader('Content-Type', 'application/json');
    }
	
//*********************************************************************************************//
/* FIN - AGREGO FUNCIONES PARA SLIM */
//*********************************************************************************************//

	public static function TraerTodosLosJuguetes()
	{
		$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta = $objetoAccesoDato->retornarConsulta("SELECT * FROM juguetes");
		$consulta->execute();			
		return $consulta->fetchAll(PDO::FETCH_CLASS, "Juguete");		
	}

	public static function TraerUnJuguete($id) 
	{
		$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta = $objetoAccesoDato->retornarConsulta("SELECT id, marca, precio, path_foto FROM juguetes where id = $id");
		$consulta->execute();
		$JugueteBuscado= $consulta->fetchObject('Juguete');
		return $JugueteBuscado;		
	}

	public function InsertarJuguete()
	{
		$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta = $objetoAccesoDato->retornarConsulta("INSERT into juguetes (marca,precio,path_foto)values(:marca,:precio,:path_foto)");
		$consulta->bindValue(':marca',$this->marca, PDO::PARAM_STR);
		$consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
		$consulta->bindValue(':path_foto', $this->path_foto, PDO::PARAM_STR);
		$consulta->execute();		
		return $objetoAccesoDato->retornarUltimoIdInsertado();
	}

	public function ModificarUno()
	{
		$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta = $objetoAccesoDato->retornarConsulta(
			"UPDATE juguetes 
				SET marca=:marca,precio=:precio,path_foto=:foto  WHERE id=:id");
		$consulta->bindValue(':id',$this->id, PDO::PARAM_INT);
		$consulta->bindValue(':marca',$this->marca, PDO::PARAM_STR);
		$consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
		$consulta->bindValue(':foto', $this->path_foto, PDO::PARAM_STR);
		$consulta->execute();
		return $consulta->rowCount();
	 }

	public function BorrarUno()
	{
	 	$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta = $objetoAccesoDato->RetornarConsulta("delete from juguetes	WHERE id=:id");	
		$consulta->bindValue(':id',$this->id, PDO::PARAM_INT);		
		$consulta->execute();
		return $consulta->rowCount();
	}

}