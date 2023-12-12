"use strict";
/// <reference path = "./auto.ts" />
var PrimerParcial;
(function (PrimerParcial) {
    class Manejadora {
        // auto_json.html
        static AgregarAutosJSON() {
            let xhttp = new XMLHttpRequest();
            let patente = (document.getElementById("patente").value);
            let marca = (document.getElementById("marca").value);
            let color = (document.getElementById("color").value);
            let precio = (document.getElementById("precio").value);
            if (patente != "" && marca != "" && color != "" && precio != "") {
                let formData = new FormData();
                xhttp.open("POST", "./BACKEND/altaAutoJSON.php");
                formData.append('patente', patente);
                formData.append('marca', marca);
                formData.append('color', color);
                formData.append('precio', precio);
                xhttp.send(formData);
                xhttp.onreadystatechange = () => {
                    console.log(xhttp.readyState + " - " + xhttp.status);
                    if (xhttp.readyState == 4 && xhttp.status == 200) {
                        console.log(xhttp.responseText);
                        alert(xhttp.responseText);
                    }
                };
            }
            else {
                alert("Debe cargar todos los datos");
            }
        }
        static Alertar() {
            let xhttp = new XMLHttpRequest();
            xhttp.open("get", "http://localhost:8080/");
            xhttp.send();
            xhttp.onreadystatechange = () => {
                console.log(xhttp.readyState + " - " + xhttp.status);
                if (xhttp.readyState == 4 && xhttp.status == 200) {
                    document.getElementById("divTabla").innerHTML = xhttp.responseText; // responseText es la respuesta del servidor
                    console.log(xhttp.responseText);
                }
            };
        }
        static ListarAutosJSON() {
            let xhttp = new XMLHttpRequest();
            xhttp.open("get", "./BACKEND/listadoAutosJSON.php");
            xhttp.send();
            xhttp.onreadystatechange = () => {
                console.log(xhttp.readyState + " - " + xhttp.status);
                if (xhttp.readyState == 4 && xhttp.status == 200) {
                    document.getElementById("divTabla").innerHTML = xhttp.responseText; // responseText es la respuesta del servidor
                    console.log(xhttp.responseText);
                }
            };
        }
        static VerificarAutoJSON() {
            let xhttp = new XMLHttpRequest();
            let patente = (document.getElementById("patente").value);
            if (patente != "") {
                let formData = new FormData();
                xhttp.open("POST", "./BACKEND/VerificarAutoJSON.php");
                formData.append('patente', patente);
                xhttp.send(formData);
                xhttp.onreadystatechange = () => {
                    console.log(xhttp.readyState + " - " + xhttp.status);
                    if (xhttp.readyState == 4 && xhttp.status == 200) {
                        console.log(xhttp.responseText);
                        alert(xhttp.responseText);
                    }
                };
            }
            else {
                alert("Debe cargar la patente");
            }
        }
    }
    PrimerParcial.Manejadora = Manejadora;
})(PrimerParcial || (PrimerParcial = {}));
//# sourceMappingURL=manejadoraa.js.map