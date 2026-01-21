<?php

namespace App\Repository;

use App\Model\User;
use App\Enum\Role;
use PDO;

class UserRepository {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getUserById(int $id): ?User {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;

        return new User((int)$row['id'], $row['username'], $row['email'], $row['password_hash'], Role::from($row['role']));
    }

    public function getAllUsers(): array {
        $stmt = $this->db->query("SELECT * FROM users");
        $users = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = new User((int)$row['id'], $row['username'], $row['email'], $row['password_hash'], Role::from($row['role']));
        }
        return $users;
    }

    public function createUser(User $user): bool {
        $stmt = $this->db->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (:username, :email, :password, :role)");
        $result = $stmt->execute([
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
            'role' => $user->getRole()->value,
        ]);
        if ($result) {
            $user->setId((int)$this->db->lastInsertId());
        }
        return $result;
    }

    public function updateUser(User $user): bool {
        $stmt = $this->db->prepare("UPDATE users SET username = :username, email = :email, password_hash = :password, role = :role WHERE id = :id");
        return $stmt->execute([
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
            'role' => $user->getRole()->value,
        ]);
    }

    public function deleteUser(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
