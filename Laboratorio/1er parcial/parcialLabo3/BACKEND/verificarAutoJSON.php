<?php

use Garcia\Lautaro\Auto;

include_once "./clases/auto.php";

$patente = isset($_POST["patente"]) ? $_POST["patente"] : "";

$auto = new Auto($patente,"","",0);
echo Auto::verificarAutoJSON($auto);


?>