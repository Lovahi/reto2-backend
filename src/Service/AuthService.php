<?php

namespace App\Service;

use App\Repository\UserRepository;
use App\Model\User;
use App\Enum\Role;
use App\DTO\UserDTO;

class AuthService {
    private UserRepository $userRepository;
    private UserService $userService;

    public function __construct(UserRepository $userRepository, UserService $userService) {
        $this->userRepository = $userRepository;
        $this->userService = $userService;
    }

    public function register(array $data): bool {

        $user = new User(
            null,
            $data['username'],
            $data['email'],
            $data['password'],
            Role::USER
        );

        return $this->userRepository->createUser($user);
    }

    public function login(string $email, string $password): ?UserDTO {
        $user = $this->userRepository->getUserByEmail($email);
        
        if ($user && password_verify($password, $user->getPassword())) {
            return new UserDTO(
                $user->getId(),
                $user->getUsername(),
                $user->getEmail(),
                $user->getRole()
            );
        }

        return null;
    }
}
