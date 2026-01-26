<?php

namespace App\Controller;

use App\Service\UserService;
use PDOException;
use Exception;

class UserController {
    private UserService $userService;

    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }

    private function jsonResponse(mixed $data, int $status = 200): void {
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code($status);
        echo json_encode($data);
    }

    private function getJsonInput(): ?array {
        $json = file_get_contents('php://input');
        return json_decode($json, true);
    }

    private function handleDatabaseException(PDOException $e): void {
        switch ($e->getCode()) {
            case 23000:
                $this->jsonResponse(['error' => 'The username or email is already in use'], 400);
                break;
            default:
                $this->jsonResponse(['error' => 'A database error occurred'], 500);
                break;
        }
    }

    public function getAllUsers(): void {
        $users = $this->userService->getAllUsers();
        $this->jsonResponse(array_map(fn($u) => $u->toArray(), $users));
    }

    public function getUserById(int $id): void {
        $user = $this->userService->getUserById($id);
        
        if ($user) {
            $this->jsonResponse($user->toArray());
        } else {
            $this->jsonResponse(['error' => 'User not found'], 404);
        }
    }

    public function createUser(): void {
        $data = $this->getJsonInput();
        
        if (!$data || !\is_array($data)) {
            $this->jsonResponse(['error' => 'Invalid JSON or empty body'], 400);
            return;
        }

        try {
            if ($this->userService->createUser($data)) {
                $this->jsonResponse(['message' => 'User created successfully'], 201);
            } else {
                $this->jsonResponse(['error' => 'Failed to create user'], 400);
            }
        } catch (PDOException $e) {
            $this->handleDatabaseException($e);
        } catch (Exception $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    public function updateUser(int $id): void {
        $data = $this->getJsonInput();
        
        if (!$data || !\is_array($data)) {
            $this->jsonResponse(['error' => 'Invalid JSON or empty body'], 400);
            return;
        }

        try {
            if ($this->userService->updateUser($id, $data)) {
                $this->jsonResponse(['message' => 'User updated successfully']);
            } else {
                $this->jsonResponse(['error' => 'Failed to update user or user not found'], 400);
            }
        } catch (PDOException $e) {
            $this->handleDatabaseException($e);
        } catch (Exception $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    public function deleteUser(int $id): void {
        if ($this->userService->deleteUser($id)) {
            $this->jsonResponse(['message' => 'User deleted successfully']);
        } else {
            $this->jsonResponse(['error' => 'Failed to delete user'], 400);
        }
    }
}
