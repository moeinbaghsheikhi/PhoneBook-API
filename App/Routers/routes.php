<?php
use App\Routers\Router as Router;
use App\Controllers\AuthController;
use App\Middlewares\AuthMiddleware;

// use Controllers
use App\Controllers\PhonesController;


// ایجاد یک نمونه از میدلور
$authMiddleware = new AuthMiddleware();
$request = (object) [
    "headers" => $_SERVER['HTTP_TOKEN'] ?? null,
    "query"   => $_GET['token'] ?? null,
    "body"    => getPostDataInput()->token ?? null
];

$response = $authMiddleware->handle($request);

if(!$response) exit();

$router = new Router();

// Define routes
$router->post('v1','/login', AuthController::class, 'login');
$router->post('v1','/register', AuthController::class, 'register');
$router->post('v1','/verify', AuthController::class, 'verify');

$router->get('v1', '/phones', PhonesController::class, 'index' );
$router->get('v1', '/phones/{id}', PhonesController::class, 'show');
$router->post('v1', '/phones', PhonesController::class, 'store');
$router->put('v1', '/phones/{id}', PhonesController::class, 'update');
$router->delete('v1', '/phones/{id}', PhonesController::class, 'destroy');