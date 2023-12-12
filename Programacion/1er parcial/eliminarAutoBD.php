<?php

use Garcia\Lautaro\AutoBD;

include_once "./clases/autoBD.php";

$auto_json = isset($_POST["auto_json"]) ? $_POST["auto_json"] : "";
if ($auto_json!="") {

    $objeto = json_decode($auto_json);
   
    if(AutoBD::Eliminar($objeto->patente) == true)
    {
        $autoBorrado = new AutoBD($objeto->patente,$objeto->marca,$objeto->color,$objeto->precio);
        
        echo $autoBorrado->guardarJSON('./archivos/autos_eliminados.json');

    }
    else 
    {
        echo  $respuesta = '{ "exito": "' . false . '", "mensaje": "No se elimino el auto "}' ; 
    }
}
else 
{
    echo  $respuesta = '{ "exito": "' . json_encode(false) . '", "mensaje": "No se cargaron datos"}' ; 
}



?>