<?php

namespace App\Repository;

use App\Model\Game;
use PDO;

class GameRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllGames(): array
    {
        $stmt = $this->db->query("SELECT * FROM games ORDER BY id DESC");
        $games = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $platforms = json_decode($row['plataformas'], true) ?: [];

            $games[] = new Game(
                (int)$row['id'],
                $row['titulo'],
                $row['genero'],
                $platforms,
                $row['imagen'],
                $row['descripcion']
            );
        }
        return $games;
    }

    public function getGameById(int $id): ?Game
    {
        $stmt = $this->db->prepare("SELECT * FROM games WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;
        $platforms = json_decode($row['plataformas'], true) ?: [];

        return new Game(
            (int)$row['id'],
            $row['titulo'],
            $row['genero'],
            $platforms,
            $row['imagen'],
            $row['descripcion']
        );
    }

    public function getGamesPaginated(int $page = 1): array
    {
        // Aseguramos que la página sea como mínimo 1
        $page = max(1, $page);
        $limit = 9;
        $offset = ($page - 1) * $limit;

        // Añadimos ORDER BY para que la paginación sea consistente
        $stmt = $this->db->prepare("SELECT * FROM games ORDER BY id DESC LIMIT :limit OFFSET :offset");

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $games = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Manejo de JSON y creación de objeto
            $platforms = json_decode($row['plataformas'] ?? '[]', true) ?: [];

            $games[] = new Game(
                (int)$row['id'],
                $row['titulo'],
                $row['genero'],
                $platforms,
                $row['imagen'],
                $row['descripcion']
            );
        }
        return $games;
    }
    public function getGamesByName(string $name): array
    {
        $stmt = $this->db->prepare("SELECT * FROM games WHERE titulo LIKE :name");
        $stmt->execute(['name' => '%' . $name . '%']);

        $games = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $platforms = json_decode($row['plataformas'], true) ?: [];
            $games[] = new Game(
                (int)$row['id'],
                $row['titulo'],
                $row['genero'],
                $platforms,
                $row['imagen'],
                $row['descripcion']
            );
        }
        return $games;
    }

    public function getGamesCounter(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM games");
        $counter = (int) $stmt->fetchColumn();
        return $counter;
    }
}
