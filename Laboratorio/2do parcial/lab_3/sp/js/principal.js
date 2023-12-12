"use strict";
$(() => {
    VerificarJWT();
    AdministrarListadoJuguetes();
    AdministrarAltaJuguetes();
    $("#divTablaDer").on("click", "#btnEnviar", function () {
        AgregarJuguete();
    });
});
function VerificarJWT() {
    let jwt = localStorage.getItem("jwt");
    $.ajax({
        type: "GET",
        url: URL_API + "login",
        dataType: "json",
        headers: { "Authorization": "Bearer " + jwt }
    })
        .done(function (obj_rta) {
        if (obj_rta.exito) {
            $("#nombre_usuario").html("");
            $("#nombre_usuario").html(obj_rta.payload.usuario.Nombre).show(1000);
            console.log(obj_rta);
        }
    })
        .fail((jqXHR, textStatus, errorThrown) => {
        let retorno = JSON.parse(jqXHR.responseText);
        console.log(retorno.mensaje);
        alert(retorno.mensaje + "\n Redirigiendo a inicio !");
        setTimeout(() => {
            $(location).attr('href', URL_BASE + "login.html");
        }, 1500);
    });
}
function AdministrarListadoJuguetes() {
    $("#listado_juguetes").on("click", (e) => {
        MostrarListadoJuguetes();
    });
}
function MostrarListadoJuguetes() {
    let jwt = localStorage.getItem("jwt");
    $.ajax({
        type: "GET",
        url: URL_API + "listarJuguetesBD",
        dataType: "json",
        headers: { "Authorization": "Bearer " + jwt }
    })
        .done(function (resultado) {
        console.log(resultado);
        let tabla = ArmarTablaJuguetes(resultado.dato);
        $("#divTablaIzq").html(tabla).show(1000);
    })
        .fail((jqXHR, textStatus, errorThrown) => {
        let retorno = JSON.parse(jqXHR.responseText);
        alert(retorno.mensaje + "\nVolviendo al login!");
        localStorage.removeItem("jwt");
        setTimeout(() => {
            $(location).attr('href', URL_BASE + "login.html");
        }, 1500);
    });
}
function ArmarTablaJuguetes(juguetes) {
    let tabla = '<table class="table table-hover">';
    tabla += '<tr> <th>ID</th> <th>MARCA</th> <th>PRECIO</th> <th>FOTO</th> </tr>';
    if (juguetes.length == 0) {
        tabla += '<tr><td>---</td><td>---</td><td>---</td><td>---</td><th>---</td></tr>';
    }
    else {
        juguetes.forEach((jug) => {
            tabla += "<tr><td>" + jug.id + "</td><td>" + jug.marca + "</td><td>" + jug.precio + "</td>" +
                "<td><img src='" + URL_API + "juguetes/fotos/" + jug.path_foto + "' width='50px' height='50px'></td>" +
                "</tr>";
        });
    }
    tabla += "</table>";
    return tabla;
}
function AdministrarAltaJuguetes() {
    $("#alta_juguete").on("click", (e) => {
        let tabla = ArmarFormularioAlta();
        $("#divTablaDer").html(tabla).show(1000);
    });
}
function AgregarJuguete() {
    let jwt = localStorage.getItem("jwt");
    let marca = $("#txtMarca").val();
    let precio = $("#txtPrecio").val();
    let foto = $("#txtFoto")[0];
    let formData = new FormData();
    formData.append("juguete_json", JSON.stringify({ "marca": marca, "precio": precio }));
    formData.append("foto", foto.files[0]);
    $.ajax({
        type: "POST",
        url: URL_API + "agregarJugueteBD",
        dataType: "json",
        data: formData,
        cache: false,
        processData: false,
        contentType: false,
        headers: { "Authorization": "Bearer " + jwt }
    })
        .done(function (obj_rta) {
        let cadena = JSON.stringify(obj_rta);
        alert(cadena);
        console.log(cadena);
    })
        .fail((jqXHR, textStatus, errorThrown) => {
        let retorno = JSON.parse(jqXHR.responseText);
        console.log(retorno.mensaje);
        alert(retorno.mensaje + "\n Redirigiendo a inicio !");
        setTimeout(() => {
            $(location).attr('href', URL_BASE + "login.html");
        }, 1500);
    });
}
function ArmarFormularioAlta() {
    let tabla = `
    <div class="container-fluid">
        <br>
        <div class="row">
            <div class="offset-4 col-8 text-info">
                <h2>
                    JUGUETES
                </h2>
            </div>
        </div>
    
        <div class="row">
    
            <div class="offset-4 col-4">
    
                <div class="form-bottom" style="background-color: darkcyan;">
    
                    <form role="form" action="" method="" class="">
                        <br>
                        <div class="form-group">                                  
                            <div class="input-group m-2">
                                <div class="input-group-prepend">
                                    <span class="input-group-text fas fa-trademark"></span> 
                                    <input type="text" class="form-control" name="marca" id="txtMarca" style="width:248px;" placeholder="Marca" />
                                </div>
                            </div>
                        </div>
    
                        <div class="form-group">    
                            <div class="input-group m-2">
                                <div class="input-group-prepend">
                                    <span class="input-group-text fas fa-dollar-sign"></span> 
                                    <input type="text" class="form-control" name="precio" id="txtPrecio" style="width:250px;" placeholder="Precio" />
                                </div>
                            </div>
                        </div>
    
                        <div class="form-group">
                            <div class="input-group m-2">
                                <div class="input-group-prepend">
                                    <span class="input-group-text fas fa-camera"></span> 
                                    <input type="file" class="form-control" name="foto" id="txtFoto" style="width:250px;" placeholder="Foto" />
                                </div>
                            </div>
                        </div>
    
                        <div class="row m-2">
                            <div class="col-6">
                                <button type="button" class="btn btn-success btn-block" id="btnEnviar">Agregar</button>
                            </div>
                            <div class="col-6">
                                <button type="reset" class="btn btn-warning btn-block">Limpiar</button>
                            </div>
                        </div>
    
                        <br>
                    </form>
    
    
                </div>
    
            </div>
    
        </div>
    
    </div>
    `;
    return tabla;
}
//# sourceMappingURL=principal.js.map