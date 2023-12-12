<?php

use Garcia\Lautaro\AutoBD;
include_once "./clases/autoBD.php";
require_once __DIR__ . '/vendor/autoload.php';



    header('content-type:application/pdf'); // indico que el tipo de archivo que voy a usar es de tipo pdf   -----> siempre tiene que dar salida esto, sino genera error, por eso va primero la validacion

    $mpdf = new \Mpdf\mpdf(['orientation' => 'P', 
    'pagenumPrefix' => 'Página nro. ',
    'pagenumSuffix' => ' - ',
    'nbpgPrefix' => ' de ',
    'nbpgSuffix' => ' páginas']);

    $nombreCompleto = "Garcia Lautaro";

    $mpdf->SetHeader($nombreCompleto.'||{PAGENO}{nbpg}');
    $mpdf->SetFooter('|{DATE d-m-Y}|');

    $arrayAutos = AutoBD::traer();
   

    $tabla = '<table border="1">' . "<br>";
    $tabla.= '<caption>Listado de autos</caption>';
    $tabla.= "<tr>
            <th>Patente</th>
            <th>Marca</th>
            <th>Color</th>
            <th>Precio</th>
            <th>Foto</th>
            </tr>" . "<br>";
    foreach ($arrayAutos as $auto) {
        
        $tabla.="<tr>
                    <td> ". $auto->getPatente() . "</td>".
                    "<td>". $auto->getMarca() . "</td>".
                    "<td>". $auto->getColor() . "</td> ".
                    "<td>". $auto->getPrecio() . "</td> ".
                    "<td><img src='./autos/imagenes/".$auto->getPathFoto() . "' width='100px' height='100px'></td>" .
                "</tr>";
        
    }
    $tabla.= '</table>';
    $mpdf->WriteHTML($tabla);
    $mpdf->Output();  



?>