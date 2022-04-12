<?php
if( !session_id() ) @session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require '../vendor/autoload.php';

use Aura\SqlQuery\QueryFactory;
use League\Plates\Engine;
use PDO;


$builder = new DI\ContainerBuilder();
$builder->addDefinitions([
     Engine::class => function() {
         return new Engine('../app/views');
     },
     PDO::class => function() {
        return new PDO("mysql:host=localhost;dbname=alla_doodee_r_db; charset=utf8;", "alla_doodee__usr", "zij0ylH0574WY1aK" );
     },
     QueryFactory::class => function() {
        return new QueryFactory('mysql');
    },
    \Delight\Auth\Auth::class => function($container) {
        return  new \Delight\Auth\Auth($container->get('PDO'));
    },
]);
$container = $builder->build();



$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/', ['App\controllers\UserController', 'register']);
    $r->addRoute('GET', '/create_user', ['App\controllers\UserController', 'create']);
    $r->addRoute('GET', '/users', ['App\controllers\UserController', 'users']);
    $r->addRoute('GET', '/edit/{id:\d+}', ['App\controllers\UserController', 'edit']); 
    $r->addRoute('GET', '/security/{id:\d+}', ['App\controllers\UserController', 'security']); 
    $r->addRoute('GET', '/media/{id:\d+}', ['App\controllers\UserController', 'media']); 
    $r->addRoute('GET', '/login', ['App\controllers\UserController', 'login']); 
    $r->addRoute('GET', '/profile/{id:\d+}', ['App\controllers\UserController', 'profile']); 
    $r->addRoute('GET', '/register', ['App\controllers\UserController', 'register']); 
    $r->addRoute('GET', '/status/{id:\d+}', ['App\controllers\UserController', 'status']);
    $r->addRoute('GET', '/delete/{id:\d+}', ['App\controllers\AuthController', 'deleteUser']);
    $r->addRoute('GET', '/verification', ['App\controllers\AuthController', 'verification']);
    $r->addRoute('GET', '/mail', ['App\controllers\UserController', 'mail']);
    $r->addRoute('GET', '/paginator', ['App\controllers\UserController', 'paginator']);
    //actions
    $r->addRoute('POST', '/status/edit', ['App\controllers\UserController', 'statusEdit']);
    $r->addRoute('POST', '/register/user', ['App\controllers\UserController', 'formInsert']);
    $r->addRoute('POST', '/register/update', ['App\controllers\UserController', 'formUpdate']); 
    $r->addRoute('POST', '/media/update', ['App\controllers\UserController', 'mediaUpdate']);  
    $r->addRoute('POST', '/security/update', ['App\controllers\AuthController', 'changePassword']);
    $r->addRoute('POST', '/signup', ['App\controllers\AuthController', 'signup']);
    $r->addRoute('POST', '/createuser', ['App\controllers\AuthController', 'createuser']);
    $r->addRoute('POST', '/signin', ['App\controllers\AuthController', 'signin']);
    $r->addRoute('POST', '/logout', ['App\controllers\AuthController', 'logout']);
     
});
 
// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        echo '404';
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        echo '405 Method Not Allowed';
        break;
    case FastRoute\Dispatcher::FOUND:

        $container->call($routeInfo[1]);

        break;
}


?>