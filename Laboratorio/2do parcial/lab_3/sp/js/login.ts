/// <reference path="../node_modules/@types/jquery/index.d.ts" />

$(()=>{
    Login();
    
})

function Login()
{
    $("#btnEnviar").on("click" , (e:any)=>
    {
        e.preventDefault();

        let correo = $("#correo").val();
        let clave = $("#clave").val();
        if (clave != "" && correo != "") {
            let datos : any = {};
            datos.correo = correo;
            datos.clave = clave;
            
    
            $.ajax({
                type : "POST",
                url: URL_API + "login",
                dataType: "text",
                data : datos,
                headers : {"Authorization" : "Bearer  aaa"}
            })
            .done( function(obj_rta : any) {
                let obj = JSON.parse(obj_rta);
                if(obj.exito)
                {
                    localStorage.setItem("jwt",obj.jwt);
                    
                    setTimeout( () => {
                        $(location).attr("href",URL_BASE + "principal.html");
                    },2000) 
                    alert(obj.mensaje+"\nRedigiriendo la pagina a principal.php");
                }
                else{
                    alert(obj.jwt);
                    console.log(obj.jwt);
                }
                
            })
            .fail((jqXHR:any, textStatus:any, errorThrown:any) => {
                    let retorno = JSON.parse(jqXHR.responseText);
                    console.log(retorno.mensaje);
                    alert(retorno.mensaje);
              });
        }
        else{
            // $("#div_mensaje").html("Debe cargar los datos primero");
            console.log("Debe cargar los datos primero");
            alert("Debe cargar los datos primero");
        }
    })
}