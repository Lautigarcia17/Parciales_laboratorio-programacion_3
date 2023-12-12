<?php

include_once "./clases/autoBD.php";
require_once __DIR__ . '/vendor/autoload.php';

use Garcia\Lautaro\AutoBD;


    header('content-type:application/pdf'); // indico que el tipo de archivo que voy a usar es de tipo pdf   -----> siempre tiene que dar salida esto, sino genera error, por eso va primero la validacion

    $mpdf = new \Mpdf\mpdf(['orientation' => 'P', 
    'pagenumPrefix' => 'Página nro. ',
    'pagenumSuffix' => ' - ',
    'nbpgPrefix' => ' de ',
    'nbpgSuffix' => ' páginas']);

    $nombreCompleto = "Garcia, Lautaro Nahuel";

    $mpdf->SetHeader($nombreCompleto.'||{PAGENO}{nbpg}');
    $mpdf->SetFooter('|{DATE d-m-Y}|');

    $arrayAutos = AutoBD::traer();
   
    $i = 0;
    $tabla='
        <html>
        <head>
            <title>Listado de Autos</title>
        </head>
        <body>
            <table border="1"> "<br>"
                <caption>Listado de autos</caption>
                <thead>
                    "<tr>
                            <th>Patente</th>
                            <th>Marca</th>
                            <th>Color</th>
                            <th>Precio</th>
                            <th>Foto</th>
                    </tr>"
                </thead>';

    foreach ($arrayAutos as $auto) {

        $tabla.="<tr>
                    <td> ". $auto->getPatente() . "</td>".
                    "<td>". $auto->getMarca() . "</td>".
                    '<td style="background-color: '. $auto->getColor() . '; width: 20px; height: 20px;"></td> '.
                    "<td>". $auto->getPrecio() . "</td> ".
                    "<td><img src='./autos/imagenes/". $auto->getPathFoto() . "' width='100px' height='100px'></td>" .
                "</tr>";        
    }
    $tabla.= '</table> 
              </body> </html>';
    $mpdf->WriteHTML($tabla);
    $mpdf->Output();  
    



?>