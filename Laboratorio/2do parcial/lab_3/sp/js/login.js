"use strict";
$(() => {
    Login();
});
function Login() {
    $("#btnEnviar").on("click", (e) => {
        e.preventDefault();
        let correo = $("#correo").val();
        let clave = $("#clave").val();
        if (clave != "" && correo != "") {
            let datos = {};
            datos.correo = correo;
            datos.clave = clave;
            $.ajax({
                type: "POST",
                url: URL_API + "login",
                dataType: "text",
                data: datos,
                headers: { "Authorization": "Bearer  aaa" }
            })
                .done(function (obj_rta) {
                let obj = JSON.parse(obj_rta);
                if (obj.exito) {
                    localStorage.setItem("jwt", obj.jwt);
                    setTimeout(() => {
                        $(location).attr("href", URL_BASE + "principal.html");
                    }, 2000);
                    alert(obj.mensaje + "\nRedigiriendo la pagina a principal.php");
                }
                else {
                    alert(obj.jwt);
                    console.log(obj.jwt);
                }
            })
                .fail((jqXHR, textStatus, errorThrown) => {
                let retorno = JSON.parse(jqXHR.responseText);
                console.log(retorno.mensaje);
                alert(retorno.mensaje);
            });
        }
        else {
            console.log("Debe cargar los datos primero");
            alert("Debe cargar los datos primero");
        }
    });
}
//# sourceMappingURL=login.js.map