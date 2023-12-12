/// <reference path = "./autoBD.ts" />


namespace PrimerParcial
{
    window.addEventListener("load", ():void => {
        PrimerParcial.ManejadoraAutoFotos.ListarAutosBD();
    }); 

    
    export class ManejadoraAutoFotos
    {
        
        public static AgregarAutoFotoBD()
        {
            let xhttp : XMLHttpRequest = new XMLHttpRequest();

            let patente : string = ((<HTMLInputElement> document.getElementById("patente")).value);
            let marca : string = ((<HTMLInputElement> document.getElementById("marca")).value);
            let color : string = ((<HTMLInputElement> document.getElementById("color")).value);
            let precio : number = parseFloat(((<HTMLInputElement> document.getElementById("precio")).value));
            let foto : any = (<HTMLInputElement>document.getElementById("foto"));


            if (patente !="" && marca !="" && color !="" && precio !=-1) 
            {
                let auto : Garcia.AutoBD = new Garcia.AutoBD(patente,marca,color,precio,foto);

                let formData : FormData = new FormData();
                xhttp.open("POST","./BACKEND/agregarAutoBD.php");
                formData.append('patente', auto.patente);
                formData.append('marca', auto.marca);
                formData.append('color', auto.color);
                formData.append('precio', auto.precio.toString());

                formData.append('foto', foto.files[0]);

                xhttp.setRequestHeader("enctype","multipart/form-data");
                xhttp.send(formData);

                xhttp.onreadystatechange = () => 
                {
                    console.log(xhttp.readyState + " - " + xhttp.status);
                    
                    if (xhttp.readyState == 4 && xhttp.status == 200) 
                    {
                        console.log(xhttp.responseText);
                        alert(xhttp.responseText);
                        ManejadoraAutoFotos.ListarAutosBD();
                    }
                };
            }
            else
            {
                alert("Debe cargar todos los datos");
            }
            
        }


        public  static ListarAutosBD()
        {
            let xhttp : XMLHttpRequest = new XMLHttpRequest();

            xhttp.open("get","./BACKEND/listadoAutosBD.php");
            // xhttp.open("get","./BACKEND/listadoAutosBD.php?tabla=mostrar");

            xhttp.send();

            xhttp.onreadystatechange = () => 
            {
                console.log(xhttp.readyState + " - " + xhttp.status);
                
                if (xhttp.readyState == 4 && xhttp.status == 200) 
                {
                    let objeto = JSON.parse(xhttp.responseText);
                    if (objeto.formato == "tabla") 
                    {
                        (<HTMLDivElement>document.getElementById("divTablaAutoFotos")).innerHTML = objeto.datos;
                    }
                    else if(objeto.formato == "json")
                    {
                        let tabla : string = `<table class="table table-hover">
                        <tr>
                            <th>PATENTE</th><th>MARCA</th><th>COLOR</th><th>PRECIO</th><th>FOTO</th>
                        <tr>`;
                        objeto.datos.forEach((elemento : any) => {
                            if (elemento != "") 
                            {
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
                        (<HTMLDivElement>document.getElementById("divTablaAutoFotos")).innerHTML = tabla; // responseText es la respuesta del servidor
                    }  

                    var botonModificar= document.querySelectorAll('[data-action="Modificar"]');
                    botonModificar.forEach(function(boton : any) {
                        boton.addEventListener('click', function() {
                        var objAuto = JSON.parse(boton.getAttribute('data-json'));
                        
                        (<HTMLInputElement> document.getElementById("patente")).value = objAuto.patente;
                        (<HTMLInputElement> document.getElementById("marca")).value = objAuto.marca;
                        (<HTMLInputElement> document.getElementById("color")).value = objAuto.color;
                        (<HTMLInputElement> document.getElementById("precio")).value = objAuto.precio;  
                        (<HTMLInputElement> document.getElementById("imgFoto")).src = "BACKEND/autos/imagenes/" + objAuto.pathFoto;  
                        
                        const inputPatente = document.getElementById("patente") as HTMLInputElement;
                        if (inputPatente) {
                            inputPatente.readOnly = true; 
                        }
                        // Manejadora.ModificarAuto();
                    });
                });
                
                
                var botonEliminar= document.querySelectorAll('[data-action="Eliminar"]');
                botonEliminar.forEach(function(boton : any) {
                    boton.addEventListener('click', function() {
                        var objAuto = JSON.parse(boton.getAttribute('data-json'));
                         
                        (<HTMLInputElement> document.getElementById("patente")).value = objAuto.patente;
                        (<HTMLInputElement> document.getElementById("marca")).value = objAuto.marca;
                        (<HTMLInputElement> document.getElementById("color")).value = objAuto.color;
                        (<HTMLInputElement> document.getElementById("precio")).value = objAuto.precio;  
                        (<HTMLInputElement> document.getElementById("imgFoto")).src = "BACKEND/autos/imagenes/" + objAuto.pathFoto;

                        const inputPatente = document.getElementById("patente") as HTMLInputElement;
                        if (inputPatente) {
                            inputPatente.readOnly = true; 
                        }
                            ManejadoraAutoBd.EliminarAuto();
                        });
                    });
                }
            };
        }

        public static ModificarAutoFotoBD()
        {
            let xhttp : XMLHttpRequest = new XMLHttpRequest();

            let patente : string = ((<HTMLInputElement> document.getElementById("patente")).value);
            let marca : string = ((<HTMLInputElement> document.getElementById("marca")).value);
            let color : string = ((<HTMLInputElement> document.getElementById("color")).value);
            let precio : number = parseFloat(((<HTMLInputElement> document.getElementById("precio")).value));
            let foto : any = (<HTMLInputElement>document.getElementById("foto"));

            let auto : Garcia.AutoBD = new Garcia.AutoBD(patente,marca,color,precio);

            let formData : FormData = new FormData();

            

            xhttp.open("POST","./BACKEND/modificarAutoBDFoto.php");
            formData.append('auto_json', auto.toJSON());
            formData.append('foto', foto.files[0]);

            xhttp.setRequestHeader("enctype","multipart/form-data");
            xhttp.send(formData);

            xhttp.onreadystatechange = () => 
            {
                console.log(xhttp.readyState + " - " + xhttp.status);
                
                if (xhttp.readyState == 4 && xhttp.status == 200) 
                {
                    console.log(xhttp.responseText);
                    alert(xhttp.responseText);
                    const inputPatente = document.getElementById("patente") as HTMLInputElement;
                    ManejadoraAutoFotos.ListarAutosBD();
                    if (inputPatente) {
                        inputPatente.readOnly = false; 
                    }
                }
            };
        }

        public static EliminarAutoFotoBD()
        {
            let xhttp : XMLHttpRequest = new XMLHttpRequest();

            let patente : string = ((<HTMLInputElement> document.getElementById("patente")).value);
            let marca : string = ((<HTMLInputElement> document.getElementById("marca")).value);
            let color : string = ((<HTMLInputElement> document.getElementById("color")).value);
            let precio : number = parseInt(((<HTMLInputElement> document.getElementById("precio")).value));
            let foto : any = (<HTMLInputElement>document.getElementById("foto"));


            let auto : Garcia.AutoBD = new Garcia.AutoBD(patente,marca,color,precio,foto);

            let formData : FormData = new FormData();

            if (confirm(`¿ Seguro de eliminar el auto con la patente : ${patente} y
                la marca: ${marca} ?`)) 
            {

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
                xhttp.open("POST","./BACKEND/eliminarAutoBDFoto.php");
                formData.append('auto_json', auto.toJSON());
                
                xhttp.send(formData);
                
                xhttp.onreadystatechange = () => 
                {
                    console.log(xhttp.readyState + " - " + xhttp.status);
                
                    if (xhttp.readyState == 4 && xhttp.status == 200) 
                    {
                        console.log(xhttp.responseText);
                        alert(xhttp.responseText);
                        ManejadoraAutoFotos.ListarAutosBD();
                        const inputPatente = document.getElementById("patente") as HTMLInputElement;
                        if (inputPatente) {
                            inputPatente.readOnly = false; 
                        }
                    }
                };
             }
        }

        public  static ListarAutosPDF()
        {
            window.location.href = "./backend/listadoAutosPDF.php";
        }

    }
}