<?php

use Garcia\Lautaro\Auto;

include_once "./clases/auto.php";

$arrayAutos = Auto::traerJSON('./archivos/autos.json');
$autosJson = json_encode($arrayAutos);

echo $autosJson;






?>
