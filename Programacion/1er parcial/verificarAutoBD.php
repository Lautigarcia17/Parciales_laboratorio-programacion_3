<?php

use Garcia\Lautaro\AutoBD;

include_once "./clases/autoBD.php";

$patente = isset($_POST["obj_auto"]) ? $_POST["obj_auto"] : "";

if ($patente !="") 
{
    $objPatente = json_decode($patente);
    $autoPatente = new AutoBD($objPatente->patente,"","",0,"");

    $arrayAutos = AutoBD::traer();

    $respuesta = '{}';
    foreach ($arrayAutos as $value) {
        if ($autoPatente->getPatente() == $value->getPatente()) {
            $respuesta = $value->toJSON();
            break;
        }
    }

    echo $respuesta;
}
else 
{
    echo  $respuesta = '{ "exito": "' . json_encode(false) . '", "mensaje": "No se cargaron datos"}' ; 
}


?>
