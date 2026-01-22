<?php

namespace App\Service;

use App\Repository\UserRepository;
use App\Model\User;
use App\DTO\UserDTO;
use App\Enum\Role;

class UserService {
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function getUserById(int $id): ?UserDTO {
        $user = $this->userRepository->getUserById($id);
        if (!$user) return null;

        return new UserDTO($user->getId(), $user->getUsername(), $user->getEmail(), $user->getRole());
    }

    public function getAllUsers(): array {
        $users = $this->userRepository->getAllUsers();
        return array_map(fn($user) => new UserDTO(
            $user->getId(), 
            $user->getUsername(), 
            $user->getEmail(),
            $user->getRole()
        ), $users);
    }

    public function createUser(array $data): bool {
        if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
            throw new \Exception("Missing required fields: username, email, password");
        }

        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
        
        $user = new User(
            null,
            $data['username'],
            $data['email'],
            $hashedPassword,
            Role::from($data['role'])
        );

        return $this->userRepository->createUser($user);
    }

    public function updateUser(int $id, array $data): bool {
        $user = $this->userRepository->getUserById($id);
        if (!$user) return false;

        if (isset($data['username'])) $user->setUsername($data['username']);
        if (isset($data['email'])) $user->setEmail($data['email']);
        if (isset($data['password'])) {
            $user->setPassword(password_hash($data['password'], PASSWORD_BCRYPT));
        }
        if (isset($data['role'])) $user->setRole($data['role']);

        return $this->userRepository->updateUser($user);
    }

    public function deleteUser(int $id): bool {
        return $this->userRepository->deleteUser($id);
    }
}
