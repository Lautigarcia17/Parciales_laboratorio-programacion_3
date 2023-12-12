<?php

namespace Garcia\Lautaro
{

    use JsonSerializable;

    class Auto implements JsonSerializable
    {
        
        protected string $patente;
        protected string $marca;
        protected string $color;
        protected int $precio;
        

        public function __construct($patenteIngresada,$marcaIngresada,$colorIngresado,$precioIngresado)
        {
            $this->patente = $patenteIngresada;
            $this->marca = $marcaIngresada;
            $this->color = $colorIngresado;
            $this->precio = $precioIngresado;
        }

        public function getPatente() : string
        {
            return $this->patente;
        }
        public function getMarca() : string
        {
            return $this->marca;
        }
        public function getColor() : string
        {
            return $this->color;
        }
        public function getPrecio() : int
        {
            return $this->precio;
        }

        public function jsonSerialize() // para que json reconozca mis atributos protegidos
        {
            return [
                'patente' => $this->patente,
                'marca' => $this->marca,
                'color' => $this->color,
                'precio' => $this->precio
            ];
        }


        public function toJSON() : string
        {
            return '{ "patente": "' . $this->patente . '", "marca": "' . $this->marca . '", "color": "' . $this->color . '", "precio": "'. $this->precio . '"}'; 
        }

        public function guardarJSON($path) : string
        {
            $respuesta = "";

            $archivo = fopen( $path,"a"); 
            $retorno = fwrite($archivo,$this->toJSON()."\n");
            if ($retorno) {
                $respuesta = '{ "exito": "' . true . '", "mensaje": "Se cargo el auto "}' ; 
            }else {
                $respuesta = '{ "exito": "' . false . '", "mensaje": "No Se cargo el auto "}' ; 
            }

            fclose($archivo);

            return $respuesta;
        }

        public static function traerJSON($path) : array
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
                        array_push($arrayAutos,new Auto($objeto->patente,$objeto->marca,$objeto->color,$objeto->precio));
                    }
                }
                fclose($archivo);

            }else 
            {
                echo "No existe el archivo";
            }
            return $arrayAutos;  
        }

        public static function verificarAutoJSON($auto) : string
        {
            $retornoJson = '{ "exito": "' . json_encode(false)  . '", "mensaje": "No se encontro el auto "}' ; 

            if (file_exists("./archivos/autos.json")) 
            {
                $archivo = fopen("./archivos/autos.json","r");
                while(!feof($archivo))
                {
                    $linea = fgets($archivo);
                    $linea = trim($linea);
    
                    if ($linea !="") 
                    {
                        $objeto = json_decode($linea); //json a objeto
                        if ($auto->patente == $objeto->patente) {
                            $retornoJson = '{ "exito": "' . json_encode(true) . '", "mensaje": "Se encontro el auto "}' ; 
                            break;
                        }
                    }
                }
                fclose($archivo);
            }else 
            {
                echo "No existe el archivo";
            }
            return $retornoJson; 
        }


    }
}





?>