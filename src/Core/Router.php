<?php

namespace App\Core;

class Router {
    private array $routes = [];

    public function add(string $method, string $path, callable|array $handler): void {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }

    public function dispatch(string $method, string $uri): void {
        $uri = explode('?', $uri)[0];

        foreach ($this->routes as $route) {
            $pattern = preg_replace('/\{(\w+)\}/', '(\d+)', $route['path']);
            $pattern = "#^" . $pattern . "$#";

            if ($route['method'] === $method && preg_match($pattern, $uri, $matches)) {
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

    private function sendNotFound(): void {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Route not found']);
    }
}
