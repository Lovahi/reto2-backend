<?php

use App\Core\Router;
use App\Core\Database;
use App\Controller\UserController;
use App\Service\UserService;
use App\Repository\UserRepository;

// 1. Configuración de cabeceras (CORS y JSON)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Manejo de peticiones preflight
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 2. Autoloader PSR-4 Simplificado
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../src/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) require $file;
});

// 3. Inicialización de dependencias
try {
    $db = Database::getConnection();
    $userRepository = new UserRepository($db);
    $userService = new UserService($userRepository);
    $userController = new UserController($userService);

    // 4. Configuración del Router
    $router = new Router();

    // User API Routes
    $router->add('GET',    '/api/users',       [$userController, 'getAllUsers']);
    $router->add('GET',    '/api/users/{id}',  [$userController, 'getUserById']);
    $router->add('POST',   '/api/users',       [$userController, 'createUser']);
    $router->add('PUT',    '/api/users/{id}',  [$userController, 'updateUser']);
    $router->add('DELETE', '/api/users/{id}',  [$userController, 'deleteUser']);

    // ---- API Routes

    ///////////////////////////////////////////////////////////////////////

    // 5. Ejecución
    $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'An unexpected error occurred',
        'message' => $e->getMessage()
    ]);
}
