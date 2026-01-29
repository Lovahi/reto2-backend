<?php

namespace App\Controller;

use App\Service\AuthService;
use Exception;
use PDOException;
use App\Core\ApiResponseTrait;

class AuthController {
    use ApiResponseTrait;

    private AuthService $authService;

    public function __construct(AuthService $authService) {
        $this->authService = $authService;
    }



    public function register(): void {
        $data = $this->getJsonInput();

        if (!$data || !\is_array($data)) {
            $this->jsonResponse(['error' => 'Invalid JSON or empty body'], 400);
            return;
        }

        try {
            if ($this->authService->register($data)) {
                $this->jsonResponse(['message' => 'User registered successfully'], 201);
            } else {
                $this->jsonResponse(['error' => 'Failed to register user'], 400);
            }
        } catch (PDOException $e) {
            $this->handleDatabaseException($e);
        } catch (Exception $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    public function login(): void {
        $data = $this->getJsonInput();

        if (!$data || empty($data['email']) || empty($data['password'])) {
            $this->jsonResponse(['error' => 'Email and password are required'], 400);
            return;
        }

        try {
            $userDto = $this->authService->login($data['email'], $data['password']);
            if ($userDto) {
                $this->jsonResponse([
                    'message' => 'Login successful',
                    'user' => $userDto->toArray()
                ]);
            } else {
                $this->jsonResponse(['error' => 'Invalid email or password'], 401);
            }
        } catch (Exception $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    public function logout(): void {
        $this->authService->logout();
        $this->jsonResponse(['message' => 'Logged out successfully']);
    }
}
