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
    
        foreach ($arrayAutos as $auto)
        {
            $grilla .= '    <tr> ' .
                                '<td>"'.$auto->getPatente(). '"</td>'.
                                '<td>"'.$auto->getMarca(). '"</td>'.
                                '<td><input type="color" value="'.$auto->getColor(). '"/></td>'.
                                '<td>'.$auto->getPrecio(). '"</td>'.
                                '<td><img src="./BACKEND/autos/imagenes/'.$auto->getPathFoto(). '" width="50px" height="50px"></td>'.
                                '<td>'.
                                '<input type="button" value="Modificar" data-json=\'' . json_encode($auto) . '\' data-action="Modificar">' .
                                '<input type = "button" value="Eliminar" data-json=\'' . json_encode($auto) .  '\' data-action="Eliminar">'.
                                '</td>'.
                        '</tr>';
        }
    
        $grilla .= '    </table>
                    </body>';

        $response = array(
            "formato" => "tabla",
            "datos" =>$grilla
        );
    }
    else {
        $grilla = $arrayAutos;
        $response = array(
            "formato" => "json",
            "datos" =>$grilla
        );
    }
    
    echo json_encode($response);
}
else 
{
    echo'{ "exito": "' . json_encode(false) . '", "mensaje": "No hay autos cargados "}' ; 

}



?>