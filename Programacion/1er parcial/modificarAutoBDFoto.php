<?php

use Garcia\Lautaro\AutoBD;

include_once "./clases/autoBD.php";

$auto_json = isset($_POST["auto_json"]) ? $_POST["auto_json"] : "";
$foto = isset($_FILES["foto"]["name"]) ? $_FILES["foto"]["name"] : "";

if ($auto_json!="" && $foto!="") {

    $objeto = json_decode($auto_json);
    $auto = new AutoBD($objeto->patente,$objeto->marca,$objeto->color,$objeto->precio,$foto);

    // GENERO OBJETO CON LA RUTA VIEJA DEL OBJETO A MODIFICAR
    $objetoPathViejo = new stdClass();
    $objetoPathViejo->patente = $auto->getPatente();
    if(AutoBD::obtenerPathFotoAuto($objetoPathViejo) == false)
    {
        echo'{ "exito": "' . json_encode(false) . '", "mensaje": "No se encontro la foto del auto"}' ; 
    }

    //


    if ($auto->modificar()) 
    {
        move_uploaded_file($_FILES["foto"]["tmp_name"],"./autos/imagenes/" . $auto->getPathFoto());

        if ($objetoPathViejo->pathFoto !=""){
            $nuevoNombre = $objetoPathViejo->pathFoto !="" ?  $objetoPathViejo->patente . ".modificado." . date("His") . "." . pathinfo($objetoPathViejo->pathFoto, PATHINFO_EXTENSION) : ""; // Genero el path de la ruta antigua
            $rutaAntigua =  'autos/imagenes/' . $objetoPathViejo->pathFoto;
            $rutaNueva =  'autosModificados/' . $nuevoNombre;
    
            if (copy($rutaAntigua, $rutaNueva)) { // borro donde estaba anteriormente la ruta, y la mando a la lista de fotos modificadas
                unlink($rutaAntigua);  // borro la antigua foto
            }
        }

        echo'{ "exito": "' . true . '", "mensaje": "Se modifico el auto "}' ; 
    }
    else 
    {
        echo'{ "exito": "' . false . '", "mensaje": "No se modifico el auto "}' ; 

    }
}
else 
{
    $arrayAutos = AutoBD::traer();
    
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

    echo $grilla;
}



?>