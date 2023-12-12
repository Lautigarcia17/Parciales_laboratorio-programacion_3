"use strict";
/// <reference path = "./autoBD.ts" />
var PrimerParcial;
(function (PrimerParcial) {
    window.addEventListener("load", () => {
        PrimerParcial.ManejadoraAutoFotos.ListarAutosBD();
    });
    class ManejadoraAutoFotos {
        static AgregarAutoFotoBD() {
            let xhttp = new XMLHttpRequest();
            let patente = (document.getElementById("patente").value);
            let marca = (document.getElementById("marca").value);
            let color = (document.getElementById("color").value);
            let precio = parseFloat((document.getElementById("precio").value));
            let foto = document.getElementById("foto");
            if (patente != "" && marca != "" && color != "" && precio != -1) {
                let auto = new Garcia.AutoBD(patente, marca, color, precio, foto);
                let formData = new FormData();
                xhttp.open("POST", "./BACKEND/agregarAutoBD.php");
                formData.append('patente', auto.patente);
                formData.append('marca', auto.marca);
                formData.append('color', auto.color);
                formData.append('precio', auto.precio.toString());
                formData.append('foto', foto.files[0]);
                xhttp.setRequestHeader("enctype", "multipart/form-data");
                xhttp.send(formData);
                xhttp.onreadystatechange = () => {
                    console.log(xhttp.readyState + " - " + xhttp.status);
                    if (xhttp.readyState == 4 && xhttp.status == 200) {
                        console.log(xhttp.responseText);
                        alert(xhttp.responseText);
                        ManejadoraAutoFotos.ListarAutosBD();
                    }
                };
            }
            else {
                alert("Debe cargar todos los datos");
            }
        }
        static ListarAutosBD() {
            let xhttp = new XMLHttpRequest();
            xhttp.open("get", "./BACKEND/listadoAutosBD.php");
            // xhttp.open("get","./BACKEND/listadoAutosBD.php?tabla=mostrar");
            xhttp.send();
            xhttp.onreadystatechange = () => {
                console.log(xhttp.readyState + " - " + xhttp.status);
                if (xhttp.readyState == 4 && xhttp.status == 200) {
                    let objeto = JSON.parse(xhttp.responseText);
                    if (objeto.formato == "tabla") {
                        document.getElementById("divTablaAutoFotos").innerHTML = objeto.datos;
                    }
                    else if (objeto.formato == "json") {
                        let tabla = `<table class="table table-hover">
                        <tr>
                            <th>PATENTE</th><th>MARCA</th><th>COLOR</th><th>PRECIO</th><th>FOTO</th>
                        <tr>`;
                        objeto.datos.forEach((elemento) => {
                            if (elemento != "") {
                                tabla += `<tr> 
                                                <td>  ${elemento.patente}  </td> 
                                                <td>  ${elemento.marca}  </td>
                                                <td><input type='color' value="${elemento.color}"/></td>
                                                <td>  ${elemento.precio}  </td>
                                                <td><img src="./BACKEND/autos/imagenes/${elemento.pathFoto}" width='50px' height='50px'></td>
                                                <td>
                                                    <input type="button" value="Modificar" data-json=\' ${JSON.stringify(elemento)} \' data-action="Modificar">
                                                    <input type ="button" value="Eliminar" data-json=\' ${JSON.stringify(elemento)} \' data-action="Eliminar">
                                                </td>
                                        <tr>`;
                            }
                        });
                        tabla += "</table>";
                        document.getElementById("divTablaAutoFotos").innerHTML = tabla; // responseText es la respuesta del servidor
                    }
                    var botonModificar = document.querySelectorAll('[data-action="Modificar"]');
                    botonModificar.forEach(function (boton) {
                        boton.addEventListener('click', function () {
                            var objAuto = JSON.parse(boton.getAttribute('data-json'));
                            document.getElementById("patente").value = objAuto.patente;
                            document.getElementById("marca").value = objAuto.marca;
                            document.getElementById("color").value = objAuto.color;
                            document.getElementById("precio").value = objAuto.precio;
                            document.getElementById("imgFoto").src = "BACKEND/autos/imagenes/" + objAuto.pathFoto;
                            const inputPatente = document.getElementById("patente");
                            if (inputPatente) {
                                inputPatente.readOnly = true;
                            }
                            // Manejadora.ModificarAuto();
                        });
                    });
                    var botonEliminar = document.querySelectorAll('[data-action="Eliminar"]');
                    botonEliminar.forEach(function (boton) {
                        boton.addEventListener('click', function () {
                            var objAuto = JSON.parse(boton.getAttribute('data-json'));
                            document.getElementById("patente").value = objAuto.patente;
                            document.getElementById("marca").value = objAuto.marca;
                            document.getElementById("color").value = objAuto.color;
                            document.getElementById("precio").value = objAuto.precio;
                            document.getElementById("imgFoto").src = "BACKEND/autos/imagenes/" + objAuto.pathFoto;
                            const inputPatente = document.getElementById("patente");
                            if (inputPatente) {
                                inputPatente.readOnly = true;
                            }
                            PrimerParcial.ManejadoraAutoBd.EliminarAuto();
                        });
                    });
                }
            };
        }
        static ModificarAutoFotoBD() {
            let xhttp = new XMLHttpRequest();
            let patente = (document.getElementById("patente").value);
            let marca = (document.getElementById("marca").value);
            let color = (document.getElementById("color").value);
            let precio = parseFloat((document.getElementById("precio").value));
            let foto = document.getElementById("foto");
            let auto = new Garcia.AutoBD(patente, marca, color, precio);
            let formData = new FormData();
            xhttp.open("POST", "./BACKEND/modificarAutoBDFoto.php");
            formData.append('auto_json', auto.toJSON());
            formData.append('foto', foto.files[0]);
            xhttp.setRequestHeader("enctype", "multipart/form-data");
            xhttp.send(formData);
            xhttp.onreadystatechange = () => {
                console.log(xhttp.readyState + " - " + xhttp.status);
                if (xhttp.readyState == 4 && xhttp.status == 200) {
                    console.log(xhttp.responseText);
                    alert(xhttp.responseText);
                    const inputPatente = document.getElementById("patente");
                    ManejadoraAutoFotos.ListarAutosBD();
                    if (inputPatente) {
                        inputPatente.readOnly = false;
                    }
                }
            };
        }
        static EliminarAutoFotoBD() {
            let xhttp = new XMLHttpRequest();
            let patente = (document.getElementById("patente").value);
            let marca = (document.getElementById("marca").value);
            let color = (document.getElementById("color").value);
            let precio = parseInt((document.getElementById("precio").value));
            let foto = document.getElementById("foto");
            let auto = new Garcia.AutoBD(patente, marca, color, precio, foto);
            let formData = new FormData();
            if (confirm(`¿ Seguro de eliminar el auto con la patente : ${patente} y
                la marca: ${marca} ?`)) {
                // xhttp.open("GET","./BACKEND/eliminarAutoBDFoto.php");
                // xhttp.send();
                // xhttp.onreadystatechange = () => 
                // {
                //     console.log(xhttp.readyState + " - " + xhttp.status);
                //     if (xhttp.readyState == 4 && xhttp.status == 200) 
                //     {
                //         console.log(xhttp.responseText);
                //         (<HTMLDivElement>document.getElementById("divTablaAutoFotos")).innerHTML = xhttp.responseText; 
                //     }
                // };
                xhttp.open("POST", "./BACKEND/eliminarAutoBDFoto.php");
                formData.append('auto_json', auto.toJSON());
                xhttp.send(formData);
                xhttp.onreadystatechange = () => {
                    console.log(xhttp.readyState + " - " + xhttp.status);
                    if (xhttp.readyState == 4 && xhttp.status == 200) {
                        console.log(xhttp.responseText);
                        alert(xhttp.responseText);
                        ManejadoraAutoFotos.ListarAutosBD();
                        const inputPatente = document.getElementById("patente");
                        if (inputPatente) {
                            inputPatente.readOnly = false;
                        }
                    }
                };
            }
        }
        static ListarAutosPDF() {
            window.location.href = "./backend/listadoAutosPDF.php";
        }
    }
    PrimerParcial.ManejadoraAutoFotos = ManejadoraAutoFotos;
})(PrimerParcial || (PrimerParcial = {}));
//# sourceMappingURL=manejadoraAutoFotos.js.map