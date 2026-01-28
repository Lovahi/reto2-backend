<?php

session_start();

use App\Core\Router;
use App\Core\Database;
use App\Controller\EventController;
use App\Controller\UserController;
use App\Controller\AuthController;
use App\Service\EventService;
use App\Controller\GameController;
use App\Service\UserService;
use App\Service\AuthService;
use App\Service\GameService;
use App\Repository\UserRepository;
use App\Repository\EventRepository;
use App\Repository\GameRepository;

use App\Enum\Role;
use App\Enum\Type;

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
    
    $authService = new AuthService($userRepository, $userService);
    $authController = new AuthController($authService);
    
    $eventRepository = new EventRepository($db);
    $eventService = new EventService($eventRepository);
    $eventController = new EventController($eventService);

    $gameRepository = new GameRepository($db);
    $gameService = new GameService($gameRepository);
    $gameController = new GameController($gameService);

    // 4. Configuración del Router
    $router = new Router();

    // User API Routes
    $router->add('GET',    '/api/users',       [$userController, 'getAllUsers']);
    $router->add('GET',    '/api/users/{id}',  [$userController, 'getUserById'], ['auth' => true]);
    $router->add('POST',   '/api/users',       [$userController, 'createUser'],  ['auth' => true, 'role' => Role::ADMIN]);
    $router->add('PUT',    '/api/users/{id}',  [$userController, 'updateUser'],  ['auth' => true, 'role' => Role::ADMIN]);
    $router->add('DELETE', '/api/users/{id}',  [$userController, 'deleteUser'],  ['auth' => true, 'role' => Role::ADMIN]);

    // Auth API Routes
    $router->add('POST',   '/api/auth/register', [$authController, 'register']);
    $router->add('POST',   '/api/auth/login',    [$authController, 'login']);
    $router->add('POST',   '/api/auth/logout',   [$authController, 'logout']);
    
    // Event API Routes
    $router->add('GET',    '/api/events',                    [$eventController, 'getEvents']);
    $router->add('GET',    '/api/events/{id}',               [$eventController, 'getEventById']);
    $router->add('GET',    '/api/events/title/{title}',      [$eventController, 'getEventsByName']);
    $router->add('POST',   '/api/events',                    [$eventController, 'createEvent'], ['auth' => true, 'role' => Role::ADMIN]);

    // Game API Routes
    $router->add('GET',    '/api/games',              [$gameController, 'getAllGames']);
    $router->add('GET',    '/api/games/{id}',         [$gameController, 'getGamesById']);
    $router->add('GET',    '/api/games/name/{name}',  [$gameController, 'getGamesByName']);
    
    // User-Events API Routes
    
    // 5. Ejecución
    $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'An unexpected error occurred',
        'message' => $e->getMessage()
    ]);
}
