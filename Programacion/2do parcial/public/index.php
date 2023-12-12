<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use \Slim\Routing\RouteCollectorProxy;
use Slim\Factory\AppFactory;


require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . "/../src/poo/usuario.php";
require_once __DIR__ . "/../src/poo/juguete.php";
require_once __DIR__ . "/../src/poo/mw.php";

//NECESARIO PARA GENERAR EL JWT
use Firebase\JWT\JWT;


$app = AppFactory::create();

$app->get('/', \Usuario::class . ':TraerTodos');

$app->post('/', \Juguete::class . ':AgregarJuguete');

$app->get('/juguetes', \Juguete::class . ':TraerTodos');


$app->post('/login', \Usuario::class . ':VerificarUsuario')
->add(MW::class . ':VerificarUsuarioBD')
->add(MW::class . ':ValidarParametrosVacios');

$app->get('/login', \Usuario::class . ':ChequearJWT');


$app->group('/toys', function (RouteCollectorProxy $grupo) {
  $grupo->delete("/{id_juguete}", \Juguete::class . ':BorrarJuguete');
  $grupo->post("/", \Juguete::class . ':ModificarJuguete');
})->add(MW::class . ':ChequearJWT');



$app->group('/tablas', function (RouteCollectorProxy $grupo) {
  $grupo->get('/usuarios', \Usuario::class . ':TraerTodos')->add(\MW::class . ':MostrarTablaSinClave');
  $grupo->post('/usuarios', \Usuario::class . ':TraerTodos')->add(\MW::class . '::MostrarTablaPropietario');
  $grupo->get('/juguetes', \Juguete::class . ':TraerTodos')->add(\MW::class . ':MostrarTablaJuguetes');
});

$app->post('/usuarios', \Usuario::class . ':AgregarUsuario')
->add(MW::class . ':ValidarParametrosVacios')
->add(MW::class . ':VerificarCorreo')
->add(MW::class . ':ChequearJWT');;




//CORRE LA APLICACIÓN.
$app->run();