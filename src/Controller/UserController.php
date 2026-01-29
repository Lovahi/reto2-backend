<?php

namespace App\Controller;

use App\Service\UserService;
use PDOException;
use Exception;
use App\Core\ApiResponseTrait;

class UserController {
    use ApiResponseTrait;

    private UserService $userService;

    public function __construct(UserService $userService) {
        $this->userService = $userService;
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
