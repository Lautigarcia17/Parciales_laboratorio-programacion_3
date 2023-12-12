<?php

use Garcia\Lautaro\Auto;

include_once "./clases/auto.php";

$patente = isset($_POST["patente"]) ? $_POST["patente"] : "";
$marca = isset($_POST["marca"]) ? $_POST["marca"] : "";
$color = isset($_POST["color"]) ?  $_POST["color"] : "";
$precio = isset($_POST["precio"]) ?  (float)$_POST["precio"] : 0;

if ($patente !="" && $marca !="" && $color !="" && $precio !="") 
{
    $auto = new Auto($patente,$marca,$color,$precio);
    echo $auto->guardarJSON('./archivos/autos.json');

}
else 
{
    echo  $respuesta = '{ "exito": "' . json_encode(false) . '", "mensaje": "No se cargaron todos los datos"}' ; 
}






?>