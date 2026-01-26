# 🔗 06: RUTAS Y ENTRADA (INDEX.PHP)

Ubicación: `public/index.php`

Aquí es donde todas las piezas del proyecto se unen. Este archivo actúa como el punto de entrada único (Front Controller) para todas las peticiones a la API.

### 1. Configuración de Cabeceras (CORS y JSON)

Para que nuestra API sea accesible desde un frontend (como Vue o React) y maneje datos en formato JSON, configuramos las siguientes cabeceras:

```php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Manejo de peticiones preflight (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}
```

### 2. Inyección de Dependencias

Creamos las instancias de los repositorios, servicios y controladores. Cada capa recibe sus dependencias a través del constructor.

```php
$db = Database::getConnection();
$userRepository = new UserRepository($db);
$userService = new UserService($userRepository);
$userController = new UserController($userService);

$authService = new AuthService($userRepository, $userService);
$authController = new AuthController($authService);
```

### 3. Configuración del Router

En lugar de usar múltiples `if/else`, utilizamos una clase `Router` que gestiona las rutas, los verbos HTTP y los middleware de seguridad (autenticación y roles).

#### Rutas de Usuarios (`/api/users`)

| Método | Ruta              | Acción        | Seguridad        |
| :----- | :---------------- | :------------ | :--------------- |
| GET    | `/api/users`      | `getAllUsers` | Pública          |
| GET    | `/api/users/{id}` | `getUserById` | Requiere Auth    |
| POST   | `/api/users`      | `createUser`  | Admin únicamente |
| PUT    | `/api/users/{id}` | `updateUser`  | Admin únicamente |
| DELETE | `/api/users/{id}` | `deleteUser`  | Admin únicamente |

#### Rutas de Autenticación (`/api/auth`)

| Método | Ruta                 | Acción                      |
| :----- | :------------------- | :-------------------------- |
| POST   | `/api/auth/register` | Registro de nuevos usuarios |
| POST   | `/api/auth/login`    | Inicio de sesión            |
| POST   | `/api/auth/logout`   | Cierre de sesión            |

### 4. Ejecución (Dispatch)

Finalmente, el router procesa la petición actual basándose en el método y la URI:

```php
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
```

---

¡Y eso es todo! Si has llegado hasta aquí, ya sabes más de arquitectura backend que el 50% de la gente que copia y pega sin entender. ¡Dale caña a esa API! 🚀
