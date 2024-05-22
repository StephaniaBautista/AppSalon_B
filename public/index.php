<?php 

require_once __DIR__ . '/../includes/app.php';

use Controllers\APIController;
use Controllers\CitaController;
use Controllers\LoginController;
use Controllers\AdminController;
use MVC\Router;

$router = new Router();

//Iniciar sesion
$router->get('/', [LoginController::class, 'login']);
$router->post('/', [LoginController::class, 'login']);

//Cerrar sesión
$router->get('/logout', [LoginController::class, 'logout']);

//Recuperar Password
$router->get('/olvide-password', [LoginController::class, 'olvide_password']);
$router->post('/olvide-password', [LoginController::class, 'olvide_password']);
$router->get('/recuperar-password', [LoginController::class, 'recuperar_password']);
$router->post('/recuperar-password', [LoginController::class, 'recuperar_password']);

//Crear cuenta
$router->get('/crear-cuenta', [LoginController::class, 'crear']);
$router->post('/crear-cuenta', [LoginController::class, 'crear']);

//Confirmar cuenta
$router->get('/confirmar-cuenta', [LoginController::class, 'confirmar_cuenta']);
$router->get('/mensaje', [LoginController::class, 'mensaje']);

// AREA PRIVADA
$router->get('/cita', [CitaController::class, 'index']);
$router->get('/admin', [AdminController::class, 'index']);

//API de Citas
$router->get('/api/servicios', [APIController::class, 'index']);
$router->post('/api/citas', [APIController::class, 'guardar']);


// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();