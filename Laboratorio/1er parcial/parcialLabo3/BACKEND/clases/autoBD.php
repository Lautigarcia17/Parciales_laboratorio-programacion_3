<?php


namespace Garcia\Lautaro
{
    include_once "auto.php";
    include_once "IParte1.php";
    include_once "IParte2.php";
    include_once "IParte3.php";
    use Garcia\Lautaro\Auto;
    use IParte1;
    use IParte2;
    use IParte3;
    use PDO;
    use PDOException;

    class AutoBD extends Auto implements IParte1,IParte2,IParte3
    {
        protected string $pathFoto;


        public function __construct($patenteIngresada,$marcaIngresada,$colorIngresado,$precioIngresado,$pathFotoIngresada="") {
            parent::__construct($patenteIngresada,$marcaIngresada,$colorIngresado,$precioIngresado);
            $this->pathFoto = $pathFotoIngresada;
        }
        public function getPathFoto() {
            return $this->pathFoto;
        }
        
        public function jsonSerialize() // para que json reconozca mis atributos protegidos
        {
            $data = parent::jsonSerialize();
            $data["pathFoto"] = $this->pathFoto;
            return $data;
        }

        public function toJSON() : string
        {
            $data = json_decode(parent::toJSON(),true); // true para que sea un array asociativo
            $data['pathFoto'] = $this->pathFoto;
            return json_encode($data); 
        }

        public function agregar() : bool
        {
            $rtn = false;
            try {
                if ($this->pathFoto !="") {
                    $this->pathFoto=$this->patente . "." . date("His") . "." . pathinfo($this->pathFoto, PATHINFO_EXTENSION);
                }


                $pdo = new PDO("mysql:host=localhost;dbname=garage_bd","root","");
                $sql = $pdo->prepare("INSERT INTO `autos`(`patente`, `marca`,`color`, `precio`,`foto`) VALUES (:patente,:marca,:color,:precio,:foto)"); // retonar false o retorna un PDOStatement
                $sql->bindParam(':patente',$this->patente,PDO::PARAM_STR,30);
                $sql->bindParam(':marca',$this->marca,PDO::PARAM_STR,30);
                $sql->bindParam(':color',$this->color,PDO::PARAM_STR,15);
                $sql->bindParam(':precio',$this->precio,PDO::PARAM_INT);
                $sql->bindParam(':foto',$this->pathFoto,PDO::PARAM_STR,50);
            
                $sql->execute();
                $rtn = $sql->rowCount() > 0 ? true : false;
    
                
            } catch (PDOException $e) {
                echo " Error = ". $e->getMessage();
            }
            return $rtn;
        }
        public static function traer() : array
        {
            try 
            {
                $arrayAutos = array();
                $pdo = new PDO("mysql:host=localhost;dbname=garage_bd","root","");
                $sql = $pdo->query("SELECT * FROM autos"); 
    
                if($sql !=false)
                {
                    $resultado = $sql->fetchAll();
                    foreach ($resultado as $value) {
                        $foto = $value["foto"] != null ? $value["foto"] : ""; 
                        array_push($arrayAutos,new AutoBD($value["patente"],
                                                          $value["marca"],
                                                          $value["color"],$value["precio"],
                                                          $foto));
                    }
                } 
    
            } catch (PDOException $e) {
                echo " Error = ". $e->getMessage();
            }
            return $arrayAutos;
        }

        public static function eliminar($patente) : bool 
        {
            $rtn = false;
            try {
    
                $pdo = new PDO("mysql:host=localhost;dbname=garage_bd","root","");
                $sql = $pdo->prepare("DELETE FROM `autos` WHERE patente = :patente");    
                $sql->bindParam(':patente',$patente,PDO::PARAM_STR,30);
                $sql->execute();
                
                $rtn = $sql->rowCount() > 0 ? true : false;
    
            } catch (PDOException $e) {
                echo " Error = ". $e->getMessage();
            }
    
            return $rtn;  
        }
        public function modificar() : bool
        {
            try {
                if ($this->pathFoto !="") {
                    $this->pathFoto=$this->patente . "." . date("His") . "." . pathinfo($this->pathFoto, PATHINFO_EXTENSION);
                }

                $rtn = false;
                $pdo = new PDO("mysql:host=localhost;dbname=garage_bd","root","");
                $sql = $pdo->prepare("UPDATE `autos` SET `marca`=:marca,`color`= :color,`precio`=:precio,`foto`=:foto WHERE patente = :patente"); // retonar false o retorna un PDOStatement
               
                $sql->bindParam(':patente',$this->patente,PDO::PARAM_STR,30);
                $sql->bindParam(':marca',$this->marca,PDO::PARAM_STR,30);
                $sql->bindParam(':color',$this->color,PDO::PARAM_STR,15);
                $sql->bindParam(':precio',$this->precio,PDO::PARAM_INT);
                $sql->bindParam(':foto',$this->pathFoto,PDO::PARAM_STR,50);
                
                $sql->execute();
     
                $rtn = $sql->rowCount() > 0 ? true : false;
                
            } catch (PDOException $e) {
                echo " Error = ". $e->getMessage();
            }
    
            return $rtn;
        }

        public function existe($arrayAutos) : bool
        {
            $rtn=false;
            foreach ($arrayAutos as $value) {
                
                if ($value->patente == $this->patente) {
                    $rtn=true;
                    break;
                }
            }
            return $rtn;
        }
        public function guardarEnArchivo() : string
        {
            $respuesta = "";

            $nuevoNombre = $this->pathFoto !="" ?  $this->patente . "." . $this->marca .".borrado." . date("His") . "." . pathinfo($this->pathFoto, PATHINFO_EXTENSION) : "";
            $rutaAntigua = __DIR__ . '/../autos/imagenes/' . $this->pathFoto;
            $rutaNueva = __DIR__ . '/autosBorrados/' . $nuevoNombre;
            
            $this->pathFoto = $nuevoNombre;
            $archivo = fopen("./archivos/autosbd_borrados.txt","a"); 
            $retorno = fwrite($archivo,$this->toJSON()."\n");

            if (copy($rutaAntigua, $rutaNueva)) {
                unlink($rutaAntigua); 
                echo 'Reemplazo de imagen exitoso.';
            
            }

            if ($retorno) {
                $respuesta = '{ "exito": "' . true . '", "mensaje": "Se cargo el auto en el archivo "}' ; 
            }else {
                $respuesta = '{ "exito": "' . false . '", "mensaje": "No Se cargo el auto en el archivo "}' ; 
            }

            fclose($archivo);

            return $respuesta;
        }

        public static function traerJSONFoto($path) : array
        {     
            if (file_exists($path)) 
            {
                $arrayAutos = array();
                $archivo = fopen($path,"r");
                while(!feof($archivo))
                {
                    $linea = fgets($archivo);
                    $linea = trim($linea);

                    if ($linea !="") 
                    {
                        $objeto = json_decode($linea); //json a objeto
                        array_push($arrayAutos,new AutoBD($objeto->patente,$objeto->marca,$objeto->color,$objeto->precio,$objeto->pathFoto));
                    }
                }
                fclose($archivo);

            }else 
            {
                echo "No existe el archivo";
            }
            return $arrayAutos;  
        }

        public static function obtenerPathFotoAuto($auto) : bool
        {
            $arrayAutos = AutoBD::traer();
            $rtn = false;

            foreach ($arrayAutos as $value) {
                if ($value->patente == $auto->patente) {

                    $auto->pathFoto = $value->pathFoto;
                    $rtn = true;
                    break;
                }
            }
            return $rtn;
        }

    }
}

?>