/// <reference path = "./auto.ts" />

namespace PrimerParcial
{
    export class Manejadora
    {
        // auto_json.html
        public static AgregarAutosJSON()
        {
            let xhttp : XMLHttpRequest = new XMLHttpRequest();

            let patente : string = ((<HTMLInputElement> document.getElementById("patente")).value);
            let marca : string = ((<HTMLInputElement> document.getElementById("marca")).value);
            let color : string = ((<HTMLInputElement> document.getElementById("color")).value);
            let precio : string = ((<HTMLInputElement> document.getElementById("precio")).value);

            if (patente !="" && marca !="" && color !="" && precio !="") 
            {
                let formData : FormData = new FormData();
                xhttp.open("POST","./BACKEND/altaAutoJSON.php");
                formData.append('patente', patente);
                formData.append('marca', marca);
                formData.append('color', color);
                formData.append('precio', precio);

                xhttp.send(formData);

                xhttp.onreadystatechange = () => 
                {
                    console.log(xhttp.readyState + " - " + xhttp.status);
                    
                    if (xhttp.readyState == 4 && xhttp.status == 200) 
                    {
                        console.log(xhttp.responseText);
                        alert(xhttp.responseText);
                    }
                };
            }
            else
            {
                alert("Debe cargar todos los datos");
            }         
        }

        public static ListarAutosJSON()
        {
            let xhttp : XMLHttpRequest = new XMLHttpRequest();

            xhttp.open("get","./BACKEND/listadoAutosJSON.php");

            xhttp.send();

            xhttp.onreadystatechange = () => 
            {
                console.log(xhttp.readyState + " - " + xhttp.status);
                
                if (xhttp.readyState == 4 && xhttp.status == 200) 
                {
                    (<HTMLDivElement>document.getElementById("divTabla")).innerHTML = xhttp.responseText; // responseText es la respuesta del servidor
                    console.log(xhttp.responseText);
                }
            };
        }

        public static VerificarAutoJSON()
        {
            let xhttp : XMLHttpRequest = new XMLHttpRequest();

            let patente : string = ((<HTMLInputElement> document.getElementById("patente")).value);
            if (patente !="") 
            {
                let formData : FormData = new FormData();


                xhttp.open("POST","./BACKEND/VerificarAutoJSON.php");
                formData.append('patente', patente);

                xhttp.send(formData);

                xhttp.onreadystatechange = () => 
                {
                    console.log(xhttp.readyState + " - " + xhttp.status);
                    
                    if (xhttp.readyState == 4 && xhttp.status == 200) 
                    {
                        console.log(xhttp.responseText);
                        alert(xhttp.responseText);
                    }
                };
            }
            else
            {
                alert("Debe cargar la patente");
            }
            
        }

    }
}