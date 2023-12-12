<?php


include_once "./clases/autoBD.php";
use Garcia\Lautaro\AutoBD;

$tabla = isset($_GET["tabla"]) ? $_GET["tabla"] : "";
$arrayAutos = AutoBD::traer();
if (count($arrayAutos) > 0) 
{
    if ($tabla =="mostrar") {
        $grilla = '
                <html>
                <head>
                    <title>Listado de Autos</title>
                </head>
                <body>
                    <table class="table" border="1">
                        <thead>
                            <tr>
                                <th> patente            </th>
                                <th> marca        </th>
                                <th> color        </th>
                                <th> precio     </th>
                                <th> foto     </th>
                            </tr>
                        </thead>';
    
        foreach ($arrayAutos as $value)
        {
            $grilla .= "    <tr>
                                <td>".$value->getPatente()."</td>
                                <td>".$value->getMarca()."</td>
                                <td>".$value->getColor()."</td>
                                <td>".$value->getPrecio()."</td>
                                <td><img src='./autos/imagenes/".$value->getPathFoto()."' width='50px' height='50px'></td>
                            </tr>";
        }
    
        $grilla .= '    </table>
                    </body>';
    }
    else {
        $grilla = json_encode($arrayAutos);
    }
    
    echo $grilla;
}
else 
{
    echo'{ "exito": "' . json_encode(false) . '", "mensaje": "No hay autos cargados "}' ; 

}



?>