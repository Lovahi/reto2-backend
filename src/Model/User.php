<?php

namespace App\Model;

use App\Enum\Role;

class User {
    private ?int $id;
    private string $username;
    private string $email;
    private string $password;
    private Role $role;

    public function __construct(?int $id, string $username, string $email, string $password, Role $role) {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
    }

    // Getters and Setters
    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getUsername(): string { return $this->username; }
    public function setUsername(string $username): void { $this->username = $username; }

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): void { $this->email = $email; }

    public function getPassword(): string { return $this->password; }
    public function setPassword(string $password): void { $this->password = $password; }

    public function getRole(): Role { return $this->role; }
    public function setRole(Role $role): void { $this->role = $role; }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'role' => $this->role
        ];
    }
}
