<?php
ini_set('display_errors', 1);
session_start();

// Base
require_once '../app/Database/Database.php';
require_once '../app/Guard.php';
require_once '../app/Config.php';
require_once '../app/Request.php';
require_once '../app/Router.php';
require_once '../app/Response.php';

// Controllers
require_once '../app/Controllers/HomeController.php';

// Middlewares
require_once '../app/Middlewares/TestMiddleware.php';

$response = new Response();

$requestUri = explode('?', $_SERVER['REQUEST_URI'], 2)[0];
$projectRoot = dirname($_SERVER['SCRIPT_NAME']);
$path = trim(str_replace($projectRoot, '', $requestUri), '/');
$method = $_SERVER['REQUEST_METHOD'];

$router = new Router($response);

$router->get('', 'HomeController', 'index');

$router->group('test', [TestMiddleware::class], function ($router) {
    $router->get('', 'HomeController', 'index');
});

$router->dispatch($path, $method);
