<?php

namespace App\DTO;

use App\Enum\Role;

class UserDTO {
    public ?int $id;
    public string $username;
    public string $email;
    public Role $role;

    public function __construct(?int $id, string $username, string $email, Role $role) {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->role = $role;
    }

    /**
     * Convierte el DTO a un array para enviarlo como JSON.
     * En Spring, Jackson lo hace solo. Aquí usamos esto.
     */
    public function toArray(): array {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'role' => $this->role,
        ];
    }
}
