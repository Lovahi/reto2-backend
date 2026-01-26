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
        if (empty($data['password'])) {
            throw new \Exception("Password is required");
        }

        $hashedPassword = \password_hash($data['password'], PASSWORD_BCRYPT);
        $user = new User(
            null,
            $data['username'],
            $data['email'],
            $hashedPassword,
            Role::USER
        );

        return $this->userRepository->createUser($user);
    }

    public function login(string $email, string $password): ?UserDTO {
        $user = $this->userRepository->getUserByEmail($email);
        
        if ($user && \password_verify($password, $user->getPassword())) {
            // Guardar datos básicos en la sesión
            $_SESSION['user_id'] = $user->getId();
            $_SESSION['user_role'] = $user->getRole()->value;
            $_SESSION['user_email'] = $user->getEmail();

            return new UserDTO(
                $user->getId(),
                $user->getUsername(),
                $user->getEmail(),
                $user->getRole()
            );
        }

        return null;
    }

    public function logout(): void {
        \session_unset();
        \session_destroy();
    }

    public static function isLoggedIn(): bool {
        return isset($_SESSION['user_id']);
    }

    public static function hasRole(Role $role): bool {
        return self::isLoggedIn() && $_SESSION['user_role'] === $role->value;
    }

    public static function isAdmin(): bool {
        return self::hasRole(Role::ADMIN);
    }
}
