<?php

namespace App\Core;

use App\Service\AuthService;
use App\Enum\Role;

class Router {
    private array $routes = [];

    public function add(string $method, string $path, callable|array $handler, array $options = []): void {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'options' => $options
        ];
    }

    public function dispatch(string $method, string $uri): void {
        $uri = explode('?', $uri)[0];

        foreach ($this->routes as $route) {
            $pattern = preg_replace('/\{(\w+)\}/', '(\d+)', $route['path']);
            $pattern = "#^" . $pattern . "$#";

            if ($route['method'] === $method && preg_match($pattern, $uri, $matches)) {
                // Check authorization
                if (!$this->checkAuthorization($route['options'])) {
                    return;
                }

                array_shift($matches);
                
                $handler = $route['handler'];
                if (is_array($handler)) {
                    [$controller, $action] = $handler;
                    call_user_func_array([$controller, $action], $matches);
                } else {
                    call_user_func_array($handler, $matches);
                }
                return;
            }
        }

        $this->sendNotFound();
    }

    private function checkAuthorization(array $options): bool {
        if (isset($options['auth']) && $options['auth'] === true) {
            if (!AuthService::isLoggedIn()) {
                $this->sendUnauthorized('Authentication required');
                return false;
            }
        }

        if (isset($options['role'])) {
            $requiredRole = $options['role'] instanceof Role ? $options['role'] : Role::from($options['role']);
            if (!AuthService::hasRole($requiredRole)) {
                $this->sendForbidden('Insufficient permissions');
                return false;
            }
        }

        return true;
    }

    private function sendUnauthorized(string $message): void {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['error' => $message]);
    }

    private function sendForbidden(string $message): void {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['error' => $message]);
    }

    private function sendNotFound(): void {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['error' => $message ?? 'Route not found']);
    }
}
