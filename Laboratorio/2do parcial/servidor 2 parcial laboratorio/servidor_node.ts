const express = require('express');

const app = express();

app.set('puerto', 2023);

//#region Configuraciones

//AGREGO FILE SYSTEM
const fs = require('fs');

//AGREGO JSON
app.use(express.json());

//AGREGO JWT
const jwt = require("jsonwebtoken");

//SE ESTABLECE LA CLAVE SECRETA PARA EL TOKEN
// app.set("key_jwt", "cl@ve_secreta");
app.set("key_jwt", "garcia.lautaro");

app.use(express.urlencoded({extended:false}));

//AGREGO MULTER
const multer = require('multer');

//AGREGO MIME-TYPES
const mime = require('mime-types');

//AGREGO STORAGE
const storage = multer.diskStorage({

    destination: "public/juguetes/fotos/",
});

const upload = multer({

    storage: storage
});

//AGREGO CORS
const cors = require("cors");

//AGREGO MW CORS A NIVEL DE APLICACIÓN
app.use(cors());

//DIRECTORIO DE ARCHIVOS ESTÁTICOS
app.use(express.static("public"));

//AGREGO MYSQL y EXPRESS-MYCONNECTION
const mysql = require('mysql');
const myconn = require('express-myconnection');
const db_options = {
    host: 'localhost',
    port: 3306,
    user: 'root',
    password: '',
    database: 'jugueteria_bd'
};

//AGREGO MW MYSQL A NIVEL DE APLICACIÓN
app.use(myconn(mysql, db_options, 'single'));

const verificar_jwt = express.Router();

verificar_jwt.use((request : any,response : any,next : any)=>{

    let token = request.headers["authorization"];

    if (! token){
        response.status(403).json({
            exito : false,
            mensaje : "El Token es requerido",
        })
        return;
    }

    if (token.startsWith("Bearer ")){
        token = token.slice(7,token.length);
    }

    if (token) {
        jwt.verify(token, app.get("key_jwt"), (error:any, decoded:any)=>{
            if (error)
            {
                return response.status(403).json({
                    exito : false,
                    mensaje : "El JWT NO es valido",
                })   
            }
            else
            {
                response.jwt = decoded;
                next();
            }
        })
    }
    else{
        response.status(401).send({
            error: "El JWT está vacío!!!"
        });
    }
});


const verificar_usuario = express.Router();

verificar_usuario.use((request : any,response : any,next : any)=>{

    let datos = request.body;

    request.getConnection((error: any, conn:any)=>{
        if(error) throw("Error al conectarse con la base de datos");
        
        conn.query("select * from usuarios where correo = ? and clave = ?", [datos.correo, datos.clave], (err:any, fila:any)=>{

                if(err) throw("Error en la consulta de la base de datos");
                 
                if (fila.length == 1) 
                {
                    response.obj_usuario = fila[0];
                    next();
                }
                else
                {
                    response.status(403).json({
                        exito : false,
                        mensaje : "Correo y/o Clave incorrectas.",
                        jwt : null
                    });
                }   
        });   
    })
});



app.post("/agregarJugueteBD",verificar_jwt,upload.single("foto") ,(request:any, response:any)=>{

    let file = request.file;
    let extension = mime.extension(file.mimetype);
    let obj = JSON.parse(request.body.juguete_json);
    let path : string = file.destination + obj.marca + "." + extension;

    fs.renameSync(file.path, path);

    // obj.path_foto = path.split("public/")[1];
    obj.path_foto = path.split("public/juguetes/fotos/")[1];

    request.getConnection((err:any, conn:any)=>{

        if(err) throw("Error al conectarse a la base de datos.");

        conn.query("insert into juguetes set ?", [obj], (err:any, rows:any)=>{

            if(err) {console.error("Error en la consulta de la base de datos:", err); throw("Error en consulta de base de datos.");}

            response.status(200).json({
                exito : true,
                mensaje : "Juguete agregado a la bd.",
            });
        });
    });
});


app.get("/listarJuguetesBD",verificar_jwt ,(request:any, response:any)=>{


    request.getConnection((error: any, conn:any)=>{
        if(error) throw("Error al conectarse con la base de datos");
        
        conn.query("select * from juguetes ", (err:any, fila:any)=>{

            if(err)
            {
                response.status(424).json({
                    exito : false,
                    mensaje : "No se cargo el listado de juguetes",
                    dato : null
                });
            }
            else{
                response.status(200).json({
                    exito : true,
                    mensaje : "Se cargo el listado de juguetes",
                    dato : fila
                });
            }
        });   
    })

});


app.post("/login",verificar_usuario,(request:any, response:any)=>{

    const user = response.obj_usuario;

    const payload = {
        usuario :{
            Id : user.id,
            Correo : user.correo,
            Nombre : user.nombre,
            Apellido : user.apellido,
            Foto : user.foto,
            Perfil : user.perfil
        },
        alumno : "Lautaro Nahuel Garcia",
        dni_alumno : 45040166
    };

    const token = jwt.sign(payload,app.get("key_jwt"),{
        expiresIn : "2m"
    })

    response.status(200).json({
        exito: true,
        mensaje : "JWT creado!!!",
        jwt : token
    })
    
});


app.get("/login",verificar_jwt,(request:any, response:any)=>{

    const obj = response.jwt;

    response.status(200).json({
        exito : true,
        mensaje : "El JWT es valido",
        payload : obj
    })
});

app.delete("/toys",verificar_jwt,(request:any, response:any)=>{

    let obj = request.body;

    let path_foto : string = "public/juguetes/fotos/";
    let hay_registro = false;

    request.getConnection((err:any, conn:any)=>{

        if(err) throw("Error al conectarse a la base de datos.");

        //obtengo el path de la foto del juguete a ser eliminado
        conn.query("select path_foto from juguetes where id = ?", [obj.id_juguete], (err:any, result:any)=>{

            if(err) throw("Error en consulta de base de datos.");
            
            //console.log(result.length);
            if(result.length > 0)
            {
                path_foto += result[0].path_foto;
                hay_registro = true;
            }

            if(hay_registro)
            {
                request.getConnection((err:any, conn:any)=>{

                    if(err) throw("Error al conectarse a la base de datos.");

                    conn.query("delete from juguetes where id = ?", [obj.id_juguete], (err:any, rows:any)=>{

                        if(err) {console.log(err); throw("Error en consulta de base de datos.");}

                        fs.unlink(path_foto, (err:any) => {
                            if(err){
                                response.status(418).json({
                                    exito : false,
                                    mensaje : "La foto no fue borrada.",
                                });
                                return;
                            }
                        });

                        response.status(200).json({
                            exito : true,
                            mensaje : "Juguete eliminado de la bd.",
                        });
                    });
                });
            }
            else
            {
                response.status(418).json({
                    exito : false,
                    mensaje : "Producto NO eliminado de la bd.",
                });
            }
        });
    });
});

app.post("/toys",verificar_jwt,upload.single("foto") ,(request:any, response:any)=>{

    let file = request.file;
    let extension = mime.extension(file.mimetype);
    let obj = JSON.parse(request.body.juguete);
    let path : string = file.destination + obj.marca + "__modificacion." + extension;
    let status = 200;
    fs.renameSync(file.path, path);

    obj.path = path.split("public/")[1];

    let obj_modif : any = {};
    //para excluir la pk (codigo)
    obj_modif.marca = obj.marca;
    obj_modif.precio = obj.precio;
    obj_modif.path_foto = path.split("public/juguetes/fotos/")[1];

    request.getConnection((err:any, conn:any)=>{

        if(err) throw("Error al conectarse a la base de datos.");

        conn.query("update juguetes set ? where id = ?", [obj_modif, obj.id_juguete], (err:any, rows:any)=>{

            if(err) {console.log(err); throw("Error en consulta de base de datos.");}
            
            //console.log(rows);
            let hay_registro = rows.affectedRows == 0 ? false : true;

            if( ! hay_registro)
            {
                fs.unlink("public/"+obj.path, (err:any) => {
                    if(err){
                        response.json({
                            exito : false,
                            mensaje : "La foto no fue borrada.",
                        });
                        return;
                    }
                });
                status = 418;
            }
            response.status(status).json({
                exito : hay_registro,
                mensaje : hay_registro ? "Producto modificado en la bd." : "Producto NO modificado en la bd.",
            });
        });
    });
});

app.listen(app.get('puerto'), ()=>{
    console.log('Servidor corriendo sobre puerto:', app.get('puerto'));
});
